<?php


namespace Hleb\Scheme\App\Controllers;

/**
 * Implements the ability to transfer data between controllers and ensures class testability:
 *
 * Реализует возможность передачи данных между контроллерами и обеспечивает тестируемость класса:
 *
 * (new DefaultController($testData))->index();
 */
class BaseController
{
    private static $persistentData = [];

    private $temporaryData = null;

    /**
     * Parameter to use only for class testing.
     *
     * Параметр использовать только для тестирования класса.
     *
     * @param array|null $data
     */
    function __construct(array $data = null)
    {
        is_null($data) or $this->temporaryData = $data;
    }

    /**
     * Returns the general data by key or in its entirety.
     *
     * Возвращает общие данные по ключу или целиком.
     *
     * @param string|null $attribute
     * @return mixed
     */
    protected function getControllerData(string $attribute = null)
    {
        return is_null($this->temporaryData) ?
            (empty($attribute) ? self::$persistentData : self::$persistentData[$attribute] ?? null) :
            (empty($attribute) ? $this->temporaryData : $this->temporaryData[$attribute] ?? null);
    }

    /**
     * Sets data by key.
     *
     * Устанавливает данные по ключу.
     *
     * @param string $attribute
     * @param mixed $value
     */
    protected function setControllerData(string $attribute, $value)
    {
        is_null($this->temporaryData) ? (self::$persistentData[$attribute] = $value) : $this->temporaryData[$attribute] = $value;
    }

    /**
     * Clears all shared controller data.
     *
     * Очищает все общие данные контроллеров.
     */
    protected function clearControllerData()
    {
        is_null($this->temporaryData) ? self::$persistentData = [] : $this->temporaryData = [];
    }

    /**
     * Returns the keys of the saved data.
     *
     * Возвращает идентификаторы сохраненных данных.
     *
     * @return array
     */
    protected function getControllerDataKeys(): array
    {
        return is_null($this->temporaryData) ? array_keys(self::$persistentData ?? []) : array_keys($this->temporaryData ?? []);
    }
}

