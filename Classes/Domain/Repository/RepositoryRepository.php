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

/**
 *
 *
 * @package sourcero
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class Tx_Sourcero_Domain_Repository_RepositoryRepository extends Tx_Extbase_Persistence_Repository {
	/**
	 * The property mapper
	 *
	 * @var Tx_Extbase_Property_PropertyMapper
	 * @inject
	 */
	protected $propertyMapper;

	/**
	 * The property mapping configuration builder
	 *
	 * @var Tx_Extbase_Property_PropertyMappingConfigurationBuilder
	 * @inject
	 */
	protected $propertyMappingConfigurationBuilder;

	/**
	 * Array of package objects
	 *
	 * @var SplObjectStorage
	 */
	protected $repositories = NULL;

	/**
	 * An array of SCM types with their according directory names
	 * @var array
	 */
	protected $directoryNameForSCMType = array(
		Tx_Sourcero_Domain_Enum_SCMType::GIT			=> 'git',
		Tx_Sourcero_Domain_Enum_SCMType::MERCURIAL 		=> 'hg',
		Tx_Sourcero_Domain_Enum_SCMType::SUBVERSION 	=> 'svn',
		Tx_Sourcero_Domain_Enum_SCMType::CVS 			=> 'cvs',

		// Aliases
		'Concurrent Versions System' 					=> 'cvs',
	);

	/**
	 * An array of directory names with their according SCM types
	 * @var array
	 */
	protected $SCMTypeForDirectoryName = array(
		'git'	=> Tx_Sourcero_Domain_Enum_SCMType::GIT,
		'hg' 	=> Tx_Sourcero_Domain_Enum_SCMType::MERCURIAL,
		'svn' 	=> Tx_Sourcero_Domain_Enum_SCMType::SUBVERSION,
		'cvs' 	=> Tx_Sourcero_Domain_Enum_SCMType::CVS,
	);

	/**
	 * Returns all objects of this repository.
	 *
	 * @return array
	 * @api
	 */
	public function findAll() {
		if (!$this->repositories) {
			$this->repositories = new \SplObjectStorage();

			$repositoryList = $this->_getDirectoryBasedRepositoriesWithWildcard();
			ksort($repositories);
			$index = 0;
			foreach ($repositories as &$repository) {
				$repository->_setProperty('uid', $index++);
			}

			foreach ($repositoryList as $repositoryData) {
				$repository = $this->_getObjectFromRepositoryData($repositoryData);
				if ($repository) {
					$this->repositories->attach($repository);
				}
			}
		}
		return $this->repositories;
	}

	/**
	 * Finds an object matching the given identifier.
	 *
	 * @param string $uid The identifier of the object to find
	 * @return Tx_Sourcero_Domain_Model_Repository The matching object if found, otherwise NULL
	 * @api
	 */
	public function findByUid($uid) {
		if (!is_numeric($uid)) {
			return $this->findOneByTitle($uid);
		}
		$repositories = $this->findAll();
		if (isset($repositories[$uid])) {
			return $repositories[$uid];
		}
		return NULL;
	}

	/**
	 * Finds an object matching the given title.
	 *
	 * @param string $title Extension key
	 * @return Tx_Sourcero_Domain_Model_Repository The matching object if found, otherwise NULL
	 * @api
	 */
	public function findOneByTitle($title) {
		$object = NULL;
		if ($title === 'framework') {
			$object = $this->_getFileadminFrameworkRepository();
		} else if ($title === 'fileadmin') {
			$object = $this->_getFileadminRepository();
		}

		if (!$object) {
			try {
				$object = $this->_getDirectoryBasedRepositoryWithWildcard($title);
			} catch (BadFunctionCallException $exception) {
				/*
				 * If the given $title is not an $extension name look inside the
				 * composer directory
				 */
				$composerVendorDir = $this->_getComposerVendorDirectory();
				if ($composerVendorDir && $exception->getCode() === 1270853878) {
					$object = $this->_getDirectoryBasedRepositoryWithWildcard($title, NULL, '.', $composerVendorDir . $title . '/');
				}
			}
		}
		if ($object) {
			$object = $this->_getObjectFromRepositoryData($object);
		}

		return $object;
	}

	/**
	 * Finds the objects matching the given title.
	 *
	 * This method only wraps the result of findOneByTitle() into an array
	 *
	 * @param string $title Extension key
	 * @return array<Tx_Sourcero_Domain_Model_Repository> The matching object if found, otherwise NULL
	 * @api
	 */
	public function findByTitle($title) {
		$object = $this->findOneByTitle($title);
		if ($object) {
			return array($object);
		}
		return array();
	}

	/**
	 * Returns a new repository with the given data
	 * @param  array<string> $data Repository data
	 * @return Tx_Sourcero_Domain_Model_Repository
	 */
	protected function _getObjectFromRepositoryData($data) {
		// Prepare the property mapping configuration
		$propertyMappingConfiguration = $this->propertyMappingConfigurationBuilder->build();

		// Filter the properties
		$data = array_intersect_key($data, array_flip($this->_getDomainModelsProperties()));
		// Doesn't work in extbase: $propertyMappingConfiguration->allowProperties($properties);

		$repository = $this->propertyMapper->convert($data, 'Tx_Sourcero_Domain_Model_Repository');
		return $repository;
	}

	/**
	 * Returns the list of all git repositories
	 *
	 * @return array<string>
	 */
	public function getGitRepositories() {
		return $this->_getDirectoryBasedRepositoriesWithType('git');
	}

	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* GET REPOSITORY WITH KNOWN TYPE   WMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/**
	 * Returns the list of all extensions that contain a repository
	 *
	 * @param string $type Type/name of the SCM directory (i.e.: 'git', 'svn')
	 * @param string $prefix Optional prefix for the SCM directory
	 * @return array<string>
	 */
	protected function _getDirectoryBasedRepositoriesWithType($type, $prefix = '.') {
		$repositories = array();
		$extensions = explode(',', t3lib_extMgm::getEnabledExtensionList());

		foreach ($extensions as $extension) {
			$repository = $this->_getDirectoryBasedRepositoryWithType($extension, $type, $prefix);
			if ($repository) {
				$repositories[$repository['title']] = $repository;
			}
		}

		if ($this->_getComposerVendorDirectory()) {
			$repositories = array_merge($repositories, $this->_getComposerDirectoryBasedRepositoriesWithType($type, $prefix));
		}
		if (!$repositories) {
			$repositories = array();
		}
		return $repositories;
	}

	/**
	 * Returns the list of all packages inside of cundd_composer that contain a
	 * repository
	 *
	 * @param string $type Type/name of the SCM directory (i.e.: 'git', 'svn')
	 * @param string $prefix Optional prefix for the SCM directory
	 * @return array<string>
	 */
	protected function _getComposerDirectoryBasedRepositoriesWithType($type, $prefix = '.') {
		$repositories = array();
		$composerVendorDir = $this->_getComposerVendorDirectory();
		if (file_exists($composerVendorDir)) {
			foreach (new GlobIterator($composerVendorDir . '*/*') as $fileInfo) {
				#Ir::pd($fileInfo, $fileInfo->isDir(), $fileInfo->getPathname());
				if ($fileInfo->isDir()) {
					$extension = basename($fileInfo->getPath()) . '/' .  $fileInfo->getFilename();

					// Check if the given type is a wildcard
					if ($type === NULL) {
						$repository = $this->_getDirectoryBasedRepositoryWithWildcard($extension, $type, $prefix, $fileInfo->getPathname() . '/');
					} else {
						$repository = $this->_getDirectoryBasedRepositoryWithType($extension, $type, $prefix, $fileInfo->getPathname() . '/');
					}
					if ($repository) {
						$repositories[$repository['title']] = $repository;
					}
				}
			#	if($fileInfo->isDot()) continue;
			#	echo $fileInfo->getFilename() . "<br>\n";
			}
		}
		return $repositories;
	}

	/**
	 * Returns a (virtual) repository for the fileadmin directory
	 * @return array<string>
	 */
	protected function _getFileadminRepository() {
		$fileadminPath = PATH_site . '/fileadmin/';
		if (!file_exists($fileadminPath)) {
			return NULL;
		}
		return $this->_getDirectoryBasedRepositoryWithWildcard('fileadmin', NULL, '.', $fileadminPath);
	}

	/**
	 * Returns a (virtual) repository for the framework directory
	 * @return array<string>
	 */
	protected function _getFileadminFrameworkRepository() {
		$fileadminFrameworkPath = PATH_site . '/fileadmin/framework/';
		if (!file_exists($fileadminFrameworkPath)) {
			return NULL;
		}
		return $this->_getDirectoryBasedRepositoryWithWildcard('framework', NULL, '.', $fileadminFrameworkPath);
	}

	/**
	 * Returns the repository data for the repository with the given key and SCM type
	 *
	 * @param string $extensionKey
	 * @param string $type Type/name of the SCM directory (i.e.: 'git', 'svn')
	 * @param string $prefix Optional prefix for the SCM directory
	 * @param string $directoryRootPath Path to the $extension, can be used to monitor SCMs outside of extensions
	 * @return array<string>	Returns the repository data or NULL if it wasn't found
	 */
	protected function _getDirectoryBasedRepositoryWithType($extensionKey, $type, $prefix = '.', $directoryRootPath = '') {
		if (!$directoryRootPath) {
			$directoryRootPath = t3lib_extMgm::extPath($extensionKey);
		}

		$directoryName = $this->getDirectoryNameForSCMType($type);
		$directoryPath = $directoryRootPath . $prefix . $directoryName;
		if (file_exists($directoryPath)) {
			return array(
				'type' 		=> $type,
				'title' 	=> $extensionKey,
				'path' 		=> $directoryRootPath,
			);
		}
		return NULL;
	}

	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* GET REPOSITORY WITH UNKNOWN TYPE   WMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/**
	 * Returns the list of all repositories
	 *
	 * @param string $wildcard Wildcard to match the SCM directory
	 * @param string $prefix Optional prefix for the SCM directory
	 * @return array<string>
	 */
	protected function _getDirectoryBasedRepositoriesWithWildcard($wildcard = NULL, $prefix = '.') {
		$repositories = array();
		$extensions = explode(',', t3lib_extMgm::getEnabledExtensionList());

		foreach ($extensions as $extension) {
			$repository = $this->_getDirectoryBasedRepositoryWithWildcard($extension, $wildcard, $prefix);
			if ($repository) {
				$repositories[$repository['title']] = $repository;
			}
		}

		// Add composer repositories
		if ($this->_getComposerVendorDirectory()) {
			$repositories = array_merge($repositories, $this->_getComposerDirectoryBasedRepositoriesWithType($wildcard, $prefix));
		}

		// Add fileadmin and/or fileadmin/framework
		$fileadminRepository = $this->_getFileadminRepository();
		if ($fileadminRepository) {
			array_unshift($repositories, $fileadminRepository);
		}
		$fileadminFrameworkRepository = $this->_getFileadminFrameworkRepository();
		if ($fileadminFrameworkRepository) {
			array_unshift($repositories, $fileadminFrameworkRepository);
		}

		if (!$repositories) {
			$repositories = array();
		}
		return $repositories;
	}

	/**
	 * Returns the repository data for the repository with the given key and an unknown type
	 *
	 * @param string $extensionKey
	 * @param string $wildcard Wildcard to match the SCM directory
	 * @param string $prefix Optional prefix for the SCM directory
	 * @param string $directoryRootPath Path to the $extension, can be used to monitor SCMs outside of extensions
	 * @return array<string>	Returns the repository data or NULL if it wasn't found
	 */
	protected function _getDirectoryBasedRepositoryWithWildcard($extensionKey, $wildcard = NULL, $prefix = '.', $directoryRootPath = '') {
		if (!$directoryRootPath) {
			$directoryRootPath = t3lib_extMgm::extPath($extensionKey);
		}
		if ($wildcard === NULL) {
			$wildcard = '{' . implode(',', $this->_getAllDirectoryNameForSCMTypes()) . '}';
		}
		$directoryPath = $directoryRootPath . $prefix . $wildcard;
		$foundPaths = glob($directoryPath, GLOB_BRACE);

		$currentPath = current($foundPaths);
		$currentDirectoryName = basename($currentPath);
		while ($currentDirectoryName === '.' || $currentDirectoryName === '..') {
			$currentPath = next($foundPaths);
			$currentDirectoryName = basename($currentPath);
		}
		if (!$currentPath) {
			return NULL;
		}

		$directoryName = substr($currentDirectoryName, strlen($prefix));
		$type = $this->getSCMTypeForDirectoryName($directoryName);

		return array(
			'type' 			=> $type,
			'title' 		=> $extensionKey,
			'path' 			=> $directoryRootPath,
			'directoryName' => $directoryName,
		);
	}

	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* HELPER METHODS                     WMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/**
	 * Returns the name of the directory where the versioning systems data is stored
	 * @param  string $type Type/name of the SCM
	 * @return string
	 */
	public function getDirectoryNameForSCMType($type) {
		if (isset($this->directoryNameForSCMType[$type])) {
			return $this->directoryNameForSCMType[$type];
		}
		return strtolower($type);
	}

	/**
	 * Returns the SCM type of the directory name
	 * @param  string $directoryName
	 * @return string
	 */
	public function getSCMTypeForDirectoryName($directoryName) {
		if (isset($this->SCMTypeForDirectoryName[$directoryName])) {
			return $this->SCMTypeForDirectoryName[$directoryName];
		}
		return NULL;
	}

	/**
	 * Returns the directory names for all SCM types
	 * @return array<string>
	 */
	protected function _getAllDirectoryNameForSCMTypes() {
		return array_flip(array_flip($this->directoryNameForSCMType));
	}

	/**
	 * Returns the properties of the domain model
	 * @return array<string>
	 */
	protected function _getDomainModelsProperties() {
		static $properties;
		if (!$properties) {
			// Get the Repository domain object properties
			$properties = new Tx_Sourcero_Domain_Model_Repository();
			$properties = array_keys($properties->_getProperties());
		}
		return $properties;
	}

	/**
	 * Returns the path to the composer extensions vendor dir
	 * @return string Returns the path or FALSE if cundd_composer isn't loaded
	 */
	public function _getComposerVendorDirectory() {
		if (t3lib_extMgm::isLoaded('cundd_composer')) {
			return t3lib_extMgm::extPath('cundd_composer') . 'vendor/';
		}
		return FALSE;
	}

}
?>