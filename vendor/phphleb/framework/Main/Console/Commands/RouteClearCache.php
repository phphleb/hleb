<?php

declare(strict_types=1);

namespace Hleb\Main\Console\Commands;

use Hleb\Helpers\DirectoryCleaner;
use Hleb\Static\Path;

/**
 * @internal
 */
final class RouteClearCache
{
    private int $code = 0;

    /**
     * Returns the code of the executed command or a default value.
     *
     * Возвращает код выполненной команды или значение по умолчанию.
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * Deletes the route cache.
     *
     * Удаляет кеш маршрутов.
     */
    public function run(): string
    {
        try {
               $cleaner = new DirectoryCleaner();
               $cleaner->forceRemoveDir(Path::get('@storage/cache/routes'));
               if ($cleaner->getErrors()) {
                   $this->code = 1;
                   return 'ERROR:' . implode(PHP_EOL, $cleaner->getErrors()) . PHP_EOL;
               }
        } catch (\Throwable $e) {
            $this->code = 1;
            return $e->getMessage() . PHP_EOL;
        }
        // Если расширение установлено для CLI
        if (\function_exists('opcache_reset')) {
            \opcache_reset();
        }
        $this->code = 0;
        return 'The route cache has been successfully cleared!' . PHP_EOL;
    }
}
