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

/**
 * Test case for class Tx_Sourcero_Domain_Model_Repository.
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
class Tx_Sourcero_Domain_Model_RepositoryTest extends Tx_Extbase_Tests_Unit_BaseTestCase {
	/**
	 * @var Tx_Sourcero_Domain_Model_Repository
	 */
	protected $fixture;

	public function setUp() {
		$this->fixture = new Tx_Sourcero_Domain_Model_Repository();
	}

	public function tearDown() {
		unset($this->fixture);
	}

	/**
	 * @test
	 */
	public function getTitleReturnsInitialValueForString() { }

	/**
	 * @test
	 */
	public function setTitleForStringSetsTitle() { 
		$this->fixture->setTitle('Conceived at T3CON10');

		$this->assertSame(
			'Conceived at T3CON10',
			$this->fixture->getTitle()
		);
	}
	
	/**
	 * @test
	 */
	public function getTypeReturnsInitialValueForString() { }

	/**
	 * @test
	 */
	public function setTypeForStringSetsType() { 
		$this->fixture->setType('Conceived at T3CON10');

		$this->assertSame(
			'Conceived at T3CON10',
			$this->fixture->getType()
		);
	}
	
	/**
	 * @test
	 */
	public function getPathReturnsInitialValueForString() { }

	/**
	 * @test
	 */
	public function setPathForStringSetsPath() { 
		$this->fixture->setPath('Conceived at T3CON10');

		$this->assertSame(
			'Conceived at T3CON10',
			$this->fixture->getPath()
		);
	}
	
	/**
	 * @test
	 */
	public function getHomepageReturnsInitialValueForString() { }

	/**
	 * @test
	 */
	public function setHomepageForStringSetsHomepage() { 
		$this->fixture->setHomepage('Conceived at T3CON10');

		$this->assertSame(
			'Conceived at T3CON10',
			$this->fixture->getHomepage()
		);
	}
	
	/**
	 * @test
	 */
	public function getRemoteUrlReturnsInitialValueForString() { }

	/**
	 * @test
	 */
	public function setRemoteUrlForStringSetsRemoteUrl() { 
		$this->fixture->setRemoteUrl('Conceived at T3CON10');

		$this->assertSame(
			'Conceived at T3CON10',
			$this->fixture->getRemoteUrl()
		);
	}
	
}
?>