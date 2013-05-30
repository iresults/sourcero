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
 * Service to add additional features (like links to the files source) to the
 * SCM output
 *
 * @package sourcero
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class Tx_Sourcero_Service_OutputFormatterService implements t3lib_singleton {
	/**
	 * The repository the output belongs to
	 * @var Tx_Sourcero_Domain_Model_Repository
	 */
	protected $repository;

	/**
	 * URI builder
	 * @var \TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder
	 * @inject
	 */
	protected $uriBuilder;

	/**
	 * @var \TYPO3\CMS\Extbase\Mvc\Web\Request
	 * @inject
	 */
	protected $request;

	/**
	 * An array of commands whose output should be checked for file paths
	 * @var array
	 */
	protected $commandsWithLinks = array(
		'status',

	);

	/**
	 * Converts the console colors to colored spans
	 * @param  string $code The original output
	 * @param  Tx_Sourcero_Domain_Model_Repository $repository
	 * @param  string $executedCommand
	 * @return string         The colored output
	 */
	public function styleOutput($code, Tx_Sourcero_Domain_Model_Repository $repository = NULL, $executedCommand = '') {
		if ($repository) {
			$this->repository = $repository;
		}
		$code = $this->colorize($code);

		if ($executedCommand && in_array($executedCommand, $this->commandsWithLinks)) {
			return $this->addLinks($code);
		}
		return $code;
	}


	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* FILE LINKS                       WMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/**
	 * Replace occurences of file paths with links to the files
	 * @param string $code
	 * @return string
	 */
	public function addLinks($code) {
		$matches = array();
		$output = $code;

		// !#\t+((new file:\s+){0,1}|(modi:\s+){0,1})!u
		// !([\w\-]+[/\.]+)+!
		// !\.*([\w_\-]+\/)*([\w_\-]+\.[\w_\-]+)+\W|[\w_\-]+\.[\w_\-]+\W!u

		if (preg_match_all('!\s[\w/\.\-]+\.[\w/\.\-]+\s!', $code, $matches)) {
			$matches = current($matches);
            $matches = array_unique($matches);
			foreach ($matches as $match) {
				$match = trim($match);

				$link = '';
				$link .= $this->buildEditLinkForFile($match);
				$link .= $this->buildDiffLinkForFile($match);
                $output = preg_replace("!$match!", $link, $output);
			}
		}
		return $output;
	}

	/**
	 * Returns the link to edit the given file of the current repository
	 * @param  string $relativePath Relative path to the file
	 * @return string               Full link code
	 */
	public function buildEditLinkForFile($relativePath) {
		$url = $this->getUriForFileAndAction($relativePath, 'show', 'IDE');
		$icon = ' <i class="icon-pencil icon-white"></i>';
		return '<a href="' . $url . '" target="_blank" class="editLink"><span class="editLinkFile">' . $relativePath . '</span>' . $icon . '</a>';
	}

	/**
	 * Returns the link to edit the given file of the current repository
	 * @param  string $relativePath Relative path to the file
	 * @return string               Full link code
	 */
	public function buildDiffLinkForFile($relativePath) {
		$arguments = array(
			'command' => 'diff --color ' . $this->getAbsolutePathOfFile($relativePath, FALSE),
			'repository' => $this->repository->getTitle()
		);


		$url = $this->getUriForFileAndAction($relativePath, 'executeCommand', 'Repository', $arguments);
		return '<a href="' . $url . '" target="" class="diffLink icon-rotate-90 icon-code-fork"></a>';
	}

	/**
	 * Returns the link to the given file and action of the current repository
	 * @param  string $relativePath Relative path to the file
	 * @param  string $action		Action to invoke
	 * @param  string $controller	Name of the controller
	 * @param  array  $arguments	An array of arguments
	 * @return string               URL to the file
	 */
	public function getUriForFileAndAction($relativePath, $action = 'executeCommand', $controller = 'Repository', $arguments = array()) {
		$arguments['file'] = $this->getAbsolutePathOfFile($relativePath);
		$this->getUriBuilder()->reset();
		return $this->getUriBuilder()->uriFor($action, $arguments, $controller, 'sourcero', 'tools_sourcerosourcero');
	}

	/**
	 * Returns the absolute path to the given file in the current repository
	 * @param string $relativePath
	 * @param bool $urlencoded
	 * @return string
	 */
	public function getAbsolutePathOfFile($relativePath, $urlencoded = TRUE) {
		$filePath = $this->repository->getPath() . $relativePath;
		if ($urlencoded) {
			$filePath = urlencode($filePath);
		}
		return $filePath;
	}

	/**
	 * Returns the link to the given file of the current repository
	 * @param  string $relativePath Relative path to the file
	 * @return string               URL to the file
	 */
	public function getDownloadUriForFile($relativePath) {
		return $this->repository->getUrl() . $relativePath;
	}

	/**
	 * Returns the URI builder
	 * @return \TYPO3\CMS\Extbase\Mvc\Web\Request
	 */
	public function getUriBuilder() {
		static $didSetRequest = FALSE;
		if (!$didSetRequest) {
			$this->uriBuilder->setRequest($this->request);
		}
		return $this->uriBuilder;
	}


	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* COLORIZE                         WMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/* MWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWMWM */
	/**
	 * Converts the console colors to colored spans
	 * @param  string $code 	The original output
	 * @return string         	The colored output
	 */
	public function colorize($code) {
		$code = htmlspecialchars($code);
		$code = htmlspecialchars($code);

		$lines = explode(PHP_EOL, $code);
		foreach ($lines as $lineNumber => &$line) {
			$line = $this->replaceColorWithClassInLine('[1m', 'bold', $line);

			$line = $this->replaceColorWithClassInLine('[31m', 'red', $line);
			$line = $this->replaceColorWithClassInLine('[32m', 'green', $line);
			$line = $this->replaceColorWithClassInLine('[33m', 'yellow', $line);
			$line = $this->replaceColorWithClassInLine('[34m', 'blue', $line);
			$line = $this->replaceColorWithClassInLine('[35m', 'magenta', $line);
			$line = $this->replaceColorWithClassInLine('[36m', 'cyan', $line);
			$line = $this->replaceColorWithClassInLine('[37m', 'white', $line);


			$line = $this->replaceColorWithClassInLine('[41m', 'ascii-bg background-red', $line);
			$line = $this->replaceColorWithClassInLine('[42m', 'ascii-bg background-green', $line);
			$line = $this->replaceColorWithClassInLine('[43m', 'ascii-bg background-yellow', $line);
			$line = $this->replaceColorWithClassInLine('[44m', 'ascii-bg background-blue', $line);
			$line = $this->replaceColorWithClassInLine('[45m', 'ascii-bg background-magenta', $line);
			$line = $this->replaceColorWithClassInLine('[46m', 'ascii-bg background-cyan', $line);
			$line = $this->replaceColorWithClassInLine('[47m', 'ascii-bg background-white', $line);
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
