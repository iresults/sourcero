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
	 * Returns all objects of this repository.
	 *
	 * @return array
	 * @api
	 */
	public function findAll() {
		if (!$this->repositories) {
			$this->repositories = new \SplObjectStorage();

			$repositoryList = $this->getGitRepositories();
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
	 * @return object The matching object if found, otherwise NULL
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
	 * @return object The matching object if found, otherwise NULL
	 * @api
	 */
	public function findOneByTitle($title) {
		$object = $this->_getDirectoryBasedRepositoryWithType($title, 'git');
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
	 * @return array<object> The matching object if found, otherwise NULL
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
		return $repositories;
	}

	/**
	 * Returns the repository data for the given extension key and SCM type
	 *
	 * @param string $extensionKey
	 * @param string $type Type/name of the SCM directory (i.e.: 'git', 'svn')
	 * @param string $prefix Optional prefix for the SCM directory
	 * @return array<string>	Returns the repository data or NULL if it wasn't found
	 */
	protected function _getDirectoryBasedRepositoryWithType($extensionKey, $type, $prefix = '.') {
		$directoryRootPath = t3lib_extMgm::extPath($extensionKey);
		$directoryPath = $directoryRootPath . $prefix . $type;
		if (file_exists($directoryPath)) {
			return array(
				'type' 		=> $type,
				'title' 	=> $extensionKey,
				'path' 		=> $directoryRootPath,
			);
		}
		return NULL;
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

}
?>