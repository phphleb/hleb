<?php
/**
 * @author  Foma Tuturov <fomiash@yandex.ru>
 */

declare(strict_types=1);

namespace Hleb\Base;

use Hleb\Constructor\Attributes\AvailableAsParent;

/**
 * The base class of the middleware controller,
 * all middleware controllers must be inherited from it.
 *
 * Базовый класс контроллера-посредника (middleware),
 * все контроллеры-посредники должны быть унаследованы от него.
 */
#[AvailableAsParent]
abstract class Middleware extends Container
{
    public function __construct(#[\SensitiveParameter] array $config = [])
    {
        // The parent constructor must always be initialized first.
        // Сначала всегда должна идти инициализация родительского конструктора.
        parent::__construct($config);
    }
}
