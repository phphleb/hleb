<?php

/*declare(strict_types=1);*/

namespace Hleb\Helpers;

use Hleb\Constructor\Attributes\Accessible;

/**
 * Allows you to get the class name and namespace from the text.
 *
 * Позволяет получить из текста название класса и namespace.
 */
#[Accessible]
final class ClassDataInFile
{
    private array|null $data = null;

    private string|null $content;

    public function __construct(string $file)
    {
        $this->content = \file_get_contents($file);
    }

    /**
     * Returns the full class name or false if it is absent.
     *
     * Возвращает полное название класса или false при его отсутствии.
     */
    public function getClass(): string|false
    {
        $this->data === null and $this->data = $this->parse(\token_get_all($this->content));
        if ($this->isClass()) {
            [$namespace, $class,] = $this->data;
            if ($namespace) {
                $namespace = \rtrim($namespace, '\\') . '\\';
            }
            return $namespace . \rtrim($class, '\\');
        }
        return false;
    }

    /**
     * Returns the namespace of the class, or false if it is absent.
     *
     * Возвращает namespace класса или false при его отсутствии.
     */
    public function getNamespace(): string|false
    {
        $this->data === null and $this->data = $this->parse(\token_get_all($this->content));

        if ($this->isClass()) {
            return \current($this->data);
        }
        return false;
    }

    /**
     * Returns the result of the presence of a class, interface or trait in the content.
     *
     * Возвращает результат присутствия класса, интерфейса или трейта в контенте.
     */
    public function isClass(): bool
    {
        $this->data === null and $this->data = $this->parse(\token_get_all($this->content));

        return \count($this->data) === 3;

    }

    /**
     * Returns the result of the class being found in the content.
     *
     * Возвращает результат нахождения класса в контенте.
     */
    public function isStandardClass(): bool
    {
        $this->data === null and $this->data = $this->parse(\token_get_all($this->content));

        return \count($this->data) && end($this->data) === T_CLASS;

    }

    /**
     * Returns the result of finding the trait in the content.
     *
     * Возвращает результат нахождения трейта в контенте.
     */
    public function isTrait(): bool
    {
        $this->data === null and $this->data = $this->parse(\token_get_all($this->content));

        return \count($this->data) && end($this->data) === T_TRAIT;

    }

    /**
     * Returns the result of finding the interface in the content.
     *
     * Возвращает результат нахождения интерфейса в контенте.
     */
    public function isInterface(): bool
    {
        $this->data === null and $this->data = $this->parse(\token_get_all($this->content));

        return \count($this->data) && end($this->data) === T_INTERFACE;

    }

    /**
     * Returns an array with class namespace and class name.
     * If class data is not found in the content, it returns an empty array.
     *
     * Возвращает массив с namespace класса и названием класса.
     * Если в контенте не найдены данные класса - возвращает пустой массив.
     *
     * @param array $tokens - the result of the token_get_all() function.
     *                      - результат функции token_get_all().
     */
    private function parse(array $tokens): array
    {
        $lastLine = 1;
        $prevNum = 0;
        $namespace = '';
        foreach ($tokens as $token) {
            // Non-arrays are excluded, for example - ;
            // Исключаются не-массивы, например - ;
            if (!\is_array($token) || \count($token) < 3) {
               continue;
            }
            [$num, $str, $line] = $token;
            if ($num === T_WHITESPACE || $num === T_LOGICAL_OR) {
                continue;
            }
            if ($num === T_CLASS && \in_array($prevNum, [T_DOUBLE_COLON, T_NEW])) {
                continue;
            }
            if ($line !== $lastLine) {
                $prevNum = 0;
                $lastLine = $line;
            }
            if ($prevNum === T_NAMESPACE) {
                $namespace = $str;
                continue;
            }
            if (\in_array($prevNum, [T_CLASS, T_TRAIT, T_INTERFACE])) {
                $class = $str;
                return [$namespace, $class, $prevNum];
            }

            $prevNum = $num;
        }
        return [];
    }
}
