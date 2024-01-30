<?php

namespace Hleb;

use Hleb\Constructor\Attributes\NotFinal;

/**
 * A fatal error in the core of the framework,
 * which occurred due to errors in the code itself,
 * and not during its execution.
 *
 * Фатальная ошибка в ядре фреймворка,
 * которая возникла при ошибках в самом коде,
 * а не при его выполнении.
 */
#[NotFinal]
class CoreErrorException extends \Exception implements CoreException
{
}
