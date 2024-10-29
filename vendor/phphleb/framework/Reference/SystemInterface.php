<?php

namespace Hleb\Reference;

use Hleb\Database\PdoManager;

interface SystemInterface
{
    /**
     * Returns the current name of the route, or null if it is not set.
     *
     * Возвращает текущее название маршрута или null, если оно не задано.
     */
    public function getRouteName(): ?string;

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
    public function getActualLogLevel(): string;

    /**
     * Returns a list of all available logging levels.
     *
     * Возвращает перечень всех доступных уровней логирования.
     */
    public function getLogLevelList(): array;

    /**
     * System information from the cache about the current route.
     *
     * Системная информация из кеша о текущем маршруте.
     */
    public function getRouteCacheData(): array;

    /**
     * General system information about cached routes.
     *
     * Общая системная информация о кешируемых маршрутах.
     */
    public function getRouteCacheInfo(): array;

    /**
     * Returns the UNIX start time of the request.
     *
     * Возвращает UNIX-время старта запроса.
     */
    public function getStartTime(): ?float;

    /**
     * Returns the UNIX timestamp set when the request ended.
     *
     * Возвращает установленную при завершении запроса метку UNIX-времени.
     */
    public function getEndTime(): ?float;

    /**
     * Returns the UNIX timestamp set when the framework was loaded.
     *
     * Возвращает установленную при завершении загрузки фреймворка метку UNIX-времени.
     */
    public function getCoreEndTime(): ?float;

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
    public function getRequestId(): string;

    /**
     * Returns a key to define a request to the internal resources of the framework.
     *
     * Возвращает ключ для определения запроса к внутренним ресурсам фреймворка.
     */
    public function getLibraryKey(): string;

    /**
     * In debug mode, returns system debug information.
     *
     * В режиме отладки возвращает системные отладочные данные.
     */
    public function getDataFromDA(?string $key = null): array;

    /**
     * Returns information about loaded classes from debug data.
     *
     * Возвращает информацию о загруженных классах из отладочных данных.
     */
    public function getClassesAutoloadDataFromDA(): array;

    /**
     * Returns information about the templates used from the debug data.
     *
     * Возвращает информацию об используемых шаблонах из отладочных данных.
     */
    public function getInsertTemplateDataFromDA(): array;

    /**
     * Returns information from the debug data about the middleware controllers being used.
     *
     * Возвращает информацию  из отладочных данных об используемых контроллерах-посредниках.
     */
    public function getMiddlewareDataFromDA(): array;

    /**
     * Returns information from debug data about the current controller.
     *
     * Возвращает информацию из отладочных данных о текущем контроллере.
     */
    public function getInitiatorDataFromDA(): array;

    /**
     * Returns general debug information.
     *
     * Возвращает общие отладочные данные.
     */
    public function getDebugDataFromDA(): array;

    /**
     * Returns debug data for development.
     *
     * Возвращает отладочные данные для разработки.
     */
    public function getHlCheckDataFromDA(): array;

    /**
     * Returns debug information about database queries.
     *
     * Возвращает отладочную информацию о запросах к базе данных.
     */
    public function getDbDebugDataFromDA(): array;

    /**
     * Returns the version of the framework
     * as it appears in the console command.
     *
     * Возвращает данные о версии фреймворка в том виде,
     * в каком они выводятся в консольной команде.
     */
    public function getHlebVersionAsConsoleFormat(): string;

    /**
     * Returns the list of routes
     * as it appears in the console command.
     *
     * Возвращает список маршрутов в том виде,
     * в каком он выводится в консольной команде.
     */
    public function getRoutesAsConsoleFormat(): string;

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
    public function getPdoManager(#[\SensitiveParameter] ?string $configKey = null): PdoManager;

    /**
     * Updates the route cache and returns the message
     * as it appears in the console command.
     *
     * Обновляет кеш маршрутов и возвращает сообщение в том виде,
     * в каком оно выводится в консольной команде.
     */
    public function updateRouteCacheAsConsoleFormat(): string;

    /**
     * API version for system calls.
     *
     * Версия API для системных вызовов.
     */
    public function getFrameworkApiVersion(): string;

    /**
     * Returns the URI prefix for internal framework resources.
     *
     * Возвращает префикс URI для внутренних ресурсов фреймворка.
     */
    public function getFrameworkResourcePrefix(): string;

    /**
     * The current version of the framework.
     *
     * Текущая версия фреймворка.
     */
    public function getFrameworkVersion(): string;

    /**
     * The current version of the framework.
     *
     * Текущая версия фреймворка.
     *
     * @see self::getFrameworkVersion()
     */
    public function getVersion(): string;

    /**
     * The current version of the framework.
     *
     * Текущая версия фреймворка.
     *
     * @see self::getFrameworkApiVersion()
     */
    public function getApiVersion(): string;

    /**
     * Whether the current console is connected via the Web interface.
     *
     * Подключена ли текущая консоль через Веб-интерфейс.
     */
    public function isWebConsoleActive(): bool;

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
    public function createSqlQueryLog(
        float   $startTime,
        string  $query,
        ?string $configKey = null,
        string  $tag = 'special',
        ?string $driver = null,
    ): void;

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
    public function createCustomLog(
        #[\SensitiveParameter] string $sql,
        float $microtime,
        array $params = [],
        ?string $dbname = null,
        ?string $driver = null,
    ): void;

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
    public static function getTaskPermissions(string $taskClass): array;
}
