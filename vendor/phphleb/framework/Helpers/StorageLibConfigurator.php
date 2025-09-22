<?php

/*declare(strict_types=1);*/

namespace Hleb\Helpers;

use Hleb\Constructor\Attributes\Accessible;
use Hleb\Static\Settings;
use JsonException;
use Phphleb\Nicejson\JsonConverter;

/**
 * Interaction with component configuration.
 *
 * Взаимодействие с конфигурацией компонентов.
 */
#[Accessible]
final readonly class StorageLibConfigurator
{
    private string $path;

    /**
     * @param string $component - path to the component, for example 'phphleb/demo-updater'.
     *                          - путь к компоненту, например 'phphleb/demo-updater'.
     */
    public function __construct(string $component)
    {
        $this->path = Settings::getPath("@storage/lib/$component");
    }

    /**
     * @param string $file - relative path to the configuration file for the component.
     *                     - относительный путь к файлу конфигурации для компонента.
     *
     * @return array|false
     *
     * @throws JsonException
     */
    public function getConfig(string $file): array|false
    {
        if (!\file_exists($this->path)) {
            return false;
        }
        $path = $this->path . DIRECTORY_SEPARATOR . \rtrim($file, '\\/');
        if (!\file_exists($path)) {
            throw new \DomainException("No config file found at $path");
        }
        return \json_decode(\file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * Changing a parameter in the configuration (without nesting).
     *
     * Изменение параметра в конфигурации (без вложенности).
     *
     * @param string $file - relative path to the configuration file for the component.
     *                     - относительный путь к файлу конфигурации для компонента.
     *
     * @param string $name - parameter name.
     *                     - название параметра.
     *
     * @param mixed $value - assigned value.
     *                     - присваиваемое значение.
     *
     * @param string $type - conversion to the desired type ('string', 'int' or 'float').
     *                     - конвертация в нужный тип ('string', 'int' или 'float').
     *
     * @return bool
     *
     * @throws JsonException
     */
    public function setConfigOption(string $file, string $name, mixed $value, string $type = 'string'): bool
    {
        \settype($value, $type);
        $config = $this->getConfig($file);
        if ($config === false) {
            return false;
        }
        if (\array_key_exists($name, $config) && $config[$name] === $value) {
            return true;
        }
        $config[$name] = $value;
        $converter = new JsonConverter();
        $path = $this->path . DIRECTORY_SEPARATOR . \rtrim($file, '\\/');

        $result = \file_put_contents($path, $converter->get($config)) !== false;

        @\chmod($path, 0664);

        return $result;
    }
}
