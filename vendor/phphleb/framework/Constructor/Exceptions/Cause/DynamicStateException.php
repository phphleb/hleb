<?php

namespace Hleb;

use Hleb\Constructor\Attributes\NotFinal;

/**
 * An error if the data in the function/method or they themselves are ill-formed.
 * In a framework, these can be functions/methods created at runtime,
 * whose input parameters are initially unknown.
 *
 * Ошибка в случае, если данные в функции/методе или они сами некорректно сформированы.
 * Во фреймворке это могут быть  функции/методы, создаваемые в процессе выполнения,
 * входные параметры которых изначально не известны.
 *
 * @see \RuntimeException - base SPL class.
 *                        - базовый SPL-класс.
 */
#[NotFinal]
class DynamicStateException extends CoreProcessException
{
}
