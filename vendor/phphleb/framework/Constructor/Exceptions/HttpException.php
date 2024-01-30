<?php

declare(strict_types=1);

namespace Hleb;

use Hleb\Constructor\Attributes\NotFinal;
use Hleb\Main\BaseErrorPage;
use RuntimeException;

/**
 * Allows you to display standard HTTP errors of the framework.
 * All inherited error classes will be treated as HTTP errors by the framework.
 *
 * Позволяет выводить стандартные HTTP-ошибки фреймворка.
 * Все унаследованные классы ошибок будут обработаны фреймворком как HTTP-ошибки.
 */
#[NotFinal]
abstract class HttpException extends RuntimeException implements CoreException
{
    /**
     * HTTP error code.
     *
     * HTTP-код ошибки.
     */
    protected int $httpCode;

    /**
     * HTML error code or any other text such as JSON.
     *
     * HTML-код ошибки или любой другой текст, например JSON.
     */
    protected string $messageContent;

    /** @internal */
    public function getHttpStatus(): int
    {
        return $this->httpCode;
    }

    /** @internal */
    public function getMessageContent(): string
    {
        return $this->messageContent;
    }

    /**
     * Method for displaying standard error handling by the framework.
     *
     * Метод для вывода стандартного оформления ошибки фреймворком.
     */
    protected function initException(int $httpCode, string $message): void
    {
        $this->httpCode = $httpCode;

        $this->messageContent = (new BaseErrorPage($httpCode, $message))->insert();
    }
}
