<?php

/*declare(strict_types=1);*/

namespace Hleb;

use Hleb\Constructor\Attributes\NotFinal;

/**
 * 401 error template.
 * Authentication is required to receive the requested response.
 * The status is similar to the 403 status, but, in this case,
 * authentication is possible.
 * (!) Returns the standard error formatting by the framework,
 * to change the format you need to create your own class
 * inherited from Hleb\HttpException.
 *
 * Шаблон ошибки 401.
 * Для получения запрашиваемого ответа нужна аутентификация.
 * Статус похож на статус 403, но, в этом случае,
 * аутентификация возможна.
 * (!) Возвращает стандартное оформление ошибки фреймворком,
 * для изменения формата необходимо создать собственный класс,
 * унаследованный от Hleb\HttpException.
 */
#[NotFinal]
class Http401UnauthorizedException extends HttpException
{
    #[NotFinal]
    public function __construct(string $message = 'Unauthorized')
    {
        $this->initException(401, $message);

        parent::__construct($message);
    }
}
