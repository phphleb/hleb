<?php

/*declare(strict_types=1);*/

namespace Hleb\Static;

use App\Bootstrap\BaseContainer;
use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\ForTestOnly;
use Hleb\CoreProcessException;
use Hleb\Main\Insert\BaseSingleton;
use Hleb\Reference\TemplateInterface;

#[Accessible]
final class Template extends BaseSingleton
{
    private static TemplateInterface|null $replace = null;

    /**
     * Returns the content of the initialized template.
     *
     * Возвращает содержимое инициализированного шаблона.
     *
     * @param string $viewPath - special path to the template file.
     *                         - специальный путь к файлу шаблона.
     *
     * @param array $extractParams - a named array of values converted into variables inside the template.
     *                             - именованный массив значений преобразуемых в переменные внутри шаблона.
     *
     * @param array $config - config for replacing data in the transferred container when testing the template.
     *                      - конфиг для замены данных в передаваемом контейнере при тестировании шаблона.
     */
    public static function get(string $viewPath, array $extractParams = [], array $config = []): string
    {
        if (self::$replace) {
            return self::$replace->get($viewPath, $extractParams, $config);
        }

        return BaseContainer::instance()->get(TemplateInterface::class)->get($viewPath, $extractParams, $config);
    }

    /**
     * Inserting a template and assigning variables from its parameters.
     * Example: Template::insert('test', ['param' => 'value']);
     * Outputs the template /resources/views/test.php where the $param variable is equal to 'value'.
     * Example: Template::insert('test.twig'); outputs template /resources/views/test.twig similarly
     * Example for a module: for an active module, the path will point to a folder
     * /modules/{module_name}/views/test.php
     *
     * Вставка шаблона и назначение переменных из его параметров.
     * Пример: Template::insert('test', ['param' => 'value']);
     * Выводит шаблон /resources/views/test.php в котором переменная $param равна 'value'.
     * Пример: Template::insert('test.twig'); аналогично выводит шаблон /resources/views/test.twig
     * Пример для модуля: для активного модуля путь будет указывать в папку
     * /modules/{module_name}/views/test.php
     *
     * @param string $viewPath - special path to the template file.
     *                         - специальный путь к файлу шаблона.
     *
     * @param array $extractParams - a named array of values converted into variables inside the template.
     *                             - именованный массив значений преобразуемых в переменные внутри шаблона.
     *
     * @param array $config - config for replacing data in the transferred container when testing the template.
     *                      - конфиг для замены данных в передаваемом контейнере при тестировании шаблона.
     */
    public static function insert(string $viewPath, array $extractParams = [], array $config = []): void
    {
        if (self::$replace) {
            self::$replace->insert($viewPath, $extractParams, $config);
        } else {
            BaseContainer::instance()->get(TemplateInterface::class)->insert($viewPath, $extractParams, $config);
        }
    }

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
     * @param string $viewPath - special path to the template file.
     *                         - специальный путь к файлу шаблона.
     *
     * @param array $extractParams - a named array of values converted into variables inside the template.
     *                             - именованный массив значений преобразуемых в переменные внутри шаблона.
     *
     * @param int $sec - number of seconds for caching.
     *                 - количество секунд для кеширования.
     *
     * @param array $config - config for replacing data in the transferred container when testing the template.
     *                      - конфиг для замены данных в передаваемом контейнере при тестировании шаблона.
     *
     */
    public static function insertCache(string $viewPath, array $extractParams = [], int $sec = Cache::DEFAULT_TIME, array $config = []): void
    {
        if (self::$replace) {
            self::$replace->insertCache($viewPath, $extractParams, $sec, $config);
        } else {
            BaseContainer::instance()->get(TemplateInterface::class)->insertCache($viewPath, $extractParams, $sec, $config);
        }
    }

    /**
     * @internal
     *
     * @see TemplateForTest
     */
    #[ForTestOnly]
    public static function replaceWithMock(TemplateInterface|null $mock): void
    {
        if (\defined('HLEB_CONTAINER_MOCK_ON') && !HLEB_CONTAINER_MOCK_ON) {
            throw new CoreProcessException('The action is prohibited in the settings.');
        }
        self::$replace = $mock;
    }
}
