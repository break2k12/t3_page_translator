<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

if (TYPO3_MODE === 'BE') {

	/**
	 * Registers a Backend Module
	 */
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
		'EC.' . $_EXTKEY,
		'tools',	 // Make module a submodule of 'tools'
		'ecpagetranslator',	// Submodule key
		'',						// Position
		array(
			'Pages' => 'list, show, listLangs, insertLangPages',
		),
		array(
			'access' => 'user,group',
			'icon'   => 'EXT:' . $_EXTKEY . '/ext_icon.gif',
			'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_ecpagetranslator.xlf',
		)
	);

}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'PageLanguageHandler');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_ecpagetranslator_domain_model_pages', 'EXT:ec_page_translator/Resources/Private/Language/locallang_csh_tx_ecpagetranslator_domain_model_pages.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_ecpagetranslator_domain_model_pages');
