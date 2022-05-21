<?php

declare(strict_types=1);

namespace Hleb\Scheme\Home\Constructor\Handlers;

class ResourceStandard
{
    /** @internal */
    const NEEDED_TAGS = ["\"", "'", '<', '>'];

    /** @internal */
    const REPLACING_TAGS = ['&quot;', '&apos;', '&lt;', '&gt;'];


    protected static function convertPrivateTags(string $value) {
        return str_replace(self::NEEDED_TAGS, self::REPLACING_TAGS, $value);
    }

    protected static function convertPrivateTagsInArray(array $value) {
        return array_map([self::class, 'convertPrivateTags'], $value);
    }

    protected static function returnPrivateTags(string $value) {
        return str_replace(self::REPLACING_TAGS, self::NEEDED_TAGS, $value);
    }
}



