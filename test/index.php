<?php
require '../vendor/autoload.php';

$tcaptcha = new \Tcaptcha\Tcaptcha([
    'type' => 'normal', // operation是简答的计算

    // 公用配置
    'width' => 100,
//    'height' => 20,
    'points' => 100, // 杂点数量
    'angle' => 10,
    'fontSize' => 12,
    'backgroundColor' => '255,255,255',

    // 文字类型的配置
//    'captchaOptions' => [
//
//        'type' => 'number|letter|blend|zh_cm',
//
//        // 字符列表
//        'showCodes' => '',
//
//        // 显示数量
//        'size' => 4,
//    ],

    // 计算类型的配置
    'captchaOptions' => [
        'type' => '+',
        'digit' => 3
    ],
]);

$tcaptcha->generate();