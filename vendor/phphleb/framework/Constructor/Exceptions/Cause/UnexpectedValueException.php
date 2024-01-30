<?php

namespace Hleb;

use Hleb\Constructor\Attributes\NotFinal;

/**
 * An error occurs when a block of code returns a value that it shouldn't.
 *
 * Ошибка, когда блок кода возвращает значение, которое не должен возвращать.
 *
 * @see \RuntimeException - base SPL class.
 *                        - базовый SPL-класс.
 */
#[NotFinal]
class UnexpectedValueException extends \UnexpectedValueException implements CoreException
{
}
