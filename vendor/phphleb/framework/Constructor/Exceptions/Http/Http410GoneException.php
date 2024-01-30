<?php

/*declare(strict_types=1);*/

namespace Hleb;

use Hleb\Constructor\Attributes\NotFinal;

/**
 * 410 error template.
 * The server sends this response if the resource used to be at the specified URL,
 * but was deleted and is now unavailable.
 * (!) Returns the standard error formatting by the framework,
 * to change the format you need to create your own class
 * inherited from Hleb\HttpException.
 *
 * Шаблон ошибки 410.
 * Такой ответ сервер посылает, если ресурс раньше был по указанному URL,
 * но был удалён и теперь недоступен.
 * (!) Возвращает стандартное оформление ошибки фреймворком,
 * для изменения формата необходимо создать собственный класс,
 * унаследованный от Hleb\HttpException.
 */
#[NotFinal]
class Http410GoneException extends HttpException
{
    #[NotFinal]
    public function __construct(string $message = 'Gone')
    {
        $this->initException(410, $message);

        parent::__construct($message);
    }
}
