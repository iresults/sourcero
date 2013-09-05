<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Andreas Thurnheer-Meier <tma@iresults.li>, iresults
 *  			Daniel Corn <cod@iresults.li>, iresults
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
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

if (!class_exists('Tx_Sourcero_Driver_SvnDriver')) {
	class Tx_Sourcero_Driver_SubversionDriver extends Tx_Sourcero_Driver_AbstractDriver {
		public function getStatusCode($onlyLocal = FALSE) {}
		protected function _executeCommand($command, $arguments = array(), &$error = FALSE) {}
		public function getVersionInformation() {}
		public function getShortVersionInformation() {}
	}
	class Tx_Sourcero_Driver_MercurialDriver extends Tx_Sourcero_Driver_AbstractDriver {
		public function getStatusCode($onlyLocal = FALSE) {}
		protected function _executeCommand($command, $arguments = array(), &$error = FALSE) {}
		public function getVersionInformation() {}
		public function getShortVersionInformation() {}
	}
}

/**
 * Test case for class Tx_Sourcero_Controller_RepositoryController.
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @package TYPO3
 * @subpackage Sourcero
 *
 * @author Andreas Thurnheer-Meier <tma@iresults.li>
 * @author Daniel Corn <cod@iresults.li>
 */
class Tx_Sourcero_Service_SCMServiceTest extends Tx_Extbase_Tests_Unit_BaseTestCase {
	/**
	 * @var Tx_Sourcero_Service_SCMService
	 */
	protected $fixture;

	public function setUp() {
		$this->fixture = $this->objectManager->get('Tx_Sourcero_Service_SCMService');
	}

	/**
	 * @test
	 */
	public function getDriverTest() {
		$gitRepository = new Tx_Sourcero_Domain_Model_Repository();
		$gitRepository->setTitle('MyRepo');
		$gitRepository->setPath('/path/to/nowhere/');
		$gitRepository->setHomepage('http://www.myrepo-home.com/MyRepo');
		$gitRepository->setRemoteUrl('http://www.myrepo-home.com/MyRepo.git');
		$gitRepository->setType(Tx_Sourcero_Domain_Enum_SCMType::GIT);

		$svnRepository = new Tx_Sourcero_Domain_Model_Repository();
		$svnRepository->setTitle('MyRepo');
		$svnRepository->setPath('/path/to/nowhere/');
		$svnRepository->setHomepage('http://www.myrepo-home.com/MyRepo');
		$svnRepository->setRemoteUrl('http://www.myrepo-home.com/MyRepo.git');
		$svnRepository->setType(Tx_Sourcero_Domain_Enum_SCMType::SUBVERSION);

		$mercurialRepository = new Tx_Sourcero_Domain_Model_Repository();
		$mercurialRepository->setTitle('MyRepo');
		$mercurialRepository->setPath('/path/to/nowhere/');
		$mercurialRepository->setHomepage('http://www.myrepo-home.com/MyRepo');
		$mercurialRepository->setRemoteUrl('http://www.myrepo-home.com/MyRepo.git');
		$mercurialRepository->setType(Tx_Sourcero_Domain_Enum_SCMType::MERCURIAL);

		$this->assertInstanceOf('Tx_Sourcero_Driver_GitDriver', $this->fixture->getDriverForRepository($gitRepository));
		$this->assertInstanceOf('Tx_Sourcero_Driver_SubversionDriver', $this->fixture->getDriverForRepository($svnRepository));
		$this->assertInstanceOf('Tx_Sourcero_Driver_MercurialDriver', $this->fixture->getDriverForRepository($mercurialRepository));
	}

	/**
	 * @test
	 * @expectedException Tx_Sourcero_Service_Exception_DriverNotFoundException
	 */
	public function unfoundDriverTest() {
		$repository = new Tx_Sourcero_Domain_Model_Repository();
		$repository->setTitle('MyRepo');
		$repository->setPath('/path/to/nowhere/');
		$repository->setHomepage('http://www.myrepo-home.com/MyRepo');
		$repository->setRemoteUrl('http://www.myrepo-home.com/MyRepo.git');
		$repository->setType('badType');

		$this->fixture->getDriverForRepository($repository);
	}

}
?>