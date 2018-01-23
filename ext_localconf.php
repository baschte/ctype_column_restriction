<?php

if (TYPO3_MODE === 'BE') {

    // only if gridelements is not installed
    if(!TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('gridelements'))
    {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects']['TYPO3\\CMS\\Backend\\View\\PageLayoutView'] = array(
            'className' => 'Baschte\\CtypeColumnRestriction\\View\\PageLayoutView'
        );
    }

}