<?php

/*declare(strict_types=1);*/

namespace Hleb\Reference;

use Hleb\Base\RollbackInterface;
use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\AvailableAsParent;
use Hleb\Main\Insert\ContainerUniqueItem;

#[Accessible] #[AvailableAsParent]
class DtoReference extends ContainerUniqueItem implements DtoInterface, Interface\Dto, RollbackInterface
{
    private static array $data = [];

    /** @internal */
    public function __construct()
    {
        $this->rollback();
    }

    /** @inheritDoc */
    #[\Override]
    public function get($name)
    {
        return self::$data[$name] ?? null;
    }

    /** @inheritDoc */
    #[\Override]
    public function set($name, #[\SensitiveParameter] $value): void
    {
        self::$data[$name] = $value;
    }

    /** @inheritDoc */
    #[\Override]
    public function clear(): void
    {
        $this->rollback();
    }

    /** @inheritDoc */
    #[\Override]
    public function list(): array
    {
        return self::$data;
    }

    /** @inheritDoc */
    #[\Override]
    public static function rollback(): void
    {
        self::$data = [];
    }
}
