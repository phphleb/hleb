<?php

namespace Hleb;

use Hleb\Constructor\Attributes\NotFinal;

/**
 * An error occurred when applying reflection as a result of code execution.
 *
 * Ошибка применения рефлексии в результате выполнения кода.
 */
#[NotFinal]
class ReflectionProcessException extends CoreProcessException
{
}
