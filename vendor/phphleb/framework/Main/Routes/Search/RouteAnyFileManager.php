<?php

/*declare(strict_types=1);*/

namespace Hleb\Main\Routes\Search;

class RouteAnyFileManager extends RouteFileManager
{

    /**
     * Request a block for an arbitrary request while saving the state of the current request.
     *
     * Запрос блока для произвольного запроса с сохранением состояния текущего запроса.
     *
     * @inheritDoc
     *
     * @internal
     */
    public function getBlock(): false|array
    {
        $infoCacheDuplicate = self::$infoCache;
        $stubDataDuplicate = self::$stubData;
        $result = parent::getBlock();
        self::$infoCache = $infoCacheDuplicate;
        self::$stubData = $stubDataDuplicate;

        return $result;
    }
}
