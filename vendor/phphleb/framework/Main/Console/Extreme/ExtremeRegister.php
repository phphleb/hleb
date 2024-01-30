<?php

/*declare(strict_types=1);*/

namespace Hleb\Main\Console\Extreme;

/**
 * Displaying the terminal access form.
 *
 * Вывод формы доступа к терминалу.
 *
 * @internal
 */
final readonly class ExtremeRegister
{
    public function run(): false
    {
        $uri = ExtremeRequest::getUri();
        $name = ExtremeIdentifier::KEY_NAME;

        // You can increase the strength of the key by lengthening it or adding other characters.
        // Вы можете увеличить надёжность ключа, удлинив его или добавив другие символы.
        $keyPath = ExtremeIdentifier::KEY_PATH;

        $m = "
       <h2>Web Console</h2><hr>
       <p>A login key has been created in the project file: /" . $keyPath . '</p>';
        $m .= "
        <form name='register' action='$uri' method='post' autocomplete='off'>
           Login key: <input name='$name' type='text' value='' autocomplete='off' minlength='72'><button type='submit'>Send</button>
        </form>";
        echo $m;

        return false;
    }
}
