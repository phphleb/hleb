<?php

declare(strict_types=1);

namespace Hleb\Main\Console\Commands\Help;

use Hleb\Helpers\ReflectionMethod;
use Hleb\Main\Console\Console;
use Hleb\Main\Console\Specifiers\ArgType;

/**
 * @internal
 */
final class HelpGenerator
{
    private const LEFT_INDENTS = 3;

    private const MAX_DEFAULT_TEXT = 25;

    private const MAX_DESCRIPTION_TEXT = 25;

    /**
     * Returns the unified description of the custom command.
     *
     * Возвращает унифицированное описание пользовательской команды.
     */
    public function get(string $commandClass): string
    {
        $help = PHP_EOL . 'Usage: COMMAND [ARGUMENTS] [OPTIONS]' . PHP_EOL;
        $indents = \str_repeat(' ', self::LEFT_INDENTS);
        $arguments = $this->getArgumentFormatList(new ReflectionMethod($commandClass, 'run'));
        if ($arguments) {
            $help .= PHP_EOL . 'Ordinal command arguments:' . PHP_EOL;
            foreach ($arguments as $arg) {
                $type = \array_key_exists('type', $arg) ? \implode('|', $arg['type']) . ' ' : '';
                $default = \array_key_exists('default', $arg) ? '(default ' . $arg['default'] . ')' : '';
                \strlen($default) > self::MAX_DEFAULT_TEXT and $default = '(default text)';
                $help .= $indents . '<' . $arg['name'] . '> ' . $type . $default . PHP_EOL;
            }
        }
        $namedArguments = $this->getNamedArguments($commandClass);
        if ($namedArguments) {
            $help .= PHP_EOL . 'Available named options:' . PHP_EOL;
            $help .= \implode(PHP_EOL, $namedArguments);
            $help .= PHP_EOL;
        }
        $help .= ($arguments || $namedArguments ? PHP_EOL . 'Extra options:' : '' ) . PHP_EOL;
        $help .= $indents . '--desc   Displays information from the description.' . PHP_EOL;
        $help .= $indents . '--quiet  Disable text output.' . PHP_EOL;

        return $help;
    }

    private function getNamedArguments(string $class): array
    {
        try {
            /** @var Console $class */
            $rules = (new $class)->getRules();
        } catch (\Throwable) {
            return [];
        }
        $args = [];
        /** @var ArgType $rule */
        foreach ($rules as $rule) {
            $list = $rule->toArray();
            $row = \str_repeat(' ', self::LEFT_INDENTS);
            $shortNames = $list['shortName'] ?? [];
            $row .= '-' . \implode(', -', $shortNames);
            $baseName = $list['name'] ?? null;
            $baseName and $row .= ($row ? ', ' : '') . '--' . $baseName;
            $desc = $list['description'] ?? '';
            if (\strlen($desc) > self::MAX_DESCRIPTION_TEXT) {
                $desc = \substr($desc, 0, self::MAX_DESCRIPTION_TEXT) . '...';
            }
            $default = $list['default'] ?? null;
            if (\array_key_exists('default', $list)) {
                \is_null($default) and $default = 'null';
                !\is_numeric($default) and $default = "'$default'";
                $default = "(default $default)";
                \strlen($default) > self::MAX_DEFAULT_TEXT and $default = '(default text)';
            }
            $types = null;
            $commandTypes = \array_intersect(ArgType::TYPES, \array_keys($list));
            if ($commandTypes) {
                $types = \implode('|', $commandTypes);
            }
            $req = null;
            if (!empty($list['required'])) {
                $req = 'required';
            }
            $label = null;
            if (!empty($list['label'])) {
                $req = 'label';
            }
            $args[] = \implode(' ', \array_filter([$row . ' ', $types, $req, $label, $default, $desc]));
        }
        return $args;
    }

    private function getArgumentFormatList(ReflectionMethod $method): array
    {
        $args = $method->getArgNameList();
        $defaultArgs = $method->getArgDefaultValueList();
        $typeArgs = $method->getArgTypeList();
        $result = [];
        foreach ($args as $arg) {
            $row = ['name' => $arg];
            if (\array_key_exists($arg, $defaultArgs)) {
                $default = $defaultArgs[$arg];
                \is_string($default) and $default = "'$default'";
                $default === null and $default = 'null';
                $row['default'] = $default;
            }
            if (isset($typeArgs[$arg])) {
                $types = $typeArgs[$arg];
                $row['type'] = [];
                if (\in_array('float', $types)) {
                    $row['type'][] = 'numeric';
                } else if (\in_array('int', $types)) {
                    $row['type'][] = 'integer';
                }
                if (\in_array('string', $types)) {
                    $row['type'][] = 'string';
                }
            }
            $result[$arg] = $row;
        }

        return $result;
    }
}
