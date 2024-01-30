<?php
/**
 * @author  Foma Tuturov <fomiash@yandex.ru>
 */

declare(strict_types=1);

namespace Hleb\Base;

use Hleb\Constructor\Attributes\AvailableAsParent;

/**
 * The base class for the module controller, all module controllers must inherit from it.
 *
 * Базовый класс для контроллера модуля, все контроллеры модуля должны быть унаследованы от него.
 */
#[AvailableAsParent]
abstract class Module extends Container
{
    public function __construct(#[\SensitiveParameter] array $config = [])
    {
        // The parent constructor must always be initialized first.
        // Сначала всегда должна идти инициализация родительского конструктора.
        parent::__construct($config);
    }
}
