<?php

/*declare(strict_types=1);*/

namespace Hleb\Static;

use App\Bootstrap\BaseContainer;
use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\ForTestOnly;
use Hleb\CoreProcessException;
use Hleb\Main\Insert\BaseSingleton;
use Hleb\Reference\PathInterface;

#[Accessible]
final class Path extends BaseSingleton
{
    private static PathInterface|null $replace = null;

    /**
     * Converts the full path to relative to the project's root directory.
     * The result can be used in notifications given to the user.
     * For example:
     *
     * Преобразует полный путь в относительный по отношению к корневой директории проекта.
     * Результат можно использовать в отдаваемых пользователю оповещениях.
     * Например:
     *
     * '/home/user/projects/hleb/public/index.php' => '@/public/index.php'
     *
     * @see PathInfoDoc::special()
     */
    public static function relative(string $path): string
    {
        if (self::$replace) {
            return self::$replace->relative($path);
        }

        return BaseContainer::instance()->get(PathInterface::class)->relative($path);
    }

    /**
     * Recursively creates a directory according to the file path.
     *
     * Создаёт рекурсивно директорию для файлового пути.
     */
    public static function createDirectory(string $path, int $permissions = 0775): bool
    {
        if (self::$replace) {
            return self::$replace->createDirectory($path, $permissions);
        }

        return BaseContainer::instance()->get(PathInterface::class)->createDirectory($path, $permissions);
    }

    /**
     * Similar to the file_exists function, but can additionally
     * accept special paths with '@' at the beginning.
     *
     * Аналог функции file_exists, но дополнительно
     * может принимать специальные пути с '@' в начале.
     *
     * @see PathInfoDoc::special()
     */
    public static function exists(string $path): bool
    {
        if (self::$replace) {
            return self::$replace->exists($path);
        }

        return BaseContainer::instance()->get(PathInterface::class)->exists($path);
    }

    /**
     * Similar to the file_get_contents function, but can additionally
     * accept special paths with '@' at the beginning.
     *
     * Аналог функции file_get_contents, но дополнительно
     * может принимать специальные пути с '@' в начале.
     *
     * @see PathInfoDoc::special()
     */
    public static function contents(string $path, bool $use_include_path = false, $context = null, int $offset = 0, ?int $length = null): false|string
    {
        if (self::$replace) {
            return self::$replace->contents($path, $use_include_path, $context, $offset, $length);
        }

        return BaseContainer::instance()->get(PathInterface::class)->contents($path, $use_include_path, $context, $offset, $length);
    }

    /**
     * Similar to the file_put_contents function, but can additionally
     * accept special paths with '@' at the beginning.
     *
     * Аналог функции file_put_contents, но дополнительно
     * может принимать специальные пути с '@' в начале.
     *
     * @see PathInfoDoc::special()
     */
    public static function put(string $path, mixed $data, int $flags = 0, $context = null): false|int
    {
        if (self::$replace) {
            return self::$replace->put($path, $data, $flags, $context);
        }

        return BaseContainer::instance()->get(PathInterface::class)->put($path, $data, $flags, $context);
    }

    /**
     * Similar to the is_dir function, but can additionally
     * accept special paths with '@' at the beginning.
     *
     * Аналог функции is_dir, но дополнительно
     * может принимать специальные пути с '@' в начале.
     *
     * @see PathInfoDoc::special()
     */
    public static function isDir(string $path): bool
    {
        if (self::$replace) {
            return self::$replace->isDir($path);
        }

        return BaseContainer::instance()->get(PathInterface::class)->isDir($path);
    }

    /**
     * Returns the full path to the specified project file or folder according to the project settings.
     * Returns `false` if the file does not exist.
     * For example:
     * '@' or 'global' - the path to the project's root directory.
     * 'public' - path to the public folder of the project.
     * 'storage' - path to the project data folder.
     * 'views' - path to folder with file templates.
     * 'modules' - path to the `modules` folder (if it exists).
     * It is also possible to supplement this request by specifying a continuation to an EXISTING folder
     * or file. For example: Path::getReal('@{name}/resources') or Path::getReal('@{name}/favicon.ico'),
     * where `name` is `global` and `public` respectively.
     *
     * Возвращает полный путь до указанного файла или папки проекта согласно настройкам проекта.
     * Вернёт `false` если файл не существует.
     * Например:
     * '@' или 'global' - путь до корневой директории проекта.
     * 'public' - путь до публичной папки проекта.
     * 'storage' - путь до папки с данными проекта.
     * 'views' - путь до папки с шаблонами файлов.
     * 'modules' - путь до папки с модулями (при её существовании).
     * Также можно дополнить этот запрос, указав продолжение к СУЩЕСТВУЮЩЕЙ папке
     * или файлу. Например: Path::getReal('@{name]/resources') или Path::getReal('@{name}/favicon.ico'),
     * где `name` соответственно `global` и `public`.
     *
     * @see PathInfoDoc::special()
     */
    public static function getReal(string $keyOrPath): false|string
    {
        if (self::$replace) {
            return self::$replace->getReal($keyOrPath);
        }

        return BaseContainer::instance()->get(PathInterface::class)->getReal($keyOrPath);
    }

    /**
     * Similar to the Path::getReal() method. The difference is that when accessing a non-existent path,
     * returns a string with the target folder of the project and the specified file in it.
     * Checking the existence of a file and the correctness of its name is not checked in this case.
     *
     * Аналогично методу Path::getReal(). Отличие в том, что при обращении к несуществующему пути,
     * возвращает строку с целевой папкой проекта и указанным файлом в ней.
     * Проверка существования файла и корректность его имени в этом случае не проверяется.
     */
    public static function get(string $keyOrPath): false|string
    {
        if (self::$replace) {
            return self::$replace->get($keyOrPath);
        }

        return BaseContainer::instance()->get(PathInterface::class)->get($keyOrPath);
    }

    /**
     * @internal
     *
     * @see PatchForTest
     */
    #[ForTestOnly]
    public static function replaceWithMock(PathInterface|null $mock): void
    {
        if (\defined('HLEB_CONTAINER_MOCK_ON') && !HLEB_CONTAINER_MOCK_ON) {
            throw new CoreProcessException('The action is prohibited in the settings.');
        }
        self::$replace = $mock;
    }
}
