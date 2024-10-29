<?php

namespace Hleb\Reference;

use Hleb\Main\Info\PathInfoDoc;

interface PathInterface
{
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
    public function relative(string $path): string;

    /**
     * Recursively creates a directory according to the file path.
     *
     * Создаёт рекурсивно директорию для файлового пути.
     */
    public function createDirectory(string $path, int $permissions = 0775): bool;

    /**
     * Similar to the file_exists function, but can additionally
     * accept special paths with '@' at the beginning.
     *
     * Аналог функции file_exists, но дополнительно
     * может принимать специальные пути с '@' в начале.
     *
     * @see PathInfoDoc::special()
     */
    public function exists(string $path): bool;

    /**
     * Similar to the file_get_contents function, but can additionally
     * accept special paths with '@' at the beginning.
     *
     * Аналог функции file_get_contents, но дополнительно
     * может принимать специальные пути с '@' в начале.
     *
     * @see PathInfoDoc::special()
     */
    public function contents(string $path, bool $use_include_path = false, $context = null, int $offset = 0, ?int $length = null): false|string;

    /**
     * Similar to the file_put_contents function, but can additionally
     * accept special paths with '@' at the beginning.
     *
     * Аналог функции file_put_contents, но дополнительно
     * может принимать специальные пути с '@' в начале.
     *
     * @see PathInfoDoc::special()
     */
    public function put(string $path, mixed $data, int $flags = 0, $context = null): false|int;

    /**
     * Similar to the is_dir function, but can additionally
     * accept special paths with '@' at the beginning.
     *
     * Аналог функции is_dir, но дополнительно
     * может принимать специальные пути с '@' в начале.
     *
     * @see PathInfoDoc::special()
     */
    public function isDir(string $path): bool;

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
     * or file. For example: ->getReal('@{name}/resources') or ->getReal('@{name}/favicon.ico'),
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
     * или файлу. Например: ->getReal('@{name]/resources') или ->getReal('@{name}/favicon.ico'),
     * где `name` соответственно `global` и `public`.
     *
     * @see PathInfoDoc::special()
     */
    public function getReal(string $keyOrPath): false|string;

    /**
     * Similar to the ->getReal() method. The difference is that when accessing a non-existent path,
     * returns a string with the target folder of the project and the specified file in it.
     * Checking the existence of a file and the correctness of its name is not checked in this case.
     *
     * Аналогично методу ->getReal(). Отличие в том, что при обращении к несуществующему пути,
     * возвращает строку с целевой папкой проекта и указанным файлом в ней.
     * Проверка существования файла и корректность его имени в этом случае не проверяется.
     *
     * @see PathInfoDoc::special()
     */
    public function get(string $keyOrPath): false|string;
}
