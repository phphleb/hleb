<?php

declare(strict_types=1);

namespace Phphleb\Debugpan\Controllers;

use Hleb\HttpMethods\Intelligence\AsyncConsolidator;
use Hleb\Static\Request;
use Hleb\Static\Session;
use Hleb\Static\Settings;

class StateController
{
    use ResponseTrait;

    private const STATE_NAME = 'HLEB_DEBUGPAN_STATE_NAME';

    public function __construct()
    {
        AsyncConsolidator::initAllCookies();
    }

    /**
     * /{key}/state/set
     */
    public function actionSet(): ?string
    {
        $state = Request::get('name');
        if ($state->value()) {
            Session::set(self::STATE_NAME, $state->toString());
        }

        return $this->getSuccessfulResponse(['name' => $state->toString()]);
    }

    /**
     * /{key}/state/get
     */
    public function actionGet(): ?string
    {
        return $this->getSuccessfulResponse(['name' => Session::get(self::STATE_NAME)]);
    }
}