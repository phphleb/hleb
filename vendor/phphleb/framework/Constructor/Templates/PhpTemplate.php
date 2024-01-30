<?php

/*declare(strict_types=1);*/

namespace Hleb\Constructor\Templates;

use Hleb\Constructor\Data\SystemSettings;

/**
 * @internal
 */
final class PhpTemplate implements TemplateInterface
{
    public function __construct(
        private string $path,
        private array $data,
    )
    {
    }

    /**
     * Template output with data injection.
     *
     * Вывод шаблона с внедрением данных.
     */
    public function view(): void
    {
        \extract($this->data);
        unset($this->data);
        if (!\str_ends_with($this->path, '.php')) {
            $this->path = "{$this->path}.php";
        }

        require SystemSettings::getRealPath($this->path);
    }
}
