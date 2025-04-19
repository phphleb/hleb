<?php

declare(strict_types=1);

namespace Hleb\Database;

use Hleb\Constructor\Data\DynamicParams;
use Hleb\Constructor\Data\SystemSettings;
use Hleb\Main\Logger\LogLevel;
use Hleb\Static\Log;

/**
 * @internal
 */
final class DbExcessLog
{
    protected static ?string $requestId = null;

    protected static float $queryTime = 0;

    protected static bool $notificationSent = false;

    /**
     * Summarizes the time of queries to the database within one query to the script
     * and saves a message to the log when the value from the settings is exceeded.
     * Not applicable for console commands.
     *
     * Суммирует время запросов к БД в рамках одного запроса к скрипту
     * и сохраняет в лог сообщение при превышении значения из настроек.
     * Не применимо для консольных команд.
     */
    public static function set(float|int $time): void
    {
        if (SystemSettings::isCli() || SystemSettings::getCommonValue('log.db.excess') <= 0) {
            return;
        }
        $id = DynamicParams::getDynamicRequestId();
        if (!self::$requestId) {
            self::$requestId = $id;
        }
        if (self::$requestId !== $id) {
            self::$queryTime = 0;
            self::$notificationSent = false;
        }
        self::$queryTime += $time;
        $timeExcess = SystemSettings::getCommonValue('log.db.excess');
        if (!self::$notificationSent && self::$queryTime > $timeExcess) {
            Log::log(
                LogLevel::STATE, SystemDB::DB_PREFIX . ' > ' . $timeExcess . ' sec. for request-id: ' . self::$requestId . ']',
                [\Hleb\Main\Logger\Log::B7E_NAME => \Hleb\Main\Logger\Log::DB_B7E, 'tag' => '#db_total_time_exceeded']
            );
            self::$notificationSent = true;
        }
    }
}
