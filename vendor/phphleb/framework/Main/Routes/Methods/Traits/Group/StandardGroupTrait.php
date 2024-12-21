<?php

namespace Hleb\Main\Routes\Methods\Traits\Group;

trait StandardGroupTrait
{
    use GroupPrefixTrait;
    use GroupMiddlewareTrait;
    use GroupAfterTrait;
    use GroupBeforeTrait;
    use GroupDomainTrait;
    use GroupWhereTrait;
    use GroupNoDebugTrait;
    use GroupTrait;
}
