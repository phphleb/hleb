<?php
/**
 * @author  Foma Tuturov <fomiash@yandex.ru>
 */

declare(strict_types=1);

namespace Hleb\Base;

use Hleb\Constructor\Attributes\AvailableAsParent;

/**
 * The base class of the controller, all controllers must inherit from it.
 *
 * Базовый класс контроллера, все контроллеры должны быть унаследованы от него.
 */
#[AvailableAsParent]
abstract class Controller extends Container
{
    public function __construct(#[\SensitiveParameter] array $config = [])
    {
        // The parent constructor must always be initialized first.
        // Сначала всегда должна идти инициализация родительского конструктора.
        parent::__construct($config);
    }
}
