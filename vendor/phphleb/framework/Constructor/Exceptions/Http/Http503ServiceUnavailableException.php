<?php

/*declare(strict_types=1);*/

namespace Hleb;

use Hleb\Constructor\Attributes\NotFinal;

/**
 * 503 error template.
 * The server is not ready to handle the request. Common causes are
 * a server that is down for maintenance or that is overloaded.
 * (!) Returns the standard error formatting by the framework,
 * to change the format you need to create your own class
 * inherited from Hleb\HttpException.
 *
 * Шаблон ошибки 503.
 * Сервер не готов обработать запрос. Распространённые причины — сервер на обслуживании или перегружен.
 * (!) Возвращает стандартное оформление ошибки фреймворком,
 * для изменения формата необходимо создать собственный класс,
 * унаследованный от Hleb\HttpException.
 */
#[NotFinal]
class Http503ServiceUnavailableException extends HttpException
{
    #[NotFinal]
    public function __construct(string $message = 'Service Unavailable')
    {
        $this->initException(503, $message);

        parent::__construct($message);
    }
}
