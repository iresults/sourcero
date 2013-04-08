<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

if (TYPO3_MODE === 'BE') {

	/**
	 * Registers a Backend Module
	 */
	Tx_Extbase_Utility_Extension::registerModule(
		$_EXTKEY,
		'tools',	 // Make module a submodule of 'tools'
		'sourcero',	// Submodule key
		'',						// Position
		array(
			'Repository' => 'list, show, new, create, edit, update, delete, commit, performCommit, push, pull, log, status, add, executeCommand, info, reset, performReset, updateAll',
		),
		array(
			'access' => 'user,group',
			'icon'   => 'EXT:' . $_EXTKEY . '/ext_icon.png',
			'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_sourcero.xlf',
		)
	);

}

t3lib_extMgm::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Sourcero');

t3lib_extMgm::addLLrefForTCAdescr('tx_sourcero_domain_model_repository', 'EXT:sourcero/Resources/Private/Language/locallang_csh_tx_sourcero_domain_model_repository.xlf');
t3lib_extMgm::allowTableOnStandardPages('tx_sourcero_domain_model_repository');
$TCA['tx_sourcero_domain_model_repository'] = array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:sourcero/Resources/Private/Language/locallang_db.xlf:tx_sourcero_domain_model_repository',
		'label' => 'title',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,

		'versioningWS' => 2,
		'versioning_followPages' => TRUE,
		'origUid' => 't3_origuid',
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'searchFields' => 'title,type,path,homepage,remote_url,',
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/Repository.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_sourcero_domain_model_repository.gif'
	),
);

?>