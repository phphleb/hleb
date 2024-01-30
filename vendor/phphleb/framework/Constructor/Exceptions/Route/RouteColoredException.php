<?php

/*declare(strict_types=1);*/

namespace Hleb;

use Hleb\Constructor\Attributes\NotFinal;

/**
 * Output formatted error data.
 *
 * Вывод отформатированных данных об ошибке.
 *
 * throw (new RouteColoredException(AsyncExitException::HL00_ERROR))->complete(true, ['value' => 0]);
 */
#[NotFinal]
class RouteColoredException extends AsyncRouteException
{
    /**
     * Converts the message to HTML. This message should be displayed on all devices.
     *
     * Преобразует сообщение в HTML. Это сообщение должно отображаться на всех устройствах.
     */
    #[\Override]
    protected function coloredMessage(): string
    {
        $brColor = '#CC9966';
        $bgColor = 'seashell';
        $c = '';

        if ($this->isDebug) {
            $c = PHP_EOL . '<div>' . PHP_EOL;
            $c .= '<font size="5" face="Arial">' . PHP_EOL;
            $c .= "<table width='100%' border='1' cellspacing='0' cellpadding='5' bordercolor='$brColor' bgcolor='$bgColor'>";
            $c .= PHP_EOL;
            $count = \count($this->errorInfo);
            $link = "<b>{$this->tag}</b>";

            foreach ($this->errorInfo as $key => $value) {
                $key = \strtoupper($key);
                $c .= \str_repeat(' ', 4) ."    <tr>" . PHP_EOL;
                if ($link) {
                    $c .= \str_repeat(' ', 8) . "<td width='130' rowspan='$count'>⊗$link</td>" .
                        "<td width='30'>$key</td><td>$value</td>" . PHP_EOL;
                } else {
                    $c .= \str_repeat(' ', 8) . "<td>$key</td><td>$value</td>" . PHP_EOL;
                }
                $c .= \str_repeat(' ', 4) . "</tr>" . PHP_EOL;
                $link = '';
            }
            $c .= "</table>" . PHP_EOL;
            $c .= "</font>" . PHP_EOL;
            $c .= "</div>" . PHP_EOL;
        }
        return $c;
    }
}
