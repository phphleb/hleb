<?php


namespace Hleb\Main\Helpers;


class ReflectionMethod
{
    private $class;

    private $method;

    private $params;

    public function __construct(string $className, string $methodName)
    {
       $this->class = new \ReflectionClass($className);
       $this->method = $this->class->getMethod($methodName);
       $this->params = $this->method->getParameters();
    }

    /**
     * Returns a list of argument names.
     *
     * Возвращает список названий аргументов.
     *
     * @return array
     */
    public function getArgNameList()
    {
        $result = [];
        foreach ($this->params as $value) {
            $result[]= $value->getName();
        }
        return $result;
    }

    /**
     * Returns a list of argument types.
     *
     * Возвращает список типов аргументов.
     *
     * @return array
     */
    public function getArgTypeList()
    {
        $result = [];
        foreach ($this->params as $value) {
            if (!method_exists($value, '__toString')) {
                $result[] = $value->getType();
            } else {
                $result[] = $value;
            }
        }
        return $result;
    }
}

