<?php

declare(strict_types=1);

namespace Hleb\Main\Console\Commands;

use CallbackFilterIterator;
use FilesystemIterator;
use Hleb\Helpers\DirectoryInspector;
use Hleb\Main\RouteExtractor;
use Hleb\Static\Settings;
use Phphleb\Nicejson\JsonConverter;
use RecursiveIteratorIterator;
use SplFileInfo;

/**
 * @internal
 */
final class RouteInfo
{
    use FindRouteTrait;

    private const PATTERN = '[verb]';

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
     * Returns information about the route address that matches the conditions.
     *
     * Возвращает информацию об адресе маршрута, который соответствует условиям.
     */
    public function run(null|string $url, null|string $httpMethod, null|string $domain, null|string $format): string
    {
        if ($url === null) {
            return 'Error! Required argument `url` not specified: php console --route-info (or -ri) <url> [method] [domain]' . PHP_EOL;
        }
        [$url, $domain] = $this->splitUrl($url, $domain);

        $httpMethod = $httpMethod ?? 'get';
        $info = $this->getBlock($url, $httpMethod, $domain);
        if (\is_string($info)) {
            $this->code = 1;
            return $info;
        }
        if (!\is_array($info)) {
            $this->code = 1;
            return 'Not found.' . PHP_EOL;
        }

        $sample = [];
        $name = $info['name'] ?? null;
        $sample['name'] = $name ?? '-';
        if ($info['full-address'] ?? null) {
            $sample['address'] = $info['full-address'];
        }
        if (isset($info['data'])) {
            $data = $info['data'];
            if (isset($data['view'])) {
                if (\is_string($data['view'])) {
                    $sample['view'] = 'text';
                }
                if (\is_array($data['view'])) {
                    $sample['view'] = 'template';
                    $sample['template'] = $data['view']['template'] ?? '';
                    $sample['status'] = $data['view']['status'] ?? null;
                }
            }
        }
        $controller = $info['controller'] ?? $info['page'] ?? $info['module'] ?? null;
        if (isset($controller)) {
            if (isset($controller['class'])) {
                $className = $controller['class'];
                $method = $controller['class-method'] ?? 'index';
                $countTags = \substr_count($className . $method, '<');
                $extractor = new RouteExtractor();
                if ($countTags > 0) {
                    $params = $info['params'];
                    [$className, $method] = $extractor->getCalledClassAndMethod($className, $method, $countTags, $params);
                }
                if (\str_contains($className, self::PATTERN) || \str_contains($method, self::PATTERN)) {
                    [$className, $method] = $extractor->replacePattern($className, $method, $httpMethod);
                }
                $sample['class'] = $className;
                $sample['method'] = $method;
                $sample['search'] = $className . ':' . $method . '()';
            }
        }
        if ($sample['name'] && $sample['name'] !== '-') {
            $path = Settings::getRealPath('@/routes');
            if ($path) {
                $files = new CallbackFilterIterator(
                    new RecursiveIteratorIterator(
                        new \RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)
                    ),
                    function (SplFileInfo $current) {
                        return $current->isFile();
                    }
                );
                /** @var SplFileInfo $file */
                foreach ($files as $file) {
                    $handle = \fopen($file->getRealPath(), 'r');
                    if ($handle) {
                        $num = 0;
                        while (($line = \fgets($handle)) !== false) {
                            $num++;
                            if ($line &&
                                \str_contains($line, '->name(') &&
                                (\str_contains($line, '"' . $sample['name'] . '"') ||
                                    \str_contains($line, "'" . $sample['name'] . "'"))
                            ) {
                                $sample['search'] = $file->getRealPath() . ':' . $num;
                                $sample['path'] = '@/' . (new DirectoryInspector())
                                        ->getRelativeDirectory(Settings::getRealPath('@'), $file->getRealPath());

                                break 2;
                            }
                        }
                        \fclose($handle);
                    }
                }
            }
        }

        $sample = \array_filter($sample);

        $result = 'ROUTE INFO:' . PHP_EOL;
        foreach ($sample as $name => $item) {
            $result .= ' ' . $name . ': ' . $item . PHP_EOL;
        }

        if ($format === 'json') {
            $result = (new JsonConverter())->get($sample);
            if (!$result) {
                return 'Conversion to JSON failed.' . PHP_EOL;
            }
        }

        return $result;
    }
}
