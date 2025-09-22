<?php

/*declare(strict_types=1);*/

namespace Hleb;

use Hleb\Constructor\Attributes\NotFinal;

/**
 * 429 error template.
 * The user has sent too many requests in a given amount of time ("rate limiting").
 * (!) Returns the standard error formatting by the framework,
 * to change the format you need to create your own class
 * inherited from Hleb\HttpException.
 *
 * Шаблон ошибки 429.
 * Пользователь отправил слишком много запросов за определённый промежуток времени (ограничение на частоту запросов).
 * (!) Возвращает стандартное оформление ошибки фреймворком,
 * для изменения формата необходимо создать собственный класс,
 * унаследованный от Hleb\HttpException.
 */
#[NotFinal]
class Http429TooManyRequestsException extends HttpException
{
    #[NotFinal]
    public function __construct(string $message = 'Too Many Requests')
    {
        $this->initException(429, $message);

        parent::__construct($message);
    }
}
