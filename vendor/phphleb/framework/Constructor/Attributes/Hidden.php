<?php

namespace Hleb\Constructor\Attributes;

/**
 * By using the #[Hidden] label on a class or method
 * the hiding from the given visibility is defined.
 * In this case, the interpretation of such a state
 * is transferred to the label handler.
 *
 * При помощи метки #[Hidden] для класса или метода
 * определяется скрытие из заданной видимости.
 * При этом трактовка такого состояния
 * перекладывается на обработчик метки.
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class Hidden
{
}
