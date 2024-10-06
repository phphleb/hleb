<?php

declare(strict_types=1);

namespace Hleb\Main\Info;

/**
 * @internal
 */
final class PathInfoDoc
{
    /**
     * List of standard paths for the framework from the project root directory.
     * Additional paths can be assigned in the `system` configuration.
     *
     * Список стандартных путей для фреймворка от корневой директории проекта.
     * Дополнительные пути могут быть назначены в конфигурации `system`.
     *
     * '@' => '/',
     *
     * 'global' => '/',
     *
     * 'app' => '/app',
     *
     * 'public' => "/%public%",
     *
     * 'storage' => '/storage',
     *
     * 'resources' => '/resources',
     *
     * 'views' => '/resources/views',
     *
     * 'modules' => '/modules',
     *
     * 'vendor' => "/%vendor%",
     *
     * 'library' => "/%vendor%/phphleb",
     *
     * 'framework' => "/%vendor%/phphleb/framework",
     */
    public static function special(string $public = 'public', string $vendor = 'vendor'): array
    {
        return [
            /*
             * Project root directory.
             *
             * Корневая директория проекта.
             */
            '@' => '/',
            '@global' => '/',

            /*
             * Folder with custom project classes.
             *
             * Папка с пользовательскими классами проекта.
             */
            '@app' => '/app',

            /*
             * Folder with public project files; the web server is directed to this folder.
             *
             * Папка с публичными файлами проекта, в эту папку направлен веб-сервер.
             */
            '@public' => "/$public",

            /*
             * Folder for storing auxiliary resources: logs, cache, etc.
             *
             * Папка-хранилище вспомогательных ресурсов: логи, кеш и тд.
             */
            '@storage' => '/storage',

            /*
             * A folder with various project resources: page templates, email templates, templates for building CSS, etc.
             *
             * Папка с различными ресурсами проекта: шаблоны страниц, шаблоны писем, шаблоны для сборки CSS и тд.
             */
            '@resources' => '/resources',

            /*
             * Folder with project page templates.
             *
             * Папка с шаблонами страниц проекта.
             */
            '@views' => '/resources/views',

            /*
             * Folder with project modules.
             *
             * Папка с модулями проекта.
             */
            '@modules' => '/modules',

            /*
             * Folder with included libraries.
             *
             * Папка с подключаемыми библиотеками.
             */
            '@vendor' => "/$vendor",

            /*
             * Folder with plug-in libraries phphleb.
             *
             * Папка с подключаемыми библиотеками phphleb.
             */
            '@library' => "/$vendor/phphleb",

            /*
             * Folder with core classes of the framework.
             *
             * Папка с классами ядра фреймворка.
             */
            '@framework' => "/$vendor/phphleb/framework",
        ];
    }
}
