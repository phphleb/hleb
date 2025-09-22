<?php

/*declare(strict_types=1);*/

use Hleb\Constructor\Attributes\NotFinal;

/**
 * Imitation of the exit from the script during an asynchronous request.
 * Can only be correctly used when loading the framework,
 * since outside it can be caught as an error.
 * Details in the @see async_exit() function description.
 *
 * Имитация выхода из скрипта при асинхронном запросе.
 * Может быть корректно использовано только при загрузке фреймворка,
 * так как вне его может быть перехвачено как ошибка.
 * Подробности в описании функции @see async_exit()
 */
#[NotFinal]
class AsyncExitException extends \ErrorException
{
    private int $status = 200;

    private array $headers = [];

    /**
     * Sets the HTTP status for the exception.
     *
     * Устанавливает HTTP-статус для исключения.
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * Returns the previously set HTTP status.
     *
     * Возвращает ранее установленный HTTP-статус.
     *
     * @internal
     */
    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Sets the HTTP headers for the exception.
     *
     * Устанавливает HTTP-заголовки для исключения.
     */
    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * Returns previously set HTTP headers.
     *
     * Возвращает ранее установленные HTTP-заголовки.
     *
     * @internal
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }
}
