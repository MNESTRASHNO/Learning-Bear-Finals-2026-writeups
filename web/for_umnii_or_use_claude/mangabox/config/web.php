<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'mangabox',
    'name' => 'MangaBox',
    'basePath' => dirname(__DIR__),
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'bootstrap' => ['log'],
    'components' => [
        'request' => [
            'cookieValidationKey' => 'dK3jfP9xNwQr7TmYvL2sA8bC4eH6gJ0',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '' => 'site/index',
                'manga' => 'manga/index',
                'manga/<id:\d+>' => 'manga/view',
                'manga/<mangaId:\d+>/chapter/<id:\d+>' => 'chapter/read',
                'profile' => 'profile/index',
                'profile/theme' => 'profile/theme',
                'login' => 'site/login',
                'signup' => 'site/signup',
            ],
        ],
        'assetManager' => [
            'bundles' => [
                'yii\bootstrap5\BootstrapAsset' => [
                    'css' => [],
                    'sourcePath' => null,
                    'baseUrl' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist',
                    'cssOptions' => ['crossorigin' => 'anonymous'],
                ],
                'yii\bootstrap5\BootstrapPluginAsset' => [
                    'js' => [],
                    'sourcePath' => null,
                    'baseUrl' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist',
                    'jsOptions' => ['crossorigin' => 'anonymous'],
                ],
            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
