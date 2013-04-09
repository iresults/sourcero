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
 * Enum for SCM types
 *
 * @package sourcero
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class Tx_Sourcero_Domain_Enum_SCMType {
	/**
	 * Unknown SCM type
	 */
	const UNKNOWN = 'unknown';

	/**
	 * Git
	 */
	const GIT = 'Git';

	/**
	 * Mercurial
	 */
	const MERCURIAL = 'Mercurial';

	/**
	 * Subversion
	 */
	const SUBVERSION = 'Subversion';
	const SVN 		 = 'Subversion';

	/*
	 * CVS (Concurrent Versions System)
	 */
	const CVS = 'CVS';

	/**
	 * Bazaar
	 */
	const BAZAAR = 'Bazaar';
}