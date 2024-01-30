<?php

/** @see https://www.php-fig.org/psr/psr-3/ */

namespace Hleb\Main\Logger;

/**
 * This logging mechanism interface complies with the PSR-3 standard, but does not implement its interface.
 * An adapter is designed for use with PSR-3:
 *
 *  Данный интерфейс механизма логирования соответствует стандарту PSR-3, но не имплементирует его интерфейс.
 *  Для использования PSR-3 предназначен адаптер:
 *
 * @see LoggerAdapter
 */
interface LoggerInterface
{
    /**
     * System is unusable.
     *
     * Система непригодна для использования.
     */
    public function emergency(string|\Stringable $message, array $context = []): void;

    /**
     * Action must be taken immediately.
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * Действия по исправлению должны быть произведены немедленно.
     * Пример: весь веб-сайт недоступен, база данных недоступна и т.д. В результате должны
     * запускаться SMS-оповещения и будить дежурного разработчика среди ночи.
     */
    public function alert(string|\Stringable $message, array $context = []): void;

    /**
     * Critical conditions.
     * Example: Application component unavailable, unexpected exception.
     *
     * Критическое состояние системы.
     * Пример: отдельный компонент приложения недоступен, неожиданное исключение.
     */
    public function critical(string|\Stringable $message, array $context = []): void;

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * Ошибки выполнения, которые не требуют немедленных действий, но
     * должны быть зарегистрированы и контролироваться.
     */
    public function error(string|\Stringable $message, array $context = []): void;

    /**
     * Exceptional occurrences that are not errors.
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * Исключительные случаи, не являющиеся критическими ошибками, а предупреждениями.
     * Пример: использование устаревших API, неправильное использование API,
     * другие нежелательные случаи.
     * Не утверждает, что что-то выполняется неправильно.
     */
    public function warning(string|\Stringable $message, array $context = []): void;

    /**
     * Normal but significant events.
     *
     * Обычные, но важные события.
     */
    public function notice(string|\Stringable $message, array $context = []): void;

    /**
     * Interesting events.
     * Example: User logs in, SQL logs.
     *
     * События, достойные внимания.
     * Например: пользовательские логи, логи SQL.
     */
    public function info(string|\Stringable $message, array $context = []): void;

    /**
     * Detailed debug information.
     *
     * Детальная отладочная информация.
     */
    public function debug(string|\Stringable $message, array $context = []): void;

    /**
     * Logs with an arbitrary level.
     *
     * Логи с произвольным уровнем.
     */
    public function log(mixed $level, string|\Stringable $message, array $context = []): void;

}
