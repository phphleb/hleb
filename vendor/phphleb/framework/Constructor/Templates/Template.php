<?php

/*declare(strict_types=1);*/

namespace Hleb\Constructor\Templates;

use App\Bootstrap\ContainerInterface;
use Hleb\Constructor\Data\SystemSettings;

/**
 * @internal
 */
final class Template
{
    final public const TWIG = 'twig';

    final public const PHP = 'php';

    private ?string $path = null;

    private ?string $realPath = null;

    private ?string $rootPath = null;

    private ?string $cachePath = null;

    private ?ContainerInterface $container = null;

    private array $viewPaths =  [];

    private array $invertedPaths = [];

    private array $data = [];

    /**
     * Initializing a specific template.
     *
     * Инициализация определенного шаблона.
     */
    public function __construct(readonly private string $type)
    {
    }

    public function view(): void
    {
        match ($this->type) {
            self::TWIG => (new TwigTemplate(
                $this->path,
                $this->data,
                $this->viewPaths,
                SystemSettings::getValue('common', 'twig.options'),
                $this->cachePath,
                $this->rootPath,
                $this->invertedPaths,
                $this->realPath,
                $this->container,
            ))->view(),
            self::PHP => (new PhpTemplate(
                $this->path,
                $this->data,
            ))->view(),
        };
    }

    /**
     * Setting the path for the template file, eg `@view/example.twig`.
     *
     * Установка пути для файла шаблона, например, `@view/example.twig`.
     */
    public function setPath(string $path): static
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Passing a link to the container to the template.
     *
     * Передача ссылки на контейнер в шаблон.
     */
    public function setContainer(ContainerInterface $container): static
    {
        $this->container = $container;

        return $this;
    }

    /**
     * Setting the path for the template file.
     *
     * Установка пути для файла шаблона.
     */
    public function setRealPath(string $path): static
    {
        $this->realPath = $path;

        return $this;
    }

    /**
     * Sets the data to output to the template.
     *
     * Установка данных для вывода в шаблон.
     */
    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Setting the path for template caching.
     *
     * Установка пути для кеширования шаблона.
     */
    public function setCachePath(string $path): static
    {
        $this->cachePath = $path;

        return $this;
    }

    /**
     * Setting paths for the template loader.
     *
     * Установка путей для загрузчика шаблонов.
     */
    public function setViewPaths(array $paths): static
    {
        $this->viewPaths = $paths;

        return $this;
    }

    /**
     * Setting the path for the project root directory.
     *
     * Установка пути для корневой директории проекта.
     */
    public function setRootPath(string $path): static
    {
        $this->rootPath = $path;

        return $this;
    }

    /**
     * Setting the path for the project root directory.
     *
     * Установка пути для корневой директории проекта.
     */
    public function setInvertedPath(array $paths): static
    {
        $this->invertedPaths = $paths;

        return $this;
    }
}
