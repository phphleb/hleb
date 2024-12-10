<?php

/*declare(strict_types=1);*/

namespace Hleb\Init;

use Hleb\HttpMethods\External\SystemRequest;
use Hleb\HttpMethods\External\RequestUri;

/**
 * @internal
 */
final class AddressBar
{
    private array $config;
    private ?SystemRequest $request;
    private ?string $originUrl;
    private false|string $resultUrl;
    private ?RequestUri $uri;

    public function __construct()
    {
        $this->config = [];
        $this->request = null;
        $this->uri = null;
        $this->originUrl = null;
        $this->resultUrl = false;
    }

    /**
     * This method is needed to reuse a class object.
     *
     * Данный метод нужен для повторного использования объекта класса.
     */
    public function init(array $config, SystemRequest $request): void
    {
        $this->config = $config;
        $this->request = $request;
        $this->uri = $request->getUri();
        $this->originUrl = $this->uri->getScheme() . '://' . $this->uri->getHost() . $this->uri->getPath() . $this->uri->getQuery();
    }

    /**
     * Checking incoming data for validity.
     *
     * Проверка входящих данных на валидность.
     */
    public function check(): AddressBar
    {
        $validateUrl = $this->config['system']['url.validation'];
        $endingUrl = $this->config['system']['ending.slash.url'];
        $urlPath = $this->uri->getPath();
        $method = $this->request->getMethod();
        $methods = $this->config['system']['ending.url.methods'];

        // Test for trailing slash only for set HTTP methods.
        // Проверка на конечный слэш только для установленных HTTP-методов.
        (\in_array(\strtolower($method), $methods, true) || \in_array($method, $methods, true)) or $endingUrl = false;

        // Clean up duplicate slashes.
        // Очистка дублированных слэшей.
        $urlPath = \str_contains($urlPath, '//') ? \preg_replace('!/+!', '/', $urlPath) : $urlPath;

        // If a specific value is set, the slash at the end is removed.
        // Если установлено конкретное значение - убирается слэш в конце.
        $endingUrl === false or $urlPath = \rtrim($urlPath, '/');

        // If a slash is required, it is placed at the end of the value.
        // При обязательном слэше он проставляется в конец значения.
        ($endingUrl === 1 || $endingUrl === '1') and $urlPath .= '/';

        // Must have a slash when path is missing, but with associated parameters.
        // Должно быть наличие слэша при отсутствии path, но со связанными параметрами.
        ($this->uri->getQuery() !== '' && $urlPath === '') and $urlPath = \rtrim($urlPath, '/') . '/';

        // Amend the homepage URL to match Request.
        // Поправка к URL главной страницы для соответствия с Request.
        $urlPath === '' and $urlPath = '/';

        // Check for a regular expression, if it is specified in the settings.
        // Проверка на регулярное выражение, если оно задано в настройках.
        if ($validateUrl && !\preg_match($validateUrl, $urlPath)) {
            $this->resultUrl = $this->uri->getScheme() . '://' . $this->uri->getHost();
        } else {
            $this->resultUrl = $this->uri->getScheme() . '://' . $this->uri->getHost() . $urlPath . $this->uri->getQuery();
        }

        return $this;
    }

    /**
     * Returns the result of comparing the original address and the converted address.
     *
     * Возвращает результат сравнения оригинального адреса и преобразованного в правильный.
     */
    public function isUrlCompare(): bool
    {
        return $this->resultUrl === $this->originUrl;
    }

    /**
     * The end address from the source address after applying the rules.
     *
     * Конечный адрес из исходного после применения правил.
     */
    public function getResultUrl(): false|string
    {
        return $this->resultUrl;
    }

    /**
     * Returns the original address.
     *
     * Возвращает оригинальный адрес.
     */
    public function getOriginUrl(): string
    {
        return $this->originUrl;
    }
}
