<?php

declare(strict_types=1);

/*
 * Outputting content by reference from a file using the `Twig` template engine.
 *
 * Вывод контента по ссылке из файла при помощи шаблонизатора `Twig`.
 */

namespace Hleb\Constructor;

final class TwigCreator
{
    public function view(string $path) {
        if(class_exists('Twig\Loader\FilesystemLoader') && class_exists('Twig\Environment')) {
            $loader = new \Twig\Loader\FilesystemLoader(HL_TWIG_LOADER_FILESYSTEM);
            $twig = new \Twig\Environment($loader, [
                'cache' => HL_TWIG_CACHED,
                'debug' => HLEB_PROJECT_DEBUG,
                'charset' => HL_TWIG_CHARSET,
                'auto_reload' => HL_TWIG_AUTO_RELOAD,
                'strict_variables' => HL_TWIG_STRICT_VARIABLES,
                'autoescape' => HL_TWIG_AUTOESCAPE,
                'optimizations' => HL_TWIG_OPTIMIZATIONS
            ]);
            echo $twig->render($path, hleb_data());
        } else {
            hleb_system_log('Undefined Twig class on TwigCreator');
        }
    }
}

