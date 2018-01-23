<?php
defined('TYPO3_MODE') || die();

if (TYPO3_MODE === 'BE') {

    // only if gridelements is not installed
    if(!TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('gridelements'))
    {
        /**
         * Register Hook for allowed column BE CTypes
         */
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms']['db_new_content_el']['wizardItemsHook'][] = \Baschte\CtypeColumnRestriction\Hooks\WizardItems::class;

        // DrawItemHook
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['tt_content_drawItem'][] = \Baschte\CtypeColumnRestriction\Hooks\DrawItem::class;
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-preProcess'][] = \Baschte\CtypeColumnRestriction\View\PageLayoutView::class.'->addJSCSS';
    }

}