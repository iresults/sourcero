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
 * Interface for SCM drivers
 *
 * @package sourcero
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
interface Tx_Sourcero_Driver_DriverInterface {
	/**
	 * Returns the managed repository
	 * @return  Tx_Sourcero_Domain_Model_Repository
	 */
	public function getRepository();

	/**
	 * Returns the status message for the given repository
	 * @param  array  $arguments	Additional arguments
	 * @return string
	 */
	public function status($arguments = array());

	/**
	 * Returns the log message for the given repository
	 * @param  array  $arguments	Additional arguments
	 * @return string
	 */
	public function log($arguments = array());

	/**
	 * Returns the diff message for the given repository
	 * @param  array  $arguments	Additional arguments
	 * @return string
	 */
	public function diff($arguments = array());

	/**
	 * Adds files to the given repository
	 * @param  array  $arguments	Additional arguments
	 * @return string
	 */
	public function add($arguments = array());

	/**
	 * Commit the changes the given repository
	 * @param  string  $message 	The commit message
	 * @param  array  $arguments	Additional arguments
	 * @return string
	 */
	public function commit($message, $arguments = array());

	/**
	 * Pull remote changes
	 * @param  array  $arguments	Additional arguments
	 * @return string
	 */
	public function pull($arguments = array());

	/**
	 * Push local changes
	 * @param  array  $arguments	Additional arguments
	 * @return string
	 */
	public function push($arguments = array());

	/**
	 * Reset local changes
	 * @param  array  $arguments	Additional arguments
	 * @return string
	 */
	public function reset($arguments = array());

	/**
	 * Returns the status code for the given repository
	 * @param  boolean $onlyLocal Set to TRUE if the remote changes should not be fetched
	 * @return integer|Tx_Sourcero_Service_SCMService::STATUS_CODE
	 */
	public function getStatusCode($onlyLocal = FALSE);

	/**
	 * Executes the given command
	 * @param  string $command   	Command to execute
	 * @param  array  $arguments	Additional arguments
	 * @param  boolean	$error 	 	Reference that will be set to TRUE if an error occured
	 * @return string            	Command output
	 */
	public function executeCommand($command, $arguments = array(), &$error = FALSE);
}