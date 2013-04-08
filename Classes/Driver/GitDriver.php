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

use Symfony\Component\Process\Process;

/**
 *
 *
 * @package sourcero
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class Tx_Sourcero_Driver_GitDriver extends Tx_Sourcero_Driver_AbstractCliDriver {
	/**
	 * Returns the status code for the given repository
	 * @param  boolean $onlyLocal Set to TRUE if the remote changes should not be fetched
	 * @return integer
	 */
	public function getStatusCode($onlyLocal = FALSE) {
		$status = 0;

		if (!$onlyLocal) {
			$this->_fetchIfRequired();
		}

		$output = $this->executeCommand('status');
		if (strpos($output, 'nothing to commit (working directory clean)') !== FALSE) {
			$status = Tx_Sourcero_Service_SCMService::STATUS_CODE_OK;
		} else if (strpos($output, 'Untracked files') !== FALSE
			|| strpos($output, 'Changes to be committed') !== FALSE
			|| strpos($output, 'Changes not staged for commit') !== FALSE
			) {
			 $status = Tx_Sourcero_Service_SCMService::STATUS_CODE_DIRTY;
		} else if (strpos($output, 'Your branch is ahead of') !== FALSE) {
			$status = Tx_Sourcero_Service_SCMService::STATUS_CODE_SHOULD_PUSH;
		} else if (strpos($output, 'Your branch is behind') !== FALSE) {
			$status = Tx_Sourcero_Service_SCMService::STATUS_CODE_SHOULD_PULL;
		}
		return $status;
	}

	/**
	 * Checks if the FETCH_HEAD is old and needs to be refetched
	 * @return boolean Returns TRUE if the remote data was fetched, otherwise FALSE
	 */
	protected function _fetchIfRequired() {
		$fetchIsRequired = FALSE;
		$gitPath = $this->repository->getPath() . '/.git';
		$fetchHeadPath = $gitPath . '/FETCH_HEAD';

		if (!file_exists($gitPath)) {
			throw new UnexpectedValueException('.git directory not found', 1365422844);
		}

		if (!file_exists($fetchHeadPath)) {
			$fetchIsRequired = TRUE;
		} else if (filemtime($fetchHeadPath) < (time() - 60 * 5)) { // If the head is older than 5 min
			$fetchIsRequired = TRUE;
		}

		if ($fetchIsRequired) {
			return $this->executeCommand('fetch', array('--all'));
		}
		return FALSE;
	}

	/**
	 * Builds the process to run for the current driver
	 * @param  string $subCommand   Command to execute
	 * @param  array  $arguments	Additional arguments
	 * @return string            	Raw command output
	 */
	protected function _buildProcess($subCommand, $arguments = array()) {
		if (is_object($subCommand)) {
			throw new UnexpectedValueException('The given sub command is an object', 1365424413);
		}
		$timeout = 30;
		$command = 'git ' . $subCommand . ' ';

		$username = $GLOBALS['BE_USER']->user['username'];
		$realname = $GLOBALS['BE_USER']->user['realName'];
		$email = $GLOBALS['BE_USER']->user['email'];

		$name = $username;
		if ($realname) {
			$name = $realname . ' (' . $username . ')';
		}
		$environment = array(
			'GIT_AUTHOR_NAME' => $name,
			'GIT_AUTHOR_EMAIL' => $email,
			'GIT_COMMITTER_NAME' => $name,
			'GIT_COMMITTER_EMAIL' => $email,
		);

		foreach ($arguments as $key => $argument) {
			if (is_string($key)) {
				$command .= escapeshellarg($key) . ' ';
			}
			$command .= escapeshellarg($argument) . ' ';
		}

		$command = trim($command);

		$workingDir = $this->repository->getPath();
		$process = new Process($command);
		$process->setWorkingDirectory($workingDir);
		$process->setTimeout($timeout);
		$process->setEnv($environment);
		return $process;
	}
}