<?php

/*declare(strict_types=1);*/

namespace Hleb\Main\Console\Commands\Deployer;

use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Data\SystemSettings;
use Hleb\Helpers\NameConverter;

/**
 * A highly specialized class for working with updating libraries from the console.
 *
 * Узкоспециальный класс для работы с обновлением библиотек из консоли.
 */
#[Accessible]
final class LibDeployerCreator
{
    private static ?NameConverter $helper = null;

    /**
     * Creating an object from the specified in the library ($command) and configuration.
     *
     * Создание объекта из указанного в библиотеке ($command) и конфигурации.
     */
    public function createDeployer(string $command, array $config = []): DeploymentLibInterface|false
    {
        if (!$config) {
            return false;
        }
        $command = \trim($command, '\\/');
        self::$helper or self::$helper = new NameConverter();
        $classParts = \explode('/', $command);
        $libPath = SystemSettings::getRealPath('@vendor/' . $command);
        $file = $libPath . '/Deployment/StartForHleb.php';
        if (!\file_exists($file) || \count($classParts) !== 2) {
            return false;
        }
        foreach ($classParts as &$part) {
            $part = self::$helper->convertStrToClassName($part);
        }
        $class = \implode('\\', $classParts) . '\\Deployment\\StartForHleb';
        if (!\class_exists($class, false)) {
            require $file;
        }
        return new $class($config);
    }
}
