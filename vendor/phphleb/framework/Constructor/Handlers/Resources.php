<?php

declare(strict_types=1);

/*
 * Loading the assigned resources at the bottom of the <body>...</body> block.
 *
 * Загрузка назначенных ресурсов в нижней части блока <body>...</body>.
 */

namespace Hleb\Constructor\Handlers;

use Hleb\Scheme\Home\Constructor\Handlers\ResourceInterface;
use Hleb\Scheme\Home\Constructor\Handlers\ResourceStandard;

class Resources extends ResourceStandard implements ResourceInterface
{
    protected $bottomScripts = [];

    protected $bottomStyles = [];

    protected $bottomScriptsOnce = false;

    protected $bottomStylesOnce = false;

    /**
     * @inheritDoc
     */
    public function addBottomScript(string $url, string $charset = 'utf-8') {
        $this->bottomScripts[$url] = ['url' => $url, 'charset' => $charset];
    }

    /**
     * @inheritDoc
     */
    public function getBottomScripts(int $indents = 2) {
        $result = PHP_EOL;
        $this->bottomScriptsOnce = true;
        foreach ($this->bottomScripts as $script) {
            $script = $this->convertPrivateTagsInArray($script);
            $result .= str_repeat(' ', $indents) . '<script src="' . $script["url"] . '" charset="' . $script["charset"] . '"></script>' . PHP_EOL;
        }
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function getBottomScriptsOnce(int $indents = 2) {
        if ($this->bottomScriptsOnce) return null;
        $this->bottomScriptsOnce = true;
        return $this->getBottomScripts($indents);
    }

    /**
     * @inheritDoc
     */
    public function addBottomStyles(string $url) {
        $this->bottomStyles[$url] = $url;
    }

    /**
     * @inheritDoc
     */
    public function getBottomStyles(int $indents = 2) {
        $result = PHP_EOL;
        foreach ($this->bottomStyles as $style) {
            $result .= str_repeat(' ', $indents) . '<link rel="stylesheet" href="' . $this->convertPrivateTags($style) . '" type="text/css" media="screen">' . PHP_EOL;
        }
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function getBottomStylesOnce(int $indents = 2) {
        if ($this->bottomStylesOnce) return null;
        $this->bottomStylesOnce = true;
        return $this->getBottomStyles($indents);
    }
}

