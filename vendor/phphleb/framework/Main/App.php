<?php

declare(strict_types=1);

namespace Hleb\Main;

use Hleb\Constructor\Handlers\MirrorRequest;
use Hleb\Main\Insert\BaseSingleton;
use Hleb\Main\Insert\Examples\ExampleApp;
use Hleb\Main\Logger\Log;
use Hleb\Scheme\Home\Constructor\Handlers\RequestInterface;
use Hleb\Scheme\Home\Main\DBInterface;
use Hleb\Scheme\Home\Main\LoggerInterface;

/** @see ExampleApp for testing */
class App extends BaseSingleton
{
    /**
     * Returns a Request object.
     *
     * Возвращает объект Request.
     *
     * @see \Hleb\Constructor\Handlers\Request;
     */
    public function request(): RequestInterface
    {
        return MirrorRequest::getInstance();
    }

    /**
     * Returns the initialized database PDO object.
     *
     * Возвращает инициализированный объект PDO базы данных.
     *
     * @param array|null $configKey
     * @return DBInterface
     * @see \Hleb\Main\MainDB;
     */
    public function db($configKey = null): DBInterface
    {
        return MirrorDB::getInstance()->instance($configKey);
    }

    /**
     * Full path to root folder '/'
     *
     * Полный путь к корневой папке '/'
     */
    public function projectPath()
    {
        return hleb_project_path();
    }

    /**
     * Full path to folder '/storage/public'
     *
     * Полный путь к папке '/storage/public'
     */
    public function storagePath()
    {
        return hleb_storage_public_path();
    }

    /**
     * Full path to folder '/public'
     *
     * Полный путь к папке '/public'
     */
    public function publicPath()
    {
        return hleb_public_path();
    }

    /**
     * Full path to folder '/view'
     *
     * Полный путь к папке '/view'
     */
    public function viewPath()
    {
        return hleb_view_path();
    }

    /**
     * Returns request data, similar to App()->request()->get()
     *
     * Возвращает данные запроса, аналогично App()->request()->get()
     *
     * @param string|null $name
     * @return array|int|float|null
     * @see \Hleb\Constructor\Routes\Data::return_data()
     */
    public function data(string $name = null)
    {
        return is_null($name) ? hleb_data() : hleb_data()[$name];
    }

    /**
     * Returns the HTML content for protection against CSRF attacks.
     *
     * Возвращает HTML-контент для вставки в форму для защиты от CSRF-атак.
     */
    public function getCsrfField()
    {
        return hleb_csrf_field();
    }

    /**
     * Returns the protected token for protection against CSRF attacks.
     *
     * Возвращает защищённый токен для защиты от CSRF-атак.
     */
    public function getCsrfToken()
    {
        return hleb_csrf_token();
    }

    /**
     * Redirect to an external site.
     *
     * Осуществляет перенаправление на сторонний сайт.
     *
     * @param string $url
     */
    public function redirectToSite($url) {
        hleb_redirect_to_site($url);
    }

    /**
     * Performs internal redirection with an option to specify the redirection code.
     *
     * Производит внутренний редирект с возможным указанием кода перенаправления.
     *
     * @param string $url
     * @param int $code
     */
    public function redirect(string $url, int $code = 303) {
        hleb_redirect($url, $code);
    }

    /**
     * Returns the specified URL address with an added token for protection against CSRF attacks.
     * To protect the route referred to by the URL address in full, one of the protect() methods shall be applied to it.
     *
     * Возвращает указанный URL-адрес c добавлением токена для защиты от CSRF-атак.
     * Для полноценной защиты маршрута, на который указывает URL-адрес, к нему должен быть применён один из методов protect().
     *
     * @param string $url
     * @return string
     */
    public function getProtectUrl(string $url){
        return hleb_get_protect_url($url);
    }

    /**
     * Converts a relative URL address to the full one.
     *
     * Преобразует относительный URL-адрес в полный.
     *
     * @param string $url
     * @return string
     */
    public function getFullUrl(string $url) {
        return hleb_get_full_url($url);
    }

    /**
     * Returns the current URL.
     *
     * Возвращает текущий URL-адрес.
     */
    public function getMainUrl() {
        return hleb_get_main_url();
    }

    /**
     * Returns the current URL without GET parameters.
     *
     * Возвращает текущий URL-адрес без GET-параметров.
     */
    public function getMainClearUrl() {
        return hleb_get_main_clear_url();
    }

    /**
     * Allows you to refer to the route address by the route name (if one has been assigned).
     *
     * Позволяет обратиться к адресу маршрута по имени маршрута (если оно было присвоено).
     *
     * @param string $name
     * @param array $args
     * @return bool|string
     */
    public function getUrlByName(string $name, array $args = []) {
        return hleb_get_by_name($name, $args);
    }

    /**
     * Converts the URL to the standard form.
     *
     * Приводит URL-адрес к стандартному виду.
     *
     * @param string $name
     * @return mixed
     */
    public function getStandardUrl(string $name) {
        return hleb_get_standard_url($name);
    }

    /**
     * In DEBUG mode, prints debug output over content.
     *
     * В DEBUG-режиме выводит отладочные данные поверх контента.
     */
    public function printR2($data, $desc = null) {
        hleb_print_r2($data, $desc);
    }

    /**
     * Allows you to include cached content from another template in the template
     * with the transfer of named parameters (variables).
     *
     * Позволяет включить в шаблон кешируемый контент из другого шаблона
     * с передачей именованных параметров (переменных).
     */
    public function includeCachedTemplate(string $template, array $params = []) {
        hleb_include_cached_template($template, $params);
    }

    /**
     * Allows you to include cached content from another template in the template
     * with the transfer of cached named parameters (variables).
     *
     * Позволяет включить в шаблон кешируемый контент из другого шаблона
     * с передачей кешируемых именованных параметров (переменных).
     */
    public function includeOwnCachedTemplate(string $template, array $params = []) {
        hleb_include_own_cached_template($template, $params);
    }

    /**
     * Allows you to include content from another template into the template with passing parameters (variables).
     *
     * Позволяет включить в шаблон контент из другого шаблона с передачей параметров (переменных).
     */
    public function insertTemplate(string $path, array $params = []) {
        hleb_insert_template($path, $params);
    }

    /**
     * Логирование по установленным уровням. App()->logger()->error('Message', []);
     *
     * Logging according to the established levels. App()->logger()->error('Message', []);
     *
     * @return Log
     */
    public function logger(): LoggerInterface
    {
        return Log::getInstance();
    }
}

