<?php

namespace Hleb;

use Hleb\Constructor\Attributes\NotFinal;

/**
 * Format error while parsing data.
 *
 * Ошибка формата при разборе данных.
 *
 * @see \RuntimeException - base SPL class.
 *                        - базовый SPL-класс.
 */
#[NotFinal]
class ParseException extends CoreProcessException
{
}
