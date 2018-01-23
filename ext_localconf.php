<?php

if (TYPO3_MODE === 'BE') {

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects']['TYPO3\\CMS\\Backend\\View\\PageLayoutView'] = array(
        'className' => 'Baschte\\CtypeColumnRestriction\\View\\PageLayoutView'
    );

}