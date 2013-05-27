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
	);

	protected function initializeAction() {
		FS\File::_instanceMethodForSelector('getExists', function($that) {return $that->exists();});
		FS\File::_instanceMethodForSelector('getSuffix', function($that) {
			return pathinfo($that->getPath(), PATHINFO_EXTENSION);
		});
		FS\File::_instanceMethodForSelector('getExtensionKey', function($that) {
			$relativeExtensionPath = substr($that->getPath(), strlen(PATH_typo3conf . 'ext/'));
			return substr($relativeExtensionPath, 0, strpos($relativeExtensionPath, '/'));
		});
		FS\File::_instanceMethodForSelector('getExtensionPath', function($that) {
			return t3lib_extMgm::extPath($that->getExtensionKey());
		});
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
		$this->view->assign('fileBrowser', $this->getFileBrowserForFile($file, FALSE));
		$this->view->assign('fileBrowserOpen', TRUE);
	}

	/**
	 * action show
	 *
	 * @param string $file
	 * @return void
	 */
	public function showAction($file) {
		$file = urldecode($file);
		$absFile = t3lib_div::getFileAbsFileName($file);
		if ($absFile) {
			$file = $absFile;
		}

		$fileManager = FS\FileManager::sharedFileManager();
		$file = $fileManager->getResourceAtUrl($file);

		$this->initCodeMirrorForFile($file);
		#$this->redirect('edit', 'IDE', NULL, array('file' => $file));

		$this->view->assign('fileBrowser', $this->getFileBrowserForFile($file, FALSE));
	}

	/**
	 * Returns the default CodeMirror configuration
	 * @return array
	 */
	public function getCodeMirrorConfiguration() {
		$absoluteCodeMirrorInstallPath = t3lib_extMgm::extPath('cundd_composer') . 'vendor/marijnh/codemirror/';
		$relativeCodeMirrorInstallPath = t3lib_extMgm::extRelPath('cundd_composer') . 'vendor/marijnh/codemirror/';

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
	 * @return void
	 */
	public function updateAction($path, $contents) {
		$fileManager = FS\FileManager::sharedFileManager();
		$file = $fileManager->getResourceAtUrl($path);

		$contents = $this->removeTrailingWhitespaces($contents);
		$success = $file->setContents($contents);

		if ($success) {
			$this->flashMessageContainer->add('File successfully saved');
		} else {
			$this->flashMessageContainer->add('Could not save', 'Error', t3lib_Flashmessage::WARNING);
		}
		$this->redirect('show', 'IDE', NULL, array('file' => $path));
	}

	/**
	 * action delete
	 *
	 * @param string $file
	 * @return void
	 */
	public function deleteAction($file) {
		$absFile = t3lib_div::getFileAbsFileName($file);
		if ($absFile) {
			$file = $absFile;
		}

		$fileManager = FS\FileManager::sharedFileManager();
		$file = $fileManager->getResourceAtUrl($file);
		$success = $file->delete();

		if ($success) {
			$this->flashMessageContainer->add('File successfully deleted');
		} else {
			$this->flashMessageContainer->add('Could not delete', 'Error', t3lib_Flashmessage::WARNING);
		}
		$this->redirect('list', 'Repository');
	}

	/**
	 * Replaces trailing whitespaces
	 * @param string $text
	 * @return string
	 */
	protected function removeTrailingWhitespaces($text) {
		// Normalize line endings
		// Convert all line-endings to UNIX format
		$text = str_replace("\r\n", "\n", $text);
		$text = str_replace("\r", "\n", $text);
		// Don't allow out-of-control blank lines
		$text = preg_replace("/\n{2,}/", "\n\n", $text);

		$lines = explode("\n", $text);
		foreach ($lines as &$line) {
			$line = rtrim($line);
		}
		return implode("\n", $lines);
	}

	/**
	 * Returns the list of the extensions files
	 * @param Tx_Sourcero_Domain_Model_File $file
	 * @param boolean $wit
	 * @return array
	 */
	public function getFileBrowserForFile($file, $withDirectories = FALSE) {
		$files = array();

		if ($file instanceof FS\File) {
			$path = $file->getExtensionPath();
		} else {
			$path = $file->getPath();
		}
		$treeIterator = new RecursiveTreeIterator(
			new RecursiveDirectoryIterator($path),
			RecursiveTreeIterator::BYPASS_CURRENT);

		foreach($treeIterator as $currentPath) {
			// Hide dot-files and folders
			if (strpos($currentPath, '/.')) {
				continue;
			}

			// Filter off directories
			if (!$withDirectories && is_dir($currentPath)) {
				continue;
			}

			$uri = substr($currentPath, strlen(PATH_typo3conf . 'ext/'));
			$currentRelativePath = substr($uri, strpos($uri, '/'));

			$files[] = array(
				'name' => basename($currentRelativePath),
				'path' => $currentPath,
				'relativePath' => $currentRelativePath,
				'relativeDir' => dirname($currentRelativePath),
				'uri' => 'EXT:' . $uri,
				'isDirectory' => is_dir($currentPath),
			);
		}
		return $files;
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

