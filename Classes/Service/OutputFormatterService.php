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
	 * Converts the console colors to colored spans
	 * @param  string $code The original output
	 * @param  Tx_Sourcero_Domain_Model_Repository $repository
	 * @return string         The colored output
	 */
	public function styleOutput($code, Tx_Sourcero_Domain_Model_Repository $repository = NULL) {
		if ($repository) {
			$this->repository = $repository;
		}
        return $this->addLinks(
			$this->colorize($code)
		);
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
		if (preg_match_all('!\.*([\w_\-]+\/)*([\w_\-]+\.[\w_\-]+)+\W|[\w_\-]+\.[\w_\-]+\W!u', $code, $matches)) {
			$matches = current($matches);
            $matches = array_unique($matches);
			foreach ($matches as $match) {
				$match = trim($match);

				$link = $this->buildLinkForFile($match);
                $output = preg_replace("!$match!", $link, $output);
			}
		}
		return $output;
	}

	/**
	 * Returns the link to the given file of the current repository
	 * @param  string $relativePath Relative path to the file
	 * @return string               Full link code
	 */
	public function buildLinkForFile($relativePath) {
		$url = $this->getUriForFile($relativePath);
		$icon = ' <i class="icon-pencil icon-white"></i>';
		return '<a href="' . $url . '" target="_blank" class="editLink">' . $relativePath . $icon . '</a>';
	}

	/**
	 * Returns the link to the given file of the current repository
	 * @param  string $relativePath Relative path to the file
	 * @return string               URL to the file
	 */
	public function getUriForFile($relativePath) {
		$filePath = $this->repository->getPath() . $relativePath;
		$filePath = urlencode($filePath);

		$this->getUriBuilder()->reset();
		return $this->getUriBuilder()->uriFor('show', array('file' => $filePath), 'IDE', 'sourcero', 'tools_sourcerosourcero');
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
