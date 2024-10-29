<?php

namespace Hleb\Reference;

use Hleb\Static\Cache;

interface TemplateInterface
{
    /**
     * Returns the content of the initialized template.
     *
     * Возвращает содержимое инициализированного шаблона.
     */
    public function get(string $viewPath, array $extractParams = [], array $config = []): string;

    /**
     * Inserting a template and assigning variables from its parameters.
     * Example: ->insert('test', ['param' => 'value']);
     * Outputs the template /resources/views/test.php where the $param variable is equal to 'value'.
     * Example: ->insert('test.twig'); outputs template /resources/views/test.twig similarly
     * Example for a module: for an active module, the path will point to a folder
     * /modules/{module_name}/views/test.php
     *
     * Вставка шаблона и назначение переменных из его параметров.
     * Пример: ->insert('test', ['param' => 'value']);
     * Выводит шаблон /resources/views/test.php в котором переменная $param равна 'value'.
     * Пример: ->insert('test.twig'); аналогично выводит шаблон /resources/views/test.twig
     * Пример для модуля: для активного модуля путь будет указывать в папку
     * /modules/{module_name}/views/test.php
     *
     */
    public function insert(string $viewPath, array $extractParams = [], array $config = []): void;

    /**
     * Allows you to save the template to the cache, for example
     * Template::insertCache('template', ['param' => 'value'], sec:60)
     * will save the template to the cache for 1 minute.
     * In this case, if the template parameters ($extractParams) change,
     * then this will be a newly created cache, since the parameters are included
     * in the cache key along with $viewPath.
     *
     * Позволяет сохранить шаблон в кеш, например
     * Template::insertCache('template', ['param' => 'value'], sec:60)
     * сохранит в кеш шаблон на 1 минуту.
     * При этом если параметры шаблона ($extractParams) изменятся,
     * то это будет новый созданный кеш,
     * так как параметры входят в ключ кеша вместе с $viewPath.
     *
     * @see self::insert() - more details about method arguments.
     *                     - подробнее об аргументах метода.
     */
    public function insertCache(string $viewPath, array $extractParams = [], int $sec = Cache::DEFAULT_TIME, array $config = []): void;
}
