<?php


namespace Hleb\Scheme\Home\Constructor\Handlers;

interface RequestInterface
{
    public function getSession($name = null);
    public function getCookie($name = null);
    public function get(string $name = '');
    public function getString(string $name, $default = null);
    public function getInt(string $name, $default = 0);
    public function getFloat(string $name, $default = 0.0);
    public function getMethod();
    public function getHttpProtocol();
    public function getHttpFullProtocol();
    public function getFullHost();
    public function getLang();
    public function getUri();
    public function getFullUrl();
    public function getFullUrlAddress();
    public function getReferer();
    public function getDomain();
    public function getHost();
    public function getPort();
    public function getHttpHeader($value = null);
    public function isXmlHttpRequest();
    public function getFiles();
    public function getUrlParameter();
    public function getRemoteAddress();
    public function getGet($value = null);
    public function getGetString(string $name, $default = null);
    public function getGetInt(string $name, $default = 0);
    public function getGetFloat(string $name, $default = 0.0);
    public function getPost($value = null);
    public function getPostString(string $name, $default = null);
    public function getPostInt(string $name, $default = 0);
    public function getPostFloat(string $name, $default = 0.0);
    public function getRequest($value = null);
    public function getRequestString(string $name, $default = null);
    public function getRequestInt(string $name, $default = 0);
    public function getRequestFloat(string $name, $default = 0.0);
    public function returnPrivateTags(string $value);
    public function getHead();
    public function getInputBody();
    public function getJsonBodyList();
    public function getResources();
    public function getMainConvertUrl();
    public function getMainClearUrl();
    public function getMainUrl();
}


