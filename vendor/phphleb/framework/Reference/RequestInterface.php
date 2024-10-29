<?php

namespace Hleb\Reference;

use Hleb\HttpMethods\External\RequestUri;
use Hleb\HttpMethods\Specifier\DataType;

/**
 * For backward compatibility with custom containers,
 * this interface can only be extended.
 *
 * Для обратной совместимостью с пользовательскими контейнерами
 * этот интерфейс может только расширяться.
 */
interface RequestInterface
{
    /**
     * Returns the method of the current HTTP request. For example: 'GET', 'PUT' or 'POST'.
     *
     * Возвращает метод текущего HTTP-запроса. Например: 'GET', 'PUT' или 'POST'.
     */
    public function getMethod(): string;

    /**
     * Returns the result of checking the current method with the specific method.
     *
     * Возвращает результат проверки текущего метода с конкретным методом.
     */
    public function isMethod(string $name): bool;

    /**
     * Returns the value of the GET parameter given the parameter name.
     * $this->request()->get('test')->value;   - direct value acquisition.
     * $this->request()->get('test')->value(); - direct value acquisition.
     * $this->request()->get('test')->asInt(); - return the value converted to integer, in the absence of null.
     * $this->request()->get('test')->asInt($default); - return the value converted to integer,
     * if absent, $default will be returned.
     *
     * Возвращает объект со значением GET-параметра по имени параметра с возможностью выбора формата значения.
     * $this->request()->get('test')->value;   - прямое получение значения.
     * $this->request()->get('test')->value(); - прямое получение значения.
     * $this->request()->get('test')->asInt(); - возвращение значения, преобразованного в integer, при отсутствии null.
     * $this->request()->get('test')->asInt($default); - возвращение значения, преобразованного в integer,
     * при отсутствии будет возвращено $default.
     *
     * @param string|int $name - parameter to get data by name.
     *                         - параметр для получения данных по названию.
     *
     * @see DataType - all return options.
     *               - все возвращаемые варианты.
     */
    public function get(string|int $name): ?DataType;


    /**
     * Returns $_GET parameters.
     *
     * Возвращает параметры $_GET.
     *
     * @param bool $cleared - clean data.
     *                      - производить очистку данных.
     */
    public function allGet(bool $cleared = true): array;

    /**
     * Returns the value of the POST parameter given the parameter name.
     * $this->request()->post('test')->value;   - direct value acquisition.
     * $this->request()->post('test')->value(); - direct value acquisition.
     * $this->request()->post('test')->asInt(); - return the value converted to integer, in the absence of null.
     * $this->request()->post('test')->asInt($default); - return the value converted to integer,
     * if absent, $default will be returned.
     *
     * Возвращает объект со значением POST-параметра по имени параметра с возможностью выбора формата значения.
     * $this->request()->post('test')->value;   - прямое получение значения.
     * $this->request()->post('test')->value(); - прямое получение значения.
     * $this->request()->post('test')->asInt(); - возвращение значения, преобразованного в integer, при отсутствии null.
     * $this->request()->post('test')->asInt($default); - возвращение значения, преобразованного в integer,
     * при отсутствии будет возвращено $default.
     *
     * @param string|int $name - parameter to get data by name.
     *                         - параметр для получения данных по названию.
     *
     * @see DataType - all return options.
     *               - все возвращаемые варианты.
     */
    public function post(string|int $name): DataType;


    /**
     * Returns $_POST parameters.
     *
     * Возвращает параметры $_POST.
     *
     * @param bool $cleared - clean data.
     *                      - производить очистку данных.
     */
    public function allPost(bool $cleared = true): array;

    /**
     * Returns an object with dynamic query data by parameter name with a choice of value format.
     * For example, if the `/{test}/` parameter was specified in the dynamic route,
     * and the request was in the form `/example/`, then $this->request()->param('test')->value will return 'example'.
     * $this->request()->param('test')->value;   - direct value acquisition.
     * $this->request()->param('test')->value(); - direct value acquisition.
     * $this->request()->param('test')->asInt(); - return the value converted to integer, in the absence of null.
     * $this->request()->param('test')->asInt($default); - return the value converted to integer,
     * if absent, $default will be returned.
     * If the last part of the route is an optional variable value, then that value is null.
     *
     * Возвращает объект с данными динамического запроса по имени параметра с возможностью выбора формата значения.
     * Например, если в динамическом маршруте был указан параметр `/{test}/`,
     * а запрос был в виде `/example/`, то $this->request()->param('test')->value вернет 'example'.
     * $this->request()->param('test')->value;   - прямое получение значения.
     * $this->request()->param('test')->value(); - прямое получение значения.
     * $this->request()->param('test')->asInt(); - возвращение значения, преобразованного в integer, при отсутствии null.
     * $this->request()->param('test')->asInt($default); - возвращение значения, преобразованного в integer,
     * при отсутствии будет возвращено $default.
     * Если последняя часть маршрута является необязательным переменным значением, то это значение равно null.
     *
     * @param string $name - parameter to get data by name.
     *                     - параметр для получения данных по названию.
     *
     * @return DataType - returns a DataType object.
     *                  - возвращает объект DataType.
     */
    public function param(string $name): DataType;

    /**
     * Returns a named array of all incoming dynamic route parameters.
     * If the parameter is optional, then it will be null.
     *
     * Возвращает именованный массив всех входящих параметров динамического маршрута.
     * Если параметр необязательный, то он будет равен null.
     *
     * @see self::param() - a similar request for a specific parameter.
     *                     - аналогичный запрос по конкретному параметру.
     *
     * @return DataType[]
     */
    public function data(): array;

    /**
     * Returns the generated list of data from the request body,
     * if the parameter is passed as parameters or JSON.
     * Thus, you can get a PUT, PATCH or DELETE parameters (similar to POST),
     * a parameters from the request body in JSON format.
     * All returned data is pre-converted with HTML tags.
     *
     * Возвращает сформированный список данных из тела запроса,
     * если параметр передан в виде параметров или JSON.
     * Таким образом можно получить PUT-, PATCH- или DELETE-параметры
     * (аналогично для POST), параметры из тела запроса в формате JSON.
     * Все возвращаемые данные проходят предварительное
     * преобразование HTML-тегов.
     */
    public function input(): array;

    /**
     * Returns a raw array of all incoming dynamic route parameters.
     *
     * Возвращает необработанный массив всех входящих параметров динамического маршрута.
     *
     * @return array
     */
    public function rawData(): array;

    /**
     * Returns the request body converted to an array, for example if it is in JSON format.
     * (!) The data is returned in its original form,
     * so you need to check it for vulnerabilities yourself.
     *
     * Возвращает преобразованное в массив тело запроса, например, если оно в формате JSON.
     * (!) Данные возвращаются в исходном виде, поэтому нужно
     * самостоятельно проверить их на уязвимости.
     */
    public function getParsedBody(): array;

    /**
     * Returns an object containing the URL data from the request.
     *
     * Возвращает объект с содержанием данных URL из запроса.
     */
    public function getUri(): RequestUri;

    /**
     * Returns the request body in its original form.
     *
     * Возвращает тело запроса в исходном виде.
     */
    public function getRawBody(): string;

    /**
     * Determines if the request is sent as AJAX.
     * Some frontend libraries add an appropriate
     * identification label to X-Requested-With.
     *
     * Определяет, отправлен ли запрос как AJAX.
     * Некоторые frontend-библиотеки добавляют
     * идентификационную метку в X-Requested-With.
     */
    public function isAjax(): bool;

    /**
     * Returns an array with data for uploaded files.
     * You can request one file by name, in this case
     * an array or object will be returned,
     * otherwise NULL.
     * Returns a named array of arrays or a named
     * array of objects, depending on the input,
     * and [] if no files are found.
     *
     * Возвращает массив с данными для загруженных файлов.
     * Можно запросить один файл по названию, в этом случае
     * вернется массив или объект, при отсутствии - NULL.
     * Возвращает именованный массив массивов
     * или именованный массив объектов,
     * в зависимости от входных данных,
     * а также [], если файлы не обнаружены.
     */
    public function getFiles(string|int|null $name = null): null|array|object;

    /**
     * Returns the result of checking the HTTP scheme of the request as 'https'.
     *
     * Возвращает результат проверки HTTP-схемы запроса как 'https'.
     */
    public function isHttpSecure(): bool;

    /**
     * Get the site name from the current request URL.
     * Maybe along with the port.
     * For example `example.com` or `example.com:8080`
     *
     * Получение названия сайта из URL-адреса текущего запроса.
     * Может быть вместе с портом.
     * Например, `example.com или` `example.com:8080`
     */
    public function getHost(): string;

    /**
     * Retrieving only the site name from the current request URL.
     *
     * Получение только названия сайта из URL-адреса текущего запроса.
     */
    public function getHostName(): string;

    /**
     * Getting the full URL of the current request without GET parameters.
     *
     * Получение полного URL текущего запроса без GET-параметров.
     */
    public function getAddress(): string;

    /**
     * Returns the HTTP protocol version, for example `1.1`.
     *
     * Возвращает версию HTTP-протокола, например `1.1`.
     */
    public function getProtocolVersion(): string;

    /**
     * Returns 'http' or 'https' from the current request.
     *
     * Возвращает 'http://' или 'https://' из текущего запроса.
     */
    public function getHttpScheme(): string;

    /**
     * Returns the current host with HTTP scheme.
     *
     * Возвращает текущий хост с HTTP-схемой.
     */
    public function getSchemeAndHost(): string;

    /**
     * Returns an array with request headers.
     *
     * Возвращает массив с заголовками запроса.
     */
    public function getHeaders(): array;

    /**
     * Checking for the existence of a header by name.
     *
     * Проверка существования заголовка по названию.
     */
    public function hasHeader($name): bool;

    /**
     * Retrieving an array of matching headers by title.
     * For example, for `Accept-Encoding: gzip, deflate` will return:
     *
     * Получение массива соответствующих заголовков по названию.
     * Например, для `Accept-Encoding: gzip, deflate` вернёт:
     *
     * ['gzip', 'deflate']
     */
    public function getHeader($name): array;

    /**
     * Retrieving one (first in the enumeration) header by name.
     * Suitable if you are sure that the header has only one meaning,
     * since the order is not standardized.
     * For example, for a title with two values,
     * it will return the first:
     *
     * Получение одного (первого в перечислении) заголовка по названию.
     * Подходит, если вы уверены, что у заголовка только одно значение,
     * так как порядок их не стандартизирован.
     * Например, для заголовка с двумя значениями вернёт первый:
     *
     * Accept-Encoding: gzip, deflate
     * ```php
     * $contentType = Request::getSingleHeader('Accept-Encoding');
     *
     * echo $contentType->asString();  // gzip
     * ```
     */
    public function getSingleHeader($name): DataType;

    /**
     * Get the corresponding headers as a string.
     * For example, for `Accept-Encoding: gzip, deflate` will return:
     *
     * Получение соответствующих названию заголовков в виде строки.
     * Например, для `Accept-Encoding: gzip, deflate` вернёт:
     *
     * 'gzip, deflate'
     */
    public function getHeaderLine($name): string;

    /**
     * Returns data from $_SERVER by name.
     *
     * Возвращает данные из $_SERVER по названию.
     */
    public function server($name): mixed;

    /**
     * Returns the result of comparing the current URN (the address from the URL without parameters) with $uri.
     * The trailing slash is ignored.
     *
     * Возвращает результат сравнения текущего URN (адрес из URL без параметров) с $uri.
     * Завершающая косая черта игнорируется.
     */
    public function isCurrent(string $uri): bool;

    /**
     * In exceptional cases, it is necessary to process the download stream.
     *
     * В исключительных случаях необходимо для обработки загружаемого потока.
     *
     * @see \Psr\Http\Message\StreamInterface;
     */
    public function getStreamBody(): ?object;
}
