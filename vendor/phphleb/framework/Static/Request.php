<?php

/*declare(strict_types=1);*/

namespace Hleb\Static;

use App\Bootstrap\BaseContainer;
use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\ForTestOnly;
use Hleb\CoreProcessException;
use Hleb\HttpMethods\External\RequestUri;
use Hleb\HttpMethods\Specifier\DataType;
use Hleb\Main\Insert\BaseSingleton;
use Hleb\Reference\RequestInterface;

#[Accessible]
final class Request extends BaseSingleton
{
    private static RequestInterface|null $replace = null;

    /**
     * Returns the method of the current HTTP request. For example: 'GET', 'PUT' or 'POST'.
     *
     * Возвращает метод текущего HTTP-запроса. Например: 'GET', 'PUT' или 'POST'.
     */
    public static function getMethod(): string
    {
        if (self::$replace) {
            return self::$replace->getMethod();
        }

        return BaseContainer::instance()->get(RequestInterface::class)->getMethod();
    }

    /**
     * Returns the result of checking the current method with the specific method.
     *
     * Возвращает результат сравнения текущего метода с конкретным методом.
     */
    public static function isMethod(string $name): bool
    {
        if (self::$replace) {
            return self::$replace->isMethod($name);
        }

        return BaseContainer::instance()->get(RequestInterface::class)->isMethod($name);
    }

    /**
     * Returns the value of the GET parameter given the parameter name.
     * Request::get('test')->value;   - direct value acquisition.
     * Request::get('test')->value(); - direct value acquisition.
     * Request::get('test')->asInt(); - return the value converted to integer, in the absence of null.
     * Request::get('test')->asInt($default); - return the value converted to integer,
     * if absent, $default will be returned.
     *
     * Возвращает объект со значением GET-параметра по имени параметра с возможностью выбора формата значения.
     * Request::get('test')->value;   - прямое получение значения.
     * Request::get('test')->value(); - прямое получение значения.
     * Request::get('test')->asInt(); - возвращение значения, преобразованного в integer, при отсутствии null.
     * Request::get('test')->asInt($default); - возвращение значения, преобразованного в integer,
     * при отсутствии будет возвращено $default.
     *
     * @param string|int $name - parameter to get data by name.
     *                         - параметр для получения данных по названию.
     *
     * @see DataType - all return options.
     *               - все возвращаемые варианты.
     */
    public static function get(string|int $name): DataType
    {
        if (self::$replace) {
            return self::$replace->get($name);
        }

        return BaseContainer::instance()->get(RequestInterface::class)->get($name);
    }

    /**
     * Returns $_GET parameters.
     *
     * Возвращает параметры $_GET.
     *
     * @param bool $cleared - clean data.
     *                      - производить очистку данных.
     */
    public static function allGet(bool $cleared = true): array
    {
        if (self::$replace) {
            return self::$replace->allGet($cleared);
        }

        return BaseContainer::instance()->get(RequestInterface::class)->allGet($cleared);
    }

    /**
     * Returns the value of the POST parameter given the parameter name.
     * Request::post('test')->value;   - direct value acquisition.
     * Request::post('test')->value(); - direct value acquisition.
     * Request::post('test')->asInt(); - return the value converted to integer, in the absence of null.
     * Request::post('test')->asInt($default); - return the value converted to integer,
     * if absent, $default will be returned.
     *
     * Возвращает объект со значением POST-параметра по имени параметра с возможностью выбора формата значения.
     * Request::post('test')->value;   - прямое получение значения.
     * Request::post('test')->value(); - прямое получение значения.
     * Request::post('test')->asInt(); - возвращение значения, преобразованного в integer, при отсутствии null.
     * Request::post('test')->asInt($default); - возвращение значения, преобразованного в integer,
     * при отсутствии будет возвращено $default.
     *
     * @param string|int $name - parameter to get data by name.
     *                         - параметр для получения данных по названию.
     *
     * @see DataType - all return options.
     *               - все возвращаемые варианты.
     */
    public static function post(string|int $name): DataType
    {
        if (self::$replace) {
            return self::$replace->post($name);
        }

        return BaseContainer::instance()->get(RequestInterface::class)->post($name);
    }

    /**
     * Returns $_POST parameters.
     *
     * Возвращает параметры $_POST.
     *
     * @param bool $cleared - clean data.
     *                      - производить очистку данных.
     */
    public static function allPost(bool $cleared = true): array
    {
        if (self::$replace) {
            return self::$replace->allPost($cleared);
        }

        return BaseContainer::instance()->get(RequestInterface::class)->allPost($cleared);
    }

    /**
     * Returns the generated list of data from the request body,
     * if the parameter is passed as parameters or JSON.
     * Thus, you can get a POST, PUT, PATCH or DELETE parameters,
     * a parameters from the request body in JSON format.
     * All returned data is pre-converted with HTML tags.
     *
     * Возвращает сформированный список данных из тела запроса,
     * если параметр передан в виде параметров или JSON.
     * Таким образом можно получить POST-, PUT-, PATCH- или DELETE-параметры,
     * параметры из тела запроса в формате JSON.
     * Все возвращаемые данные проходят предварительное
     * преобразование HTML-тегов.
     */
    public static function input(): array
    {
        if (self::$replace) {
            return self::$replace->input();
        }

        return BaseContainer::instance()->get(RequestInterface::class)->input();
    }

    /**
     * Returns an object with dynamic query data by parameter name with a choice of value format.
     * For example, if the `/{test}/` parameter was specified in the dynamic route,
     * and the request was in the form `/example/`, then Request::param('test')->value will return 'example'.
     * Request::param('test')->value;   - direct value acquisition.
     * Request::param('test')->value(); - direct value acquisition.
     * Request::param('test')->asInt(); - return the value converted to integer, in the absence of null.
     * Request::param('test')->asInt($default); - return the value converted to integer,
     * if absent, $default will be returned.
     * If the last part of the route is an optional variable value, then that value is null.
     *
     * Возвращает объект с данными динамического запроса по имени параметра с возможностью выбора формата значения.
     * Например, если в динамическом маршруте был указан параметр `/{test}/`,
     * а запрос был в виде `/example/`, то Request::param('test')->value вернет 'example'.
     * Request::param('test')->value;   - прямое получение значения.
     * Request::param('test')->value(); - прямое получение значения.
     * Request::param('test')->asInt(); - возвращение значения, преобразованного в integer, при отсутствии null.
     * Request::param('test')->asInt($default); - возвращение значения, преобразованного в integer,
     * при отсутствии будет возвращено $default.
     * Если последняя часть маршрута является необязательным переменным значением, то это значение будет равно null.
     *
     * @param string $name - parameter to get data by name.
     *                     - параметр для получения данных по названию.
     *
     * @return DataType - returns a DataType object.
     *                  - возвращает объект DataType.
     */
    public static function param(string $name): DataType
    {
        if (self::$replace) {
            return self::$replace->param($name);
        }

        return BaseContainer::instance()->get(RequestInterface::class)->param($name);
    }

    /**
     * Returns a named array of all incoming dynamic route parameters.
     * If the parameter is optional, then it will be null.
     *
     * Возвращает именованный массив всех входящих параметров динамического маршрута.
     * Если параметр необязательный, то он будет равен null.
     *
     * @return DataType[]
     * @see self::param() - a similar request for a specific parameter.
     *                     - аналогичный запрос по конкретному параметру.
     *
     */
    public static function data(): array
    {
        if (self::$replace) {
            return self::$replace->data();
        }

        return BaseContainer::instance()->get(RequestInterface::class)->data();
    }

    /**
     * Returns a raw array of all incoming dynamic route parameters.
     *
     * Возвращает необработанный массив всех входящих параметров динамического маршрута.
     */
    public static function rawData(): array
    {
        if (self::$replace) {
            return self::$replace->rawData();
        }

        return BaseContainer::instance()->get(RequestInterface::class)->rawData();
    }

    /**
     * Returns the request body converted to an array, for example if it is in JSON format.
     * (!) The data is returned in its original form,
     * so you need to check it for vulnerabilities yourself.
     *
     * Возвращает преобразованное в массив тело запроса, например, если оно в формате JSON.
     * (!) Данные возвращаются в исходном виде, поэтому нужно
     * самостоятельно проверить их на уязвимости.
     */
    public static function getParsedBody(): array
    {
        if (self::$replace) {
            return self::$replace->getParsedBody();
        }

        return BaseContainer::instance()->get(RequestInterface::class)->getParsedBody();
    }

    /**
     * Returns the request body in its original form.
     * Does not work with `multipart/form-data`.
     * (!) The data is returned in its original form,
     * so you need to check it for vulnerabilities yourself.
     *
     * Возвращает тело запроса в исходном виде.
     * Не работает с `multipart/form-data`.
     * (!) Данные возвращаются в исходном виде, поэтому нужно
     * самостоятельно проверить их на уязвимости.
     */
    public static function getRawBody(): string
    {
        if (self::$replace) {
            return self::$replace->getRawBody();
        }

        return BaseContainer::instance()->get(RequestInterface::class)->getRawBody();
    }

    /**
     * Returns an object containing the URL data from the request.
     *
     * Возвращает объект с содержанием данных URL из запроса.
     */
    public static function getUri(): RequestUri
    {
        if (self::$replace) {
            return self::$replace->getUri();
        }

        return BaseContainer::instance()->get(RequestInterface::class)->getUri();
    }

    /**
     * Determines if the request is sent as AJAX.
     * Some frontend libraries add this option.
     *
     * Определяет, отправлен ли запрос как AJAX.
     * Некоторые frontend-библиотеки добавляют этот параметр.
     */
    public static function isAjax(): bool
    {
        if (self::$replace) {
            return self::$replace->isAjax();
        }

        return BaseContainer::instance()->get(RequestInterface::class)->isAjax();
    }

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
    public static function getFiles(string|int|null $name = null): null|array|object
    {
        if (self::$replace) {
            return self::$replace->getFiles($name);
        }

        return BaseContainer::instance()->get(RequestInterface::class)->getFiles($name);
    }

    /**
     * Returns the result of checking the HTTP scheme of the request as 'https'.
     *
     * Возвращает результат проверки HTTP-схемы запроса как 'https'.
     */
    public static function isHttpSecure(): bool
    {
        if (self::$replace) {
            return self::$replace->isHttpSecure();
        }

        return BaseContainer::instance()->get(RequestInterface::class)->isHttpSecure();
    }

    /**
     * Get the site name from the current request URL.
     * Maybe along with the port.
     * For example `example.com` or `example.com:8080`
     *
     * Получение названия сайта из URL-адреса текущего запроса.
     * Может быть вместе с портом.
     * Например, `example.com или` `example.com:8080`
     */
    public static function getHost(): string
    {
        if (self::$replace) {
            return self::$replace->getHost();
        }

        return BaseContainer::instance()->get(RequestInterface::class)->getHost();
    }

    /**
     * Retrieving only the site name from the current request URL.
     *
     * Получение только названия сайта из URL-адреса текущего запроса.
     */
    public static function getHostName(): string
    {
        if (self::$replace) {
            return self::$replace->getHostName();
        }

        return BaseContainer::instance()->get(RequestInterface::class)->getHostName();
    }

    /**
     * Returns 'http' or 'https' from the current request.
     *
     * Возвращает 'http://' или 'https://' из текущего запроса.
     */
    public static function getHttpScheme(): string
    {
        if (self::$replace) {
            return self::$replace->getHttpScheme();
        }

        return BaseContainer::instance()->get(RequestInterface::class)->getHttpScheme();
    }

    /**
     * Returns the current host with HTTP scheme.
     *
     * Возвращает текущий хост с HTTP-схемой.
     */
    public static function getSchemeAndHost(): string
    {
        if (self::$replace) {
            return self::$replace->getSchemeAndHost();
        }

        return BaseContainer::instance()->get(RequestInterface::class)->getSchemeAndHost();
    }

    /**
     * Getting the full URL of the current request without GET parameters.
     *
     * Получение полного URL текущего запроса без GET-параметров.
     */
    public static function getAddress(): string
    {
        if (self::$replace) {
            return self::$replace->getAddress();
        }

        return BaseContainer::instance()->get(RequestInterface::class)->getAddress();
    }

    /**
     * Returns the HTTP protocol version, for example `1.1`.
     *
     * Возвращает версию HTTP-протокола, например `1.1`.
     */
    public static function getProtocolVersion(): string
    {
        if (self::$replace) {
            return self::$replace->getProtocolVersion();
        }

        return BaseContainer::instance()->get(RequestInterface::class)->getProtocolVersion();
    }

    /**
     * Returns an array with request headers.
     *
     * Возвращает массив с заголовками запроса.
     */
    public static function getHeaders(): array
    {
        if (self::$replace) {
            return self::$replace->getHeaders();
        }

        return BaseContainer::instance()->get(RequestInterface::class)->getHeaders();
    }

    /**
     * Checking for the existence of a header by name.
     *
     * Проверка существования заголовка по названию.
     */
    public static function hasHeader($name): bool
    {
        if (self::$replace) {
            return self::$replace->hasHeader($name);
        }

        return BaseContainer::instance()->get(RequestInterface::class)->hasHeader($name);
    }

    /**
     * Retrieving an array of matching headers by title.
     * For example, for `Accept-Encoding: gzip, deflate` will return:
     *
     * Получение массива соответствующих заголовков по названию.
     * Например, для `Accept-Encoding: gzip, deflate` вернёт:
     *
     * ['gzip', 'deflate']
     */
    public static function getHeader($name): array
    {
        if (self::$replace) {
            return self::$replace->getHeader($name);
        }

        return BaseContainer::instance()->get(RequestInterface::class)->getHeader($name);
    }

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
     * Content-type: text/html; charset=UTF-8
     * ```php
     * $contentType = Request::getSingleHeader('Content-Type');
     *
     * echo $contentType->asString();  // text/html
     * ```
     */
    public static function getSingleHeader($name): DataType
    {
        if (self::$replace) {
            return self::$replace->getSingleHeader($name);
        }

        return BaseContainer::instance()->get(RequestInterface::class)->getSingleHeader($name);
    }

    /**
     * Returns data from $_SERVER by name.
     *
     * Возвращает данные из $_SERVER по названию.
     */
    public static function server($name): mixed
    {
        if (self::$replace) {
            return self::$replace->server($name);
        }

        return BaseContainer::instance()->get(RequestInterface::class)->server($name);
    }

    /**
     * Returns the result of comparing the current URN (the address from the URL without parameters) with $uri.
     * The trailing slash is ignored.
     *
     * Возвращает результат сравнения текущего URN (адрес из URL без параметров) с $uri.
     * Завершающая косая черта игнорируется.
     */
    public static function isCurrent(string $uri): bool
    {
        if (self::$replace) {
            return self::$replace->isCurrent($uri);
        }

        return BaseContainer::instance()->get(RequestInterface::class)->isCurrent($uri);
    }

    /**
     * Get the corresponding headers as a string.
     * For example, for `Accept-Encoding: gzip, deflate` will return:
     *
     * Получение соответствующих названию заголовков в виде строки.
     * Например, для `Accept-Encoding: gzip, deflate` вернёт:
     *
     * 'gzip, deflate'
     */
    public static function getHeaderLine($name): string
    {
        if (self::$replace) {
            return self::$replace->getHeaderLine($name);
        }

        return BaseContainer::instance()->get(RequestInterface::class)->getHeaderLine($name);
    }

    /**
     * In exceptional cases, it is necessary to process the download stream.
     *
     * В исключительных случаях необходимо для обработки загружаемого потока.
     *
     * @see \Psr\Http\Message\StreamInterface
     */
    public static function getStreamBody(): ?object
    {
        if (self::$replace) {
            return self::$replace->getStreamBody();
        }

        return BaseContainer::instance()->get(RequestInterface::class)->getStreamBody();
    }

    /**
     * @internal
     *
     * @see RequestForTest
     */
    #[ForTestOnly]
    public static function replaceWithMock(RequestInterface|null $mock): void
    {
        if (\defined('HLEB_CONTAINER_MOCK_ON') && !HLEB_CONTAINER_MOCK_ON) {
            throw new CoreProcessException('The action is prohibited in the settings.');
        }
        self::$replace = $mock;
    }
}
