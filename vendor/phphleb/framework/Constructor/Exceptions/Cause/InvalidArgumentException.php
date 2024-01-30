<?php

namespace Hleb;

use Hleb\Constructor\Attributes\NotFinal;

/**
 * Error if the expected arguments in the function/method are malformed.
 * The data is already erroneous before the execution
 * of a specific block of code that expects this data.
 *
 * Ошибка в случае, если ожидаемые аргументы в функции/методе некорректно сформированы.
 * Данные ошибочны уже до выполнения конкретного блока кода, ожидающего эти данные.
 *
 * @see \LogicException - base SPL class.
 *                      - базовый SPL-класс.
 */
#[NotFinal]
class InvalidArgumentException extends \InvalidArgumentException implements CoreException
{
}
