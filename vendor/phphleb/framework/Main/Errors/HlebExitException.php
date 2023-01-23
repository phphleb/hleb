<?php

namespace Hleb\Main\Errors;

/**
 * Program exit emulation only for framework core.
 * Used for asynchronous requests.
 *
 * Эмуляция выхода из программы только для ядра фреймворка.
 * Используется для асинхронных запросов.
 *
 * @internal
 */
class HlebExitException extends \ErrorException
{

}