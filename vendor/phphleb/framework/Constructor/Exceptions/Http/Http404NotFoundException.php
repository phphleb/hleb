<?php

/*declare(strict_types=1);*/

namespace Hleb;

use Hleb\Constructor\Attributes\NotFinal;

/**
 * 404 error template.
 * The server cannot find the requested resource.
 * (!) Returns the standard error formatting by the framework,
 * to change the format you need to create your own class
 * inherited from Hleb\HttpException.
 *
 * Шаблон ошибки 404.
 * Сервер не может найти запрашиваемый ресурс.
 * (!) Возвращает стандартное оформление ошибки фреймворком,
 * для изменения формата необходимо создать собственный класс,
 * унаследованный от Hleb\HttpException.
 */
#[NotFinal]
class Http404NotFoundException extends HttpException
{
    #[NotFinal]
    public function __construct(string $message = 'Not Found')
    {
        $this->initException(404, $message);

        parent::__construct($message);
    }
}
