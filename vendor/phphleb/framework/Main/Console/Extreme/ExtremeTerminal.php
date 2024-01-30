<?php

/*declare(strict_types=1);*/

namespace Hleb\Main\Console\Extreme;

/**
 * Terminal output with interpretation of query parameters into console commands.
 *
 * Вывод терминала с интерпретацией параметров запроса в консольные команды.
 *
 * @internal
 */
final readonly class ExtremeTerminal
{
    public function __construct(private string|null $command)
    {
    }

    public function get(): true
    {
        $uri = ExtremeRequest::getUri();

        echo '<h2>Terminal</h2><hr>     
        <form name="console" action="' . $uri . '" method="post">
        <table border="0" height="50" width="100%" cellpadding="0" cellspacing="0"><tr>        
        <td valign="center" width="40">        
              <input name="command" type=text autocomplete="on" formmethod="post" value="php console" placeholder="php console <command>">           
        </td><td valign="center" width="20">         
              <button type="submit">Enter</button>  
        </form>  
        </td><td valign="center" width="10">              &emsp;               
        </td><td valign="center" width="20">  
              <a href="' . $uri . '?command=php+console+--help">help</a>
        </td><td valign="top" width="10">              &emsp;
        </td><td valign="center" width="20">       
              <a href="' . $uri . '?command=php+console+--list">list</a><br>     
        </td><td valign="top" width="137">
        <form name="close" method="post" action="' . $uri . '">
        </td><td valign="center" align="right">
              <button type="submit">Exit</button>
        </form> 
        </td>
        </tr></table>
        ';

        $row =  $this->command ? '<b>' . \htmlentities($this->command) . '</b><br>': '';
        echo ExtremeDataTransfer::checkCommand((string)$this->command) ? $row : '<s>' . $row . '</s>';
        echo '<pre>';

        return true;
    }
}
