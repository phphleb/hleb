<?php

declare(strict_types=1);

namespace Phphleb\Debugpan\Controllers;

use Hleb\Static\Response;

trait ResponseTrait
{
    /**
     * Convert to JSON.
     *
     * Преобразование в JSON.
     */
    private function toJson(array $data): string
    {
        Response::addHeaders(['Content-Type' => 'application/json']);
        try {
            $result = \json_encode($data, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            $result = \json_encode([
                'status' => 'error',
                'content' => null,
                'error' => [
                    'message' => $e->getMessage(),
                    'code' => 500,
                ],
            ]);
        }
        return $result;
    }

    /**
     * Converting a successful response to standard form.
     *
     * Преобразование успешного ответа в стандартный вид.
     */
    protected function getSuccessfulResponse(array $data): string
    {
        return $this->toJson([
            'status' => 'ok',
            'content' => $data,
            'error' => null,
        ]);
    }

    /**
     * Converting an error response to standard form.
     *
     * Преобразование ответа с ошибкой в стандартный вид.
     */
    protected function getErrorResponse($errorMessage = 'Runtime error', $errorCode = 500): string
    {
        return $this->toJson([
            'status' => 'error',
            'content' => null,
            'error' => [
                'message' => $errorMessage,
                'code' => $errorCode,
            ],
        ]);
    }
}