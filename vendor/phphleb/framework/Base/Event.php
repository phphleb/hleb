<?php
/**
 * @author  Foma Tuturov <fomiash@yandex.ru>
 */

declare(strict_types=1);

namespace Hleb\Base;

use Hleb\Constructor\Attributes\AvailableAsParent;

/**
 * Parent class for events.
 *
 * Родительский класс для событий.
 */
#[AvailableAsParent]
abstract class Event extends Container
{
}
