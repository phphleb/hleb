<?php

declare(strict_types=1);

namespace App\Bootstrap\Http;

use Hleb\Constructor\Attributes\Dependency;
use \App\Bootstrap\ContainerInterface;

/**
 * Implements content management of returned HTTP errors.
 *
 * Реализует управление контентом возвращаемых HTTP-ошибок.
 */
#[Dependency]
readonly final class ErrorContent
{
    /**
     * @param int $httpCode - error code, for example 404.
     *                      - код ошибки, например 404.
     *
     * @param string $message - error message, for example 'Not Found'.
     *                        - сообщение об ошибке, например 'Not Found'.
     */
    public function __construct(
        private int                $httpCode,
        private string             $message,
        private ContainerInterface $container,
    )
    {
    }

    /**
     * Returns the content for the GET method.
     *
     * Возвращает контент для метода 'GET'.
     */
    public function get(): string
    {
        return template('error', [
            'httpCode' => $this->httpCode,
            'message' => $this->message,
            'apiVersion' => $this->container->system()->getFrameworkApiVersion(),
            'uriPrefix' => $this->container->system()->getFrameworkResourcePrefix(),
        ]);
    }

    /**
     * Returns content for 'POST', 'PUT', 'PATCH', 'DELETE' methods.
     *
     * Возвращает контент для 'POST', 'PUT', 'PATCH', 'DELETE' методов.
     *
     * @throws \JsonException
     */
    public function other(): string
    {
        return (string)\json_encode([
            'error' => [
                'code' => $this->httpCode,
                'message' => $this->message
            ]
        ], JSON_THROW_ON_ERROR);
    }

}
