<?php

/*declare(strict_types=1);*/

namespace Hleb;

use Hleb\Constructor\Attributes\NotFinal;

/**
 * 409 error template.
 * The request could not be completed due to a conflicting resource access.
 * (!) Returns the standard error formatting by the framework,
 * to change the format you need to create your own class
 * inherited from Hleb\HttpException.
 *
 * Шаблон ошибки 409.
 * Запрос не может быть выполнен из-за конфликтного обращения к ресурсу.
 * (!) Возвращает стандартное оформление ошибки фреймворком,
 * для изменения формата необходимо создать собственный класс,
 * унаследованный от Hleb\HttpException.
 */
#[NotFinal]
class Http409ConflictException extends HttpException
{
    #[NotFinal]
    public function __construct(string $message = 'Conflict')
    {
        $this->initException(409, $message);

        parent::__construct($message);
    }
}
