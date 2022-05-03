<?php
/** @see https://www.php-fig.org/psr/psr-3/ */

namespace Hleb\Scheme\Home\Main;


interface LoggerInterface
{
    /**
     * System is unusable.
     */
    /*
     * Система непригодна для использования.
     */
    public function emergency(string $message, array $context = []);

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     */
    /*
     * Действия по исправлению должны быть произведены немедленно.
     *
     * Пример: весь веб-сайт недоступен, база данных недоступна и т.д. В результате должны
     * запускаться SMS-оповещения и будить дежурного разработчика среди ночи.
     */
    public function alert(string $message, array $context = []);

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     */
    /*
     * Критическое состояние системы.
     *
     * Пример: отдельный компонент приложения недоступен, неожиданное исключение.
     */
    public function critical(string $message, array $context = []);

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     */
    /*
     * Ошибки выполнения, которые не требуют немедленных действий, но
     * должны быть зарегистрированы и контролироваться.
     */
    public function error($message, array $context = []);

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     */
    /*
     * Исключительные случаи, не являющиеся критическими ошибками, а предупреждениями.
     *
     * Пример: использование устаревших API, неправильное использование API, другие нежелательные случаи.
     * Не утверждает, что что-то выполняется неправильно.
     */
    public function warning(string $message, array $context = []);

    /**
     * Normal but significant events.
     */
    /*
     * Обычные, но важные события.
     */
    public function notice(string $message, array $context = []);

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     */
    /*
     * События, достойные внимания.
     *
     * Например: пользовательские логи, логи SQL.
     */
    public function info(string $message, array $context = []);

    /**
     * Detailed debug information.
     */
    /*
     * Детальная отладочная информация.
     */
    public function debug(string $message, array $context = []);

    /**
     * Logs with an arbitrary level.
     */
    /*
     * Логи с произвольным уровнем.
     */
    public function log($level, string $message, array $context = []);

}

