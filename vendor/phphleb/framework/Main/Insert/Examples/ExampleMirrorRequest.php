<?php

declare(strict_types=1);

namespace Hleb\Main\Insert\Examples;

use Hleb\Scheme\Home\Constructor\Handlers\RequestInterface;

/**
 * @see ExampleApp
 */
class ExampleMirrorRequest implements RequestInterface
{
    private $request = [];

    /**
     * @param array $list -  Adding test values to be returned in methods.
     *
     *                    -  Добавление тестовых значений, которые будут возвращены в методах.
     */
    public function __construct(array $list)
    {
        $this->request = $list;
    }

    // Call stub.
    public static function getInstance()
    {
        return new self;
    }

    public function getSession($name = null)
    {
        return $this->request['getSession'];
    }

    public function getCookie($name = null)
    {
        return $this->request['getCookie'];
    }

    public function get(string $name = '')
    {
        return $this->request['get'];
    }

    public function getString(string $name, $default = null)
    {
        return $this->request['getString'];
    }

    public function getInt(string $name, $default = 0)
    {
        return $this->request['getInt'];
    }

    public function getFloat(string $name, $default = 0.0)
    {
        return $this->request['getFloat'];
    }

    public function getMethod()
    {
        return $this->request['getMethod'];
    }

    public function getHttpProtocol()
    {
        return $this->request['getHttpProtocol'];
    }

    public function getHttpFullProtocol()
    {
        return $this->request['getHttpFullProtocol'];
    }

    public function getFullHost()
    {
        return $this->request['getFullHost'];
    }

    public function getLang()
    {
        return $this->request['getLang'];
    }

    public function getUri()
    {
        return $this->request['getUri'];
    }

    public function getFullUrl()
    {
        return $this->request['getFullUrl'];
    }

    public function getFullUrlAddress()
    {
        return $this->request['getFullUrlAddress'];
    }

    public function getReferer()
    {
        return $this->request['getReferer'];
    }

    public function getDomain()
    {
        return $this->request['getDomain'];
    }

    public function getHost()
    {
        return $this->request['getHost'];
    }

    public function getPort()
    {
        return $this->request['getPort'];
    }

    public function getHttpHeader($value = null)
    {
        return $this->request['getHttpHeader'];
    }

    public function isXmlHttpRequest()
    {
        return $this->request['isXmlHttpRequest'];
    }

    public function getFiles()
    {
        return $this->request['getFiles'];
    }

    public function getUrlParameter()
    {
        return $this->request['getUrlParameter'];
    }

    public function getRemoteAddress()
    {
        return $this->request['getRemoteAddress'];
    }

    public function getGet($value = null)
    {
        return $this->request['getGet'];
    }

    public function getGetString(string $name, $default = null)
    {
        return $this->request['getGetString'];
    }

    public function getGetInt(string $name, $default = 0)
    {
        return $this->request['getGetInt'];
    }

    public function getGetFloat(string $name, $default = 0.0)
    {
        return $this->request['getGetFloat'];
    }

    public function getPost($value = null)
    {
        return $this->request['getPost'];
    }

    public function getPostString(string $name, $default = null)
    {
        return $this->request['getPostString'];
    }

    public function getPostInt(string $name, $default = 0)
    {
        return $this->request['getPostInt'];
    }

    public function getPostFloat(string $name, $default = 0.0)
    {
        return $this->request['getPostFloat'];
    }

    public function getRequest($value = null)
    {
        return $this->request['getRequest'];
    }

    public function getRequestString(string $name, $default = null)
    {
        return $this->request['getRequestString'];
    }

    public function getRequestInt(string $name, $default = 0)
    {
        return $this->request['getRequestInt'];
    }

    public function getRequestFloat(string $name, $default = 0.0)
    {
        return $this->request['getRequestFloat'];
    }

    public function returnPrivateTags(string $value)
    {
        return $this->request['returnPrivateTags'];
    }

    public function getHead()
    {
        return $this->request['getHead'];
    }

    public function getInputBody()
    {
        return $this->request['getInputBody'];
    }

    public function getJsonBodyList()
    {
        return $this->request['getJsonBodyList'];
    }

    public function getResources()
    {
        return $this->request['getResources'];
    }

    public function getMainConvertUrl()
    {
        return $this->request['getMainConvertUrl'];
    }

    public function getMainClearUrl()
    {
        return $this->request['getMainClearUrl'];
    }

    public function getMainUrl()
    {
        return $this->request['getMainUrl'];
    }
}


