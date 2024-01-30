<?php

/*declare(strict_types=1);*/

namespace Hleb\Main;

use Hleb\Http401UnauthorizedException;
use Hleb\Http403ForbiddenException;
use Hleb\Http404NotFoundException;

/**
 * Allows you to display frequently used HTTP error statuses in the form of a designed page.
 *
 * Позволяет выводить часто используемые статусы HTTP-ошибок в виде оформленной страницы.
 *
 * @internal
 */
final readonly class ErrorTemplates
{
    public function __construct(private string|int $template)
    {
    }

    public function searchAndThrowError(): void
    {
        $error = match ($this->template) {
            '404', 404 => new Http404NotFoundException(),
            '403', 403 => new Http403ForbiddenException(),
            '401', 401 => new Http401UnauthorizedException(),
            default => null,
        };
        if ($error) {
            throw $error;
        }
    }
}
