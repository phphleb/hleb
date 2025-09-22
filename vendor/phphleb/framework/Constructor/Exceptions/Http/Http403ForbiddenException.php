<?php

/*declare(strict_types=1);*/

namespace Hleb;

use Hleb\Constructor\Attributes\NotFinal;

/**
 * 403 error template.
 * The client does not have permission to access the content,
 * so the server refuses to give a proper response.
 * (!) Returns the standard error formatting by the framework,
 * to change the format you need to create your own class
 * inherited from Hleb\HttpException.
 *
 * Шаблон ошибки 403.
 * У клиента нет прав доступа к содержимому,
 * поэтому сервер отказывается дать надлежащий ответ.
 * (!) Возвращает стандартное оформление ошибки фреймворком,
 * для изменения формата необходимо создать собственный класс,
 * унаследованный от Hleb\HttpException.
 */
#[NotFinal]
class Http403ForbiddenException extends HttpException
{
    #[NotFinal]
    public function __construct(string $message = 'Forbidden')
    {
        $this->initException(403, $message);

        parent::__construct($message);
    }
}
