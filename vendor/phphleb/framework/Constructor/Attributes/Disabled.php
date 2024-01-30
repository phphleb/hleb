<?php

namespace Hleb\Constructor\Attributes;

/**
 * By using the #[Disabled] label on a class or method,
 * you can indicate that it is inactive.
 * In this case, the interpretation of such a state
 * is transferred to the label handler.
 *
 * При помощи метки #[Disabled] для класса или метода
 * можно указать, что он неактивен.
 * При этом трактовка такого состояния
 * перекладывается на обработчик метки.
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class Disabled
{
}
