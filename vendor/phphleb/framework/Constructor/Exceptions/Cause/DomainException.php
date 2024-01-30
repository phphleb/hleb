<?php

namespace Hleb;

use Hleb\Constructor\Attributes\NotFinal;

/**
 * Error when the value does not match the set restrictions in the code.
 * Also used for various parameter checks, when the parameters are of the desired types,
 * but do not pass the value test.
 *
 * Ошибка при несоответствии значения установленным ограничениям в коде.
 * Также используется для различных проверок параметров, когда параметры нужных типов,
 * но при этом не проходят проверку на значение.
 *
 * @see \LogicException - base SPL class.
 *                      - базовый SPL-класс.
 */
#[NotFinal]
class DomainException extends \DomainException implements CoreException
{
}
