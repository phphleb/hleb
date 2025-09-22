<?php

/*declare(strict_types=1);*/

namespace Hleb\Main\Routes\Search;

class RouteAsyncFileManager extends RouteFileManager
{
    /**
     * @inheritDoc
     */
    public function getBlock(): false|array
    {
        if (self::$infoCache === null) {
            return parent::getBlock();
        }
        /** @see hl_check() - getBlock async start */
        $this->init();

        if (self::$stubData) {
            $this->isBlocked = true;
            return \is_array(self::$stubData) ? self::$stubData : false;
        }
        return parent::searchBlock();
    }
}
