<?php

/*declare(strict_types=1);*/

namespace Hleb\Static;

use App\Bootstrap\BaseContainer;
use Hleb\Base\RollbackInterface;
use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\ForTestOnly;
use Hleb\CoreProcessException;
use Hleb\HttpMethods\External\Response as SystemResponse;
use Hleb\Main\Insert\BaseAsyncSingleton;
use Hleb\Reference\ResponseInterface;

#[Accessible]
final class Response extends BaseAsyncSingleton implements RollbackInterface
{
    private static ResponseInterface|null $replace = null;

    /**
     * Returns the original Response object that is used by the framework.
     *
     * Возвращает исходный объект Response, который используется фреймворком.
     */
    public static function getInstance(): ?SystemResponse
    {
        if (self::$replace) {
            return self::$replace->getInstance();
        }

        return BaseContainer::instance()->get(ResponseInterface::class)->getInstance();
    }

    /**
     * Returns the HTTP response code, default is 200.
     *
     * Возвращает HTTP-код ответа, по умолчанию равен 200.
     */
    public static function getStatus(): int
    {
        if (self::$replace) {
            return self::$replace->getStatus();
        }

        return BaseContainer::instance()->get(ResponseInterface::class)->getStatus();
    }

    /**
     * Sets the HTTP response code.
     *
     * Устанавливает HTTP-код ответа.
     *
     * @param int $status
     * @param string|null $reason - if the text of the response code differs from the standard one,
     *                               then it can be specified in this parameter.
     *
     *                             - если текст кода ответа отличается от стандартного,
     *                               то его можно указать в этом параметре.
     */
    public static function setStatus(int $status, ?string $reason = null): void
    {
        if (self::$replace) {
            self::$replace->setStatus($status, $reason);
        } else {
            BaseContainer::instance()->get(ResponseInterface::class)->setStatus($status, $reason);
        }
    }

    /**
     * Returns the set headers of the form ['name' => ['value1', 'value2']].
     *
     * Возвращает установленные заголовки вида ['название' => ['значение1', 'значение2']].
     */
    public static function getHeaders(): array
    {
        if (self::$replace) {
            return self::$replace->getHeaders();
        }

        return BaseContainer::instance()->get(ResponseInterface::class)->getHeaders();
    }

    /**
     * Sets the headers (replacing the entire set) of the form
     * ['name' => 'value'] or ['name' => ['value1', 'value2']].
     *
     * Устанавливает заголовки (полностью заменяя весь набор) вида
     * ['название' => 'значение'] или ['название' => ['значение1', 'значение2']].
     */
    public static function replaceHeaders(array $headers): void
    {
        if (self::$replace) {
            self::$replace->replaceHeaders($headers);
        } else {
            BaseContainer::instance()->get(ResponseInterface::class)->replaceHeaders($headers);
        }
    }

    /**
     * Sets a single HTTP header;
     * if $replace is negative and such a header already exists, then it does not replace.
     *
     * Устанавливает единичный HTTP-заголовок.
     * Если $replace отрицателен и такой заголовок уже существует, то не производит замену.
     */
    public static function setHeader(string $name, int|float|string $value, bool $replace = true): void
    {
        if (self::$replace) {
            self::$replace->setHeader($name, $value, $replace);
        } else {
            BaseContainer::instance()->get(ResponseInterface::class)->setHeader($name, $value, $replace);
        }
    }

    /**
     * Returns the result of checking for the existence of a header set for a Response by its name.
     * If the header was set to header(...), then it can be found using headers_list().
     *
     * Возвращает результат проверки на существование установленного для Response заголовка по его названию.
     * Если заголовок был установлен как header(...), то найти его можно при помощи headers_list().
     */
    public static function hasHeader(string $name): bool
    {
        if (self::$replace) {
            return self::$replace->hasHeader($name);
        }

        return BaseContainer::instance()->get(ResponseInterface::class)->hasHeader($name);
    }

    /**
     * Returns the value of the header by title set using the Response object.
     * If the header was set to header(...), then it can be found using headers_list().
     *
     * Возвращает значение заголовка по названию, установленного с помощью объекта Response.
     * Если заголовок был установлен как header(...), то найти его можно при помощи headers_list().
     */
    public static function getHeader(string $name): array
    {
        if (self::$replace) {
            return self::$replace->getHeader($name);
        }

        return BaseContainer::instance()->get(ResponseInterface::class)->getHeader($name);
    }

    /**
     * Adds headers to the set, at the same time replacing duplicates,
     * like [`name` => [`value1`, 'value2']], [`name` => 'value'] or ['name: value'].
     * if $replace is negative and such a header
     *
     * Добавляет заголовки к набору, вместе с этим заменяя дубликаты,
     * вида [`название` => [`значение1`, 'значение2']], [`название` => 'значение'] или ['название: значение'].
     * Если $replace отрицателен и такой заголовок уже существует, то не производит замену.
     */
    public static function addHeaders(array $headers, bool $replace = true): void
    {
        if (self::$replace) {
            self::$replace->addHeaders($headers, $replace);
        } else {
            BaseContainer::instance()->get(ResponseInterface::class)->addHeaders($headers, $replace);
        }
    }

    /**
     * Get added content.
     *
     * Получение добавленного контента.
     *
     * @see self::getBody()
     */
    public static function get(): string
    {
        if (self::$replace) {
            return self::$replace->get();
        }

        return BaseContainer::instance()->get(ResponseInterface::class)->get();
    }

    /**
     * Replaces the content completely.
     * Additionally, you can set the HTTP response status.
     *
     * Заменяет контент полностью.
     * Дополнительно можно установить HTTP-статус ответа.
     *
     * @see self::setBody()
     */
    public static function set(string|\Stringable $body, ?int $status = null): void
    {
        if (self::$replace) {
            self::$replace->set($body, $status);
        } else {
            BaseContainer::instance()->get(ResponseInterface::class)->set($body, $status);
        }
    }

    /**
     * Adds new content to the end of existing content.
     *
     * Добавляет новый контент в конец существующего.
     *
     * @see self::addToBody()
     */
    public static function add(mixed $content): void
    {
        if (self::$replace) {
            self::$replace->add($content);
        } else {
            BaseContainer::instance()->get(ResponseInterface::class)->add($content);
        }
    }

    /**
     * Get added content.
     *
     * Получение добавленного контента.
     */
    public static function getBody(): string
    {
        if (self::$replace) {
            return self::$replace->getBody();
        }

        return BaseContainer::instance()->get(ResponseInterface::class)->getBody();
    }

    /**
     * Replaces the content completely.
     *
     * Заменяет контент полностью.
     */
    public static function setBody($body): void
    {
        if (self::$replace) {
            self::$replace->setBody($body);
        } else {
            BaseContainer::instance()->get(ResponseInterface::class)->setBody($body);
        }
    }

    /**
     * Adds new content to the end of existing content.
     *
     * Добавляет новый контент в конец существующего.
     */
    public static function addToBody(mixed $content): void
    {
        if (self::$replace) {
            self::$replace->addToBody($content);
        } else {
            BaseContainer::instance()->get(ResponseInterface::class)->addToBody($content);
        }
    }

    /**
     * Clears all previously installed content.
     *
     * Очищает весь установленный ранее контент.
     */
    public static function clearBody(): void
    {
        if (self::$replace) {
            self::$replace->clearBody();
        } else {
            BaseContainer::instance()->get(ResponseInterface::class)->clearBody();
        }
    }

    /**
     * Removes the last added content by returning it.
     * In this case, the previous one after it becomes the last one.
     *
     * Удаляет последний добавленный контент возвращая его.
     * При этом предыдущий за ним становится последним.
     */
    public static function removeFromBody(): mixed
    {
        if (self::$replace) {
            return self::$replace->removeFromBody();
        }

        return BaseContainer::instance()->get(ResponseInterface::class)->removeFromBody();
    }

    /**
     * Get the version of the HTTP data transfer protocol being used.
     *
     * Получение версии используемого HTTP-протокола передачи данных.
     */
    public static function getVersion(): string
    {
        if (self::$replace) {
            return self::$replace->getVersion();
        }

        return BaseContainer::instance()->get(ResponseInterface::class)->getVersion();
    }

    /**
     * Setting the version of the HTTP data transfer protocol used.
     * Default is '1.1'.
     *
     * Установка версии используемого HTTP-протокола передачи данных.
     * По умолчанию '1.1'.
     */
    public function setVersion(string $version): void
    {
        if (self::$replace) {
            self::$replace->setVersion($version);
        } else {
            BaseContainer::instance()->get(ResponseInterface::class)->setVersion($version);
        }
    }

    /**
     * Returns optionally specified text describing the HTTP response code.
     *
     * Возвращает дополнительно заданный текст описания HTTP-кода ответа.
     */
    public static function getReason(): ?string
    {
        if (self::$replace) {
            return self::$replace->getReason();
        }

        return BaseContainer::instance()->get(ResponseInterface::class)->getReason();
    }

    /** @internal */
    public static function init(SystemResponse $response): void
    {
        if (self::$replace) {
            self::$replace::init($response);
        } else {
            BaseContainer::instance()->get(ResponseInterface::class)::init($response);
        }
    }

    /**
     * @inheritDoc
     *
     * @internal
     */
    #[\Override]
    public static function rollback(): void
    {
        if (self::$replace) {
            self::$replace::rollback();
        } else {
            BaseContainer::instance()->get(ResponseInterface::class)::rollback();
        }
    }

    /**
     * @internal
     *
     * @see ResponseForTest
     */
    #[ForTestOnly]
    public static function replaceWithMock(ResponseInterface|null $mock): void
    {
        if (\defined('HLEB_CONTAINER_MOCK_ON') && !HLEB_CONTAINER_MOCK_ON) {
            throw new CoreProcessException('The action is prohibited in the settings.');
        }
        self::$replace = $mock;
    }

}
