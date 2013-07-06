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
use Symfony\Component\Process\Process;
use Iresults\FS as FS;
use \TYPO3\CMS\Core\Utility as Utility;

/**
 *
 *
 * @package sourcero
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class Tx_Sourcero_Controller_IDEController extends Tx_Extbase_MVC_Controller_ActionController {

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

	/**
	 * Map of Mime Types for file suffix
	 * @var array
	 */
	protected $mimeTypeForSuffix = array(
		'js' => 'application/x-javascript',
		'json' => 'application/x-javascript',
		'css' => 'text/css',
		'scss' => 'text/x-scss',
		'html' => 'text/html',
		'xhtml' => 'text/html',
		'phtml' => 'text/html',
		'ts' => 'text/x-typoscript',
	);

	protected function initializeAction() {
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
			if (strpos($that->getPath(), '/cundd_composer/vendor/') !== FALSE) {
				list ($vendor, $extension) = $that->getVendorAndExtensionNameForComposerPackage();
				return $vendor . '/' . $extension;
			}
			$relativeExtensionPath = substr($that->getPath(), strlen(PATH_typo3conf . 'ext/'));
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
			if (strpos($that->getPath(), '/cundd_composer/vendor/') !== FALSE) {
				list ($vendor, $extension) = $that->getVendorAndExtensionNameForComposerPackage();
				return Utility\ExtensionManagementUtility::extPath('cundd_composer') . '/vendor/' . $vendor . '/' . $extension . '/';
			}
			return Utility\ExtensionManagementUtility::extPath($that->getExtensionKey());
		};
		$getVendorAndExtensionNameForComposerPackage = function($that) {
			$path = $that->getPath();
			$composerVendorDirPosition = strpos($path, '/cundd_composer/vendor/');
			if ($composerVendorDirPosition !== FALSE) {
				$extensionRelativePath = substr($path, $composerVendorDirPosition + 23);
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
		#$file = urldecode($file);
		#$file = t3lib_div::getFileAbsFileName($file);

		$fileManager = FS\FileManager::sharedFileManager();
		$file = $fileManager->getResourceAtUrl($file);
		$this->view->assign('file', $file);
		#$this->view->assign('fileBrowser', $this->getFileBrowserForFile($file, FALSE));
		$this->view->assign('fileBrowserCode', $this->getFileBrowserCodeForFile($file));
		$this->view->assign('fileBrowserOpen', TRUE);

		$this->view->assign('project', $this->getProjectForFile($file));
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
	}

	/**
	 * Returns the default CodeMirror configuration
	 * @return array
	 */
	public function getCodeMirrorConfiguration() {

		$absoluteCodeMirrorInstallPath = Utility\ExtensionManagementUtility::extPath('sourcero') . 'Resources/Public/Stylesheets/Library/CodeMirror/';
		$relativeCodeMirrorInstallPath = Utility\ExtensionManagementUtility::extRelPath('sourcero') . 'Resources/Public/Stylesheets/Library/CodeMirror/';

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
		$suffix = $file->getSuffix();

		if ($file->getName() === 'setup.txt' || $file->getName() === 'constants.txt') {
			return 'text/x-typoscript';
		}
		if (isset($this->mimeTypeForSuffix[$suffix])) {
			return $this->mimeTypeForSuffix[$suffix];
		}

		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mimeType = finfo_file($finfo, $file->getPath());
		finfo_close($finfo);
		return $mimeType;
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
		$fileManager = FS\FileManager::sharedFileManager();
		$file = $fileManager->getResourceAtUrl($path);

		$contents = $this->formatCode($contents);
		$success = $file->setContents($contents);

		// Handle AJAX/JSON requests
		if ($this->request->getFormat() === 'json') {
			if ($success) {
				return json_encode(array('success' => TRUE));
			} else {
				$this->response->setStatus(500);
				return json_encode(array('success' => FALSE, 'error' => $this->getUpdateError($file)));
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
	 * @param FS\File $file
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
	 * @param boolean $wit
	 * @return array
	 */
	public function getFileBrowserForFile($file, $withDirectories = FALSE) {
		return $this->fileBrowserService->setUriBuilder($this->uriBuilder)->getFileBrowserForFile($file, $withDirectories);
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

