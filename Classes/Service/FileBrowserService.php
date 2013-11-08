<?php

use Iresults\FS as FS;

class Tx_Sourcero_Service_FileBrowserService implements \TYPO3\CMS\Core\SingletonInterface {
	/**
	 * Replacement for slashes
	 */
	const REPLACEMENT_SLASH = '-_-';

	/**
	 * Replacement for dots
	 */
	const REPLACEMENT_DOT = '_-_';

	/**
	 * Replacement for colon
	 */
	const REPLACEMENT_COLON = '_--';

	/**
	 * @var \TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder
	 */
	protected $uriBuilder;

	/**
	 * Map of Mime Types for file suffix
	 * @var array
	 */
	static public $mimeTypeForSuffix = array(
		'js' => 'application/x-javascript',
		'json' => 'application/x-javascript',
		'css' => 'text/css',
		'scss' => 'text/x-scss',
		'html' => 'text/html',
		'xhtml' => 'text/html',
		'phtml' => 'text/html',
		'ts' => 'text/x-typoscript',
	);

	/**
	 * Returns the ID of the given file
	 *
	 * @param SplFileInfo|FS\Filesystem|string $file
	 * @return string
	 */
	static public function buildIdOfFile($file) {
		$id = '';
		if (is_array($file) && isset($file['relativePath'])) {
			$id = $file['relativePath'];
		} else if ($file instanceof SplFileInfo || $file instanceof FS\Filesystem) {
			$id = $file->getPath();
		} else if (is_string($file)) {
			$id = $file;
		}

		$id = str_replace(PATH_typo3conf . 'ext/', 'EXT:', $id);
		$id = str_replace(PATH_site, '', $id);

		// Build the ID
		if ($id[0] === '/') {
			$id = substr($id, 1);
		}
		$id = str_replace('/', self::REPLACEMENT_SLASH, $id);
		$id = str_replace('.', self::REPLACEMENT_DOT, $id);
		$id = str_replace(':', self::REPLACEMENT_COLON, $id);
		return $id;
	}

	/**
	 * Returns the file path of the given ID
	 *
	 * @param string $id
	 * @return string
	 */
	static public function buildPathOfId($id) {
		$path = $id;
		$path = str_replace(self::REPLACEMENT_SLASH,	'/', $path);
		$path = str_replace(self::REPLACEMENT_DOT,		'.', $path);
		$path = str_replace(self::REPLACEMENT_COLON,	':', $path);

		$absFile = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($path);
		if ($absFile) {
			$path = $absFile;
		}
		return $path;
	}

	/**
	 * Builds the information array containing all the file's data that will be sent (i.e. as JSON object)
	 *
	 * @param FS\FilesystemInterface $file
	 * @param bool                   $withContents
	 * @return array
	 */
	static public function buildFileInformationArrayOfFile($file, $withContents = TRUE) {
		/** @var FS\Filesystem $file */

		static $lengthOfPathToSite;
		!$lengthOfPathToSite ? $lengthOfPathToSite = strlen(PATH_site) + 1 /* +1 for the slash */ : 0;
		static $lengthOfPathToExtensionDir;
		!$lengthOfPathToExtensionDir ? $lengthOfPathToExtensionDir = strlen(PATH_typo3conf . 'ext/') : 0;

		$currentPath = $file->getPath();
		if (strpos($currentPath, 'fileadmin/') !== FALSE) {
			$uri = substr($currentPath, $lengthOfPathToSite);
		} else {
			$uri = 'EXT:' . substr($currentPath, $lengthOfPathToExtensionDir);
		}
		if (strpos($currentPath, 'cundd_composer/vendor/') !== FALSE) {
			$currentRelativePath = substr($uri, strpos($uri, 'cundd_composer/vendor') + 21);
		} else {
			$currentRelativePath = substr($uri, strpos($uri, '/'));
		}

		/** @var SplFileInfo $information */
		$information = new SplFileInfo($file->getPath());

		$currentFile = array(
			'id'			=> self::buildIdOfFile($file),
			'name' 			=> $file->getName(),
			'path' 			=> $file->getPath(),
			'relativePath' 	=> $currentRelativePath,
			'uri' 			=> $uri,

			'type'				=> static::getMimeTypeOfFile($file),
			'size'				=> $information->getSize(),
			'lastModifiedDate'	=> $information->getMTime(),
		);

		if ($file instanceof FS\File) {
			$currentFile['relativeDir'] = dirname($currentRelativePath);

			if ($withContents) {
				$currentFile['contents'] = $file->getContents();
			}
		}
		return $currentFile;
	}

	/**
	 * Returns the file's mime type
	 * @param  Iresults\FS\Filesystem $file
	 * @return string
	 */
	static public function getMimeTypeOfFile($file) {
		$suffix = $file->getSuffix();

		if ($file->getName() === 'setup.txt' || $file->getName() === 'constants.txt') {
			return 'text/x-typoscript';
		}
		if (isset(static::$mimeTypeForSuffix[$suffix])) {
			return static::$mimeTypeForSuffix[$suffix];
		}

		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mimeType = finfo_file($finfo, $file->getPath());
		finfo_close($finfo);
		return $mimeType;
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
	 * Returns the file tree of the given directory
	 * @param string $directory				The directory over which to iterate
	 * @param boolean $hideDotFiles			Specify if dot-files and -folders should be hidden
	 * @param string|object $currentFile	The current open file
	 * @param boolean $directoriesFirst		Specify if directories should be listed first
	 * @return array<array<mixed>>
	 */
	public function getFileListForFile($directory, $hideDotFiles = TRUE, $currentFile = '', $directoriesFirst = FALSE) {
		$tempObjects = array();
		/**
		 * @var SplFileInfo $object
		 */
		$object = NULL;
		$objects = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator(
				$directory,
				FilesystemIterator::KEY_AS_PATHNAME | FilesystemIterator::CURRENT_AS_FILEINFO
			),
			RecursiveIteratorIterator::SELF_FIRST
		);

		foreach ($objects as $path => $object) {
			$current = FALSE;
			$active = FALSE;

			// Hide dot-files and -folders
			if ($hideDotFiles && strpos($path, '/.') !== FALSE) {
				continue;
			}

			// Detect open file paths
			$objectPath = $object->getRealPath();
			if ($objectPath === $currentFile) {
				$current = TRUE;
				$active = TRUE;
			} else if (substr($currentFile, 0, strlen($objectPath)) === $objectPath) {
				$active = TRUE;
			}

			$isDirectory = $object->isDir();
			$currentNode = array(
				'path' 		=> $path,
				'object' 	=> $object,
				'active' 	=> $active,
				'current' 	=> $current,
				'isDir'		=> $isDirectory
			);

			if ($directoriesFirst) {
				$sortEarlyPrefix = '00___';
				$directoryPath = $object->getPath();
				$path = implode(DIRECTORY_SEPARATOR . $sortEarlyPrefix, explode(DIRECTORY_SEPARATOR, $directoryPath));
				if ($isDirectory) {
					$path .= DIRECTORY_SEPARATOR . $sortEarlyPrefix . $object->getFilename();
				} else {
					$path .= DIRECTORY_SEPARATOR . $object->getFilename();
				}
			}

			$currentNode['depth'] = $objects->getDepth();
			$tempObjects[$path] = $currentNode;
		}

		ksort($tempObjects);
		return $tempObjects;
	}

	/**
	 * Returns the filebrowser HTML code of the extensions files
	 * @param Tx_Sourcero_Domain_Model_File $file
	 * @return array
	 */
	public function getFileBrowserCodeForFile($file) {
		if ($file instanceof FS\File) {
			$path = $file->getExtensionPath();
		} else {
			$path = $file->getPath();
		}

		/**
		 * @var SplFileInfo $object
		 */
		$object = NULL;
		$objects = $this->getFileListForFile($path, TRUE, $file->getPath(), TRUE);

		$dom = new DomDocument('1.0');
		$list = $dom->createElement('ul');
		$list->setAttribute('class', 'directory-root');
		$dom->appendChild($list);
		$node = $list;
		$lastDepth = 0;
		foreach ($objects as $path => $fileSystemNode) {
			$object 		= $fileSystemNode['object'];
			$current 		= $fileSystemNode['current'];
			$active 		= $fileSystemNode['active'];
			$currentDepth 	= $fileSystemNode['depth'];
			$classOpenFiles = '';

			// Mark open file paths
			if ($current) {
				$classOpenFiles = 'act cur open ';
			} else if ($active) {
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

			if ($currentDepth == $lastDepth) {
				//the depth hasnt changed so just add another li
				$li = $dom->createElement('li');
				$li->setAttribute('class', $classOpenFiles . 'node');
				$li->appendChild($linkElement);

				$node->appendChild($li);
			} elseif ($currentDepth > $lastDepth) {
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
				$difference = $lastDepth - $currentDepth;
				for ($i = 0; $i < $difference; $difference--){
					$node = $node->parentNode->parentNode;
				}
				$li = $dom->createElement('li');
				$li->setAttribute('class', $classOpenFiles . 'node');
				$li->appendChild($linkElement);

				$node->appendChild($li);
			}
			$lastDepth = $currentDepth;
		}
		return $dom->saveHtml();
	}

	/**
	 * Returns the list of the extensions files
	 * @param Tx_Sourcero_Domain_Model_File $file
	 * @return array
	 */
	public function getFileBrowserArrayForFile($file) {
		$files = array();
		if ($file instanceof FS\File) {
			$path = $file->getExtensionPath();
		} else {
			$path = $file->getPath();
		}

		$createChildObjectCallback = function($foundObject, $foundPath, $callback, $treeObject, $children = NULL) {
			/** @var FS\Filesystem $foundObject */
			static $lengthOfPathToSite;
			!$lengthOfPathToSite ? $lengthOfPathToSite = strlen(PATH_site) + 1 /* +1 for the slash */ : 0;
			static $lengthOfPathToExtensionDir;
			!$lengthOfPathToExtensionDir ? $lengthOfPathToExtensionDir = strlen(PATH_typo3conf . 'ext/') : 0;

			$currentObjectArray = array();
			$currentFile = Tx_Sourcero_Service_FileBrowserService::buildFileInformationArrayOfFile($foundObject);
			if ($foundObject instanceof FS\Directory && !$children) {
				/** @var \Iresults\Core\Model\DataTree $treeObject */
				$children = array_values($treeObject->getChildObjectsAtPathRecursiveWithCallback($foundPath, $callback));
			}

			$currentObjectArray['obj'] = $currentFile;
			$currentObjectArray['path'] = $foundPath;
			if ($children) {
				$currentObjectArray['children'] = $children;
			}

			return $currentObjectArray;
		};

		/** @var \Iresults\Core\Model\DataTree $fileTree */
		$allPaths = array();
		$fileTree = FS\FileManager::generateTreeFromPath($path, $allPaths, 5);
		$files = array_values($fileTree->getChildObjectsAtPathRecursiveWithCallback('*', $createChildObjectCallback));
//		\Iresults\Core\Iresults::pd(($createChildObjectCallback($file, $path, $createChildObjectCallback, $fileTree, $files)));
		return $createChildObjectCallback($file, $path, $createChildObjectCallback, $fileTree, $files);




		$treeIterator = new RecursiveTreeIterator(
			new RecursiveDirectoryIterator($path),
			RecursiveTreeIterator::BYPASS_CURRENT);

		$lastDepth = 0;
		foreach($treeIterator as $key => $currentPath) {
			$currentDepth = $treeIterator->getDepth();

			// Hide dot-files and folders
			if (strpos($currentPath, '/.')) {
				continue;
			}

			if (strpos($currentPath, 'fileadmin/') !== FALSE) {
				$uri = substr($currentPath, $lengthOfPathToSite);
			} else {
				$uri = 'EXT:' . substr($currentPath, $lengthOfPathToExtensionDir);
			}
			if (strpos($path, 'cundd_composer/vendor/') !== FALSE) {
				$currentRelativePath = substr($uri, strpos($uri, 'cundd_composer/vendor') + 21);
			} else {
				$currentRelativePath = substr($uri, strpos($uri, '/'));
			}

			$currentFile = array(
				'name' 			=> basename($currentRelativePath),
				'path' 			=> $currentPath,
				'relativePath' 	=> $currentRelativePath,
				'relativeDir' 	=> dirname($currentRelativePath),
				'uri' 			=> $uri,
				'isDirectory' 	=> is_dir($currentPath),
				'isLast' 		=> strpos($key, '{E}'),
				'depth' 		=> $currentDepth,
				'depthDiff'		=> $lastDepth - $currentDepth,
			);
			if ($currentFile['isDirectory']) {
				//$currentFile['children'] =>
			}

			$lastDepth = $currentDepth;
		}

		\Iresults\Core\Iresults::pd($files);
		return $files;







		/**
		 * Thanks to http://www.php.net/manual/en/class.recursivedirectoryiterator.php#111142
		 */
		/** @var SplFileInfo $splFileInfo */

		/** @var RecursiveIteratorIterator $ritit */
		$ritit = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::CHILD_FIRST);
		$r = array();
		foreach ($ritit as $splFileInfo) {
			$path = $splFileInfo->isDir()
				? array($splFileInfo->getFilename() => array())
				: array($splFileInfo->getFilename());

			for ($depth = $ritit->getDepth() - 1; $depth >= 0; $depth--) {
				$path = array($ritit->getSubIterator($depth)->current()->getFilename() => $path);
			}
			$r = array_merge_recursive($r, $path);
		}

		\Iresults\Core\Iresults::pd($r);
		return $r;
	}

	/**
	 * Returns the list of the extensions files
	 * @param Tx_Sourcero_Domain_Model_File $file
	 * @param boolean $withDirectories
	 * @return array
	 */
	public function getFileBrowserForFile($file, $withDirectories = FALSE) {
		$files = array();
		$lengthOfPathToSite = strlen(PATH_site) + 1; // +1 for the slash
		$lengthOfPathToExtensionDir = strlen(PATH_typo3conf . 'ext/');

		if ($file instanceof FS\File) {
			$path = $file->getExtensionPath();
		} else {
			$path = $file->getPath();
		}
		$treeIterator = new RecursiveTreeIterator(
			new RecursiveDirectoryIterator($path),
			RecursiveTreeIterator::BYPASS_CURRENT);

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

			if (strpos($currentPath, 'fileadmin/') !== FALSE) {
				$uri = substr($currentPath, $lengthOfPathToSite);
			} else {
				$uri = 'EXT:' . substr($currentPath, $lengthOfPathToExtensionDir);
			}
			if (strpos($path, 'cundd_composer/vendor/') !== FALSE) {
				$currentRelativePath = substr($uri, strpos($uri, 'cundd_composer/vendor') + 21);
			} else {
				$currentRelativePath = substr($uri, strpos($uri, '/'));
			}

			$files[] = array(
				'name' 			=> basename($currentRelativePath),
				'path' 			=> $currentPath,
				'relativePath' 	=> $currentRelativePath,
				'relativeDir' 	=> dirname($currentRelativePath),
				'uri' 			=> $uri,
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
	 * @param \TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder $uriBuilder
	 */
	public function setUriBuilder($uriBuilder) {
		$this->uriBuilder = $uriBuilder;
		return $this;
	}

	/**
	 * @return \TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder
	 */
	public function getUriBuilder() {
		return $this->uriBuilder;
	}

}