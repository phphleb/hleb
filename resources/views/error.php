<?php
/**
 * HTML template for the HTTP GET method error page.
 *
 * HTML-шаблон для страницы ошибок HTTP метода GET.
 *
 * @var $httpCode int
 * @var $message string
 * @var $apiVersion int
 * @var $uriPrefix string
 */
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width" />
    <meta name="robots" content="noindex, noarchive" />
    <link rel="stylesheet" href="/<?= $uriPrefix ?>/framework/v<?= $apiVersion ?>/css/error">
    <title><?= $httpCode . '. ' . $message ?></title>
</head>
<body>
<div class="hl-error-page-content">
    <h1 class="hl-error-page-message">
        <span class="hl-error-page-code"><?= $httpCode ?></span>
        <?= $message ?>
    </h1>
</div>
</body>
</html>
