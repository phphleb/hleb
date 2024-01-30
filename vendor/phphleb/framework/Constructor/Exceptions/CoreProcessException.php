<?php

namespace Hleb;

use Hleb\Constructor\Attributes\NotFinal;

/**
 * An error in the core framework that occurred during code execution.
 *
 * Ошибка в ядре фреймворка, которая возникла во время выполнения кода.
 */
#[NotFinal]
class CoreProcessException extends \RuntimeException implements CoreException
{
}
