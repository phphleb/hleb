<?php

/*declare(strict_types=1);*/

namespace Hleb\Constructor\Cache;

use Hleb\Helpers\ArrayWriting;
use Hleb\Helpers\ProtectedFileSaver;

/**
 * @internal
 */
final class ClassWithDataCreator
{
    /**
     * Saving data as a class.
     *
     * Сохранение данных в виде класса.
     */
    public function saveContent(string $className, string $path, array $data, array $cells = [], bool $privateData = true): bool
    {
        $content = $this->getClassHeader($className, $privateData);
        $content .= (new ArrayWriting())->getString($data, 2);
        $content .= $this->getClassEndData();
        foreach ($cells as $name => $value) {
            $content .= $this->getClassCell($name, $value, \gettype($value));
        }
        $content .= $this->getClassFooter();
        $this->save($path, $content);

        return \file_exists($path);
    }

    private function getClassHeader(string $className, bool $privateData): string
    {
        $type = $privateData ? 'private' : 'public';
        return '<?php' . PHP_EOL . PHP_EOL .
            'declare(strict_types=1);' . PHP_EOL . PHP_EOL .
            $this->getDangerInfo() . PHP_EOL .
            "final class $className" . PHP_EOL .
            '{' . PHP_EOL .
            '    /**' . PHP_EOL .
            '    * @internal' . PHP_EOL .
            '    */' . PHP_EOL .
            "    $type static array \$data =";
    }

    private function getClassCell(string $name, int|string $value, $type): string
    {
        if ($type === 'string') {
            $value = "'$value'";
        }
        if ($type === 'integer') {
            $type = 'int';
        }
        return PHP_EOL .
            '    /**' . PHP_EOL .
            '    * @internal' . PHP_EOL .
            '    */' . PHP_EOL .
            "    public static $type $$name = $value;" . PHP_EOL;
    }

    private function getClassEndData(): string
    {
        return ';' . PHP_EOL . PHP_EOL;
    }

    private function getClassFooter(): string
    {
        return PHP_EOL .
            '    /**' . PHP_EOL .
            '    * @internal' . PHP_EOL .
            '    */' . PHP_EOL .
            '    public static function getData(): array' . PHP_EOL .
            '    {' . PHP_EOL .
            '        return self::$data;' . PHP_EOL .
            '    }' . PHP_EOL .
            '}' . PHP_EOL;
    }

    private function save(string $path, string $data): void
    {
        (new ProtectedFileSaver())->save($path, $data);
    }

    private function getDangerInfo(): string
    {
        return '/**' . PHP_EOL .
            '* This class is generated automatically. It will be changed during the update.' . PHP_EOL .
            '* ' . PHP_EOL .
            '* Этот класс сгенерирован автоматически. Он будет изменён при обновлении.' . PHP_EOL .
            '* ' . PHP_EOL .
            '* @internal' . PHP_EOL .
            '*/';
    }
}
