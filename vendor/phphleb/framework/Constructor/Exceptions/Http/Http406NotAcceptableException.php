<?php

/*declare(strict_types=1);*/

namespace Hleb;

use Hleb\Constructor\Attributes\NotFinal;

/**
 * 406 error template.
 * The requested URI cannot satisfy the characteristics passed in the header.
 * (!) Returns the standard error formatting by the framework,
 * to change the format you need to create your own class
 * inherited from Hleb\HttpException.
 *
 * Шаблон ошибки 406.
 * запрошенный URI не может удовлетворить переданным в заголовке характеристикам.
 * (!) Возвращает стандартное оформление ошибки фреймворком,
 * для изменения формата необходимо создать собственный класс,
 * унаследованный от Hleb\HttpException.
 */
#[NotFinal]
class Http406NotAcceptableException extends HttpException
{
    #[NotFinal]
    public function __construct(string $message = 'Not Acceptable')
    {
        $this->initException(406, $message);

        parent::__construct($message);
    }
}
