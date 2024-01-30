<?php

namespace Hleb\Reference;

use Hleb\HttpMethods\External\Response as SystemResponse;

/**
 * For backward compatibility with custom containers,
 * this interface can only be extended.
 *
 * Для обратной совместимостью с пользовательскими контейнерами
 * этот интерфейс может только расширяться.
 */
interface ResponseInterface
{
    /**
     * Returns the original Response object that is used by the framework.
     *
     * Возвращает исходный объект Response, который используется фреймворком.
     */
    public function getInstance(): ?SystemResponse;

    /**
     * Returns the HTTP response code, default is 200.
     *
     * Возвращает HTTP-код ответа, по умолчанию равен 200.
     */
    public function getStatus(): int;

    /**
     * Sets the HTTP response code.
     *
     * Устанавливает HTTP-код ответа.
     *
     * @param int $status
     * @param string|null $reason - if the text of the response code differs from the standard one,
     *                              then it can be specified in this parameter.
     *
     *                             - если текст кода ответа отличается от стандартного,
     *                               то его можно указать в этом параметре.
     */
    public function setStatus(int $status, ?string $reason = null): void;

    /**
     * Returns the set headers of the form ['name' => ['value1', 'value2']].
     *
     * Возвращает установленные заголовки вида ['название' => ['значение1', 'значение2']].
     */
    public function getHeaders(): array;

    /**
     * Sets the headers (replacing the entire set) of the form
     * ['name' => 'value'] or ['name' => ['value1', 'value2']].
     *
     * Устанавливает заголовки (полностью заменяя весь набор) вида
     * ['название' => 'значение'] или ['название' => ['значение1', 'значение2']].
     */
    public function replaceHeaders(array $headers): void;

    /**
     * Sets a single HTTP header;
     * if $replace is negative and such a header already exists, then it does not replace.
     *
     * Устанавливает единичный HTTP-заголовок.
     * Если $replace отрицателен и такой заголовок уже существует, то не производит замену.
     */
    public function setHeader(string $name, int|float|string $value, bool $replace = true): void;

    /**
     * Returns the result of checking for the existence of a header set for a Response by its name.
     * If the header was set to header(...), then it can be found using headers_list().
     *
     * Возвращает результат проверки на существование установленного для Response заголовка по его названию.
     * Если заголовок был установлен как header(...), то найти его можно при помощи headers_list().
     */
    public function hasHeader(string $name): bool;

    /**
     * Returns the value of the header by title set using the Response object.
     * If the header was set to header(...), then it can be found using headers_list().
     *
     * Возвращает значение заголовка по названию, установленного с помощью объекта Response.
     * Если заголовок был установлен как header(...), то найти его можно при помощи headers_list().
     */
    public function getHeader(string $name): array;

    /**
     * Adds headers to the set, at the same time replacing duplicates,
     * like [`name` => [`value1`, 'value2']], [`name` => 'value'] or ['name: value'].
     * if $replace is negative and such a header
     *
     * Добавляет заголовки к набору, вместе с этим заменяя дубликаты,
     * вида [`название` => [`значение1`, 'значение2']], [`название` => 'значение'] или ['название: значение'].
     * Если $replace отрицателен и такой заголовок уже существует, то не производит замену.
     */
    public function addHeaders(array $headers, bool $replace = true): void;

    /**
     * Get added content.
     *
     * Получение добавленного контента.
     */
    public function get(): string;

    /**
     * Replaces the content completely.
     * Additionally, you can set the HTTP response status.
     *
     * Заменяет контент полностью.
     * Дополнительно можно установить HTTP-статус ответа.
     */
    public function set(string|\Stringable $body, ?int $status = null): void;

    /**
     * Adds new content to the end of existing content.
     *
     * Добавляет новый контент в конец существующего.
     */
    public function add(mixed $content): void;

    /**
     * Get added content.
     *
     * Получение добавленного контента.
     */
    public function getBody(): string;

    /**
     * Replaces the content completely.
     *
     * Заменяет контент полностью.
     */
    public function setBody($body): void;

    /**
     * Adds new content to the end of existing content.
     *
     * Добавляет новый контент в конец существующего.
     */
    public function addToBody(mixed $content): void;

    /**
     * Clears all previously installed content.
     *
     * Очищает весь установленный ранее контент.
     */
    public function clearBody(): void;

    /**
     * Removes the last added content by returning it.
     * In this case, the previous one after it becomes the last one.
     *
     * Удаляет последний добавленный контент возвращая его.
     * При этом предыдущий за ним становится последним.
     */
    public function removeFromBody(): mixed;

    /**
     * Get the version of the HTTP data transfer protocol being used.
     *
     * Получение версии используемого HTTP-протокола передачи данных.
     */
    public function getVersion(): string;

    /**
     * Setting the version of the HTTP data transfer protocol used.
     * Default is '1.1'.
     *
     * Установка версии используемого HTTP-протокола передачи данных.
     * По умолчанию '1.1'.
     */
    public function setVersion(string $version): void;

    /**
     * Returns optionally specified text describing the HTTP response code.
     *
     * Возвращает дополнительно заданный текст описания HTTP-кода ответа.
     */
    public function getReason(): ?string;


    /** @internal */
    public static function init(SystemResponse $response): void;

    /**
     * Used if you need to rollback data
     * for an asynchronous request.
     *
     * Используется, если необходимо откатить
     * данные для асинхронного запроса.
     */
    public static function rollback(): void;
}
