<?php

declare(strict_types=1);

namespace Hleb\Main\Console\Commands;

use Hleb\Constructor\Data\SystemSettings;
use Hleb\Helpers\DirectoryCleaner;

/**
 * @internal
 */
final class TwigCacheUpdater
{
    private int $code = 0;

    /**
     * Returns the code of the executed command or a default value.
     *
     * Возвращает код выполненной команды или значение по умолчанию.
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * Clears the cache for the Twig templating engine.
     *
     * Очищает кеш для шаблонизатора Twig.
     */
    public function run(): string
    {
        $cleaner = new DirectoryCleaner();
        $cleaner->forceRemoveDir(SystemSettings::getRealPath('storage') . '/cache/twig/compilation');
        $errors = $cleaner->getErrors();
        if ($errors) {
            $this->code = 1;
            return \end($errors) . PHP_EOL;
        }

        return 'Successfully cleared the Twig templating cache.' . PHP_EOL;
    }
}
