<?php

/*declare(strict_types=1);*/

namespace Hleb\Helpers;

use Hleb\Constructor\Attributes\Accessible;

/**
 * Removing comments for base cases.
 *
 * Удаление комментариев для базовых случаев.
 */
#[Accessible]
final readonly class PhpCommentHelper
{
    /**
     * Removes single line comments from code that start with `//`.
     *
     * Удаляет однострочные комментарии из кода, которые начинаются с `//`.
     */
    public function clearOneLiner(string $code): string
    {
        return \preg_replace('/^\s*\/\/.*$/m', '', $code);
    }

    /**
     * Removing multiline comments.
     *
     * Удаление многострочных комментариев.
     */
    public function clearMultiLine(string $code): string
    {
        return \preg_replace('/^\s*\/\*.*?(\*\/)/ms', '', $code);
    }

}
