<?php

declare(strict_types=1);

namespace Hleb\Constructor\Handlers;


use Hleb\Main\Insert\BaseSingleton;
use Hleb\Scheme\Home\Constructor\Handlers\RequestInterface;

class MirrorRequest extends BaseSingleton implements RequestInterface
{
    public function getSession($name = null) {
        return Request::getSession($name);
    }
    public function getCookie($name = null) {
        return Request::getCookie($name);
    }
    public function get(string $name = '') {
        return Request::get($name);
    }
    public function getString(string $name, $default = null) {
        return Request::getString($name, $default);
    }
    public function getInt(string $name, $default = 0) {
        return Request::getInt($name, $default);
    }
    public function getFloat(string $name, $default = 0.0) {
        return Request::getFloat($name, $default);
    }
    public function getMethod() {
        return Request::getMethod();
    }
    public function getHttpProtocol() {
        return Request::getHttpProtocol();
    }
    public function getHttpFullProtocol() {
        return Request::getHttpFullProtocol();
    }
    public function getFullHost() {
        return Request::getFullHost();
    }
    public function getLang() {
        return Request::getLang();
    }
    public function getUri() {
        return Request::getUri();
    }
    public function getFullUrl() {
        return Request::getFullUrl();
    }
    public function getFullUrlAddress() {
        return Request::getFullUrlAddress();
    }
    public function getReferer() {
        return Request::getReferer();
    }
    public function getDomain() {
        return Request::getDomain();
    }
    public function getHost() {
        return Request::getHost();
    }
    public function getPort() {
        return Request::getPort();
    }
    public function getHttpHeader($value = null) {
        return Request::getHttpHeader($value);
    }
    public function isXmlHttpRequest() {
        return Request::isXmlHttpRequest();
    }
    public function getFiles() {
        return Request::getFiles();
    }
    public function getUrlParameter() {
        return Request::getUrlParameter();
    }
    public function getRemoteAddress() {
        return Request::getRemoteAddress();
    }
    public function getGet($value = null) {
        return Request::getGet();
    }
    public function getGetString(string $name, $default = null) {
        return Request::getGetString($name, $default);
    }
    public function getGetInt(string $name, $default = 0) {
        return Request::getGetInt($name, $default);
    }
    public function getGetFloat(string $name, $default = 0.0) {
        return Request::getGetFloat($name, $default);
    }
    public function getPost($value = null) {
        return Request::getPost($value);
    }
    public function getPostString(string $name, $default = null) {
        return Request::getPostString($name, $default);
    }
    public function getPostInt(string $name, $default = 0) {
        return Request::getPostInt($name, $default);
    }
    public function getPostFloat(string $name, $default = 0.0) {
        return Request::getPostFloat($name, $default);
    }
    public function getRequest($value = null) {
        return Request::getRequest($value);
    }
    public function getRequestString(string $name, $default = null) {
        return Request::getRequestString($name, $default);
    }
    public function getRequestInt(string $name, $default = 0) {
        return Request::getRequestInt($name, $default);
    }
    public function getRequestFloat(string $name, $default = 0.0) {
        return Request::getRequestFloat($name, $default);
    }
    public function returnPrivateTags(string $value) {
        return Request::returnPrivateTags($value);
    }
    public function getHead() {
        return Request::getHead();
    }
    public function getInputBody() {
        return Request::getInputBody();
    }
    public function getJsonBodyList() {
        return Request::getJsonBodyList();
    }
    public function getResources() {
        return Request::getResources();
    }
    public function getMainConvertUrl() {
        return Request::getMainConvertUrl();
    }
    public function getMainClearUrl() {
        return Request::getMainClearUrl();
    }
    public function getMainUrl() {
        return Request::getMainUrl();
    }
}


