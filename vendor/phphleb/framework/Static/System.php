<?php

/*declare(strict_types=1);*/

namespace Hleb\Static;

use App\Bootstrap\BaseContainer;
use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\ForTestOnly;
use Hleb\CoreProcessException;
use Hleb\Database\PdoManager;
use Hleb\Main\Insert\BaseSingleton;
use Hleb\Reference\SystemInterface;

/**
 * Various system calls for use by native framework libraries.
 *
 * Различные системные вызовы для использования собственными библиотеками фреймворка.
 */
#[Accessible]
class System extends BaseSingleton
{
    private static SystemInterface|null $replace = null;

    /**
     * Returns the current name of the route, or null if it is not set.
     *
     * Возвращает текущее название маршрута или null, если оно не задано.
     */
    public static function getRouteName(): ?string
    {
        if (self::$replace) {
            return self::$replace->getRouteName();
        }

        return BaseContainer::instance()->get(SystemInterface::class)->getRouteName();
    }

    /**
     * Returns the current logging level.
     * Since the value can be taken not only from the configuration,
     * but also changed by the console command, this value is the most accurate.
     * The level is constant within a single user request.
     *
     * Возвращает актуальный уровень логирования.
     * Так как значение может браться не только из конфигурации,
     * но и изменяемо консольной командой - это значение является самым точным.
     * Уровень постоянен в рамках одного пользовательского запроса.
     */
    public static function getActualLogLevel(): string
    {
        if (self::$replace) {
            return self::$replace->getActualLogLevel();
        }

        return BaseContainer::instance()->get(SystemInterface::class)->getActualLogLevel();
    }

    /**
     * Returns a list of all available logging levels.
     *
     * Возвращает перечень всех доступных уровней логирования.
     */
    public static function getLogLevelList(): array
    {
        if (self::$replace) {
            return self::$replace->getLogLevelList();
        }

        return BaseContainer::instance()->get(SystemInterface::class)->getLogLevelList();
    }

    /**
     * System information from the cache about the current route.
     *
     * Системная информация из кеша о текущем маршруте.
     */
    public static function getRouteCacheData(): array
    {
        if (self::$replace) {
            return self::$replace->getRouteCacheData();
        }

        return BaseContainer::instance()->get(SystemInterface::class)->getRouteCacheData();
    }

    /**
     * General system information about cached routes.
     *
     * Общая системная информация о кешируемых маршрутах.
     */
    public static function getRouteCacheInfo(): array
    {
        if (self::$replace) {
            return self::$replace->getRouteCacheInfo();
        }

        return BaseContainer::instance()->get(SystemInterface::class)->getRouteCacheInfo();
    }

    /**
     * Returns the UNIX start time of the request.
     *
     * Возвращает UNIX-время старта запроса.
     */
    public static function getStartTime(): ?float
    {
        if (self::$replace) {
            return self::$replace->getStartTime();
        }

        return BaseContainer::instance()->get(SystemInterface::class)->getStartTime();
    }

    /**
     * Returns the UNIX timestamp set when the request ended.
     *
     * Возвращает установленную при завершении запроса метку UNIX-времени.
     */
    public static function getEndTime(): ?float
    {
        if (self::$replace) {
            return self::$replace->getEndTime();
        }

        return BaseContainer::instance()->get(SystemInterface::class)->getEndTime();
    }

    /**
     * Returns the UNIX timestamp set when the framework was loaded.
     *
     * Возвращает установленную при завершении загрузки фреймворка метку UNIX-времени.
     */
    public static function getCoreEndTime(): ?float
    {
        if (self::$replace) {
            return self::$replace->getCoreEndTime();
        }

        return BaseContainer::instance()->get(SystemInterface::class)->getCoreEndTime();
    }

    /**
     * Returns the current request ID, which will change
     * on asynchronous execution.
     * Outside the framework, this parameter can be associated
     * with the relevance of the current request.
     *
     * Возвращает текущий ID запроса, который будет меняться
     * при асинхронном выполнении.
     * Вне фреймворка с этим параметром можно связать
     * актуальность текущего запроса.
     */
    public static function getRequestId(): string
    {
        if (self::$replace) {
            return self::$replace->getRequestId();
        }

        return BaseContainer::instance()->get(SystemInterface::class)->getRequestId();
    }

    /**
     * Returns a key to define a request to the internal resources of the framework.
     *
     * Возвращает ключ для определения запроса к внутренним ресурсам фреймворка.
     */
    public static function getLibraryKey(): string
    {
        if (self::$replace) {
            return self::$replace->getLibraryKey();
        }

        return BaseContainer::instance()->get(SystemInterface::class)->getLibraryKey();
    }

    /**
     * In debug mode, returns system debug information.
     *
     * В режиме отладки возвращает системные отладочные данные.
     */
    public static function getDataFromDA(?string $key = null): array
    {
        if (self::$replace) {
            return self::$replace->getDataFromDA($key);
        }

        return BaseContainer::instance()->get(SystemInterface::class)->getDataFromDA($key);
    }

    /**
     * Returns information about loaded classes from debug data.
     *
     * Возвращает информацию о загруженных классах из отладочных данных.
     */
    public static function getClassesAutoloadDataFromDA(): array
    {
        if (self::$replace) {
            return self::$replace->getClassesAutoloadDataFromDA();
        }

        return BaseContainer::instance()->get(SystemInterface::class)->getClassesAutoloadDataFromDA();
    }

    /**
     * Returns information about the templates used from the debug data.
     *
     * Возвращает информацию об используемых шаблонах из отладочных данных.
     */
    public static function getInsertTemplateDataFromDA(): array
    {
        if (self::$replace) {
            return self::$replace->getInsertTemplateDataFromDA();
        }

        return BaseContainer::instance()->get(SystemInterface::class)->getInsertTemplateDataFromDA();
    }

    /**
     * Returns information from the debug data about the middleware controllers being used.
     *
     * Возвращает информацию  из отладочных данных об используемых контроллерах-посредниках.
     */
    public static function getMiddlewareDataFromDA(): array
    {
        if (self::$replace) {
            return self::$replace->getMiddlewareDataFromDA();
        }

        return BaseContainer::instance()->get(SystemInterface::class)->getMiddlewareDataFromDA();
    }

    /**
     * Returns information from debug data about the current controller.
     *
     * Возвращает информацию из отладочных данных о текущем контроллере.
     */
    public static function getInitiatorDataFromDA(): array
    {
        if (self::$replace) {
            return self::$replace->getInitiatorDataFromDA();
        }

        return BaseContainer::instance()->get(SystemInterface::class)->getInitiatorDataFromDA();
    }

    /**
     * Returns general debug information.
     *
     * Возвращает общие отладочные данные.
     */
    public static function getDebugDataFromDA(): array
    {
        if (self::$replace) {
            return self::$replace->getDebugDataFromDA();
        }

        return BaseContainer::instance()->get(SystemInterface::class)->getDebugDataFromDA();
    }

    /**
     * Returns debug data for development.
     *
     * Возвращает отладочные данные для разработки.
     */
    public static function getHlCheckDataFromDA(): array
    {
        if (self::$replace) {
            return self::$replace->getHlCheckDataFromDA();
        }

        return BaseContainer::instance()->get(SystemInterface::class)->getHlCheckDataFromDA();
    }

    /**
     * Returns debug information about database queries.
     *
     * Возвращает отладочную информацию о запросах к базе данных.
     */
    public static function getDbDebugDataFromDA(): array
    {
        if (self::$replace) {
            return self::$replace->getDbDebugDataFromDA();
        }

        return BaseContainer::instance()->get(SystemInterface::class)->getDbDebugDataFromDA();
    }

    /**
     * Returns the version of the framework
     * as it appears in the console command.
     *
     * Возвращает данные о версии фреймворка в том виде,
     * в каком они выводятся в консольной команде.
     */
    public static function getHlebVersionAsConsoleFormat(): string
    {
        if (self::$replace) {
            return self::$replace->getHlebVersionAsConsoleFormat();
        }

        return BaseContainer::instance()->get(SystemInterface::class)->getHlebVersionAsConsoleFormat();
    }

    /**
     * Returns the list of routes
     * as it appears in the console command.
     *
     * Возвращает список маршрутов в том виде,
     * в каком он выводится в консольной команде.
     */
    public static function getRoutesAsConsoleFormat(): string
    {
        if (self::$replace) {
            return self::$replace->getRoutesAsConsoleFormat();
        }

        return BaseContainer::instance()->get(SystemInterface::class)->getRoutesAsConsoleFormat();
    }

    /**
     * Returns a wrapper over PDO with default settings.
     * According to these settings, options are enabled
     * ATTR_ERRMODE and FETCH_ASSOC for PDO.
     * Connect to the database from the original configuration
     * and does not respect settings
     * project modules, so should not be used in them.
     *
     * Возвращает обёртку над PDO с дефолтными настройками.
     * Согласно этим настройкам включены параметры
     * ATTR_ERRMODE и FETCH_ASSOC для PDO.
     * Подключение к базе данных из исходной конфигурации
     * и не учитывает настройки модулей проекта, поэтому
     * не должно в них использоваться.
     */
    public static function getPdoManager(#[\SensitiveParameter] ?string $configKey = null): PdoManager
    {
        if (self::$replace) {
            return self::$replace->getPdoManager($configKey);
        }

        return BaseContainer::instance()->get(SystemInterface::class)->getPdoManager($configKey);
    }

    /**
     * Updates the route cache and returns the message
     * as it appears in the console command.
     *
     * Обновляет кеш маршрутов и возвращает сообщение в том виде,
     * в каком оно выводится в консольной команде.
     */
    public static function updateRouteCacheAsConsoleFormat(): string
    {
        if (self::$replace) {
            return self::$replace->updateRouteCacheAsConsoleFormat();
        }

        return BaseContainer::instance()->get(SystemInterface::class)->updateRouteCacheAsConsoleFormat();
    }

    /**
     * API version for system calls.
     *
     * Версия API для системных вызовов.
     */
    public static function getFrameworkApiVersion(): string
    {
        if (self::$replace) {
            return self::$replace->getFrameworkApiVersion();
        }

        return BaseContainer::instance()->get(SystemInterface::class)->getFrameworkApiVersion();
    }

    /**
     * The current version of the framework.
     *
     * Текущая версия фреймворка.
     */
    public static function getFrameworkVersion(): string
    {
        if (self::$replace) {
            return self::$replace->getFrameworkVersion();
        }

        return BaseContainer::instance()->get(SystemInterface::class)->getFrameworkVersion();
    }

    /**
     * The current version of the framework.
     *
     * Текущая версия фреймворка.
     *
     * @see self::getFrameworkVersion()
     */
    public static function getVersion(): string
    {
        if (self::$replace) {
            return self::$replace->getVersion();
        }

        return BaseContainer::instance()->get(SystemInterface::class)->getVersion();
    }

    /**
     * The current version of the framework.
     *
     * Текущая версия фреймворка.
     *
     * @see self::getFrameworkApiVersion()
     */
    public static function getApiVersion(): string
    {
        if (self::$replace) {
            return self::$replace->getApiVersion();
        }

        return BaseContainer::instance()->get(SystemInterface::class)->getApiVersion();
    }

    /**
     * Returns the URI prefix for internal framework resources.
     *
     * Возвращает префикс URI для внутренних ресурсов фреймворка.
     */
    public function getFrameworkResourcePrefix(): string
    {
        if (self::$replace) {
            return self::$replace->getFrameworkResourcePrefix();
        }

        return BaseContainer::instance()->get(SystemInterface::class)->getFrameworkResourcePrefix();
    }

    /**
     * Whether the current console is connected via the Web interface.
     *
     * Подключена ли текущая консоль через Веб-интерфейс.
     */
    public static function isWebConsoleActive(): bool
    {
        if (self::$replace) {
            return self::$replace->isWebConsoleActive();
        }

        return BaseContainer::instance()->get(SystemInterface::class)->isWebConsoleActive();
    }

    /**
     * Logging SQL queries outside of standard framework calls.
     * If database queries are used outside the framework,
     * then their execution time must be logged independently.
     *
     * Логирование SQL-запросов вне стандартных вызовов фреймворка.
     * Если используются запросы к базе данных вне фреймворка,
     * то их время выполнения нужно помещать в логи самостоятельно.
     *
     * @param float $startTime - start time of execution in fractions of a second for the request
     *                           (ends when the method is called).     *
     *                         - время начала выполнения в долях секунды для запроса
     *                           (окончание по вызову метода).
     *
     * @param string $query - the string of the SQL query placed in the log (without data substitution).
     *                      - строка помещаемого в лог SQL-запроса (без подстановки данных).
     *
     * @param string|null $configKey - the name of the key of the database used from the framework configuration.
     *                               - название ключа используемой БД из конфигурации фреймворка.
     *
     * @param string $tag - a request execution mark that can be used to identify its source.
     *                    - метка выполнения запроса, по которому можно опознать его источник.
     *
     * @param ?string $driver - driver name, such as 'mysql' or 'pgsql'.
     *                        - название драйвера, например 'mysql' или 'pgsql'.
     */
    public static function createSqlQueryLog(
        float                          $startTime,
        #[\SensitiveParameter] string  $query,
        #[\SensitiveParameter] ?string $configKey = null,
        string                         $tag = 'special',
        ?string                        $driver = null,
    ): void {
        if (self::$replace) {
            self::$replace->createSqlQueryLog($startTime, $query, $configKey, $tag, $driver);
        } else {
            BaseContainer::instance()->get(SystemInterface::class)->createSqlQueryLog($startTime, $query, $configKey, $tag, $driver);
        }
    }

    /**
     * @param string $sql - the string of the SQL query placed in the log (without data substitution).
     *                    - строка помещаемого в лог SQL-запроса (без подстановки данных).
     *
     * @param float $microtime - execution time in milliseconds.
     *                         - время выполнения в миллисекундах.
     *
     * @param array $params - additional parameters for logging.
     *                      - дополнительные параметры для вывода в лог.
     *
     * @param string|null $dbname - name of the database.
     *                            - название базы данных.
     *
     * @param string|null $driver - the database driver used, for example 'mysql'.
     *                            - используемый драйвер БД, например 'mysql'.
     * @return void
     */
    public static function createCustomLog(
        #[\SensitiveParameter] string $sql,
        float $microtime,
        array $params = [],
        ?string $dbname = null,
        ?string $driver = null,
    ): void {
        if (self::$replace) {
            self::$replace->createCustomLog($sql, $microtime, $params, $dbname, $driver);
        } else {
            BaseContainer::instance()->get(SystemInterface::class)->createCustomLog($sql, $microtime, $params, $dbname, $driver);
        }
    }

    /**
     * Checks the command to see if it can be run in different modes and returns a list of available modes.
     *
     * Проверяет команду на возможность запуска в различных режимах и возвращает список доступных режимов.
     *
     * ```php
     * use Hleb\HlebBootstrap;
     * use Hleb\Static\System;
     * use App\Commands\DefaultTask;
     *
     * $permissions = System::getTaskPermissions(DefaultTask::class);
     *
     * if (in_array(HlebBootstrap::CONSOLE_MODE, $permissions)) {
     *     // ...
     * }
     * ```
     *
     * @param string $taskClass - the class being checked.
     *                          - проверяемый класс.
     * @return array
     */
    public static function getTaskPermissions(string $taskClass): array
    {
        if (self::$replace) {
            return self::$replace->getTaskPermissions($taskClass);
        }
        return BaseContainer::instance()->get(SystemInterface::class)->getTaskPermissions($taskClass);
    }

    /**
     * @internal
     *
     * @see SystemForTest
     */
    #[ForTestOnly]
    public static function replaceWithMock(SystemInterface|null $mock): void
    {
        if (\defined('HLEB_CONTAINER_MOCK_ON') && !HLEB_CONTAINER_MOCK_ON) {
            throw new CoreProcessException('The action is prohibited in the settings.');
        }
        self::$replace = $mock;
    }
}
