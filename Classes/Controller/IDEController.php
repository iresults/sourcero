<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Andreas Thurnheer-Meier <tma@iresults.li>, iresults
 *  Daniel Corn <cod@iresults.li>, iresults
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

if (!defined('TYPO3_MODE') || TYPO3_MODE !== 'BE') {
	echo 'Access denied';
	die();
}

Tx_CunddComposer_Autoloader::register();
use Iresults\FS as FS;
use Symfony\Component\Process\Process;
use TYPO3\CMS\Core\Utility as Utility;

/**
 *
 *
 * @package sourcero
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class Tx_Sourcero_Controller_IDEController extends Tx_Sourcero_Controller_AbstractController {

	/**
	 * repositoryRepository
	 *
	 * @var Tx_Sourcero_Domain_Repository_RepositoryRepository
	 */
	protected $repositoryRepository;

	/**
	 * @var Tx_Sourcero_Service_SCMService
	 * @inject
	 */
	protected $scmService;

	/**
	 * @var Tx_Sourcero_Service_FileBrowserService
	 * @inject
	 */
	protected $fileBrowserService;

	protected function initializeAction() {
		\Iresults\Core\Iresults::setDebugRenderer(\Iresults\Core\Iresults::RENDERER_KINT);


		$getExists = function($that) {return $that->exists();};
		$getSuffix = function($that) {
			/** @var Iresults\FS\File $that */
			return pathinfo($that->getPath(), PATHINFO_EXTENSION);
		};
		$getExtensionKey = function($that) {
			/** @var Iresults\FS\File $that */

			// If the file belongs into framework or fileadmin
			if (strpos($that->getPath(), '/fileadmin/framework/') !== FALSE) {
				return 'framework';
			} else if (strpos($that->getPath(), '/fileadmin/') !== FALSE) {
				return 'fileadmin';
			}

			// If the file belongs to a composer package
			$path = str_replace('//', '/', $that->getPath());
			if (strpos($path, 'cundd_composer/vendor/') !== FALSE) {
				list ($vendor, $extension) = $that->getVendorAndExtensionNameForComposerPackage();
				return $vendor . '/' . $extension;
			}
			$relativeExtensionPath = substr($path, strlen(PATH_typo3conf . 'ext/'));

			\Iresults\Core\Iresults::pd($relativeExtensionPath, $path, PATH_typo3conf);
			return substr($relativeExtensionPath, 0, strpos($relativeExtensionPath, '/'));
		};
		$getExtensionPath = function($that) {
			/** @var Iresults\FS\File $that */

			// If the file belongs into framework or fileadmin
			if (strpos($that->getPath(), '/fileadmin/framework/') !== FALSE) {
				return PATH_site . '/fileadmin/framework/';
			} else if (strpos($that->getPath(), '/fileadmin/') !== FALSE) {
				return PATH_site . '/fileadmin/';
			}

			// If the file belongs to a composer package
			$path = str_replace('//', '/', $that->getPath());
			if (strpos($path, 'cundd_composer/vendor/') !== FALSE) {
				list ($vendor, $extension) = $that->getVendorAndExtensionNameForComposerPackage();
				return Utility\ExtensionManagementUtility::extPath('cundd_composer') . 'vendor/' . $vendor . '/' . $extension . '/';
			}
			return Utility\ExtensionManagementUtility::extPath($that->getExtensionKey());
		};
		$getVendorAndExtensionNameForComposerPackage = function($that) {
			$path = str_replace('//', '/', $that->getPath());
			$composerVendorDirPosition = strpos($path, 'cundd_composer/vendor/');
			if ($composerVendorDirPosition !== FALSE) {
				$extensionRelativePath = substr($path, $composerVendorDirPosition + 22);
				list ($vendor, $extension, ) = explode(DIRECTORY_SEPARATOR, $extensionRelativePath);
				return array($vendor, $extension);
			}
			return FALSE;
		};

		FS\File::_instanceMethodForSelector('getExists', $getExists);
		FS\File::_instanceMethodForSelector('getSuffix', $getSuffix);
		FS\File::_instanceMethodForSelector('getExtensionKey', $getExtensionKey);
		FS\File::_instanceMethodForSelector('getExtensionPath', $getExtensionPath);
		FS\File::_instanceMethodForSelector('getVendorAndExtensionNameForComposerPackage', $getVendorAndExtensionNameForComposerPackage);

		FS\Directory::_instanceMethodForSelector('getExists', $getExists);
		FS\Directory::_instanceMethodForSelector('getSuffix', $getSuffix);
		FS\Directory::_instanceMethodForSelector('getExtensionKey', $getExtensionKey);
		FS\Directory::_instanceMethodForSelector('getExtensionPath', $getExtensionPath);
		FS\Directory::_instanceMethodForSelector('getVendorAndExtensionNameForComposerPackage', $getVendorAndExtensionNameForComposerPackage);
	}

	/**
	 * injectRepositoryRepository
	 *
	 * @param Tx_Sourcero_Domain_Repository_RepositoryRepository $repositoryRepository
	 * @return void
	 */
	public function injectRepositoryRepository(Tx_Sourcero_Domain_Repository_RepositoryRepository $repositoryRepository) {
		$this->repositoryRepository = $repositoryRepository;
	}

	/**
	 * action list
	 *
	 * @param string $file
	 * @return void
	 */
	public function listAction($file) {
		$fileManager = FS\FileManager::sharedFileManager();
		$file = $fileManager->getResourceAtUrl($file);
		$this->view->assign('file', $file);
		$this->view->assign('fileBrowser', $this->getFileBrowserForFile($file, FALSE));
		$this->view->assign('fileBrowserCode', $this->getFileBrowserCodeForFile($file));
		$this->view->assign('fileBrowserOpen', TRUE);

		$this->view->assign('project', $this->getProjectForFile($file));

		$this->setCustomFaviconWithBasePath($file->getExtensionPath());
	}

	/**
	 * Start the web app
	 *
	 * @param string $file
	 * @return void
	 */
	public function appAction($file) {
		$scripts = array();
		$stylesheets = array();
		$fileManager = FS\FileManager::sharedFileManager();
		$fileObject = $fileManager->getResourceAtUrl($file);


		$builtIndexFile = $fileManager->getResourceAtUrl(
			Utility\GeneralUtility::getFileAbsFileName('EXT:sourcero/Resources/Public/JavaScript/webapp/dist/index.html')
		);
		\Iresults\Core\Iresults::pd($builtIndexFile);


		\Iresults\Core\Iresults::pd(
			preg_match_all('!<link rel="stylesheet" href="(.*)"!', $builtIndexFile->contents())
		);

		if (preg_match_all('!<script src="(.*)"!', $builtIndexFile->contents(), $scripts)) {
			$scripts = $scripts[1];
		} else {
			$scripts = array();
		}
		if (preg_match_all('!<link rel="stylesheet" href="(.*)"!', $builtIndexFile->contents(), $stylesheets)) {
			$stylesheets = $stylesheets[1];
		} else {
			$stylesheets = array();
		}

		$this->view->assign('scripts', $scripts);
		$this->view->assign('stylesheets', $stylesheets);
		$this->view->assign('project', $this->getProjectForFile($fileObject));

		$this->setCustomFaviconWithBasePath($fileObject->getExtensionPath());
	}

	/**
	 * action show
	 *
	 * @param string $file
	 * @return void
	 */
	public function showAction($file) {
		$file = urldecode($file);
		$absFile = Utility\GeneralUtility::getFileAbsFileName($file);
		if ($absFile) {
			$file = $absFile;
		}

		$fileManager = FS\FileManager::sharedFileManager();
		$file = $fileManager->getResourceAtUrl($file);
		if ($file instanceof FS\Directory) {
			$this->redirect('list', 'IDE', NULL, array('file' => $file->getExtensionPath()));
		}

		$this->initCodeMirrorForFile($file);
		#$this->redirect('edit', 'IDE', NULL, array('file' => $file));

		$this->view->assign('fileBrowser', $this->getFileBrowserForFile($file, FALSE));
		$this->view->assign('fileBrowserCode', $this->getFileBrowserCodeForFile($file));
		$this->view->assign('fileBrowserOpen', TRUE);

		$this->view->assign('project', $this->getProjectForFile($file));

		$this->setCustomFaviconWithBasePath($file->getExtensionPath());

		$this->view->assign('repositories', $this->repositoryRepository->findAll());
	}

	/**
	 * Returns the list of files
	 * @param string $file
	 * @return mixed
	 */
	public function fileListAction($file) {
		$file = urldecode($file);
		$absFile = Utility\GeneralUtility::getFileAbsFileName($file);
		if (!$absFile) {
			$absFile = Utility\GeneralUtility::getFileAbsFileName($file . '/ext_emconf.php');
		}
		if ($absFile) {
			$file = $absFile;
		}

		\Iresults\Core\Iresults::pd('ENDE');

		/** @var FS\FileManager $fileManager */
		$fileManager = FS\FileManager::sharedFileManager();
		/** @var FS\FilesystemInterface $fileObject */
		$fileObject = $fileManager->getResourceAtUrl($file);
		/** @var array $fileArray */
		$fileArray = $this->getFileBrowserArrayForFile($fileObject, FALSE);

		\Iresults\Core\Iresults::pd($fileArray);
		\Iresults\Core\Iresults::pd(json_encode($fileArray));
		return !$this->prepareJsonResponse(array(
			'fileTree' => $fileArray
		));
	}

	/**
	 * Returns the file's content
	 * @param string $file
	 * @return mixed
	 */
	public function fileAction($file) {
		$file = urldecode($file);
		$absFile = Utility\GeneralUtility::getFileAbsFileName($file);
		if ($absFile) {
			$file = $absFile;
		}

		/** @var FS\FileManager $fileManager */
		$fileManager = FS\FileManager::sharedFileManager();
		/** @var FS\FilesystemInterface $fileObject */
		$fileObject = $fileManager->getResourceAtUrl($file);
		/** @var array $fileInformation */
		$fileInformation = Tx_Sourcero_Service_FileBrowserService::buildFileInformationArrayOfFile($fileObject);
		return !$this->prepareJsonResponse($fileInformation);
	}

	/**
	 * Prepares the response with the given JSON content
	 *
	 * @param mixed $content
	 * @throws InvalidArgumentException
	 * @return bool Returns TRUE if the response was prepared correctly
	 */
	protected function prepareJsonResponse($content) {
		if (is_string($content)) {
			$contentString = $content;
		} else {
			if (defined('JSON_UNESCAPED_UNICODE')) {
				$contentString = json_encode($content, JSON_UNESCAPED_UNICODE);
//				var_dump(gettype($content));
//				var_dump(($content));
//				var_dump(($contentString));
//			var_dump($contentString);
//			var_dump(json_last_error_msg());
				if ($contentString === FALSE) {
					throw new InvalidArgumentException(json_last_error_msg(), json_last_error());
				}
			} else {
				$contentString = json_encode($content);
			}
		}
		/** @var \TYPO3\CMS\Extbase\Mvc\Web\Response $response */
		$response = $this->response;
		$response->setHeader('Content-Type', 'application/json');
		$response->setContent($contentString);
		$this->response = $response;
		return TRUE;
	}


	/**
	 * Returns the default CodeMirror configuration
	 * @return array
	 */
	public function getCodeMirrorConfiguration() {

//		$absoluteCodeMirrorInstallPath = Utility\ExtensionManagementUtility::extPath('sourcero') . 'Resources/Public/Stylesheets/Library/CodeMirror/';
//		$relativeCodeMirrorInstallPath = Utility\ExtensionManagementUtility::extRelPath('sourcero') . 'Resources/Public/Stylesheets/Library/CodeMirror/';
		$absoluteCodeMirrorInstallPath = Utility\ExtensionManagementUtility::extPath('sourcero') . 'Resources/Public/Library/codemirror-3.15/';
		$relativeCodeMirrorInstallPath = Utility\ExtensionManagementUtility::extRelPath('sourcero') . 'Resources/Public/Library/codemirror-3.15/';

		// Find all Addons
		$addons = FS\FileManager::find($absoluteCodeMirrorInstallPath . 'addon/*/*.js');

		// Add a method to the FS\File class to return the type of the addon
		FS\File::_instanceMethodForSelector('getAddonType', function($that) {return basename(dirname($that->getPath()));});

		// Filter remove all Addons with type "runmode"
		$addons = array_filter($addons, function ($addon) {return $addon->getAddonType() !== 'runmode';});

		$codeMirrorConfiguration = array(
			'addons' => $addons,
			'installPath' => $relativeCodeMirrorInstallPath,
		);
		return $codeMirrorConfiguration;
	}

	/**
	 * Returns the name of the CodeMirror mode for the given file
	 * @param  Iresults\FS\Filesystem $file
	 * @return string
	 */
	protected function getCodeMirrorModeForFile($file) {
		$mimeType = $this->getMimeTypeOfFile($file);
		$mode = str_replace(
			array(
				'application/x-', 'text/x-',
				'application/', 'text/'
			), '', $mimeType);

		if ($mode === 'html') {
			$mode = 'htmlmixed';
		} else if ($mode === 'scss') {
			$mode = 'text/x-scss';
		}
		return $mode;
	}

	/**
	 * Returns the file's mime type
	 * @param  Iresults\FS\Filesystem $file
	 * @return string
	 */
	protected function getMimeTypeOfFile($file) {
		return Tx_Sourcero_Service_FileBrowserService::getMimeTypeOfFile($file);
	}

	/**
	 * action new
	 *
	 * @param Tx_Sourcero_Domain_Model_File $file
	 * @dontvalidate $file
	 * @return void
	 */
	public function newAction($file = NULL) {
		$this->view->assign('file', $file);
	}

	/**
	 * action create
	 *
	 * @param Tx_Sourcero_Domain_Model_File $file
	 * @return void
	 */
	public function createAction($file) {
		$this->redirect('list');
	}

	/**
	 * action edit
	 *
	 * @param Tx_Sourcero_Domain_Model_File $file
	 * @return void
	 */
	public function editAction($file) {
		$fileManager = FS\FileManager::sharedFileManager();
		$file = $fileManager->getResourceAtUrl($file);

	}

	/**
	 * action update
	 *
	 * @param string $path
	 * @param string $contents
	 * @return mixed
	 */
	public function updateAction($path, $contents) {
		$path = Tx_Sourcero_Service_FileBrowserService::buildPathOfId($path);
		$fileManager = FS\FileManager::sharedFileManager();
		$file = $fileManager->getResourceAtUrl($path);

		$contents = $this->formatCode($contents);
		$success = $file->setContents($contents);

		/** @var array $fileInformation */
		$fileInformation = Tx_Sourcero_Service_FileBrowserService::buildFileInformationArrayOfFile($file);

		// Handle AJAX/JSON requests
		if ($this->request->getFormat() === 'json') {
			if ($success) {
				return json_encode(
					array_merge($fileInformation, array(
						'meta' => array(
							'success' => TRUE,
						)
					))
				);
			} else {
				$this->response->setStatus(500);
				return json_encode(
					array_merge($fileInformation, array(
						'meta' => array(
							'success' => FALSE,
							'error' => $this->getUpdateError($file)
						)
					))
				);
			}
		}
		if ($success) {
			$this->flashMessageContainer->add('File successfully saved');
		} else {
			$this->flashMessageContainer->add('Could not save', 'Error', \TYPO3\CMS\Core\Messaging\FlashMessage::ERROR);
		}
		$this->redirect('show', 'IDE', NULL, array('file' => $path));
	}

	/**
	 * Returns the error description of the update error
	 *
	 * @param FS\File $file
	 * @return array
	 */
	public function getUpdateError($file) {
		$message = '';
		if (!$file->isWriteable()) {
			$message = 'File not writeable';
		}
		return array(
			'code' => 1373116207,
			'message' => $message
		);
	}

	/**
	 * action delete
	 *
	 * @param string $file
	 * @return void
	 */
	public function deleteAction($file) {
		$absFile = Utility\GeneralUtility::getFileAbsFileName($file);
		if ($absFile) {
			$file = $absFile;
		}

		$fileManager = FS\FileManager::sharedFileManager();
		$file = $fileManager->getResourceAtUrl($file);
		$success = $file->delete();

		if ($success) {
			$this->flashMessageContainer->add('File successfully deleted');
		} else {
			$this->flashMessageContainer->add('Could not delete', 'Error', \TYPO3\CMS\Core\Messaging\FlashMessage::ERROR);
		}
		$this->redirect('show', 'IDE', NULL, array('file' => $file->getExtensionPath())); // Show IDE
		// $this->redirect('show', 'Repository', NULL, array('repository' => $file->getExtensionKey())); // Show the Repository overview
	}

	/**
	 * Replaces trailing whitespaces
	 * @param string $text
	 * @return string
	 */
	protected function formatCode($text) {
		// Normalize line endings
		// Convert all line-endings to UNIX format
		$text = str_replace("\r\n", "\n", $text);
		$text = str_replace("\r", "\n", $text);

		// Don't allow multiple new-lines
		// $text = preg_replace("/\n{2,}/", "\n\n", $text);

		$lines = explode("\n", $text);
		foreach ($lines as &$line) {
			$line = rtrim($line);
		}
		return implode("\n", $lines);
	}

	/**
	 * Returns the filebrowser HTML code of the extensions files
	 * @param Tx_Sourcero_Domain_Model_File $file
	 * @return array
	 */
	public function getFileBrowserCodeForFile($file) {
		return $this->fileBrowserService->setUriBuilder($this->uriBuilder)->getFileBrowserCodeForFile($file);
	}

	/**
	 * Returns the list of the extensions files
	 * @param Tx_Sourcero_Domain_Model_File $file
	 * @param boolean $withDirectories
	 * @return array
	 */
	public function getFileBrowserForFile($file, $withDirectories = FALSE) {
		return $this->fileBrowserService->setUriBuilder($this->uriBuilder)->getFileBrowserForFile($file, $withDirectories);
	}

	/**
	 * Returns the nested array of the extensions files
	 *
	 * @param Tx_Sourcero_Domain_Model_File $file
	 * @param boolean $withContents
	 * @return array
	 */
	public function getFileBrowserArrayForFile($file, $withContents = TRUE) {
		return $this->fileBrowserService->setUriBuilder($this->uriBuilder)->getFileBrowserArrayForFile($file, $withContents);
	}

	/**
	 * Returns a virtual project for the given file
	 *
	 * @param Tx_Sourcero_Domain_Model_File $file
	 * @return array
	 */
	public function getProjectForFile($file) {
		if ($file instanceof FS\File) {
			$path = $file->getExtensionPath();
		} else {
			$path = $file->getPath();
		}
		return array(
			'name' 		=> $file->getExtensionKey(),
			'path' 		=> $file->getExtensionPath(),
		);
	}

	/**
	 * Initialize code mirror
	 *
	 * @param Tx_Sourcero_Domain_Model_File $file
	 * @return void
	 */
	protected function initCodeMirrorForFile($file) {
		$mimeType = $this->getMimeTypeOfFile($file);
		$codeMirrorConfiguration = $this->getCodeMirrorConfiguration();
		$codeMirrorConfiguration['mode'] = $this->getCodeMirrorModeForFile($file);

		$this->view->assign('file', $file);
		$this->view->assign('fileMimeType', $mimeType);
		$this->view->assign('codeMirror', $codeMirrorConfiguration);

		// Detect binary files
		if (substr($mimeType, 0, 6) === 'image/') {
			$this->view->assign('fileBinaryData', '<img alt="Embedded Image" src="data:' . $mimeType . ';base64,' . base64_encode($file->getContents()) . '" />');
			$this->view->assign('fileIsBinary', TRUE);
		} else if (substr($mimeType, 0, 6) === 'audio/'
			|| substr($mimeType, 0, 6) === 'video/') {
			$this->view->assign('fileIsBinary', TRUE);
		} else {
			$this->view->assign('fileIsBinary', FALSE);
		}
	}
}
?>

