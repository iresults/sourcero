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
class Tx_Sourcero_Service_SCMService implements \TYPO3\CMS\Core\SingletonInterface {
	/**
	 * Status of the SCM
	 */
	const STATUS_CODE = -1000;

	/**
	 * The status could not be detected
	 */
	const STATUS_CODE_UNKNOWN = 0;

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
	 * @var Tx_Extbase_Object_ObjectManagerInterface
	 * @inject
	 */
	protected $objectManager;

	/**
	 * Specifies if the code should be formatted
	 * @var boolean
	 */
	protected $formatCode = TRUE;

	/**
	 * Output formatter service
	 * @var Tx_Sourcero_Service_OutputFormatterService
	 * @inject
	 */
	protected $outputFormatterService;

	/**
	 * Returns the driver for the given repository
	 * @param  Tx_Sourcero_Domain_Model_Repository $repository
	 * @return Tx_Sourcero_Driver_DriverInterface
	 */
	public function getDriverForRepository($repository) {
		$type = $repository->getType();
		$driverClassName = 'Tx_Sourcero_Driver_' . ucfirst($type) . 'Driver';
		return $this->createDriverInstanceWithClass($driverClassName, $repository);
	}

	/**
	 * Returns if a driver for the given repository exists
	 * @param  Tx_Sourcero_Domain_Model_Repository $repository
	 * @return Tx_Sourcero_Driver_DriverInterface
	 */
	public function hasDriverForRepository($repository) {
		$type = $repository->getType();
		$driverClassName = 'Tx_Sourcero_Driver_' . ucfirst($type) . 'Driver';
		return class_exists($driverClassName);
	}

	/**
	 * Returns a new instance of the given driver class, managing the given
	 * repository
	 * @param  string $driverClassName Class name of the driver
	 * @param Tx_Sourcero_Domain_Model_Repository $repository The managed repository
	 * @return Tx_Sourcero_Driver_DriverInterface
	 * @throws Tx_Sourcero_Service_Exception_DriverNotFoundException If the driver class doesn't exist
	 */
	protected function createDriverInstanceWithClass($driverClassName, $repository) {
		if (!class_exists($driverClassName)) {
			throw new Tx_Sourcero_Service_Exception_DriverNotFoundException('Class ' . $driverClassName . ' doesn\'t seem to exist', 1365509951);
		}
		return $this->objectManager->create($driverClassName, $repository);
	}

	/**
	 * Returns the status code for the given repository
	 * @param  Tx_Sourcero_Domain_Model_Repository $repository
	 * @param  boolean $onlyLocal Set to TRUE if the remote changes should not be fetched
	 * @return integer
	 */
	public function getStatusCodeForRepository($repository, $onlyLocal = FALSE) {
		if (!$this->hasDriverForRepository($repository)) {
			return self::STATUS_CODE_UNKNOWN;
		}
		return $this->getDriverForRepository($repository)->getStatusCode($onlyLocal);
	}

	/**
	 * Executes the given command
	 * @param  Tx_Sourcero_Domain_Model_Repository $repository
	 * @param  string $command    Command to execute
	 * @param  array  $arguments Additional arguments
	 * @param  boolean	$error 	 Reference that will be set to TRUE if an error occured
	 * @return string            Command output
	 */
	public function executeCommand($repository, $command, $arguments = array(), &$error = FALSE) {
		$output = $this->getDriverForRepository($repository)->executeCommand($command, $arguments, $error);
		if ($this->getFormatCode()) {
			$output = $this->outputFormatterService->styleOutput($output, $repository, $command);
		}
		return $output;
	}

	/**
	 * @param boolean $formatCode
	 */
	public function setFormatCode($formatCode) {
		$this->formatCode = $formatCode;
	}

	/**
	 * @return boolean
	 */
	public function getFormatCode() {
		return $this->formatCode;
	}



}
