<?php

/*declare(strict_types=1);*/

namespace Hleb\Reference;

use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\AvailableAsParent;
use Hleb\Helpers\ArrayHelper;
use Hleb\Main\Insert\ContainerUniqueItem;

#[Accessible] #[AvailableAsParent]
class ArrReference extends ContainerUniqueItem implements ArrInterface, Interface\Arr
{
    /** @inheritDoc */
    #[\Override]
    public function isAssoc(array $array): bool
    {
        return ArrayHelper::isAssoc($array);
    }

    /** @inheritDoc */
    #[\Override]
    public function append(array $original, array $complement): array
    {
        return ArrayHelper::append($original, $complement);
    }

    /** @inheritDoc */
    #[\Override]
    public function sortDescByField(array $list, string $field): array
    {
        return ArrayHelper::sortDescByField($list, $field);
    }

    /** @inheritDoc */
    #[\Override]
    public function sortAscByField(array $array, string $field): array
    {
        return ArrayHelper::sortAscByField($array, $field);
    }

    /** @inheritDoc */
    #[\Override]
    public function moveToFirst(array $array, string $key, bool $strict = true): array
    {
        return ArrayHelper::moveToFirst($array, $key, $strict);
    }

    /** @inheritDoc */
    #[\Override]
    public function only(array $array, array $keys): array
    {
        return ArrayHelper::only($array, $keys);
    }

    /** @inheritDoc */
    #[\Override]
    public function divide(array $array): array
    {
        return ArrayHelper::divide($array);
    }

    /** @inheritDoc */
    #[\Override]
    public function get(array $array, int|string|null $key, mixed $default = null): mixed
    {
        return ArrayHelper::get($array, $key, $default);
    }

    /** @inheritDoc */
    #[\Override]
    public function forget(array &$array, array|string|int|float $keys): void
    {
        ArrayHelper::forget($array, $keys);
    }

    /** @inheritDoc */
    #[\Override]
    public function has(array $array, string|array $keys): bool
    {
        return ArrayHelper::has($array, $keys);
    }

    /** @inheritDoc */
    #[\Override]
    public function add(array $array, string|int|float $key, mixed $value): array
    {
        return ArrayHelper::add($array, $key, $value);
    }

    /** @inheritDoc */
    #[\Override]
    public function set(array &$array, string|int|null $key, mixed $value): array
    {
        return ArrayHelper::set($array, $key, $value);
    }

    /** @inheritDoc */
    #[\Override]
    public function expand(iterable $array): array
    {
        return ArrayHelper::expand($array);
    }
}
