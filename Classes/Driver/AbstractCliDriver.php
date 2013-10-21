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

Tx_CunddComposer_Autoloader::register();
use Symfony\Component\Process\Process;

/**
 * Abstract class for drivers used through CLI calls
 *
 * @package sourcero
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
abstract class Tx_Sourcero_Driver_AbstractCliDriver extends Tx_Sourcero_Driver_AbstractDriver {
	/**
	 * Builds the process to run for the current driver
	 * @param  string $command   	Command to execute
	 * @param  array  $arguments	Additional arguments
	 * @return string            	Raw command output
	 */
	abstract protected function _buildProcess($command, $arguments = array());

	/**
	 * Executes the given command
	 * @param  string $command   	Command to execute
	 * @param  array  $arguments	Additional arguments
	 * @param  boolean	$error 	 	Reference that will be set to TRUE if an error occured
	 * @return string            	Raw command output
	 */
	protected function _executeCommand($command, $arguments = array(), &$error = FALSE) {
		$process = $this->_buildProcess($command, $arguments);

		try {
			$process->run();
		} catch (Exception $exception) {
			$error = TRUE;
			return $exception->getCode() . ': ' . $exception->getMessage();
		}

		if (!$process->isSuccessful()) {
			$error = TRUE;
			return $process->getErrorOutput();
		}
		$error = FALSE;

		$output = $process->getOutput();
		if (!$output && $process->getErrorOutput()) {
			return $process->getErrorOutput();
		}
		return $output;
	}
}