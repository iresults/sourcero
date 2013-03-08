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
 *
 *
 * @package sourcero
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class Tx_Sourcero_Service_SCMService implements t3lib_singleton {
	/**
	 * The working copy doesn't contain any modifications and is up to date
	 */
	const STATUS_CODE_OK = 1;

	/**
	 * The working copy contains changes
	 */
	const STATUS_CODE_DIRTY = 2;

	/**
	 * The remote server has changes
	 */
	const STATUS_CODE_SHOULD_PULL = 3;

	/**
	 * Local changes aren't push to the server
	 */
	const STATUS_CODE_SHOULD_PUSH = 4;


	/**
	 * Returns the status code for the given repository
	 * @param  Tx_Sourcero_Domain_Model_Repository $repository
	 * @return integer
	 */
	public function getStatusCodeForRepository($repository) {
		$status = 0;
		$type = $repository->getType();
		switch ($type) {
			case 'git':
				$output = $this->performAction($repository, 'status');
				if (strpos($output, 'nothing to commit (working directory clean)') !== FALSE) {
					$status = self::STATUS_CODE_OK;
				} else if (strpos($output, 'Untracked files') !== FALSE
					|| strpos($output, 'Changes to be committed') !== FALSE
					|| strpos($output, 'Changes not staged for commit') !== FALSE
					) {
					 $status = self::STATUS_CODE_DIRTY;
				}
				break;
		}
		return $status;
	}

	/**
	 * Performs the given action
	 * @param  Tx_Sourcero_Domain_Model_Repository $repository
	 * @param  string $action    Action to perform
	 * @param  array  $arguments Additional arguments
	 * @param  boolean	$error 	 Reference that will be set to TRUE if an error occured
	 * @return string            Command output
	 */
	public function performAction($repository, $action, $arguments = array(), &$error = FALSE) {
		return $this->styleOutput($this->_performAction($repository, $action, $arguments, $error));
	}

	/**
	 * Performs the given action
	 * @param  Tx_Sourcero_Domain_Model_Repository $repository
	 * @param  string $action    Action to perform
	 * @param  array  $arguments Additional arguments
	 * @param  boolean	$error 	 Reference that will be set to TRUE if an error occured
	 * @return string            Command output
	 */
	protected function _performAction($repository, $action, $arguments = array(), &$error = FALSE) {
		$command = '';
		$timeout = 60 * 5;
		if ($repository === NULL) {
			throw new UnexpectedValueException('The given repository doesn\'t seem to exits', 1362134953);
		}
		if ($repository->getType() === 'git') {
			$command = 'git ' . $action . ' ';

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

			$timeout = 30;
		}

		foreach ($arguments as $key => $argument) {
			if (is_string($key)) {
				$command .= escapeshellarg($key) . ' ';
			}
			$command .= escapeshellarg($argument) . ' ';
		}

		$command = trim($command);

		// echo '<pre>';
		// var_dump($environment);
		// echo '</pre>';

		$workingDir = $repository->getPath();
		$process = new Process($command);
		$process->setWorkingDirectory($workingDir);
		$process->setTimeout($timeout);
		$process->setEnv($environment);
		$process->run();
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

	/**
	 * Converts the console colors to colored spans
	 * @param  string $output The original output
	 * @return string         The colored output
	 */
	public function styleOutput($output) {
		$output = htmlspecialchars($output);
		$output = htmlspecialchars($output);

		$lines = explode(PHP_EOL, $output);
		foreach ($lines as $lineNumber => &$line) {
		#	$line = htmlspecialchars($line);
			$line = $this->replaceColorWithClassInLine('[1m', 'bold', $line);
			$line = $this->replaceColorWithClassInLine('[31m', 'red', $line);
			$line = $this->replaceColorWithClassInLine('[32m', 'green', $line);
			$line = $this->replaceColorWithClassInLine('[36m', 'cyan', $line);
		}


		return implode(PHP_EOL, $lines);
	}

	/**
	 * Replace the given color with the CSS class in the given line
	 * @param  string $commandColor
	 * @param  string $class
	 * @param  string $line
	 * @return string
	 */
	protected function replaceColorWithClassInLine($commandColor, $class, $line) {
		$signal = "\033";

		if (strpos($line, $signal . $commandColor) !== FALSE) {
			// Replace the commands with a special keyword
			$line = str_replace($signal . $commandColor, 'SPECIAL_COMMAND_SIGNAL_BEGINN', $line);

			// Escape the HTML special chars
#			$line = htmlspecialchars($line);

			// Add the span
			$line = str_replace('SPECIAL_COMMAND_SIGNAL_BEGINN', '<span class="' . $class .'">', $line);
		}

		// Close all spans
		$line = str_replace($signal . '[m', '</span>', $line);

		return $line;
	}
}
