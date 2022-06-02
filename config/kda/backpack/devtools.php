<?php

$sidebarSeed = (class_exists("\\KDA\\Backpack\\DynamicSidebar\\DevTools") ? \KDA\Backpack\DynamicSidebar\DevTools::seeds(): []);
$authSeed = (class_exists("\\KDA\\Backpack\\Auth\\DevTools") ? \KDA\Backpack\Auth\DevTools::seeds(): []);

return [
    'use_dynamic_admin_sidebar'=> true,
    'generate_backpack_crud'=> true,
    'blueprint' => [
        'erase' => true,
        'source' => 'BaseModels',
    ],
    'seeds'=>[
        ...$sidebarSeed,
        ...$authSeed
    ],
    'should_restore_seeds'=> true,
    'dump_empty_seeds'=> false,
    'debug' => false,
    
    'stages'=> [
        'default'=> [

        ],
        'patch'=> [

        ]
    ],
    'stage'=> 'default'
];
