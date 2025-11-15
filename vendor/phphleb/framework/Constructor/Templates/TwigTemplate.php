<?php

/*declare(strict_types=1);*/

namespace Hleb\Constructor\Templates;

use App\Bootstrap\ContainerInterface;
use Hleb\CoreProcessException;
use Hleb\Helpers\DirectoryInspector;
use Hleb\Static\Request;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

/**
 * Outputting content by reference from a file using the `Twig` template engine.
 *
 * Вывод контента по ссылке из файла при помощи шаблонизатора `Twig`.
 *
 * @internal
 */
final readonly class TwigTemplate implements TemplateInterface
{
    public function __construct(
        private string             $path,
        private array              $data,
        private array              $twigLoader,
        private array              $twigOptions,
        private string             $cachePath,
        private string             $rootDirectory,
        private array              $invertedDirectories,
        private string             $realPath,
        private ContainerInterface $container,
        private array              $functions,
        private array              $callableList,
    )
    {
    }

    #[\Override]
    public function view(): void
    {
        $data = \array_merge($this->data(), $this->data);
        if (\class_exists('Twig\Loader\FilesystemLoader') && \class_exists('Twig\Environment')) {
            $options = $this->twigOptions;

            if (\count($this->invertedDirectories)) {
                $options['cache'] = $this->getCachingState(
                    $this->realPath,
                    $this->rootDirectory,
                    $this->invertedDirectories,
                    (bool)$options['cache']
                ) ? $this->cachePath : false;
            } else {
                $options['cache'] = $options['cache'] ? $this->cachePath : false;
            }

            $loader = new FilesystemLoader(\array_values($this->twigLoader));
            foreach ($this->twigLoader as $name => $path) {
                try {
                    $loader->addPath($path, $name);
                } catch (LoaderError $e) {
                    throw new CoreProcessException($e->getMessage());
                }
            }
            $twig = new Environment($loader, $options);
            if ($this->functions) {
                foreach($this->functions as $func) {
                    $twig->addFunction(new TwigFunction($func, $func));
                }
            }
            if ($this->callableList) {
                foreach($this->callableList as $callable) {
                    ($callable)($twig);
                }
            }
            try {
                echo $twig->render($this->path, $data);
            } catch(\Exception $e) {
                throw new CoreProcessException($e->getMessage());
            }
        } else {
            throw new CoreProcessException('Twig library is not installed. Run: composer require "twig/twig:^3.0"');
        }
    }

    /**
     * Determines whether the current entry should be cached from a list of directories and cache status.
     *
     * Определяет по списку директорий и статуса кеширования необходимость кеширования текущего вхождения.
     */
    private function getCachingState(string $dirOrFile, string $projectRootDir, array $invertedDirs, bool $isCache): bool
    {
        $helper = new DirectoryInspector();
        $relativeDir = $helper->getRelativeDirectory($projectRootDir, \dirname($dirOrFile));

        if ($relativeDir === false) {
            throw new CoreProcessException('Wrong folder name for Twig cache');
        }
        $searchDir = $helper->isDirectoryEntry($relativeDir, $invertedDirs);

        return ($isCache && !$searchDir) || (!$isCache && $searchDir);
    }


    /**
     * Adding standard variables to shared data.
     * They can be called in a .twig template like this:
     *
     * Добавление стандартных переменных в общие данные.
     * Их можно вызвать в шаблоне .twig по принципу:
     *
     * {{ app.url.path }}
     * {{ app.url.full }}
     * {{ app.container.requestId.get() }}
     *
     */
    private function data(): array
    {
        $req = Request::getUri();
        $address = Request::getAddress();
        $scheme = $req->getScheme();
        $path = $req->getPath();
        $host = $req->getHost();
        $query = $req->getQuery();
        $container = $this->container;

        return [
            'app' => [
                'url' => [
                    'path' => $path,                    // /blog/article
                    'query' => $query,                  // ?id=5
                    'full' => $address . $query,        // https://example.com/blog/article?id=5
                    'base' => $scheme . '://' . $host,  // https://example.com
                    'scheme' => $scheme,                // https
                    'host' => $host,                    // example.com
                ],
                'container' => $container,
            ]];
    }
}
