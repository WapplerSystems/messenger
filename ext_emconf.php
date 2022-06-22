<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Symfony Messenger',
    'description' => 'Default symfony messenger commands',
    'version' => '1.0.0',
    'category' => 'misc',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.0 - 11.5.99',
        ],
    ],
    'state' => 'stable',
    'clearCacheOnLoad' => false,
    'author' => 'Sven Wappler',
    'autoload' => [
        'psr-4' => [
            'WapplerSystems\\Messenger\\' => 'Classes',
        ],
    ],
];
