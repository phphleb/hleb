<?php

declare(strict_types=1);

if (!function_exists('view')) {
    /**
     * The view( ... ) function enables to specify a content template in the get( ... ) function of the router or when returning from the controller.
     *
     * Функция view( ... ) позволяет назначить шаблон контента в функции get( ... ) маршрутизатора или при возвращении из контроллера.
     *
     * @param mixed $to
     * @param mixed $data
     * @return array
     */
    function view($to, $data = null) {
        return hleb_view($to, $data);
    }
}

if (!function_exists('render')) {
    /**
     * The render( ... ) function is used as an equivalent of view( ... ) for a page designer, referring to the name of a set of templates.
     *
     * Функция render( ... ) используется как аналог view( ... ) для конструктора страниц, указывая на название для комплекта шаблонов.
     *
     * @param mixed $name
     * @param array|string|null $data
     * @return array
     */
    function render($name, $data = null) {
        return hleb_render($name, $data);
    }
}

if (!function_exists('data')) {
    /**
     * The data() function returns the $data parameters from the view( ..., $data ) function into the content.
     *
     * Функция data() возвращает в шаблон контента параметры $data из функции view( ..., $data ).
     */
    function data() {
        return hleb_data();
    }
}

if (!function_exists('csrf_field')) {
    /**
     * The csrf_field() function displays the HTML content for protection against CSRF attacks.
     *
     * Функция csrf_field() отображает HTML-контент для вставки в форму для защиты от CSRF-атак.
     */
    function csrf_field() {
        echo hleb_csrf_field();
    }
}


if (!function_exists('csrf_token')) {
    /**
     * The csrf_token() function returns the protected token for protection against CSRF attacks.
     *
     * Функция csrf_token() возвращает защищённый токен для защиты от CSRF-атак.
     */
    function csrf_token() {
        return hleb_csrf_token();
    }
}


if (!function_exists('redirectToSite')) {
    /**
     * The redirectToSite( ... ) function redirects to an external site.
     *
     * Функция redirectToSite( ... ) осуществляет перенаправление на сторонний сайт.
     *
     * @param string $url
     */
    function redirectToSite($url) {
        hleb_redirect_to_site($url);
    }
}

if (!function_exists('redirect')) {
    /**
     * The redirect( ... ) function performs internal redirection with an option to specify the redirection code.
     *
     * Функция redirect( ... ) производит внутренний редирект с возможным указанием кода перенаправления.
     *
     * @param string $url
     * @param int $code
     */
    function redirect(string $url, int $code = 303) {
        hleb_redirect($url, $code);
    }
}

if (!function_exists('getProtectUrl')) {
    /**
     * The getProtectUrl( ... ) function returns the specified URL address with an added token for protection against CSRF attacks.
     * To protect the route referred to by the URL address in full, one of the protect() methods shall be applied to it.
     *
     * Функция getProtectUrl( ... ) возвращает указанный URL-адрес c добавлением токена для защиты от CSRF-атак.
     * Для полноценной защиты маршрута, на который указывает URL-адрес, к нему должен быть применён один из методов protect().
     *
     * @param string $url
     * @return string
     */
    function getProtectUrl(string $url) {
        return hleb_get_protect_url($url);
    }
}


if (!function_exists('getFullUrl')) {
    /**
     * The getFullUrl( ... ) function converts a relative URL address to the full one.
     *
     * Функция getFullUrl( ... ) преобразует относительный URL-адрес в полный.
     *
     * @param string $url
     * @return string
     */
    function getFullUrl(string $url) {
        return hleb_get_full_url($url);
    }
}

if (!function_exists('getMainUrl')) {
    /**
     * Using the getMainUrl() function, the current URL address can be obtained.
     *
     * При помощи функции getMainUrl() можно получить текущий URL-адрес.
     */
    function getMainUrl() {
        return hleb_get_main_url();
    }
}

if (!function_exists('getMainClearUrl')) {
    /**
     * The getMainClearUrl() function returns the current URL address without GET parameters.
     *
     * Функция getMainClearUrl() возвращает текущий URL-адрес без GET-параметров.
     */
    function getMainClearUrl() {
        return hleb_get_main_clear_url();
    }
}

if (!function_exists('getUrlByName')) {
    /**
     * The getByName( ... ) function enables to access the route address by the name of the route (if it was assigned).
     *
     * Функция getByName( ... ) позволяет обратиться к адресу маршрута по имени маршрута (если оно было присвоено).
     *
     * @param string $name
     * @param array $args
     * @return bool|string
     */
    function getUrlByName($name, $args = []) {
        return hleb_get_by_name($name, $args);
    }
}

if (!function_exists('getStandardUrl')) {
    /**
     * The getStandardUrl() function converts the URL address to its conventional form.
     *
     * Функция getStandardUrl() приводит URL-адрес к стандартному виду.
     *
     * @param string $name
     * @return mixed
     */
    function getStandardUrl(string $name) {
        return hleb_get_standard_url($name);
    }
}

if (!function_exists('print_r2')) {
    /**
     * The print_r2( ... ) function in DEBUG mode outputs the debugging data on top of the content.
     *
     * Функция print_r2( ... ) в DEBUG-режиме выводит отладочные данные поверх контента.
     *
     * @param mixed $data
     * @param string|null $desc
     */
    function print_r2($data, $desc = null) {
        hleb_print_r2($data, $desc);
    }
}

if (!function_exists('includeTemplate')) {
    /**
     * The includeTemplate( ... ) function enables to include the content of another template into the template and transfer parameters (variables).
     *
     * Функция includeTemplate( ... ) позволяет включить в шаблон контент из другого шаблона с передачей параметров (переменных).
     *
     * @param string $template
     * @param array $params
     * @return false|string|null
     */
    function includeTemplate(string $template, array $params = []) {
        return hleb_include_template($template, $params);
    }
}

    if (!function_exists('includeCachedTemplate')) {
    /**
     * The includeCachedTemplate( ... ) function enables to include the cashed content of another template into the template
     * and transfer parameters (variables).
     *
     * Функция includeCachedTemplate( ... ) позволяет включить в шаблон кешируемый контент из другого шаблона
     * с передачей параметров (переменных).
     *
     * @param string $template
     * @param array $params
     */
    function includeCachedTemplate(string $template, array $params = []) {
        hleb_include_cached_template($template, $params);
    }
}

if (!function_exists('includeOwnCachedTemplate')) {
    /**
     * The includeOwnCachedTemplate( ... ) function enables to include the cashed content of another template
     * into the template and transfer cashed parameters (variables).
     *
     * Функция includeOwnCachedTemplate( ... ) позволяет включить в шаблон кешируемый контент из другого шаблона
     * с передачей кешируемых параметров (переменных).
     *
     * @param string $template
     * @param array $params
     */
    function includeOwnCachedTemplate(string $template, array $params = []) {
        hleb_include_own_cached_template($template, $params);
    }
}


if (!function_exists('getRequestResources')) {
    /**
     * The getRequestResources() function enables to get style data for output of them on the page (in its lower part).
     *
     * Функция getRequestResources() для получения данных стилей для вывода на странице (в нижней её части).
     *
     * @see ExampleResources for testing.
     */
    function getRequestResources() {
        return hleb_get_request_resources();
    }
}

if (!function_exists('getRequestHead')) {
    /**
     * The <head><?php getRequestHead()->output(); ?></head> displays all previous set Request::getHead() data.
     *
     * <head><?php getRequestHead()->output(); ?></head> отображает все предыдущие установленные данные Request::getHead().
     *
     * @see ExampleHead for testing.
     */
    function getRequestHead() {
        return hleb_request_head();
    }
}

if (!function_exists('getRequest')) {
    /**
     * The getRequest( ... ) function outputs the class Request.
     *
     *  Через функцию getRequest( ... ) можно обращаться к классу Request. Например, getRequest()::getGet();
     *
     * @see ExampleMirrorRequest for testing.
     */
    function getRequest() {
        return hleb_get_request();
    }
}

if (!function_exists('storage_path')) {
    /**
     * Full path to folder '/storage/public'
     *
     * Полный путь к папке '/storage/public'
     */
    function storage_path() {
        return hleb_storage_public_path();
    }
}


if (!function_exists('public_path')) {
    /**
     * Full path to folder '/public'
     *
     * Полный путь к папке '/public'
     */
    function public_path() {
        return hleb_public_path();
    }
}

if (!function_exists('view_path')) {
    /**
     * Full path to folder '/view'
     *
     * Полный путь к папке '/view'
     */
    function view_path() {
        return hleb_view_path();
    }
}

if (!function_exists('project_path')) {
    /**
     * Full path to root folder '/'
     *
     * Полный путь к корневой папке '/'
     */
    function project_path() {
        return hleb_project_path();
    }
}

if (!function_exists('storagePath')) {
    /**
     * @see storage_path
     */
    function storagePath() {
        return hleb_storage_public_path();
    }
}


if (!function_exists('publicPath')) {
    /**
     * @see public_path()
     */
    function publicPath() {
        return hleb_public_path();
    }
}

if (!function_exists('viewPath')) {
    /**
     * @see view_path()
     */
    function viewPath() {
        return hleb_view_path();
    }
}

if (!function_exists('projectPath')) {
    /**
     * @see project_path()
     */
    function projectPath() {
        return hleb_project_path();
    }
}

if (!function_exists('insertTemplate')) {
    /**
     * The insertTemplate( ... ) function enables to include the content of another template into the template and transfer parameters (variables).
     *
     * Функция insertTemplate( ... ) позволяет включить в шаблон контент из другого шаблона с передачей параметров (переменных).
     *
     * @param string $path
     * @param array $params
     */
    function insertTemplate(string $path, array $params = []) {
        hleb_insert_template($path, $params);
    }
}

if (!function_exists('App')) {
    /**
     * Management from one place all the functions of the framework, for example App()->request()->getHead()->setTitle('Title');
     *
     * Управление из одного места всеми функциями фреймворка, например App()->request()->getHead()->setTitle('Title');
     *
     * @return Hleb\Main\App
     *
     * @see ExampleApp for testing.
     */
    function App() {
        return Hleb\Main\App::getInstance();
    }
}

