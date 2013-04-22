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
class Tx_Sourcero_Domain_Model_File extends Iresults\FS\File {
	/**
	 * @var Tx_Sourcero_Service_SCMService
	 * @inject
	 */
	protected $_scmService;

	/**
	 * Title
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $title;

	/**
	 * SCM type
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $type;

	/**
	 * Path
	 *
	 * @var string
	 */
	protected $path;

	/**
	 * Project homepage
	 *
	 * @var string
	 */
	protected $homepage;

	/**
	 * Remote repository URL
	 *
	 * @var string
	 */
	protected $remoteUrl;

	/**
	 * Additional product data
	 * @var array
	 */
	protected $_additionalData;

	/**
	 * Current states of the repository
	 * @var Tx_Sourcero_Service_SCMService::STATUS_CODE
	 */
	protected $_statusCode;

	/**
	 * Returns the title
	 *
	 * @return string $title
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * Sets the title
	 *
	 * @param string $title
	 * @return void
	 */
	public function setTitle($title) {
		$this->title = $title;
	}

	/**
	 * Returns the type
	 *
	 * @return string $type
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * Sets the type
	 *
	 * @param string $type
	 * @return void
	 */
	public function setType($type) {
		$this->type = $type;
	}

	/**
	 * Returns the path
	 *
	 * @return string $path
	 */
	public function getPath() {
		return $this->path;
	}

	/**
	 * Sets the path
	 *
	 * @param string $path
	 * @return void
	 */
	public function setPath($path) {
		$this->path = $path;
	}

	/**
	 * Returns the homepage
	 *
	 * @return string $homepage
	 */
	public function getHomepage() {
		if (!$this->homepage) {
			$this->homepage = $this->getAdditionalDataForKey('homepage');
		}
		return $this->homepage;
	}

	/**
	 * Sets the homepage
	 *
	 * @param string $homepage
	 * @return void
	 */
	public function setHomepage($homepage) {
		$this->homepage = $homepage;
	}

	/**
	 * Returns the remoteUrl
	 *
	 * @return string $remoteUrl
	 */
	public function getRemoteUrl() {
		if (!$this->remoteUrl) {
			$this->remoteUrl = $this->getAdditionalDataForKey('remoteUrl');
		}
		return $this->remoteUrl;
	}

	/**
	 * Sets the remoteUrl
	 *
	 * @param string $remoteUrl
	 * @return void
	 */
	public function setRemoteUrl($remoteUrl) {
		$this->remoteUrl = $remoteUrl;
	}

	/**
	 * Returns the path
	 *
	 * @return string $path
	 */
	public function getUrl() {
		return substr($this->path, strlen(PATH_site));
	}

	/**
	 * Returns the status code
	 * @return integer
	 */
	public function getStatusCode() {
		if ($this->_statusCode === NULL) {
			$this->_statusCode = $this->_scmService->getStatusCodeForRepository($this);
		}
		return $this->_statusCode;
	}

	/**
	 * Returns the local status code
	 * @return integer
	 */
	public function getLocalStatusCode() {
		if ($this->_statusCode === NULL) {
			$this->_statusCode = $this->_scmService->getStatusCodeForRepository($this, TRUE);
		}
		return $this->_statusCode;
	}

	/**
	 * Returns if a driver for this SCM exists
	 * @return boolean
	 */
	public function getHasDriver() {
		return $this->_scmService->hasDriverForRepository($this);
	}

	/**
	 * Returns the additional data for this repository
	 * @param  string $key Data key to retrieve
	 * @return mixed
	 */
	protected function getAdditionalDataForKey($key) {
		if (!$this->_additionalData) {
			if (file_exists($this->getPath() . 'composer.json')) {
				$this->_additionalData = json_decode(file_get_contents($this->getPath() . 'composer.json'), TRUE);
			}
		}
		if ($this->_additionalData && isset($this->_additionalData[$key])) {
			return $this->_additionalData[$key];
		}
		return FALSE;
	}

}
?>