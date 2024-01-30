<?php

declare(strict_types=1);

namespace Hleb\Main\Console\Commands;

use Hleb\Constructor\Data\SystemSettings;
use Hleb\CoreProcessException;
use Hleb\Helpers\NameConverter;
use Hleb\InvalidArgumentException;

/**
 * @internal
 */
final class TemplateCreator
{
    final public const CREATE_FROM_TEMPLATES = ['task', 'controller', 'middleware', 'model'];

    public function run(string $template, string $class, string $description): false|string
    {
        if (!$template || !\in_array($template, self::CREATE_FROM_TEMPLATES, true)) {
            throw new InvalidArgumentException('Supported creation from templates: ' . \implode(', ', self::CREATE_FROM_TEMPLATES));
        }
        $file = 'Optional/Templates/' . $template . '_class_template.php';

        // You can override the template in the appropriate project folder.
        // Можно переопределить шаблон в соответствующей папке проекта.
        $path = SystemSettings::getRealPath("@app/$file") ?: SystemSettings::getRealPath("@framework/$file");
        if (!$path) {
            throw new InvalidArgumentException("Missing template `$template`");
        }
        if (!$class) {
            throw new InvalidArgumentException("Missing class name for template `$template`.");
        }
        if ($template === 'task') {
            if (!preg_match('/^[a-z0-9\-\/]+$/', $class)) {
                throw new InvalidArgumentException("Wrong command name format.");
            }
            $parts = \explode('/', \trim($class, '/'));
            $converter = new NameConverter();
            foreach ($parts as $key => $part) {
                $parts[$key] = $converter->convertStrToClassName($part);
            }
        } else {
            if (!preg_match('/^[a-z0-9\/\_\\\]+$/i', $class)) {
                throw new InvalidArgumentException("Invalid class name format.");
            }
            $delimiter = str_contains($class, '\\') ? '\\' : '/';
            $parts =  \explode($delimiter, \trim($class, '\\/'));
        }
        $paths = ['App'];
        $paths[] = match ($template) {
            'task' => 'Commands',
            'controller' => 'Controllers',
            'middleware' => 'Middlewares',
            'model' => 'Models',
        };
        $paths = \array_merge($paths, $parts);
        $fullClass = \lcfirst(\implode('/', $paths));
        $name = \array_pop($paths);
        $namespace = \implode('\\', $paths);

        $content = \file_get_contents($path);
        if (!$content) {
            throw new CoreProcessException("File missing content.");
        }
        $content = \str_replace(
            [$template . '_class_template', $template . '_namespace_template', $template . '_description_template'],
            [$name, $namespace, $description],
            $content
        );

        $targetFile = SystemSettings::getPath("@/$fullClass.php");
        if (\file_exists($targetFile)) {
            throw new CoreProcessException("File already exists.");
        }
        \hl_create_directory($targetFile);
        \file_put_contents($targetFile, $content);

        return 'Class successfully created: ' . $fullClass . '.php' . PHP_EOL;
    }
}
