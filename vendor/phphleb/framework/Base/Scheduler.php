<?php
/**
 * @author  Foma Tuturov <fomiash@yandex.ru>
 */

declare(strict_types=1);

namespace Hleb\Base;

use Hleb\Constructor\Attributes\AvailableAsParent;

/**
 * Helper class for executing console commands based on time
 * and depending on the properties of the current server time.
 * This assumes that the startup was initiated by `cron`,
 * so everyMinute() output is always executed.
 * To use the methods of the Scheduler class,
 * you must inherit a custom command from it instead
 * of inheriting from Task.
 *
 * Вспомогательный класс для выполнения консольных команд по времени
 * и в зависимости от свойств текущего серверного времени.
 * Подразумевается, что запуск инициирован `cron`,
 * поэтому поминутный вывод everyMinute() выполняется всегда.
 * Чтобы использовать методы класса Scheduler,
 * нужно унаследовать от него пользовательскую команду
 * вместо наследования от Task.
 */
#[AvailableAsParent]
abstract class Scheduler extends Task
{
    private \DateTimeInterface|null $date = null;

    /**
     * Executes a console command or a list of them.
     *
     * Выполняет консольную команду или их список.
     */
    protected function everyMinute(array|string $commands = []): void
    {
        $this->execute($commands);
    }

    /**
     * Executes a console command or a list of them at the beginning of every hour.
     *
     * Выполняет консольную команду или их список в начале каждого часа.
     */
    protected function everyHour(array|string $commands = []): void
    {
        // XX:00:00
        if ($this->getDate()->format('i') === '00') {
            $this->execute($commands);
        }
    }

    /**
     * Executes a console command or a list of them at the beginning of each day.
     *
     * Выполняет консольную команду или их список в начале каждого дня.
     */
    protected function everyDay(array|string $commands = []): void
    {
        // 00:00:00
        if ($this->getDate()->format('H:i') === '00:00') {
            $this->execute($commands);
        }
    }

    /**
     * Executes a console command or a list of them every five minutes.
     *
     * Выполняет консольную команду или их список каждые пять минут.
     */
    protected function every5Minutes(array|string $commands = []): void
    {
        $date = $this->getDate()->format('i');
        if ($date[1] === '0' || $date[1] === '5') {
            $this->execute($commands);
        }
    }

    /**
     * Executes a console command or a list of them every ten minutes.
     *
     * Выполняет консольную команду или их список каждые десять минут.
     */
    protected function every10Minutes($commands = []): void
    {
        $date = $this->getDate()->format('i');
        if ($date[1] === '0') {
            $this->execute($commands);
        }
    }

    /**
     * Executes a console command or a list of them every fifteen minutes.
     *
     * Выполняет консольную команду или их список каждые пятнадцать минут.
     */
    protected function every15Minutes(array|string $commands = []): void
    {
        $date = $this->getDate()->format('i');
        if (\in_array($date, ['00', '15', '30', '45'])) {
            $this->execute($commands);
        }
    }

    /**
     * Executes a console command or a list of them every twenty minutes.
     *
     * Выполняет консольную команду или их список каждые двадцать минут.
     */
    protected function every20Minutes(array|string $commands = []): void
    {
        $date = $this->getDate()->format('i');
        if (\in_array($date, ['00', '20', '40'])) {
            $this->execute($commands);
        }
    }

    /**
     * Selected time in hours 0-24
     *
     * В выбранное время часа 0-24
     */
    protected function givenHour(array|int|string $h = [0]): bool
    {
        return $this->searchData($h, 'H');
    }

    /**
     * In the selected month 1-12
     *
     * В выбранный месяц 1-12
     */
    protected function givenMonth(array|int|string $mn = [1]): bool
    {
        return $this->searchData($mn, 'm');
    }

    /**
     * Runs a console command or a list of them at the specified minute (0-60) of the hour or a list of them
     *
     * Выполняет консольную команду или их список в указанную минуту (0-60) часа или их перечень.
     */
    protected function givenMinutes(array|string $minutes = [0], array|string $commands = []): bool
    {
        return $this->searchData($minutes, 'i', $commands);
    }

    /**
     * Checking the current year for a leap year.
     *
     * Проверка текущего года на високосный.
     */
    protected function isLeapYear(): bool
    {
        return ((int)$this->getDate()->format('L')) === 1;
    }

    /**
     * Returns the check result for the time before noon.
     *
     * Возвращает результат проверки на время до полудня.
     */
    protected function isAm(): bool
    {
        return $this->getDate()->format('a') === 'am';
    }

    /**
     * Returns the check result for the afternoon time.
     *
     * Возвращает результат проверки на время после полудня.
     */
    protected function isPm(): bool
    {
        return $this->getDate()->format('a') === 'pm';
    }

    /**
     * Returns the result of checking that the current date is Monday.
     *
     * Возвращает результат проверки на то, что по текущей дате - понедельник.
     */
    protected function givenMonday(): bool
    {
        return $this->givenWeeklyDay(1);
    }

    /**
     * Returns the result of checking that the current date is Tuesday.
     *
     * Возвращает результат проверки на то, что по текущей дате - вторник.
     */
    protected function givenTuesday(): bool
    {
        return $this->givenWeeklyDay(2);
    }

    /**
     * Returns the result of checking that the current date is Wednesday.
     *
     * Возвращает результат проверки на то, что по текущей дате - среда.
     */
    protected function givenWednesday(): bool
    {
        return $this->givenWeeklyDay(3);
    }

    /**
     * Returns the result of checking that the current date is Thursday.
     *
     * Возвращает результат проверки на то, что по текущей дате - четверг.
     */
    protected function givenThursday(): bool
    {
        return $this->givenWeeklyDay(4);
    }

    /**
     * Returns the result of checking that the current date is Friday.
     *
     * Возвращает результат проверки на то, что по текущей дате - пятница.
     */
    protected function givenFriday(): bool
    {
        return $this->givenWeeklyDay(5);
    }

    /**
     * Returns the result of checking that the current date is Saturday.
     *
     * Возвращает результат проверки на то, что по текущей дате - суббота.
     */
    protected function givenSaturday(): bool
    {
        return $this->givenWeeklyDay(6);
    }

    /**
     * Returns the result of checking that the current date is Sunday.
     *
     * Возвращает результат проверки на то, что по текущей дате - воскресенье.
     */
    protected function givenSunday(): bool
    {
        return $this->givenWeeklyDay(7);
    }

    /**
     * Compares the current date with a sample.
     *
     * Сравнивает текущую дату с образцом.
     */
    protected function byPattern(string $format = 'Y-m-d H:i:s', string $date = '0000-00-00 00:00:00', array|string $commands = []): bool
    {
        if ($this->getDate()->format($format) === $date) {
            $this->execute($commands);
            return true;
        }
        return false;
    }

    /**
     * Returns the result of comparing the current day to match the New Year.
     *
     * Возвращает результат сравнения текущего дня на совпадение с Новым Годом.
     */
    protected function inNewYearDay(): bool
    {
        return $this->byPattern('m-d', '12-31');
    }

    /**
     * Returns the result of comparing the current day with the specified day of the week (1-7).
     *
     * Возвращает результат сравнения текущего дня с указанным днём недели (1-7).
     */
    protected function givenWeeklyDay(int $number): bool
    {
        return ((int)$this->getDate()->format('N')) === $number;
    }

    /**
     * Returns the result of comparing the current day with the specified day (or list of days) of the month (1-31).
     *
     * Возвращает результат сравнения текущего дня с указанным днём (или перечнем дней) месяца (1-31).
     */
    protected function givenMonthlyDay(array|int $md = [1]): bool
    {
        return $this->searchData($md, "j");
    }

    /**
     * Returns the result of comparing the current day with the specified day (or list of days) of the year (1-365).
     *
     * Возвращает результат сравнения текущего дня с указанным днём (или перечнем дней) года (1-365).
     */
    protected function givenYearDay(array|int $yd = [1]): bool
    {
        return $this->searchData($yd, "z");
    }

    // Set a constant value for the current date.
    // Установка постоянного значения текущей даты.
    protected function setDate(\DateTimeInterface $date): void
    {
        $this->date = $date;
    }

    // Search for a match by date and take action if it is positive.
    // Поиск совпадения по дате и выполнение действий в положительном случае.
    private function searchData(mixed $values, string $format, string|array $commands = []): bool
    {
        if (\is_string($values) || \is_int($values)){
            $values = [$values];
        }
        $date = $this->getDate()->format($format);
        if (\in_array((int)$date, $values, true)) {
            return $this->execute($commands);
        }
        return false;
    }

    private function execute(string|array $commands): bool
    {
        if (\is_string($commands)) {
            return !$this->executeCommand($commands);
        }
        if (\is_array($commands)) {
            $success = true;
            foreach ($commands as $cmd) {
                if (!$this->executeCommand($cmd)) {
                    $success = false;
                }
            }
            return $success;
        }
        return false;
    }

    private function executeCommand(string $commands): int
    {
        \exec($commands, $output, $var);
        echo \implode(PHP_EOL, $output);
        return $var;
    }

    private function getDate(): \DateTimeInterface
    {
        if ($this->date === null) {
            $this->date = new \DateTime('NOW');
        }
        return $this->date;
    }
}
