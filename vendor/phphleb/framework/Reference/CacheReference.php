<?php

/*declare(strict_types=1);*/

namespace Hleb\Reference;

use CallbackFilterIterator;
use FilesystemIterator;
use GlobIterator;
use Hleb\Base\RollbackInterface;
use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\AvailableAsParent;
use Hleb\Constructor\Cache\ClassWithDataCreator;
use Hleb\Constructor\Cache\ClearRandomFileCache;
use Hleb\Constructor\Cache\WebCron;
use Hleb\Constructor\Data\SystemSettings;
use Hleb\CoreProcessException;
use Hleb\Helpers\DirectoryCleaner;
use Hleb\Main\Insert\ContainerUniqueItem;
use Hleb\Main\Routes\Prepare\Defender;
use Hleb\Static\Cache;
use Hleb\Static\Settings;
use RecursiveIteratorIterator;
use RuntimeException;
use SplFileInfo;

/**
 * Caching data of various types in the framework.
 *
 * Кеширование данных различного типа во фреймворке.
 */
#[Accessible] #[AvailableAsParent]
class CacheReference extends ContainerUniqueItem implements CacheInterface, Interface\Cache, RollbackInterface
{
    public const BLOCKED_PROCESS_TAG = 'IN_PROGRESS';

    public const CLASS_PREFIX = 'HlC4d_';

    public const HEADLINE_PREFIX = '0_headlines';

    private const CLEAR_COUNT = 3;

    private const FIRST_PREFIX_LEN = 1;

    private const SECOND_PREFIX_LEN = 2;

    private static ?array $applicants = null;

    private static bool $notCleaningAction = true;

    private static ?string $cachePath = null;

    private static ?int $globalTime = null;

    private static ?ClassWithDataCreator $creator = null;

    private static array $lastKeys = [];

    private static ?Defender $defender = null;

    private static $cacheOn = true;

    public function __construct()
    {
        $this->rollback();
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public static function rollback(): void
    {
        self::$notCleaningAction = true;
        self::$applicants = null;
        self::$globalTime = null;
        // self::$lastKeys = [];
        self::$creator = null;
        self::$cachePath = null;
        self::$cacheOn = true;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function set(string $key, mixed $value, int $ttl = Cache::DEFAULT_TIME): bool
    {
        self::init();
        self::clearRandomExpiredCache();

        return self::setData($key, $value, $ttl);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function setString(string $key, string $value, int $ttl = Cache::DEFAULT_TIME): bool
    {
        self::init();
        self::clearRandomExpiredCache();

        return self::setData($key, $value, $ttl);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function setList(string $key, array $value, int $ttl = Cache::DEFAULT_TIME): bool
    {
        self::init();
        self::clearRandomExpiredCache();

        return self::setData($key, $value, $ttl);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function setObject(string $key, object $value, int $ttl = Cache::DEFAULT_TIME): bool
    {
        self::init();
        self::clearRandomExpiredCache();

        return self::setData($key, $value, $ttl);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function getConform(string $key, callable $func, int $ttl = Cache::DEFAULT_TIME): mixed
    {
        self::init();
        if (self::isExists($key)) {
            return self::get($key);
        }
        $data = $func();
        self::set($key, $data, $ttl);

        return $data;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function get(string $key, mixed $default = false): mixed
    {
        self::init();
        self::clearRandomExpiredCache();

        if (!self::isExists($key)) {
            return $default;
        }

        $data = self::getData($key);
        if (\is_array($data)) {
            if ($data['type'] === 'object') {
                return (object)\unserialize($data['data'], ['allowed_classes' => true]);
            }
            if ($data['type'] === 'mixed') {
                return \unserialize($data['data'], ['allowed_classes' => true]);
            }
            return $data['data'];
        }
        return $default;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function getDel(string $key, mixed $default = false): mixed
    {
        $data = self::get($key, $default);
        self::delete($key);

        return $data;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function getString(string $key, string|false $default = false): string|false
    {
        self::init();
        self::clearRandomExpiredCache();

        if (!self::isExists($key)) {
            return $default;
        }

        $data = self::getData($key);

        if (\is_array($data)) {
            if ($data['type'] !== 'string') {
                throw new CoreProcessException(
                    'Wrong type of get value for cache (string expected).' .
                    ' Perhaps the get() method is used instead of getList().'
                );
            }
            return $data['data'];
        }
        return $default;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function getStringDel(string $key, string|false $default = false): string|false
    {
        $data = self::getString($key, $default);
        self::delete($key);

        return $data;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function getList(string $key, array|false $default = false): array|false
    {
        self::init();
        self::clearRandomExpiredCache();

        if (!self::isExists($key)) {
            return $default;
        }

        $data = self::getData($key);
        if (\is_array($data)) {
            if ($data['type'] !== 'array') {
                throw new CoreProcessException(
                    'Wrong type of get value for cache (an array was expected).' .
                    ' Perhaps the getList() method is used instead of get().'
                );
            }
            return $data['data'];
        }
        return $default;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function getListDel(string $key, array|false $default = false): string|false
    {
        $data = self::getList($key, $default);
        self::delete($key);

        return $data;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function getObject(string $key, object|false $default = false): object|false
    {
        self::init();
        self::clearRandomExpiredCache();

        if (!self::isExists($key)) {
            return $default;
        }

        $data = self::getData($key);
        if (\is_array($data)) {
            if ($data['type'] !== 'object') {
                throw new CoreProcessException(
                    'Wrong type of get value for cache (object expected).' .
                    ' Perhaps the getObject() method is used instead of get().'
                );
            }
            return (object)unserialize($data['data'], ['allowed_classes' => true]);
        }
        return $default;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function getObjectDel(string $key, object|false $default = false): object|false
    {
        $data = self::getObject($key, $default);
        self::delete($key);

        return $data;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function getMultiple(array $keys, mixed $default = null): array
    {
        $result = [];
        foreach ($keys as $key) {
            $data = self::get($key);
            if ($data === false) {
                if ($default !== null) {
                    $result[] = $default;
                }
            } else {
                $result[] = $data;
            }
        }
        return $result;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function setMultiple(array $values, int $ttl = Cache::DEFAULT_TIME): bool
    {
        $status = true;
        foreach ($values as $key => $value) {
            $result = self::set($key, $value, $ttl);
            $status = $status ? $result : false;
        }
        return $status;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function deleteMultiple(array $values): bool
    {
        $status = true;
        foreach ($values as $key) {
            $result = self::delete($key);
            $status = $status ? $result : false;
        }
        return $status;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function delete(string $key): bool
    {
        self::init();
        $cacheKey = self::getKey($key);
        $file = self::$cachePath . \ltrim(self::getFilePath($cacheKey), DIRECTORY_SEPARATOR);
        self::errorSuppression(function() use ($file) {
            \unlink($file);
        });
        \clearstatcache(true, $file);


        $class = self::CLASS_PREFIX . $cacheKey;

        if (\class_exists($class, false)) {
            $class::$expired = 1;
        }
        self::clearExpireData($cacheKey);

        return \file_exists($file) === false;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function has(string $key): bool
    {
        return self::isExists($key);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function isExists(string $key): bool
    {
        $expired = self::getExpire($key);

        return $expired !== false && $expired > 0;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function getExpire(string $key): int|false
    {
        self::init();

        if (!self::$cacheOn) {
            // When caching is disabled, the behavior of ttl=0 is simulated.
            // При отключенном кешировании имитируется поведение ttl=0.
            return false;
        }
        $file = self::getExpirePath(self::getKey($key));

        if (\file_exists($file)) {
            $content = file_get_contents($file);
            if ($content === self::BLOCKED_PROCESS_TAG) {
                return false;
            }
            $time = (int)@\file_get_contents($file);
            if ($time && $time > self::$globalTime) {
                return $time - self::$globalTime;
            }
            self::delete($key);
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function setExpire(string $key, int $ttl): bool
    {
        self::init();

        if (!self::$cacheOn) {
            // When caching is disabled, the behavior of ttl=0 is simulated.
            // При отключенном кешировании имитируется поведение ttl=0.
            return true;
        }

        $cacheKey = self::getKey($key);
        $time = self::$globalTime + $ttl;
        $data = self::getData($key);
        if ($data === false) {
            return false;
        }
        $data = self::get($key);

        return self::setData($key, $data, $ttl) && self::setExpireData($cacheKey, $time);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function count(): int
    {
        self::init();
        if (!\is_dir(self::$cachePath)) {
            return 0;
        }
        return (new GlobIterator(self::$cachePath . '*/*.php'))->count();
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function clear(): bool
    {
        self::init();
        return (new DirectoryCleaner())->forceRemoveDir(self::$cachePath);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function clearExpired(): void
    {
        self::init();
        self::clearAllExpiredCache();
    }

    /**
     * Returns the system cache key.
     *
     * Возвращает системный ключ кеша.
     */
    private static function getKey(string $key): string
    {
        if (isset(self::$lastKeys[$key])) {
            // Skip creating cache for already known keys.
            // Пропуск создания кеша для уже известных ключей.
            return self::$lastKeys[$key];
        }
        $hash = \substr(\sha1($key), 0, 10) . \sha1($key . '_salt');
        if (\count(self::$lastKeys) > 10) {
            \array_shift(self::$lastKeys);
        }

        return self::$lastKeys[$key] = $hash;
    }

    /**
     * Returns data in the required format.
     *
     * Возвращает данные в необходимом формате.
     */
    private static function createData(string $key, array|string $data, int $time, string $type): array
    {
        return [
            'time' => $time,
            'key' => $key,
            'data' => $data,
            'type' => $type,
        ];
    }

    /**
     * General data retrieval by key.
     *
     * Общее получение данных по ключу.
     */
    private static function getData(string $key): string|array|false
    {
        if (!self::$cacheOn) {
            // When caching is disabled, the behavior of ttl=0 is simulated.
            // При отключенном кешировании имитируется поведение ttl=0.
            return false;
        }
        $cacheKey = self::getKey($key);
        /** @var object $class */
        $class = self::CLASS_PREFIX . $cacheKey;
        $file = self::$cachePath . \ltrim(self::getFilePath($cacheKey), DIRECTORY_SEPARATOR);
        if (!\class_exists($class, false)) {
            if (!\file_exists($file)) {
                return false;
            }
            try {
                require $file;
            } catch (\Throwable $e) {
                return false;
            }
        }
        if ($class::$expired === 0) {
            /** @var array $data */
            return $class::getData();
        }

        return false;
    }

    /**
     * Setting the cache expiration time in a separate repository.
     *
     * Установка времени истечения кеша в отдельном репозитории.
     */
    private static function setExpireData(string $cacheKey, int $time): bool
    {
        $file = self::getExpirePath($cacheKey);
        \hl_create_directory($file);
        \file_put_contents($file, $time);
        @\chmod($file, 0664);

        return \file_exists($file);
    }

    /**
     * Prevents multiple processes from overwriting the cache at the same time.
     * Such a problem may occur after clearing the cache,
     * when all processes begin to fill it.
     * Returning true means that the cache does not need to be rewritten.
     * If it returns false, then the cache can be written.
     *
     * Предотвращает одновременное переписывание кеша несколькими процессами.
     * Такая проблема может возникнуть после очистки кеша,
     * когда все процессы начнут его заполнять.
     * Возвращение true означает, что кеш переписывать не нужно.
     * Если возвращает false, то кеш можно записать.
     */
    private static function solutionCacheStampede(string $cacheKey): bool
    {
        $file = self::getExpirePath($cacheKey);
        // The maximum number of cycles to wait for the cache.
        // Максимальное количество циклов для ожидания кеша.
        $cycles = 30;
        for ($i = 0; $i < $cycles; $i++) {
            if (!\file_exists($file)) {
                return false;
            }
            if (\file_get_contents($file) === self::BLOCKED_PROCESS_TAG) {
                // The longer a process hangs, the more likely it is to be updated.
                // Чем дольше висит процесс, тем больше вероятность, что он будет обновлён.
                if (\rand(0, $cycles - $i) !== 1) {
                    // Not all concurrent processes participate in updating a stuck cache.
                    // Не все конкурентные процессы участвуют в обновлении зависшего кеша.
                    return true;
                }
                \sleep(1);
                continue;
            }
            // If the file was locked first and then replaced.
            // Если сначала файл был заблокирован, а потом заменен.
            return $i > 0;
        }
        return false;
    }

    /**
     * Returns the path to the preliminary cache information file.
     *
     * Возвращает путь до предварительного файла с информацией о кеше.
     */
    private static function getExpirePath(string $cacheKey): string
    {
        return self::$cachePath . self::HEADLINE_PREFIX . self::getFilePath($cacheKey, 'txt');
    }

    /**
     * Clearing the cache expiration time in a separate repository.
     *
     * Очистка времени истечения кеша в отдельном репозитории.
     */
    private static function clearExpireData($cacheKey): void
    {
        $path = self::$cachePath . self::HEADLINE_PREFIX . self::getFilePath($cacheKey, 'txt');
        self::errorSuppression(function() use ($path) {
            \unlink($path);
        });
        \clearstatcache(true, $path);
    }


    /**
     * General storage of data in the cache.
     *
     * Общее сохранение данных в кеш.
     */
    private static function setData(string $key, mixed $data, int $ttl): bool
    {
        if (!self::$cacheOn) {
            // When caching is disabled, the behavior of ttl=0 is simulated.
            // При отключенном кешировании имитируется поведение ttl=0.
            return true;
        }
        $cacheKey = self::getKey($key);
        if (self::solutionCacheStampede($cacheKey)) {
            // Data saved by another process.
            // Данные сохранены другим процессом.
            return true;
        }
        $path = self::getExpirePath($cacheKey);
        \hl_create_directory($path);
        \file_put_contents($path, self::BLOCKED_PROCESS_TAG, LOCK_EX);
        @\chmod($path, 0664);
        \clearstatcache(true, $path);

        $prefix = \substr($cacheKey, 0, self::FIRST_PREFIX_LEN);
        $secondPrefix = \substr($cacheKey, 0, self::SECOND_PREFIX_LEN);

        $path = self::$cachePath . $prefix . DIRECTORY_SEPARATOR . $secondPrefix;
        \hl_create_directory($path);

        $class = self::CLASS_PREFIX . $cacheKey;
        $path .= DIRECTORY_SEPARATOR . $class . '.php';
        $time = \time() + $ttl;

        if (\is_string($data)) {
            $type = 'string';
        } else if (\is_array($data)) {
            $type = 'array';
        } else if (\is_object($data)) {
            $type = 'object';
            $data = \serialize($data);
        } else {
            $type = 'mixed';
            $data = \serialize($data);
        }
        $data = self::createData($key, $data, $time, $type);
        self::$defender->handle($data);

        self::$creator->saveContent(
            className: $class,
            path: $path,
            data: $data,
            cells: ['cacheTime' => $ttl, 'time' => $time, 'expired' => 0],
            privateData: false,
        );

        self::setExpireData($cacheKey, $time);

        if (\class_exists($class, false)) {
            $class::$data = $data;
            $class::$cacheTime = $ttl;
            $class::$time = $time;
            $class::$expired = 0;
        }

        return \file_exists($path);
    }

    /**
     * Returns the path to the cache file.
     *
     * Возвращает путь к файлу кеша.
     */
    private static function getFilePath(string $cacheKey, string $extension = 'php'): string
    {
        $prefix = \substr($cacheKey, 0, self::FIRST_PREFIX_LEN);
        $secondPrefix = \substr($cacheKey, 0, self::SECOND_PREFIX_LEN);
        return DIRECTORY_SEPARATOR . $prefix . DIRECTORY_SEPARATOR . $secondPrefix . DIRECTORY_SEPARATOR . self::CLASS_PREFIX . $cacheKey . '.' . $extension;
    }

    /**
     * Arbitrary deletion of expired cache.
     *
     * Произвольное удаление просроченного кеша.
     */
    private static function clearRandomExpiredCache(): void
    {
        if (self::$notCleaningAction) {
            self::clearRandomExpiredFiles();
            $maxSize = Settings::getParam('common', 'max.cache.size');
            if ($maxSize > 0) {
                self::removalForOversizing();
            }
        }
        self::$notCleaningAction = false;
    }

    /**
     * Deleting all expired cache.
     *
     * Удаление всего кеша с истекшим сроком действия.
     */
    private static function clearAllExpiredCache(): void
    {
        $iterator = self::getFileIterator(self::$cachePath . self::HEADLINE_PREFIX);
        foreach ($iterator as $file) {
            self::deleteExpiredCacheFile($file);
        }
    }

    /**
     * Deleting expired cache files.
     *
     * Удаление файлов кеша с истекшим сроком действия.
     */
    private static function clearRandomExpiredFiles(): void
    {
        $I = DIRECTORY_SEPARATOR;
        $path = self::$cachePath . self::HEADLINE_PREFIX;
        if ((self::$applicants === null) && \file_exists($path)) {
            self::$applicants = [];
            $applicants = (array)\scandir($path);
            foreach ($applicants as $applicant) {
                if (\str_starts_with($applicant, '.')) {
                    continue;
                }
                self::$applicants[] = $applicant;
            }
        }
        if (!self::$applicants) {
            return;
        }
        \shuffle(self::$applicants);
        $firstDir = self::$applicants[0];
        $dir = $path . $I . $firstDir;
        $dirs = (array)\scandir($dir);
        foreach ($dirs as $key => $value) {
            if (\str_starts_with($value, '.')) {
                unset($dirs[$key]);
            }
        }
        $count = \count($dirs);
        if (!$count) {
            $dataPath = \str_replace(self::HEADLINE_PREFIX . $I, '', $dir);
            unset(self::$applicants[0]);
            self::$applicants = \array_values(self::$applicants);
        }
        \shuffle($dirs);
        $dirs = \array_slice($dirs, 0, \min($count, self::CLEAR_COUNT));

        $checked = 0;

        foreach ($dirs as $dir) {
            $dir = $path . $I . $firstDir . $I . $dir;
            $dataPath = \str_replace(self::HEADLINE_PREFIX . $I, '', $dir);
            // Due to competitive deletion of directories, they may not be available immediately after receiving the list.
            // Из-за конкурентного удаления директорий они могут отсутствовать сразу после получения списка.
            if (!\file_exists($dir)) {
                continue;
            }

            $files = (array)\scandir($dir);
            foreach ($files as $key => $file) {
                if (\str_starts_with($file, '.')) {
                    unset($files[$key]);
                }
            }
            \shuffle($files);
            if (!$files) {
                @rmdir($dir);
                $dataFiles = (array)\scandir($dataPath);
                foreach ($dataFiles as $k => $dataFile) {
                    if (\str_starts_with($dataFile, '.')) {
                        unset($dataFiles[$k]);
                    }
                }
                if (!$dataFiles && \file_exists($dataPath)) {
                    @rmdir($dataPath);
                }
                continue;
            }

            foreach ($files as $file) {
                self::deleteExpiredCacheFile($dir . $I . $file);
                $checked++;
                if ($checked > 100) {
                    break 2;
                }
            }
        }
        \clearstatcache();
    }

    /**
     * Deleting a single cache.
     *
     * Удаление единичного кеша.
     */
    private static function deleteExpiredCacheFile(string $path): void
    {
        if (!\file_exists($path)) {
            return;
        }
        $content = @\file_get_contents($path);
        // The file is in process and should not be deleted.
        // Файл занят процессом и его не надо удалять.
        if (!$content || $content === self::BLOCKED_PROCESS_TAG) {
            return;
        }
        $expired = (int)$content;

        if ($expired < self::$globalTime - 1) {
            $expiredPath = \str_replace([self::HEADLINE_PREFIX . DIRECTORY_SEPARATOR, '.txt'], ['', '.php'], $path);

            // Due to concurrent deletion of files, they may be missing immediately after checking for existence.
            // Из-за конкурентного удаления файлов они могут отсутствовать сразу после проверки на существование.
            self::errorSuppression(function() use ($expiredPath) {
                \unlink($expiredPath);
            });
            \clearstatcache(true, $expiredPath);
            self::errorSuppression(function() use ($path) {
                \unlink($path);
            });
            \clearstatcache(true, $path);

            $class = self::CLASS_PREFIX . \str_replace('.txt', '', \explode(self::CLASS_PREFIX, $path)[1]);
            if (\class_exists($class, false)) {
                /** @var object $class */
                $class::$expired = 1;
            }
        }
    }

    /**
     * Returns an iterator for the files in the folder.
     *
     * Возвращает итератор для файлов в папке.
     */
    private static function getFileIterator(string $secondPath): CallbackFilterIterator
    {
        return new CallbackFilterIterator(
            new RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($secondPath, FilesystemIterator::SKIP_DOTS)
            ),
            function (SplFileInfo $current) {
                return $current->isFile();
            }
        );
    }

    /**
     * Every minute it checks the size of the cache folder and,
     * if exceeded, deletes random cache files.
     *
     * Каждую минуту проверяет размер папки кэша и, если
     * он превышен, удаляет случайные файлы кэша.
     */
    private static function removalForOversizing(): void
    {
        WebCron::offer('hl_all_file_cache', static function () {
            (new ClearRandomFileCache())->run(self::$cachePath . self::HEADLINE_PREFIX);
        }, 60);
    }

    /**
     * Delayed initialization of handlers.
     *
     * Отложенная инициализация обработчиков.
     */
    private static function init(): void
    {
        if (!self::$creator) {
            self::$cachePath = SystemSettings::getPath('storage') . '/cache/source/';
            self::$cacheOn = SystemSettings::getCommonValue('app.cache.on');
            self::$creator = new ClassWithDataCreator();
            self::$globalTime = \time();
        }
        if (!self::$defender) {
            self::$defender = new Defender();
        }
    }

    /**
     * Due to possible concurrent access, some errors need to be suppressed.
     *
     * Из-за возможного конкурентного доступа некоторые ошибки нужно подавлять.
     */
    private static function errorSuppression(callable $callback): void
    {
        try {
            \set_error_handler(function ($_errno, $errstr) {
                throw new RuntimeException($errstr);
            });
            $callback();
        } catch (RuntimeException) {
        } finally {
            \restore_error_handler();
        }
    }
}
