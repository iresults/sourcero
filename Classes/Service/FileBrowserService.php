<?php

use Iresults\FS as FS;

class Tx_Sourcero_Service_FileBrowserService implements \TYPO3\CMS\Core\SingletonInterface {
	/**
	 * @var \TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder
	 */
	protected $uriBuilder;

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