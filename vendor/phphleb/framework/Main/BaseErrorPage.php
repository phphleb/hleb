<?php

declare(strict_types=1);

namespace Hleb\Main;

use App\Bootstrap\Http\ErrorContent;
use Hleb\Constructor\Data\DynamicParams;
use Hleb\ParseException;
use Hleb\Static\Container;
use Hleb\Static\Response;

/**
 * @internal
 */
final readonly class BaseErrorPage
{
    private ErrorContent $target;

    public function __construct(private int $code = 404, private string $text = 'Not Found')
    {
        $container = Container::getContainer();

        $this->target = new ErrorContent($code, $this->text, $container);
    }

    /**
     * Returns the contents of the default error page.
     *
     * Возвращает содержимое дефолтной страницы ошибки.
     */
    public function insert(bool $onlyText = false): string
    {
        return match (DynamicParams::getRequest()->getMethod()) {
            'GET' => $this->get($onlyText),
            'OPTIONS' => $this->options($onlyText),
            default => $this->other($onlyText),
        };
    }

    /**
     * Формирует ошибку 404.
     *
     * Generates a 404 error.
     */
    public function insertInResponse(): void
    {
        Response::setBody($this->insert());
    }

    public function other(bool $onlyText = false): string
    {
        if (!$onlyText) {
            Response::addHeaders(['Content-Type' => 'application/json; charset=utf-8']);
            Response::setStatus($this->code);
        }
        try {
            return $this->target->other();
        } catch (\JsonException $e) {
            throw new ParseException((string)$e);
        }
    }

    public function options(bool $onlyText = false): string
    {
        if (!$onlyText) {
            Response::addHeaders(['Allow' => 'OPTIONS']);
            Response::setStatus(200);
        }

        return '';
    }

    public function get(bool $onlyText = false): string
    {
        if (!$onlyText) {
            Response::setStatus($this->code);
            Response::addHeaders(['Content-Type' => 'text/html; charset=utf-8']);
        }

        return $this->target->get();
    }
}
