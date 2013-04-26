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
 * Abstract class for drivers
 *
 * @package sourcero
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
abstract class Tx_Sourcero_Driver_AbstractDriver implements Tx_Sourcero_Driver_DriverInterface {
	/**
	 * The managed repository
	 * @var Tx_Sourcero_Domain_Model_Repository
	 */
	protected $repository;

	/**
	 * Output formatter service
	 * @var Tx_Sourcero_Service_OutputFormatterService
	 * @inject
	 */
	protected $outputFormatterService;


	/**
	 * Executes the given command
	 * @param  string $command   	Command to execute
	 * @param  array  $arguments	Additional arguments
	 * @param  boolean	$error 	 	Reference that will be set to TRUE if an error occured
	 * @return string            	Raw command output
	 */
	abstract protected function _executeCommand($command, $arguments = array(), &$error = FALSE);

	/**
	 * Constructor
	 * @param Tx_Sourcero_Domain_Model_Repository $repository The managed repository
	 */
	public function __construct(Tx_Sourcero_Domain_Model_Repository $repository) {
		$this->repository = $repository;
	}

	/**
	 * Returns the managed repository
	 * @return  Tx_Sourcero_Domain_Model_Repository
	 */
	public function getRepository() {
		return $this->repository;
	}

	/**
	 * Returns the status message for the given repository
	 * @param  array  $arguments	Additional arguments
	 * @return string
	 */
	public function status($arguments = array()) {
		return $this->executeCommand('status', $arguments, $error);
	}

	/**
	 * Returns the log message for the given repository
	 * @param  array  $arguments	Additional arguments
	 * @return string
	 */
	public function log($arguments = array()) {
		return $this->executeCommand('log', $arguments, $error);
	}

	/**
	 * Returns the diff message for the given repository
	 * @param  array  $arguments	Additional arguments
	 * @return string
	 */
	public function diff($arguments = array()) {
		return $this->executeCommand('diff', $arguments, $error);
	}

	/**
	 * Adds files to the given repository
	 * @param  array  $arguments	Additional arguments
	 * @return string
	 */
	public function add($arguments = array()) {
		return $this->executeCommand('add', $arguments, $error);
	}

	/**
	 * Commit the changes the given repository
	 * @param  string  $message 	The commit message
	 * @param  array  $arguments	Additional arguments
	 * @return string
	 */
	public function commit($message, $arguments = array()) {
		return $this->executeCommand('commit', $arguments, $error);
	}

	/**
	 * Pull remote changes
	 * @param  array  $arguments	Additional arguments
	 * @return string
	 */
	public function pull($arguments = array()) {
		return $this->executeCommand('pull', $arguments, $error);
	}

	/**
	 * Push local changes
	 * @param  array  $arguments	Additional arguments
	 * @return string
	 */
	public function push($arguments = array()) {
		return $this->executeCommand('push', $arguments, $error);
	}

	/**
	 * Reset local changes
	 * @param  array  $arguments	Additional arguments
	 * @return string
	 */
	public function reset($arguments = array()) {
		return $this->executeCommand('reset', $arguments, $error);
	}

	/**
	 * Executes the given command
	 * @param  string $command   	  Command to execute
	 * @param  array  $arguments	  Additional arguments
	 * @param  boolean	$error 	 	  Reference that will be set to TRUE if an error occured
     * @param  boolean  $formatOutput If set to FALSE the output will not be formatted
	 * @return string            	Command output
	 */
	public function executeCommand($command, $arguments = array(), &$error = FALSE, $formatOutput = TRUE) {
		if ($command === NULL) {
			throw new UnexpectedValueException('No command specified', 1362134973);
		}
        $output = $this->_executeCommand($command, $arguments, $error);
        if ($formatOutput) {
            $output = $this->outputFormatterService->styleOutput($output, $this->repository);
        }
        return $output;
    }
}