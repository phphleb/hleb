<?php

/*declare(strict_types=1);*/

namespace Hleb;

use Hleb\Constructor\Attributes\NotFinal;

/**
 * 500 error template.
 * This response means that there was a runtime error on the server.
 * (!) Returns the standard error formatting by the framework, to change the format
 * you need to create your own class inherited from Hleb\HttpException.
 *
 * Шаблон ошибки 500.
 * Этот ответ означает, что возникла ошибка на стороне сервера.
 * (!) Возвращает стандартное оформление ошибки фреймворком,для изменения формата
 * необходимо создать собственный класс, унаследованный от Hleb\HttpException.
 */
#[NotFinal]
class Http500InternalServerErrorException extends HttpException
{
    #[NotFinal]
    public function __construct(string $message = 'Internal Server Error')
    {
        $this->initException(500, $message);

        parent::__construct($message);
    }
}
