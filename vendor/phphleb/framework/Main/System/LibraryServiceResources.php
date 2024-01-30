<?php

/*declare(strict_types=1);*/

namespace Hleb\Main\System;

use Hleb\Constructor\Data\DynamicParams;
use Hleb\Constructor\Data\SystemSettings;
use Hleb\HttpMethods\External\SystemRequest;

/**
 * This class is needed to interact with the public content of the framework libraries.
 *
 * Этот класс нужен для взаимодействия с публичным содержимым библиотек фреймворка.
 *
 * @internal
 */
final readonly class LibraryServiceResources
{
    private ?SystemRequest $request;

    /**
     * @internal
     */
    public function __construct()
    {
        $this->request = DynamicParams::getRequest();
    }

    /**
     * Checks the request for compliance with the system one, returns the result of the call if found.
     *
     * Проверяет запрос на соответствие системному, при нахождении возвращает результат вызова.
     *
     * @internal
     */
    public function place(): bool
    {
        $address = $this->request->getUri()->getPath();
        $key = LibraryServiceAddress::KEY;

        if (\str_starts_with($address, "/$key/")) {
            return $this->getResource();
        }
        return false;
    }

    /**
     * Redirects url like /hlresource/{lib}/{version}]/{ext}/{name}
     * to phphleb/{lib}/web/index.php library
     *
     * Перенаправляет url вида /hlresource/{lib}/{version}]/{ext}/{name}
     * в библиотеку phphleb/{lib}/web/index.php
     */
    private function getResource(): bool
    {
        $parts = \explode('/', \trim($this->request->getUri()->getPath(), '/'));
        if (\count($parts) !== 5) {
            return false;
        }
        \array_shift($parts);

        if (!$parts) {
            return false;
        }
        $systemPath = SystemSettings::getRealPath('@library/' . $parts[0]);
        if (!$systemPath) {
            return false;
        }

        $file = \implode(DIRECTORY_SEPARATOR, [$systemPath, 'web', 'index.php']);
        if (!\file_exists($file)) {
            return false;
        }

        $request = $this->request;

        return (require $file) ?? false;
    }
}
