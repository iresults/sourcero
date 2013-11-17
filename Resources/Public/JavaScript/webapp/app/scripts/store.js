Sourcero.Store = DS.Store.extend();
//Sourcero.ApplicationAdapter = DS.FixtureAdapter;


Sourcero.AdapterConfiguration = ENV.AdapterConfiguration || {
	/**
	 * Currently active project
	 */
	pkg: {
		name: 'bingo',
		path: 'EXT:bingo'
//		path: '/Applications/MAMP/htdocs/typo3conf/ext/bingo'
//		path: 'EXT:sourcero/'
	}
}
Sourcero.ApplicationAdapter = Sourcero.DataAdapter.extend({
	pkg: Sourcero.AdapterConfiguration.pkg
});

//Sourcero.Store = DS.Store.create({
//	adapter: Sourcero.LSAdapter.create()
//});




//Sourcero.FileSystemDummy = {"fileTree":{"obj":{"id":"EXT_--bingo-_-","name":"bingo","path":"\/Applications\/MAMP\/web_cvjm\/typo3conf\/ext\/bingo\/","relativePath":"\/","uri":"EXT:bingo\/","type":"directory","size":476,"lastModifiedDate":1365858888},"path":"\/Applications\/MAMP\/web_cvjm\/typo3conf\/ext\/bingo\/","children":[{"obj":{"id":"EXT_--bingo-_-Classes-_-","name":"Classes","path":"\/Applications\/MAMP\/web_cvjm\/typo3conf\/ext\/bingo\/Classes\/","relativePath":"\/Classes\/","uri":"EXT:bingo\/Classes\/","type":"directory","size":272,"lastModifiedDate":1375545141},"path":"ext\/Classes","children":[{"obj":{"id":"EXT_--bingo-_-Classes-_-AJavascriptFile_-_js","name":"AJavascriptFile.js","path":"\/Applications\/MAMP\/web_cvjm\/typo3conf\/ext\/bingo\/Classes\/AJavascriptFile.js","relativePath":"\/Classes\/AJavascriptFile.js","relativeDir":"\/Classes","uri":"EXT:bingo\/Classes\/AJavascriptFile.js","type":"text\/plain; charset=us-ascii","size":30,"lastModifiedDate":1366456198},"path":"ext\/Classes\/1"},{"obj":{"id":"EXT_--bingo-_-Classes-_-Bam_-_html","name":"Bam.html","path":"\/Applications\/MAMP\/web_cvjm\/typo3conf\/ext\/bingo\/Classes\/Bam.html","relativePath":"\/Classes\/Bam.html","relativeDir":"\/Classes","uri":"EXT:bingo\/Classes\/Bam.html","type":"text\/html; charset=us-ascii","size":163,"lastModifiedDate":1375531001},"path":"ext\/Classes\/2"},{"obj":{"id":"EXT_--bingo-_-Classes-_-Bam_-_js","name":"Bam.js","path":"\/Applications\/MAMP\/web_cvjm\/typo3conf\/ext\/bingo\/Classes\/Bam.js","relativePath":"\/Classes\/Bam.js","relativeDir":"\/Classes","uri":"EXT:bingo\/Classes\/Bam.js","type":"text\/plain; charset=us-ascii","size":107,"lastModifiedDate":1375529903},"path":"ext\/Classes\/3"},{"obj":{"id":"EXT_--bingo-_-Classes-_-Bam_-_php","name":"Bam.php","path":"\/Applications\/MAMP\/web_cvjm\/typo3conf\/ext\/bingo\/Classes\/Bam.php","relativePath":"\/Classes\/Bam.php","relativeDir":"\/Classes","uri":"EXT:bingo\/Classes\/Bam.php","type":"text\/x-php; charset=us-ascii","size":216,"lastModifiedDate":1372517178},"path":"ext\/Classes\/4"},{"obj":{"id":"EXT_--bingo-_-Classes-_-Bam_-_scss","name":"Bam.scss","path":"\/Applications\/MAMP\/web_cvjm\/typo3conf\/ext\/bingo\/Classes\/Bam.scss","relativePath":"\/Classes\/Bam.scss","relativeDir":"\/Classes","uri":"EXT:bingo\/Classes\/Bam.scss","type":"text\/plain; charset=us-ascii","size":74,"lastModifiedDate":1375545673},"path":"ext\/Classes\/5"},{"obj":{"id":"EXT_--bingo-_-Classes-_-jquery_-_bam_-_js","name":"jquery.bam.js","path":"\/Applications\/MAMP\/web_cvjm\/typo3conf\/ext\/bingo\/Classes\/jquery.bam.js","relativePath":"\/Classes\/jquery.bam.js","relativeDir":"\/Classes","uri":"EXT:bingo\/Classes\/jquery.bam.js","type":"text\/plain; charset=us-ascii","size":32,"lastModifiedDate":1373117581},"path":"ext\/Classes\/6"}]},{"obj":{"id":"EXT_--bingo-_-Configuration-_-","name":"Configuration","path":"\/Applications\/MAMP\/web_cvjm\/typo3conf\/ext\/bingo\/Configuration\/","relativePath":"\/Configuration\/","uri":"EXT:bingo\/Configuration\/","type":"directory","size":136,"lastModifiedDate":1359743145},"path":"ext\/Configuration","children":[{"obj":{"id":"EXT_--bingo-_-Configuration-_-ExtensionBuilder-_-","name":"ExtensionBuilder","path":"\/Applications\/MAMP\/web_cvjm\/typo3conf\/ext\/bingo\/Configuration\/ExtensionBuilder\/","relativePath":"\/Configuration\/ExtensionBuilder\/","uri":"EXT:bingo\/Configuration\/ExtensionBuilder\/","type":"directory","size":102,"lastModifiedDate":1359743145},"path":"ext\/Configuration\/ExtensionBuilder","children":[{"obj":{"id":"EXT_--bingo-_-Configuration-_-ExtensionBuilder-_-settings_-_yaml","name":"settings.yaml","path":"\/Applications\/MAMP\/web_cvjm\/typo3conf\/ext\/bingo\/Configuration\/ExtensionBuilder\/settings.yaml","relativePath":"\/Configuration\/ExtensionBuilder\/settings.yaml","relativeDir":"\/Configuration\/ExtensionBuilder","uri":"EXT:bingo\/Configuration\/ExtensionBuilder\/settings.yaml","type":"text\/plain; charset=us-ascii","size":2384,"lastModifiedDate":1359743145},"path":"ext\/Configuration\/ExtensionBuilder\/1"}]},{"obj":{"id":"EXT_--bingo-_-Configuration-_-TCA-_-","name":"TCA","path":"\/Applications\/MAMP\/web_cvjm\/typo3conf\/ext\/bingo\/Configuration\/TCA\/","relativePath":"\/Configuration\/TCA\/","uri":"EXT:bingo\/Configuration\/TCA\/","type":"directory","size":68,"lastModifiedDate":1359743145},"path":"ext\/Configuration\/TCA"}]},{"obj":{"id":"EXT_--bingo-_-Documentation-_-","name":"Documentation","path":"\/Applications\/MAMP\/web_cvjm\/typo3conf\/ext\/bingo\/Documentation\/","relativePath":"\/Documentation\/","uri":"EXT:bingo\/Documentation\/","type":"directory","size":442,"lastModifiedDate":1359743145},"path":"ext\/Documentation","children":[{"obj":{"id":"EXT_--bingo-_-Documentation-_-AdministratorManual_-_rst","name":"AdministratorManual.rst","path":"\/Applications\/MAMP\/web_cvjm\/typo3conf\/ext\/bingo\/Documentation\/AdministratorManual.rst","relativePath":"\/Documentation\/AdministratorManual.rst","relativeDir":"\/Documentation","uri":"EXT:bingo\/Documentation\/AdministratorManual.rst","type":"text\/plain; charset=utf-8","size":1257,"lastModifiedDate":1359743145},"path":"ext\/Documentation\/1"},{"obj":{"id":"EXT_--bingo-_-Documentation-_-DeveloperCorner_-_rst","name":"DeveloperCorner.rst","path":"\/Applications\/MAMP\/web_cvjm\/typo3conf\/ext\/bingo\/Documentation\/DeveloperCorner.rst","relativePath":"\/Documentation\/DeveloperCorner.rst","relativeDir":"\/Documentation","uri":"EXT:bingo\/Documentation\/DeveloperCorner.rst","type":"text\/plain; charset=utf-8","size":246,"lastModifiedDate":1359743145},"path":"ext\/Documentation\/2"},{"obj":{"id":"EXT_--bingo-_-Documentation-_-Images-_-","name":"Images","path":"\/Applications\/MAMP\/web_cvjm\/typo3conf\/ext\/bingo\/Documentation\/Images\/","relativePath":"\/Documentation\/Images\/","uri":"EXT:bingo\/Documentation\/Images\/","type":"directory","size":204,"lastModifiedDate":1359743145},"path":"ext\/Documentation\/Images","children":[{"obj":{"id":"EXT_--bingo-_-Documentation-_-Images-_-AdministratorManual-_-","name":"AdministratorManual","path":"\/Applications\/MAMP\/web_cvjm\/typo3conf\/ext\/bingo\/Documentation\/Images\/AdministratorManual\/","relativePath":"\/Documentation\/Images\/AdministratorManual\/","uri":"EXT:bingo\/Documentation\/Images\/AdministratorManual\/","type":"directory","size":102,"lastModifiedDate":1359743145},"path":"ext\/Documentation\/Images\/AdministratorManual","children":[{"obj":{"id":"EXT_--bingo-_-Documentation-_-Images-_-AdministratorManual-_-ExtensionManager_-_png","name":"ExtensionManager.png","path":"\/Applications\/MAMP\/web_cvjm\/typo3conf\/ext\/bingo\/Documentation\/Images\/AdministratorManual\/ExtensionManager.png","relativePath":"\/Documentation\/Images\/AdministratorManual\/ExtensionManager.png","relativeDir":"\/Documentation\/Images\/AdministratorManual","uri":"EXT:bingo\/Documentation\/Images\/AdministratorManual\/ExtensionManager.png","type":"image\/png; charset=binary","size":254292,"lastModifiedDate":1359743145},"path":"ext\/Documentation\/Images\/AdministratorManual\/1"}]},{"obj":{"id":"EXT_--bingo-_-Documentation-_-Images-_-IntroductionPackage_-_png","name":"IntroductionPackage.png","path":"\/Applications\/MAMP\/web_cvjm\/typo3conf\/ext\/bingo\/Documentation\/Images\/IntroductionPackage.png","relativePath":"\/Documentation\/Images\/IntroductionPackage.png","relativeDir":"\/Documentation\/Images","uri":"EXT:bingo\/Documentation\/Images\/IntroductionPackage.png","type":"image\/png; charset=binary","size":114718,"lastModifiedDate":1359743145},"path":"ext\/Documentation\/Images\/1"},{"obj":{"id":"EXT_--bingo-_-Documentation-_-Images-_-Typo3_-_png","name":"Typo3.png","path":"\/Applications\/MAMP\/web_cvjm\/typo3conf\/ext\/bingo\/Documentation\/Images\/Typo3.png","relativePath":"\/Documentation\/Images\/Typo3.png","relativeDir":"\/Documentation\/Images","uri":"EXT:bingo\/Documentation\/Images\/Typo3.png","type":"image\/png; charset=binary","size":11202,"lastModifiedDate":1359743145},"path":"ext\/Documentation\/Images\/2"},{"obj":{"id":"EXT_--bingo-_-Documentation-_-Images-_-UserManual-_-","name":"UserManual","path":"\/Applications\/MAMP\/web_cvjm\/typo3conf\/ext\/bingo\/Documentation\/Images\/UserManual\/","relativePath":"\/Documentation\/Images\/UserManual\/","uri":"EXT:bingo\/Documentation\/Images\/UserManual\/","type":"directory","size":68,"lastModifiedDate":1372519239},"path":"ext\/Documentation\/Images\/UserManual"}]},{"obj":{"id":"EXT_--bingo-_-Documentation-_-Index_-_rst","name":"Index.rst","path":"\/Applications\/MAMP\/web_cvjm\/typo3conf\/ext\/bingo\/Documentation\/Index.rst","relativePath":"\/Documentation\/Index.rst","relativeDir":"\/Documentation","uri":"EXT:bingo\/Documentation\/Index.rst","type":"text\/plain; charset=us-ascii","size":1808,"lastModifiedDate":1359743145},"path":"ext\/Documentation\/3"},{"obj":{"id":"EXT_--bingo-_-Documentation-_-ProjectInformation_-_rst","name":"ProjectInformation.rst","path":"\/Applications\/MAMP\/web_cvjm\/typo3conf\/ext\/bingo\/Documentation\/ProjectInformation.rst","relativePath":"\/Documentation\/ProjectInformation.rst","relativeDir":"\/Documentation","uri":"EXT:bingo\/Documentation\/ProjectInformation.rst","type":"text\/plain; charset=utf-8","size":1630,"lastModifiedDate":1359743145},"path":"ext\/Documentation\/4"},{"obj":{"id":"EXT_--bingo-_-Documentation-_-RestructuredtextHelp_-_rst","name":"RestructuredtextHelp.rst","path":"\/Applications\/MAMP\/web_cvjm\/typo3conf\/ext\/bingo\/Documentation\/RestructuredtextHelp.rst","relativePath":"\/Documentation\/RestructuredtextHelp.rst","relativeDir":"\/Documentation","uri":"EXT:bingo\/Documentation\/RestructuredtextHelp.rst","type":"text\/plain; charset=utf-8","size":6907,"lastModifiedDate":1359743145},"path":"ext\/Documentation\/5"},{"obj":{"id":"EXT_--bingo-_-Documentation-_-TyposcriptReference_-_rst","name":"TyposcriptReference.rst","path":"\/Applications\/MAMP\/web_cvjm\/typo3conf\/ext\/bingo\/Documentation\/TyposcriptReference.rst","relativePath":"\/Documentation\/TyposcriptReference.rst","relativeDir":"\/Documentation","uri":"EXT:bingo\/Documentation\/TyposcriptReference.rst","type":"text\/plain; charset=utf-8","size":1328,"lastModifiedDate":1359743145},"path":"ext\/Documentation\/6"},{"obj":{"id":"EXT_--bingo-_-Documentation-_-UserManual_-_rst","name":"UserManual.rst","path":"\/Applications\/MAMP\/web_cvjm\/typo3conf\/ext\/bingo\/Documentation\/UserManual.rst","relativePath":"\/Documentation\/UserManual.rst","relativeDir":"\/Documentation","uri":"EXT:bingo\/Documentation\/UserManual.rst","type":"text\/plain; charset=utf-8","size":1131,"lastModifiedDate":1359743145},"path":"ext\/Documentation\/7"},{"obj":{"id":"EXT_--bingo-_-Documentation-_-_De-_-","name":"_De","path":"\/Applications\/MAMP\/web_cvjm\/typo3conf\/ext\/bingo\/Documentation\/_De\/","relativePath":"\/Documentation\/_De\/","uri":"EXT:bingo\/Documentation\/_De\/","type":"directory","size":136,"lastModifiedDate":1359743145},"path":"ext\/Documentation\/_De","children":[{"obj":{"id":"EXT_--bingo-_-Documentation-_-_De-_-Images-_-","name":"Images","path":"\/Applications\/MAMP\/web_cvjm\/typo3conf\/ext\/bingo\/Documentation\/_De\/Images\/","relativePath":"\/Documentation\/_De\/Images\/","uri":"EXT:bingo\/Documentation\/_De\/Images\/","type":"directory","size":102,"lastModifiedDate":1359743145},"path":"ext\/Documentation\/_De\/Images","children":[{"obj":{"id":"EXT_--bingo-_-Documentation-_-_De-_-Images-_-UserManual-_-","name":"UserManual","path":"\/Applications\/MAMP\/web_cvjm\/typo3conf\/ext\/bingo\/Documentation\/_De\/Images\/UserManual\/","relativePath":"\/Documentation\/_De\/Images\/UserManual\/","uri":"EXT:bingo\/Documentation\/_De\/Images\/UserManual\/","type":"directory","size":102,"lastModifiedDate":1359743145},"path":"ext\/Documentation\/_De\/Images\/UserManual","children":[{"obj":{"id":"EXT_--bingo-_-Documentation-_-_De-_-Images-_-UserManual-_-BackendView_-_png","name":"BackendView.png","path":"\/Applications\/MAMP\/web_cvjm\/typo3conf\/ext\/bingo\/Documentation\/_De\/Images\/UserManual\/BackendView.png","relativePath":"\/Documentation\/_De\/Images\/UserManual\/BackendView.png","relativeDir":"\/Documentation\/_De\/Images\/UserManual","uri":"EXT:bingo\/Documentation\/_De\/Images\/UserManual\/BackendView.png","type":"image\/png; charset=binary","size":185871,"lastModifiedDate":1359743145},"path":"ext\/Documentation\/_De\/Images\/UserManual\/1"}]}]},{"obj":{"id":"EXT_--bingo-_-Documentation-_-_De-_-UserManual_-_rst","name":"UserManual.rst","path":"\/Applications\/MAMP\/web_cvjm\/typo3conf\/ext\/bingo\/Documentation\/_De\/UserManual.rst","relativePath":"\/Documentation\/_De\/UserManual.rst","relativeDir":"\/Documentation\/_De","uri":"EXT:bingo\/Documentation\/_De\/UserManual.rst","type":"text\/plain; charset=utf-8","size":755,"lastModifiedDate":1359743145},"path":"ext\/Documentation\/_De\/1"}]},{"obj":{"id":"EXT_--bingo-_-Documentation-_-_Fr-_-","name":"_Fr","path":"\/Applications\/MAMP\/web_cvjm\/typo3conf\/ext\/bingo\/Documentation\/_Fr\/","relativePath":"\/Documentation\/_Fr\/","uri":"EXT:bingo\/Documentation\/_Fr\/","type":"directory","size":136,"lastModifiedDate":1359743145},"path":"ext\/Documentation\/_Fr","children":[{"obj":{"id":"EXT_--bingo-_-Documentation-_-_Fr-_-Images-_-","name":"Images","path":"\/Applications\/MAMP\/web_cvjm\/typo3conf\/ext\/bingo\/Documentation\/_Fr\/Images\/","relativePath":"\/Documentation\/_Fr\/Images\/","uri":"EXT:bingo\/Documentation\/_Fr\/Images\/","type":"directory","size":102,"lastModifiedDate":1359743145},"path":"ext\/Documentation\/_Fr\/Images","children":[{"obj":{"id":"EXT_--bingo-_-Documentation-_-_Fr-_-Images-_-UserManual-_-","name":"UserManual","path":"\/Applications\/MAMP\/web_cvjm\/typo3conf\/ext\/bingo\/Documentation\/_Fr\/Images\/UserManual\/","relativePath":"\/Documentation\/_Fr\/Images\/UserManual\/","uri":"EXT:bingo\/Documentation\/_Fr\/Images\/UserManual\/","type":"directory","size":102,"lastModifiedDate":1359743145},"path":"ext\/Documentation\/_Fr\/Images\/UserManual","children":[{"obj":{"id":"EXT_--bingo-_-Documentation-_-_Fr-_-Images-_-UserManual-_-BackendView_-_png","name":"BackendView.png","path":"\/Applications\/MAMP\/web_cvjm\/typo3conf\/ext\/bingo\/Documentation\/_Fr\/Images\/UserManual\/BackendView.png","relativePath":"\/Documentation\/_Fr\/Images\/UserManual\/BackendView.png","relativeDir":"\/Documentation\/_Fr\/Images\/UserManual","uri":"EXT:bingo\/Documentation\/_Fr\/Images\/UserManual\/BackendView.png","type":"image\/png; charset=binary","size":185871,"lastModifiedDate":1359743145},"path":"ext\/Documentation\/_Fr\/Images\/UserManual\/1"}]}]},{"obj":{"id":"EXT_--bingo-_-Documentation-_-_Fr-_-UserManual_-_rst","name":"UserManual.rst","path":"\/Applications\/MAMP\/web_cvjm\/typo3conf\/ext\/bingo\/Documentation\/_Fr\/UserManual.rst","relativePath":"\/Documentation\/_Fr\/UserManual.rst","relativeDir":"\/Documentation\/_Fr","uri":"EXT:bingo\/Documentation\/_Fr\/UserManual.rst","type":"text\/plain; charset=utf-8","size":627,"lastModifiedDate":1359743145},"path":"ext\/Documentation\/_Fr\/1"}]},{"obj":{"id":"EXT_--bingo-_-Documentation-_-_IncludedDirectives_-_rst","name":"_IncludedDirectives.rst","path":"\/Applications\/MAMP\/web_cvjm\/typo3conf\/ext\/bingo\/Documentation\/_IncludedDirectives.rst","relativePath":"\/Documentation\/_IncludedDirectives.rst","relativeDir":"\/Documentation","uri":"EXT:bingo\/Documentation\/_IncludedDirectives.rst","type":"text\/plain; charset=utf-8","size":869,"lastModifiedDate":1359743145},"path":"ext\/Documentation\/8"}]},{"obj":{"id":"EXT_--bingo-_-ExtensionBuilder_-_json","name":"ExtensionBuilder.json","path":"\/Applications\/MAMP\/web_cvjm\/typo3conf\/ext\/bingo\/ExtensionBuilder.json","relativePath":"\/ExtensionBuilder.json","relativeDir":"\/","uri":"EXT:bingo\/ExtensionBuilder.json","type":"text\/plain; charset=us-ascii","size":516,"lastModifiedDate":1359743145},"path":"ext\/1"},{"obj":{"id":"EXT_--bingo-_-Readme_-_rst","name":"Readme.rst","path":"\/Applications\/MAMP\/web_cvjm\/typo3conf\/ext\/bingo\/Readme.rst","relativePath":"\/Readme.rst","relativeDir":"\/","uri":"EXT:bingo\/Readme.rst","type":"text\/plain; charset=us-ascii","size":80,"lastModifiedDate":1359967968},"path":"ext\/2"},{"obj":{"id":"EXT_--bingo-_-Resources-_-","name":"Resources","path":"\/Applications\/MAMP\/web_cvjm\/typo3conf\/ext\/bingo\/Resources\/","relativePath":"\/Resources\/","uri":"EXT:bingo\/Resources\/","type":"directory","size":136,"lastModifiedDate":1359743145},"path":"ext\/Resources","children":[{"obj":{"id":"EXT_--bingo-_-Resources-_-Private-_-","name":"Private","path":"\/Applications\/MAMP\/web_cvjm\/typo3conf\/ext\/bingo\/Resources\/Private\/","relativePath":"\/Resources\/Private\/","uri":"EXT:bingo\/Resources\/Private\/","type":"directory","size":136,"lastModifiedDate":1359743145},"path":"ext\/Resources\/Private","children":[{"obj":{"id":"EXT_--bingo-_-Resources-_-Private-_-Language-_-","name":"Language","path":"\/Applications\/MAMP\/web_cvjm\/typo3conf\/ext\/bingo\/Resources\/Private\/Language\/","relativePath":"\/Resources\/Private\/Language\/","uri":"EXT:bingo\/Resources\/Private\/Language\/","type":"directory","size":136,"lastModifiedDate":1359743145},"path":"ext\/Resources\/Private\/Language","children":[{"obj":{"id":"EXT_--bingo-_-Resources-_-Private-_-Language-_-locallang_-_xlf","name":"locallang.xlf","path":"\/Applications\/MAMP\/web_cvjm\/typo3conf\/ext\/bingo\/Resources\/Private\/Language\/locallang.xlf","relativePath":"\/Resources\/Private\/Language\/locallang.xlf","relativeDir":"\/Resources\/Private\/Language","uri":"EXT:bingo\/Resources\/Private\/Language\/locallang.xlf","type":"application\/xml; charset=us-ascii","size":247,"lastModifiedDate":1359743145},"path":"ext\/Resources\/Private\/Language\/1"},{"obj":{"id":"EXT_--bingo-_-Resources-_-Private-_-Language-_-locallang_db_-_xlf","name":"locallang_db.xlf","path":"\/Applications\/MAMP\/web_cvjm\/typo3conf\/ext\/bingo\/Resources\/Private\/Language\/locallang_db.xlf","relativePath":"\/Resources\/Private\/Language\/locallang_db.xlf","relativeDir":"\/Resources\/Private\/Language","uri":"EXT:bingo\/Resources\/Private\/Language\/locallang_db.xlf","type":"application\/xml; charset=us-ascii","size":247,"lastModifiedDate":1359743145},"path":"ext\/Resources\/Private\/Language\/2"}]}]},{"obj":{"id":"EXT_--bingo-_-Resources-_-Public-_-","name":"Public","path":"\/Applications\/MAMP\/web_cvjm\/typo3conf\/ext\/bingo\/Resources\/Public\/","relativePath":"\/Resources\/Public\/","uri":"EXT:bingo\/Resources\/Public\/","type":"directory","size":102,"lastModifiedDate":1359743145},"path":"ext\/Resources\/Public","children":[{"obj":{"id":"EXT_--bingo-_-Resources-_-Public-_-Icons-_-","name":"Icons","path":"\/Applications\/MAMP\/web_cvjm\/typo3conf\/ext\/bingo\/Resources\/Public\/Icons\/","relativePath":"\/Resources\/Public\/Icons\/","uri":"EXT:bingo\/Resources\/Public\/Icons\/","type":"directory","size":102,"lastModifiedDate":1359743145},"path":"ext\/Resources\/Public\/Icons","children":[{"obj":{"id":"EXT_--bingo-_-Resources-_-Public-_-Icons-_-relation_-_gif","name":"relation.gif","path":"\/Applications\/MAMP\/web_cvjm\/typo3conf\/ext\/bingo\/Resources\/Public\/Icons\/relation.gif","relativePath":"\/Resources\/Public\/Icons\/relation.gif","relativeDir":"\/Resources\/Public\/Icons","uri":"EXT:bingo\/Resources\/Public\/Icons\/relation.gif","type":"image\/gif; charset=binary","size":233,"lastModifiedDate":1359743145},"path":"ext\/Resources\/Public\/Icons\/1"}]}]}]},{"obj":{"id":"EXT_--bingo-_-doc-_-","name":"doc","path":"\/Applications\/MAMP\/web_cvjm\/typo3conf\/ext\/bingo\/doc\/","relativePath":"\/doc\/","uri":"EXT:bingo\/doc\/","type":"directory","size":102,"lastModifiedDate":1359743145},"path":"ext\/doc","children":[{"obj":{"id":"EXT_--bingo-_-doc-_-manual_-_sxw","name":"manual.sxw","path":"\/Applications\/MAMP\/web_cvjm\/typo3conf\/ext\/bingo\/doc\/manual.sxw","relativePath":"\/doc\/manual.sxw","relativeDir":"\/doc","uri":"EXT:bingo\/doc\/manual.sxw","type":"application\/octet-stream; charset=binary","size":93632,"lastModifiedDate":1359743145},"path":"ext\/doc\/1"}]},{"obj":{"id":"EXT_--bingo-_-ext_emconf_-_php","name":"ext_emconf.php","path":"\/Applications\/MAMP\/web_cvjm\/typo3conf\/ext\/bingo\/ext_emconf.php","relativePath":"\/ext_emconf.php","relativeDir":"\/","uri":"EXT:bingo\/ext_emconf.php","type":"text\/x-php; charset=us-ascii","size":974,"lastModifiedDate":1359743145},"path":"ext\/3"},{"obj":{"id":"EXT_--bingo-_-ext_icon_-_gif","name":"ext_icon.gif","path":"\/Applications\/MAMP\/web_cvjm\/typo3conf\/ext\/bingo\/ext_icon.gif","relativePath":"\/ext_icon.gif","relativeDir":"\/","uri":"EXT:bingo\/ext_icon.gif","type":"image\/gif; charset=binary","size":177,"lastModifiedDate":1359743145},"path":"ext\/4"},{"obj":{"id":"EXT_--bingo-_-ext_tables_-_php","name":"ext_tables.php","path":"\/Applications\/MAMP\/web_cvjm\/typo3conf\/ext\/bingo\/ext_tables.php","relativePath":"\/ext_tables.php","relativeDir":"\/","uri":"EXT:bingo\/ext_tables.php","type":"text\/x-php; charset=us-ascii","size":181,"lastModifiedDate":1359743145},"path":"ext\/5"},{"obj":{"id":"EXT_--bingo-_-ext_tables_-_sql","name":"ext_tables.sql","path":"\/Applications\/MAMP\/web_cvjm\/typo3conf\/ext\/bingo\/ext_tables.sql","relativePath":"\/ext_tables.sql","relativeDir":"\/","uri":"EXT:bingo\/ext_tables.sql","type":"inode\/x-empty; charset=binary","size":0,"lastModifiedDate":1359743145},"path":"ext\/6"}]}};


Sourcero.File.FIXTURES = [{
	id: 'Classes-Controller-RepositoryController-php',

	name: 'RepositoryController.php',
	path: '/Classes/Controller/RepositoryController.php',
	directory: '/Classes/Controller/',

	type: 'text/x-php',

	size: 2500,
	lastModifiedDate: new Date(1375014198),
	contents: "<?php\n"
}, {
	id: 'Classes-Controller-IDEController-php',

	name: 'IDEController.php',
	path: '/Classes/Controller/IDEController.php',
	directory: '/Classes/Controller/',

	type: 'text/x-php',

	size: 2500,
	lastModifiedDate: new Date(1375014098),
	contents: "<?php\n\
\n\
/***************************************************************\n\
 *  Copyright notice\n\
 *\n\
 *  (c) 2013 Andreas Thurnheer-Meier <tma@iresults.li>, iresults\n\
 *  Daniel Corn <cod@iresults.li>, iresults\n\
 *\n\
 *  All rights reserved\n\
 *\n\
 *  This script is part of the TYPO3 project. The TYPO3 project is\n\
 *  free software; you can redistribute it and/or modify\n\
 *  it under the terms of the GNU General Public License as published by\n\
 *  the Free Software Foundation; either version 3 of the License, or\n\
 *  (at your option) any later version.\n\
 *\n\
 *  The GNU General Public License can be found at\n\
 *  http://www.gnu.org/copyleft/gpl.html.\n\
 *\n\
 *  This script is distributed in the hope that it will be useful,\n\
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of\n\
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the\n\
 *  GNU General Public License for more details.\n\
 *\n\
 *  This copyright notice MUST APPEAR in all copies of the script!\n\
 ***************************************************************/\n\
\n\
if (!defined('TYPO3_MODE') || TYPO3_MODE !== 'BE') {\n\
	echo 'Access denied';\n\
	die();\n\
}\n\
\n\
Tx_CunddComposer_Autoloader::register();\n\
use Symfony\\Component\\Process\\Process;\n\
use Iresults\\FS as FS;\n\
use \\TYPO3\\CMS\\Core\\Utility as Utility;\n\
\n\
/**\n\
 *\n\
 *\n\
 * @package sourcero\n\
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later\n\
 *\n\
 */\n\
class Tx_Sourcero_Controller_IDEController extends Tx_Sourcero_Controller_AbstractController {\n\
\n\
	/**\n\
	 * repositoryRepository\n\
	 *\n\
	 * @var Tx_Sourcero_Domain_Repository_RepositoryRepository\n\
	 */\n\
	protected $repositoryRepository;\n\
\n\
	/**\n\
	 * @var Tx_Sourcero_Service_SCMService\n\
	 * @inject\n\
	 */\n\
	protected $scmService;\n\
\n\
	/**\n\
	 * @var Tx_Sourcero_Service_FileBrowserService\n\
	 * @inject\n\
	 */\n\
	protected $fileBrowserService;\n\
\n\
	/**\n\
	 * Map of Mime Types for file suffix\n\
	 * @var array\n\
	 */\n\
	protected $mimeTypeForSuffix = array(\n\
		'js' => 'application/x-javascript',\n\
		'json' => 'application/x-javascript',\n\
		'css' => 'text/css',\n\
		'scss' => 'text/x-scss',\n\
		'html' => 'text/html',\n\
		'xhtml' => 'text/html',\n\
		'phtml' => 'text/html',\n\
		'ts' => 'text/x-typoscript',\n\
	);\n\
\n\
	protected function initializeAction() {\n\
		$getExists = function($that) {return $that->exists();};\n\
		$getSuffix = function($that) {\n\
			/** @var Iresults\\FS\\File $that */\n\
			return pathinfo($that->getPath(), PATHINFO_EXTENSION);\n\
		};\n\
		$getExtensionKey = function($that) {\n\
			/** @var Iresults\\FS\\File $that */\n\
\n\
			// If the file belongs into framework or fileadmin\n\
			if (strpos($that->getPath(), '/fileadmin/framework/') !== FALSE) {\n\
				return 'framework';\n\
			} else if (strpos($that->getPath(), '/fileadmin/') !== FALSE) {\n\
				return 'fileadmin';\n\
			}\n\
\n\
			// If the file belongs to a composer package\n\
			$path = str_replace('//', '/', $that->getPath());\n\
			if (strpos($path, 'cundd_composer/vendor/') !== FALSE) {\n\
				list ($vendor, $extension) = $that->getVendorAndExtensionNameForComposerPackage();\n\
				return $vendor . '/' . $extension;\n\
			}\n\
			$relativeExtensionPath = substr($that->getPath(), strlen(PATH_typo3conf . 'ext/'));\n\
			return substr($relativeExtensionPath, 0, strpos($relativeExtensionPath, '/'));\n\
		};\n\
		$getExtensionPath = function($that) {\n\
			/** @var Iresults\\FS\\File $that */\n\
\n\
			// If the file belongs into framework or fileadmin\n\
			if (strpos($that->getPath(), '/fileadmin/framework/') !== FALSE) {\n\
				return PATH_site . '/fileadmin/framework/';\n\
			} else if (strpos($that->getPath(), '/fileadmin/') !== FALSE) {\n\
				return PATH_site . '/fileadmin/';\n\
			}\n\
\n\
			// If the file belongs to a composer package\n\
			$path = str_replace('//', '/', $that->getPath());\n\
			if (strpos($path, 'cundd_composer/vendor/') !== FALSE) {\n\
				list ($vendor, $extension) = $that->getVendorAndExtensionNameForComposerPackage();\n\
				return Utility\\ExtensionManagementUtility::extPath('cundd_composer') . 'vendor/' . $vendor . '/' . $extension . '/';\n\
			}\n\
			return Utility\\ExtensionManagementUtility::extPath($that->getExtensionKey());\n\
		};\n\
		$getVendorAndExtensionNameForComposerPackage = function($that) {\n\
			$path = str_replace('//', '/', $that->getPath());\n\
			$composerVendorDirPosition = strpos($path, 'cundd_composer/vendor/');\n\
			if ($composerVendorDirPosition !== FALSE) {\n\
				$extensionRelativePath = substr($path, $composerVendorDirPosition + 22);\n\
				list ($vendor, $extension, ) = explode(DIRECTORY_SEPARATOR, $extensionRelativePath);\n\
				return array($vendor, $extension);\n\
			}\n\
			return FALSE;\n\
		};\n\
\n\
		FS\\File::_instanceMethodForSelector('getExists', $getExists);\n\
		FS\\File::_instanceMethodForSelector('getSuffix', $getSuffix);\n\
		FS\\File::_instanceMethodForSelector('getExtensionKey', $getExtensionKey);\n\
		FS\\File::_instanceMethodForSelector('getExtensionPath', $getExtensionPath);\n\
		FS\\File::_instanceMethodForSelector('getVendorAndExtensionNameForComposerPackage', $getVendorAndExtensionNameForComposerPackage);\n\
\n\
		FS\\Directory::_instanceMethodForSelector('getExists', $getExists);\n\
		FS\\Directory::_instanceMethodForSelector('getSuffix', $getSuffix);\n\
		FS\\Directory::_instanceMethodForSelector('getExtensionKey', $getExtensionKey);\n\
		FS\\Directory::_instanceMethodForSelector('getExtensionPath', $getExtensionPath);\n\
		FS\\Directory::_instanceMethodForSelector('getVendorAndExtensionNameForComposerPackage', $getVendorAndExtensionNameForComposerPackage);\n\
	}\n\
\n\
	/**\n\
	 * injectRepositoryRepository\n\
	 *\n\
	 * @param Tx_Sourcero_Domain_Repository_RepositoryRepository $repositoryRepository\n\
	 * @return void\n\
	 */\n\
	public function injectRepositoryRepository(Tx_Sourcero_Domain_Repository_RepositoryRepository $repositoryRepository) {\n\
		$this->repositoryRepository = $repositoryRepository;\n\
	}\n\
\n\
	/**\n\
	 * action list\n\
	 *\n\
	 * @param string $file\n\
	 * @return void\n\
	 */\n\
	public function listAction($file) {\n\
		$fileManager = FS\\FileManager::sharedFileManager();\n\
		$file = $fileManager->getResourceAtUrl($file);\n\
		$this->view->assign('file', $file);\n\
		$this->view->assign('fileBrowser', $this->getFileBrowserForFile($file, FALSE));\n\
		$this->view->assign('fileBrowserCode', $this->getFileBrowserCodeForFile($file));\n\
		$this->view->assign('fileBrowserOpen', TRUE);\n\
\n\
		$this->view->assign('project', $this->getProjectForFile($file));\n\
\n\
		$this->setCustomFaviconWithBasePath($file->getExtensionPath());\n\
	}\n\
\n\
	/**\n\
	 * action show\n\
	 *\n\
	 * @param string $file\n\
	 * @return void\n\
	 */\n\
	public function showAction($file) {\n\
		$file = urldecode($file);\n\
		$absFile = Utility\\GeneralUtility::getFileAbsFileName($file);\n\
		if ($absFile) {\n\
			$file = $absFile;\n\
		}\n\
\n\
		$fileManager = FS\\FileManager::sharedFileManager();\n\
		$file = $fileManager->getResourceAtUrl($file);\n\
		if ($file instanceof FS\\Directory) {\n\
			$this->redirect('list', 'IDE', NULL, array('file' => $file->getExtensionPath()));\n\
		}\n\
\n\
		$this->initCodeMirrorForFile($file);\n\
		#$this->redirect('edit', 'IDE', NULL, array('file' => $file));\n\
\n\
		$this->view->assign('fileBrowser', $this->getFileBrowserForFile($file, FALSE));\n\
		$this->view->assign('fileBrowserCode', $this->getFileBrowserCodeForFile($file));\n\
		$this->view->assign('fileBrowserOpen', TRUE);\n\
\n\
		$this->view->assign('project', $this->getProjectForFile($file));\n\
\n\
		$this->setCustomFaviconWithBasePath($file->getExtensionPath());\n\
\n\
		$this->view->assign('repositories', $this->repositoryRepository->findAll());\n\
	}\n\
\n\
	/**\n\
	 * Returns the default CodeMirror configuration\n\
	 * @return array\n\
	 */\n\
	public function getCodeMirrorConfiguration() {\n\
\n\
//		$absoluteCodeMirrorInstallPath = Utility\\ExtensionManagementUtility::extPath('sourcero') . 'Resources/Public/Stylesheets/Library/CodeMirror/';\n\
//		$relativeCodeMirrorInstallPath = Utility\\ExtensionManagementUtility::extRelPath('sourcero') . 'Resources/Public/Stylesheets/Library/CodeMirror/';\n\
		$absoluteCodeMirrorInstallPath = Utility\\ExtensionManagementUtility::extPath('sourcero') . 'Resources/Public/Library/codemirror-3.15/';\n\
		$relativeCodeMirrorInstallPath = Utility\\ExtensionManagementUtility::extRelPath('sourcero') . 'Resources/Public/Library/codemirror-3.15/';\n\
\n\
		// Find all Addons\n\
		$addons = FS\\FileManager::find($absoluteCodeMirrorInstallPath . 'addon/*/*.js');\n\
\n\
		// Add a method to the FS\\File class to return the type of the addon\n\
		FS\\File::_instanceMethodForSelector('getAddonType', function($that) {return basename(dirname($that->getPath()));});\n\
\n\
		// Filter remove all Addons with type \"runmode\"\n\
		$addons = array_filter($addons, function ($addon) {return $addon->getAddonType() !== 'runmode';});\n\
\n\
		$codeMirrorConfiguration = array(\n\
			'addons' => $addons,\n\
			'installPath' => $relativeCodeMirrorInstallPath,\n\
		);\n\
		return $codeMirrorConfiguration;\n\
	}\n\
\n\
	/**\n\
	 * Returns the name of the CodeMirror mode for the given file\n\
	 * @param  Iresults\\FS\\Filesystem $file\n\
	 * @return string\n\
	 */\n\
	protected function getCodeMirrorModeForFile($file) {\n\
		$mimeType = $this->getMimeTypeOfFile($file);\n\
		$mode = str_replace(\n\
			array(\n\
				'application/x-', 'text/x-',\n\
				'application/', 'text/'\n\
			), '', $mimeType);\n\
\n\
		if ($mode === 'html') {\n\
			$mode = 'htmlmixed';\n\
		} else if ($mode === 'scss') {\n\
			$mode = 'text/x-scss';\n\
		}\n\
		return $mode;\n\
	}\n\
\n\
	/**\n\
	 * Returns the file's mime type\n\
	 * @param  Iresults\\FS\\Filesystem $file\n\
	 * @return string\n\
	 */\n\
	protected function getMimeTypeOfFile($file) {\n\
		$suffix = $file->getSuffix();\n\
\n\
		if ($file->getName() === 'setup.txt' || $file->getName() === 'constants.txt') {\n\
			return 'text/x-typoscript';\n\
		}\n\
		if (isset($this->mimeTypeForSuffix[$suffix])) {\n\
			return $this->mimeTypeForSuffix[$suffix];\n\
		}\n\
\n\
		$finfo = finfo_open(FILEINFO_MIME_TYPE);\n\
		$mimeType = finfo_file($finfo, $file->getPath());\n\
		finfo_close($finfo);\n\
		return $mimeType;\n\
	}\n\
\n\
	/**\n\
	 * action new\n\
	 *\n\
	 * @param Tx_Sourcero_Domain_Model_File $file\n\
	 * @dontvalidate $file\n\
	 * @return void\n\
	 */\n\
	public function newAction($file = NULL) {\n\
		$this->view->assign('file', $file);\n\
	}\n\
\n\
	/**\n\
	 * action create\n\
	 *\n\
	 * @param Tx_Sourcero_Domain_Model_File $file\n\
	 * @return void\n\
	 */\n\
	public function createAction($file) {\n\
		$this->redirect('list');\n\
	}\n\
\n\
	/**\n\
	 * action edit\n\
	 *\n\
	 * @param Tx_Sourcero_Domain_Model_File $file\n\
	 * @return void\n\
	 */\n\
	public function editAction($file) {\n\
		$fileManager = FS\\FileManager::sharedFileManager();\n\
		$file = $fileManager->getResourceAtUrl($file);\n\
\n\
	}\n\
\n\
	/**\n\
	 * action update\n\
	 *\n\
	 * @param string $path\n\
	 * @param string $contents\n\
	 * @return mixed\n\
	 */\n\
	public function updateAction($path, $contents) {\n\
		$fileManager = FS\\FileManager::sharedFileManager();\n\
		$file = $fileManager->getResourceAtUrl($path);\n\
\n\
		$contents = $this->formatCode($contents);\n\
		$success = $file->setContents($contents);\n\
\n\
		// Handle AJAX/JSON requests\n\
		if ($this->request->getFormat() === 'json') {\n\
			if ($success) {\n\
				return json_encode(array('success' => TRUE));\n\
			} else {\n\
				$this->response->setStatus(500);\n\
				return json_encode(array('success' => FALSE, 'error' => $this->getUpdateError($file)));\n\
			}\n\
		}\n\
		if ($success) {\n\
			$this->flashMessageContainer->add('File successfully saved');\n\
		} else {\n\
			$this->flashMessageContainer->add('Could not save', 'Error', \\TYPO3\\CMS\\Core\\Messaging\\FlashMessage::ERROR);\n\
		}\n\
		$this->redirect('show', 'IDE', NULL, array('file' => $path));\n\
	}\n\
\n\
	/**\n\
	 * Returns the error description of the update error\n\
	 * @param FS\\File $file\n\
	 */\n\
	public function getUpdateError($file) {\n\
		$message = '';\n\
		if (!$file->isWriteable()) {\n\
			$message = 'File not writeable';\n\
		}\n\
		return array(\n\
			'code' => 1373116207,\n\
			'message' => $message\n\
		);\n\
	}\n\
\n\
	/**\n\
	 * action delete\n\
	 *\n\
	 * @param string $file\n\
	 * @return void\n\
	 */\n\
	public function deleteAction($file) {\n\
		$absFile = Utility\\GeneralUtility::getFileAbsFileName($file);\n\
		if ($absFile) {\n\
			$file = $absFile;\n\
		}\n\
\n\
		$fileManager = FS\\FileManager::sharedFileManager();\n\
		$file = $fileManager->getResourceAtUrl($file);\n\
		$success = $file->delete();\n\
\n\
		if ($success) {\n\
			$this->flashMessageContainer->add('File successfully deleted');\n\
		} else {\n\
			$this->flashMessageContainer->add('Could not delete', 'Error', \\TYPO3\\CMS\\Core\\Messaging\\FlashMessage::ERROR);\n\
		}\n\
		$this->redirect('show', 'IDE', NULL, array('file' => $file->getExtensionPath())); // Show IDE\n\
		// $this->redirect('show', 'Repository', NULL, array('repository' => $file->getExtensionKey())); // Show the Repository overview\n\
	}\n\
\n\
	/**\n\
	 * Replaces trailing whitespaces\n\
	 * @param string $text\n\
	 * @return string\n\
	 */\n\
	protected function formatCode($text) {\n\
		// Normalize line endings\n\
		// Convert all line-endings to UNIX format\n\
		$text = str_replace(\"\\r\\n\", \"\\n\", $text);\n\
		$text = str_replace(\"\\r\", \"\\n\", $text);\n\
\n\
		// Don't allow multiple new-lines\n\
		// $text = preg_replace(\"/\\n{2,}/\", \"\\n\\n\", $text);\n\
\n\
		$lines = explode(\"\\n\", $text);\n\
		foreach ($lines as &$line) {\n\
			$line = rtrim($line);\n\
		}\n\
		return implode(\"\\n\", $lines);\n\
	}\n\
\n\
	/**\n\
	 * Returns the filebrowser HTML code of the extensions files\n\
	 * @param Tx_Sourcero_Domain_Model_File $file\n\
	 * @return array\n\
	 */\n\
	public function getFileBrowserCodeForFile($file) {\n\
		return $this->fileBrowserService->setUriBuilder($this->uriBuilder)->getFileBrowserCodeForFile($file);\n\
	}\n\
\n\
	/**\n\
	 * Returns the list of the extensions files\n\
	 * @param Tx_Sourcero_Domain_Model_File $file\n\
	 * @param boolean $wit\n\
	 * @return array\n\
	 */\n\
	public function getFileBrowserForFile($file, $withDirectories = FALSE) {\n\
		return $this->fileBrowserService->setUriBuilder($this->uriBuilder)->getFileBrowserForFile($file, $withDirectories);\n\
	}\n\
\n\
	/**\n\
	 * Returns a virtual project for the given file\n\
	 *\n\
	 * @param Tx_Sourcero_Domain_Model_File $file\n\
	 * @return array\n\
	 */\n\
	public function getProjectForFile($file) {\n\
		if ($file instanceof FS\\File) {\n\
			$path = $file->getExtensionPath();\n\
		} else {\n\
			$path = $file->getPath();\n\
		}\n\
		return array(\n\
			'name' 		=> $file->getExtensionKey(),\n\
			'path' 		=> $file->getExtensionPath(),\n\
		);\n\
	}\n\
\n\
	/**\n\
	 * Initialize code mirror\n\
	 *\n\
	 * @param Tx_Sourcero_Domain_Model_File $file\n\
	 * @return void\n\
	 */\n\
	protected function initCodeMirrorForFile($file) {\n\
		$mimeType = $this->getMimeTypeOfFile($file);\n\
		$codeMirrorConfiguration = $this->getCodeMirrorConfiguration();\n\
		$codeMirrorConfiguration['mode'] = $this->getCodeMirrorModeForFile($file);\n\
\n\
		$this->view->assign('file', $file);\n\
		$this->view->assign('fileMimeType', $mimeType);\n\
		$this->view->assign('codeMirror', $codeMirrorConfiguration);\n\
\n\
		// Detect binary files\n\
		if (substr($mimeType, 0, 6) === 'image/') {\n\
			$this->view->assign('fileBinaryData', '<img alt=\"Embedded Image\" src=\"data:' . $mimeType . ';base64,' . base64_encode($file->getContents()) . '\" />');\n\
			$this->view->assign('fileIsBinary', TRUE);\n\
		} else if (substr($mimeType, 0, 6) === 'audio/'\n\
			|| substr($mimeType, 0, 6) === 'video/') {\n\
			$this->view->assign('fileIsBinary', TRUE);\n\
		} else {\n\
			$this->view->assign('fileIsBinary', FALSE);\n\
		}\n\
	}\n\
}\n\
?>\n\
"
}, {
	id: 'Classes-Controller-AbstractController-php',

	name: 'AbstractController.php',
	path: '/Classes/Controller/AbstractController.php',
	directory: '/Classes/Controller/',

	type: 'text/x-php',

	size: 2241,
	lastModifiedDate: new Date(1375014047),
	contents: "<?php\n\
\n\
/***************************************************************\n\
 *  Copyright notice\n\
 *\n\
 *  (c) 2013 Andreas Thurnheer-Meier <tma@iresults.li>, iresults\n\
 *  Daniel Corn <cod@iresults.li>, iresults\n\
 *\n\
 *  All rights reserved\n\
 *\n\
 *  This script is part of the TYPO3 project. The TYPO3 project is\n\
 *  free software; you can redistribute it and/or modify\n\
 *  it under the terms of the GNU General Public License as published by\n\
 *  the Free Software Foundation; either version 3 of the License, or\n\
 *  (at your option) any later version.\n\
 *\n\
 *  The GNU General Public License can be found at\n\
 *  http://www.gnu.org/copyleft/gpl.html.\n\
 *\n\
 *  This script is distributed in the hope that it will be useful,\n\
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of\n\
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the\n\
 *  GNU General Public License for more details.\n\
 *\n\
 *  This copyright notice MUST APPEAR in all copies of the script!\n\
 ***************************************************************/\n\
\n\
if (!defined('TYPO3_MODE') || TYPO3_MODE !== 'BE') {\n\
	echo 'Access denied';\n\
	die();\n\
}\n\
\n\
/**\n\
 *\n\
 *\n\
 * @package sourcero\n\
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later\n\
 *\n\
 */\n\
abstract class Tx_Sourcero_Controller_AbstractController extends Tx_Extbase_MVC_Controller_ActionController {\n\
	/**\n\
	 * Assigns the path to the custom favicon to the view or FALSE if none is found\n\
	 *\n\
	 * @param string $basePath\n\
	 * @return string|FALSE\n\
	 */\n\
	protected function setCustomFaviconWithBasePath($basePath) {\n\
		$this->view->assign('customFavicon', static::getCustomFaviconWithBasePath($basePath));\n\
	}\n\
\n\
	/**\n\
	 * Returns the path to the custom favicon or FALSE if none is found\n\
	 *\n\
	 * @param string $basePath\n\
	 * @return string|FALSE\n\
	 */\n\
	static public function getCustomFaviconWithBasePath($basePath) {\n\
		$customFavicon = FALSE;\n\
		if (file_exists($basePath . 'ext_icon.gif')) {\n\
			$customFavicon = $basePath . 'ext_icon.gif';\n\
		} else if (file_exists($basePath . 'ext_icon.png')) {\n\
			$customFavicon = $basePath . 'ext_icon.png';\n\
		}\n\
		$customFavicon = str_replace(PATH_site, '', $customFavicon);\n\
		if ($customFavicon) {\n\
			return '/' . $customFavicon;\n\
		}\n\
		return FALSE;\n\
	\n\
	}\n\
}\n\
?>\n\
"
}];





