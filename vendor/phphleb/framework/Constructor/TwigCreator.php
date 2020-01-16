<?php

declare(strict_types=1);

namespace Hleb\Constructor;

class TwigCreator
{
    public function view(string $path)
    {
        $loader = new \Twig\Loader\FilesystemLoader(HL_TWIG_LOADER_FILESYSTEM);
        $twig = new \Twig\Environment($loader, array(
            'cache' => HL_TWIG_CACHED,
            'debug' => HLEB_PROJECT_DEBUG,
            'charset' => HL_TWIG_CHARSET,
            'auto_reload' => HL_TWIG_AUTO_RELOAD,
            'strict_variables' => HL_TWIG_STRICT_VARIABLES,
            'autoescape' => HL_TWIG_AUTOESCAPE,
            'optimizations' => HL_TWIG_OPTIMIZATIONS
        ));
        echo $twig->render($path, hleb_to0me1cd6vo7gd_data());
    }
}

