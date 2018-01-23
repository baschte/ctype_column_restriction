<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'CType Column restriction',
    'description' => 'allows to restrict columns to specific CTypes',
    'category' => 'backend',
    'constraints' => [
        'depends' => [
            'typo3'  => '8.7.0'
        ],
        'conflicts' => [
        ],
    ],
    'state' => 'alpha',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 1,
		'author' => 'Sebastian Richter',
		'author_email' => 'info@baschte.de',
    'version' => '0.0.1',
];

?>
