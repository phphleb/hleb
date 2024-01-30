<?php
use Hleb\Constructor\Script\Hlogin;
use Hleb\Static\System;
?><!DOCTYPE html>
<html lang="en">
<head>
    <!-- Demo page / Демонстрационная страница -->
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width"/>
    <meta name="robots" content="noindex, noarchive"/>
    <meta name="description" content="Framework HLEB"/>
    <meta name="theme-color" content="#ff786c"/>
    <link rel="icon" href="/favicon.ico" type="image/x-icon"/>
    <link rel="stylesheet" href="/hlresource/framework/v<?= System::getApiVersion() ?>/css/default"/>
    <title>HLEB Start Page</title>
</head>
<body>
<div class="header">
    <div class="wrapper">
        <div class="header-indent"></div>
        <div class="header-version">PHP HLEB Framework v<?= System::getVersion() ?></div>
        <div class="header-block"></div>
    </div>
</div>
<div class="info">
    <div class="content-wrapper">
        <div class="content">
            <img src="/hlresource/framework/v<?= System::getApiVersion() ?>/svg/logo" width="200" height="200" alt="HL"><br>
            <div class="text"><i>Demo page.</i></div>
            <?= Hlogin::info(); ?>
        </div>
    </div>
</div>
<div class="footer">
    <div class="repository">GitHub:
        <a href="https://github.com/phphleb/hleb/">phphleb/hleb<span class="footer-link">&#10551;</span></a>
        <a href="https://github.com/phphleb/framework/">phphleb/framework<span class="footer-link">&#10551;</span></a>
        <a href="https://github.com/phphleb/hlogin/">phphleb/hlogin<span class="footer-link">&#10551;</span></a>
    </div>
    <div class="php-version">PHP v<?= \phpversion() ?></div>
</div>
<?= Hlogin::get(); ?>
</body>
