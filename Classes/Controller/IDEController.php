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
		$getExists = function($that) {return $that->exists();};
		$getSuffix = function($that) {
			return pathinfo($that->getPath(), PATHINFO_EXTENSION);
		};
		$getExtensionKey = function($that) {
			$relativeExtensionPath = substr($that->getPath(), strlen(PATH_typo3conf . 'ext/'));
			return substr($relativeExtensionPath, 0, strpos($relativeExtensionPath, '/'));
		};
		$getExtensionPath = function($that) {
			return t3lib_extMgm::extPath($that->getExtensionKey());
		};

		FS\File::_instanceMethodForSelector('getExists', $getExists);
		FS\File::_instanceMethodForSelector('getSuffix', $getSuffix);
		FS\File::_instanceMethodForSelector('getExtensionKey', $getExtensionKey);
		FS\File::_instanceMethodForSelector('getExtensionPath', $getExtensionPath);

		FS\Directory::_instanceMethodForSelector('getExists', $getExists);
		FS\Directory::_instanceMethodForSelector('getSuffix', $getSuffix);
		FS\Directory::_instanceMethodForSelector('getExtensionKey', $getExtensionKey);
		FS\Directory::_instanceMethodForSelector('getExtensionPath', $getExtensionPath);
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

		#$this->view->assign('fileBrowser', $this->getFileBrowserForFile($file, TRUE));
		$this->view->assign('fileBrowserCode', $this->getFileBrowserCodeForFile($file));
		$this->view->assign('fileBrowserOpen', TRUE);
	}

	/**
	 * Returns the default CodeMirror configuration
	 * @return array
	 */
	public function getCodeMirrorConfiguration() {

		$absoluteCodeMirrorInstallPath = t3lib_extMgm::extPath('sourcero') . 'Resources/Public/Stylesheets/Library/CodeMirror/';
		$relativeCodeMirrorInstallPath = t3lib_extMgm::extRelPath('sourcero') . 'Resources/Public/Stylesheets/Library/CodeMirror/';

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
	 * Returns the filebrowser HTML code of the extensions files
	 * @param Tx_Sourcero_Domain_Model_File $file
	 * @param boolean $wit
	 * @return array
	 */
	public function getFileBrowserCodeForFile($file) {
		if ($file instanceof FS\File) {
			$path = $file->getExtensionPath();
		} else {
			$path = $file->getPath();
		}

		$filePath = $file->getPath();
		$filePathLength = strlen($filePath);

		#act = rootline
		#cur = current

		/**
		 * @var SplFileInfo $object
		 */
		$object = NULL;
		$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);
		$dom = new DomDocument('1.0');
		$list = $dom->createElement('ul');
		$list->setAttribute('class', 'directory-root');
		$dom->appendChild($list);
		$node = $list;
		$depth = 0;
		foreach ($objects as $path => $object) {
			$current = FALSE;
			$act = FALSE;
			$classOpenFiles = '';


			// Hide dot-files and folders
			if (strpos($path, '/.') !== FALSE) {
				continue;
			}

			// Detect open file paths
			$objectPath = $object->getRealPath();

//			echo 'obp:' . $objectPath . '<br>';
//			echo 'flp:' . $filePath . '<br>';
//			echo substr($filePath, 0, strlen($objectPath)) . ' : ' . $objectPath . ' ' . (substr($filePath, 0, strlen($objectPath)) === $objectPath) . '<br>';

			if ($objectPath === $filePath) {
				$current = TRUE;
				$act = TRUE;
				$classOpenFiles = 'act cur open ';
			} else if (substr($filePath, 0, strlen($objectPath)) === $objectPath) {
				$act = TRUE;
				$classOpenFiles = 'act open ';
			}

			// Create the link
			$link = $this->getEditUriForFile($object);
			$linkElement = $dom->createElement('a', $object->getFilename());
			$linkElement->setAttribute('href', '#');

			$class = $classOpenFiles . 'fileEdit';
			if ($object->isDir()) {
				$class = $classOpenFiles . 'directoryEdit';
			} else {
				$linkElement->setAttribute('href', $link);
			}
			$linkElement->setAttribute('class', $class);


			if ($objects->getDepth() == $depth) {
				//the depth hasnt changed so just add another li
				$li = $dom->createElement('li');
				$li->setAttribute('class', $classOpenFiles . 'node');
				$li->appendChild($linkElement);

				$node->appendChild($li);
			} elseif ($objects->getDepth() > $depth) {
				//the depth increased, the last li is a non-empty folder
				$li = $node->lastChild;
				#echo $classOpenFiles . $object->getRealPath() . '<br>';
				#$li->setAttribute('class', $classOpenFiles . 'directory');
				$ul = $dom->createElement('ul');
				$ul->setAttribute('class', $classOpenFiles . 'directory-container');
				$li->appendChild($ul);

				$filesystemLi = $dom->createElement('li');
				$filesystemLi->setAttribute('class', $classOpenFiles . 'node');
				$filesystemLi->appendChild($linkElement);

				$ul->appendChild($filesystemLi);
				$node = $ul;
			} else {
				//the depth decreased, going up $difference directories
				$difference = $depth - $objects->getDepth();
				for ($i = 0; $i < $difference; $difference--){
					$node = $node->parentNode->parentNode;
				}
				$li = $dom->createElement('li');
				$li->setAttribute('class', $classOpenFiles . 'node');
				$li->appendChild($linkElement);

				$node->appendChild($li);
			}
			$depth = $objects->getDepth();
		}
		return $dom->saveHtml();
	}

	/**
	 * Returns the URI to edit the given file
	 * @param  SplFileInfo $file 	File object
	 * @param  array  $arguments	An array of arguments
	 * @return string               Full link code
	 */
	public function getEditUriForFile($file, $arguments = array()) {
		$path = $file->getRealPath();
		$arguments['file'] = urlencode($path);
		$this->uriBuilder->reset();
		return $this->uriBuilder->uriFor('show', $arguments, 'IDE', 'sourcero', 'tools_sourcerosourcero');
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

		$treeIterator->setPrefixPart(RecursiveTreeIterator::PREFIX_LEFT, '</div><div class="line" style=""><span class="pop">[');
//		$treeIterator->setPrefixPart(RecursiveTreeIterator::PREFIX_LEFT, '</div><div class="line" style=""><span class="pop" style="width:340px;display:inline-block;">&nbsp;{L&nbsp;&nbsp;');
		$treeIterator->setPrefixPart(RecursiveTreeIterator::PREFIX_MID_HAS_NEXT, '-&nbsp;');
//		$treeIterator->setPrefixPart(RecursiveTreeIterator::PREFIX_MID_HAS_NEXT, '&nbsp;{m&nbsp;&nbsp;');
		$treeIterator->setPrefixPart(RecursiveTreeIterator::PREFIX_MID_LAST, '—&nbsp;');
		$treeIterator->setPrefixPart(RecursiveTreeIterator::PREFIX_END_HAS_NEXT, '⎜');
//		$treeIterator->setPrefixPart(RecursiveTreeIterator::PREFIX_END_HAS_NEXT, '&nbsp;{e&nbsp;&nbsp;');
		$treeIterator->setPrefixPart(RecursiveTreeIterator::PREFIX_END_LAST, '⎦&nbsp;'); // Is last
		$treeIterator->setPrefixPart(RecursiveTreeIterator::PREFIX_RIGHT, '</span>');
//		$treeIterator->setPrefixPart(RecursiveTreeIterator::PREFIX_RIGHT, '&nbsp;&nbsp;R}&nbsp;</span>');


//		const integer PREFIX_LEFT = 0 ;
//		const integer PREFIX_MID_HAS_NEXT = 1 ;
//		const integer PREFIX_MID_LAST = 2 ;
//		const integer PREFIX_END_HAS_NEXT = 3 ;
//		const integer PREFIX_END_LAST = 4 ;
//		const integer PREFIX_RIGHT = 5 ;



		echo <<<ECHOS
<style>
.line {
}

.line:hover {
background: rgba(230, 45, 45, 0.5);
}
.pop {
width:110px;
display:inline-block;
background: rgba(45, 230, 45, 0.2);
}
</style>
ECHOS;



		$lastDepth = 0;
		foreach($treeIterator as $key => $currentPath) {
			$currentDepth = $treeIterator->getDepth();

			// Hide dot-files and folders
			if (strpos($currentPath, '/.')) {
				continue;
			}

			// Filter off directories
			if (!$withDirectories && is_dir($currentPath)) {
				continue;
			}

			#echo $key . '<br>';

			$uri = substr($currentPath, strlen(PATH_typo3conf . 'ext/'));
			$currentRelativePath = substr($uri, strpos($uri, '/'));

			$files[] = array(
				'name' 			=> basename($currentRelativePath),
				'path' 			=> $currentPath,
				'relativePath' 	=> $currentRelativePath,
				'relativeDir' 	=> dirname($currentRelativePath),
				'uri' 			=> 'EXT:' . $uri,
				'isDirectory' 	=> is_dir($currentPath),
				'isLast' 		=> strpos($key, '{E}'),
				'depth' 		=> $currentDepth,
				'depthDiff'		=> $lastDepth - $currentDepth,
				'close' 		=> str_repeat('</ul>', $lastDepth - $currentDepth),
			);

			$lastDepth = $currentDepth;
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

