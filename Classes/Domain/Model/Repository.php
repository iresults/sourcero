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
class Tx_Sourcero_Domain_Model_Repository extends Tx_Extbase_DomainObject_AbstractEntity {

	/**
	 * Type
	 *
	 * @var string
	 */
	protected $type;

	/**
	 * Title
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $title;

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

}
?>