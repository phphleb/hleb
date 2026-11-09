<?php

declare(strict_types=1);

/*
 * Removes expired framework cache entries and enforces its maximum size.
 *
 * Удаляет просроченные записи кеша фреймворка и контролирует его максимальный размер.
 */

namespace App\Commands;

use Hleb\Base\Task;
use Hleb\Static\Cache;

class ClearExpiredCache extends Task
{
    /** php console clear-expired-cache **/

    /**
     * Remove expired cache entries and enforce the maximum cache size.
     *
     * Удаление просроченных записей и контроль максимального размера кеша.
     */
    protected function run(): int
    {
        Cache::clearExpired();
        echo 'Expired and oversized cache has been cleared.' . PHP_EOL;

        return self::SUCCESS_CODE;
    }
}
