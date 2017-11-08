<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Advanced TYPO3 Locking',
    'description' => 'Features TYPO3 with an advanced locking.',
    'category' => 'misc',
    'author' => 'Daniel Hürtgen',
    'author_email' => 'daniel@higidi.de',
    'state' => 'alpha',
    'clearCacheOnLoad' => true,
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '7.6.0-8.7.99',
            'php' => '5.6.0-7.1.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
