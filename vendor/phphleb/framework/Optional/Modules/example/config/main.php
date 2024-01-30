<?php

if (\file_exists(__DIR__ . '/main-local.php')) { return (require __DIR__ . '/main-local.php');}

/**
 * Custom module settings that override settings from /config/main.php.
 * Configuration files other than `main` and `database` are not overridden.
 *
 * Пользовательские настройки модуля, которые переопределяют настройки из /config/main.php.
 * Другие файлы конфигурации, кроме `main` и `database`, не переопределяются.
 */
return [];
