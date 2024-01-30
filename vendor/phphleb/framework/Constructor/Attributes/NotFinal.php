<?php

namespace Hleb\Constructor\Attributes;

/**
 * You can use the #[NotFinal] tag on a class or method
 * to indicate that it should not be final.
 *
 * При помощи метки #[NotFinal] для класса или метода
 * можно указать, что он не должен быть финальным.
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class NotFinal
{
}
