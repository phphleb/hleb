<?php

declare(strict_types=1);

/*
 * Helper class for executing console commands based on time and depending on the properties of the current server time.
 * This assumes that the startup was initiated by `cron`, so everyMinute() output is always executed.
 *
 * Вспомогательный класс для выполнения консольных команд по времени и в зависимости от свойств текущего серверного времени.
 * Подразумевается, что запуск инициирован `cron`, поэтому поминутный вывод everyMinute() выполняется всегда.
 */

namespace Hleb\Main\Commands;

use Hleb\Scheme\App\Commands\MainTask;

class MainLaunchTask extends MainTask
{
    private $date = null;

    /**
     * Executes a console command or a list of them.
     *
     * Выполняет консольную команду или их список.
     * @param string|array $commands
     */
    protected function everyMinute($commands = []) {
        $this->run($commands);
    }

    /**
     * Executes a console command or a list of them at the beginning of every hour.
     *
     * Выполняет консольную команду или их список в начале каждого часа.
     * @param string|array $commands
     */
    protected function everyHour($commands = []) {
        // XX:00:00
        if ($this->getDate()->format('i') === '00') $this->run($commands);
    }

    /**
     * Executes a console command or a list of them at the beginning of each day.
     *
     * Выполняет консольную команду или их список в начале каждого дня.
     * @param string|array $commands
     */
    protected function everyDay($commands = []) {
        // 00:00:00
        if ($this->getDate()->format('H:i') === '00:00') $this->run($commands);
    }

    /**
     * Executes a console command or a list of them every five minutes.
     *
     * Выполняет консольную команду или их список каждые пять минут.
     * @param string|array $commands
     */
    protected function every5Minutes($commands = []) {
        $date = $this->getDate()->format('i');
        if ($date[1] == '0' || $date[1] === '5') $this->run($commands);
    }

    /**
     * Executes a console command or a list of them every ten minutes.
     *
     * Выполняет консольную команду или их список каждые десять минут.
     * @param string|array $commands
     */
    protected function every10Minutes($commands = []) {
        $date = $this->getDate()->format('i');
        if ($date[1] == '0') $this->run($commands);
    }

    /**
     * Executes a console command or a list of them every fifteen minutes.
     *
     * Выполняет консольную команду или их список каждые пятнадцать минут.
     * @param string|array $commands
     */
    protected function every15Minutes($commands = []) {
        $date = $this->getDate()->format('i');
        if (in_array($date, ['00', '15', '30', '45'])) $this->run($commands);
    }

    /**
     * Executes a console command or a list of them every twenty minutes.
     *
     * Выполняет консольную команду или их список каждые двадцать минут.
     * @param string|array $commands
     */
    protected function every20Minutes($commands = []) {
        $date = $this->getDate()->format('i');
        if (in_array($date, ['00', '20', '40'])) $this->run($commands);
    }

    /**
     * Selected time in hours 0-24
     *
     * В выбранное время часа 0-24
     * @param array|int|string $h
     * @return bool
     */
    protected function givenHour($h = [0]) {
        return $this->searchData($h, 'H');
    }

    /**
     * In the selected month 1-12
     *
     * В выбранный месяц 1-12
     * @param array|int|string $mn
     * @return bool
     */
    protected function givenMonth($mn = [1]) {
        return $this->searchData($mn, 'm');
    }

    /**
     * Runs a console command or a list of them at the specified minute (0-60) of the hour or a list of them
     *
     * Выполняет консольную команду или их список в указанную минуту (0-60) часа или их перечень.
     * @param string|array $minutes
     * @param string|array $commands
     * @return bool
     */
    protected function givenMinutes($minutes = [0], $commands = []) {
        return $this->searchData($minutes, 'i', $commands);
    }

    /**
     * Checking the current year for a leap year.
     *
     * Проверка текущего года на високосный.
     * @return bool
     */
    protected function changeLeapYear() {
        return intval($this->getDate()->format('L')) === 1;
    }

    /**
     * Returns the check result for the time before noon.
     *
     * Возвращает результат проверки на время до полудня.
     * @return bool
     */
    protected function changeAm() {
        return $this->getDate()->format('a') === 'am';
    }

    /**
     * Returns the check result for the afternoon time.
     *
     * Возвращает результат проверки на время после полудня.
     * @return bool
     */
    protected function changePm() {
        return $this->getDate()->format('a') === 'pm';
    }

    /**
     * Returns the result of checking that the current date is Monday.
     *
     * Возвращает результат проверки на то, что по текущей дате - понедельник.
     * @return bool
     */
    protected function givenMonday() {
        return $this->givenWeeklyDay(1);
    }

    /**
     * Returns the result of checking that the current date is Tuesday.
     *
     * Возвращает результат проверки на то, что по текущей дате - вторник.
     * @return bool
     */
    protected function givenTuesday() {
        return $this->givenWeeklyDay(2);
    }

    /**
     * Returns the result of checking that the current date is Wednesday.
     *
     * Возвращает результат проверки на то, что по текущей дате - среда.
     * @return bool
     */
    protected function givenWednesday() {
        return $this->givenWeeklyDay(3);
    }

    /**
     * Returns the result of checking that the current date is Thursday.
     *
     * Возвращает результат проверки на то, что по текущей дате - четверг.
     * @return bool
     */
    protected function givenThursday() {
        return $this->givenWeeklyDay(4);
    }

    /**
     * Returns the result of checking that the current date is Friday.
     *
     * Возвращает результат проверки на то, что по текущей дате - пятница.
     * @return bool
     */
    protected function givenFriday() {
        return $this->givenWeeklyDay(5);
    }

    /**
     * Returns the result of checking that the current date is Saturday.
     *
     * Возвращает результат проверки на то, что по текущей дате - суббота.
     * @return bool
     */
    protected function givenSaturday() {
        return $this->givenWeeklyDay(6);
    }

    /**
     * Returns the result of checking that the current date is Sunday.
     *
     * Возвращает результат проверки на то, что по текущей дате - воскресенье.
     * @return bool
     */
    protected function givenSunday() {
        return $this->givenWeeklyDay(7);
    }

    /**
     * Compares the current date with a sample.
     *
     * Сравнивает текущую дату с образцом.
     * @param string $format
     * @param string $date
     * @param string|array $commands
     * @return bool
     */
    protected function byPattern(string $format = 'Y-m-d H:i:s', string $date = '0000-00-00 00:00:00', $commands = []) {
        if ($this->getDate()->format($format) === $date) {
            $this->run($commands);
            return true;
        }
        return false;
    }

    /**
     * Returns the result of comparing the current day to match the New Year.
     *
     * Возвращает результат сравнения текущего дня на совпадение с Новым Годом.
     * @return bool
     */
    protected function inNewYearDay() {
        return $this->byPattern('m-d', '12-31');
    }

    /**
     * Returns the result of comparing the current day to match Halloween.
     *
     * Возвращает результат сравнения текущего дня на совпадение с Хэллоуином.
     * @return bool
     */
    protected function inHalloweenDay() {
        return $this->byPattern('m-d', '10-31');
    }

    /**
     * Returns the result of comparing the current day with the specified day of the week (1-7).
     *
     * Возвращает результат сравнения текущего дня с указанным днём недели (1-7).
     * @param int $number
     * @return bool
     */
    protected function givenWeeklyDay(int $number){
        return $this->getDate()->format('N') === $number;
    }

    /**
     * Returns the result of comparing the current day with the specified day (or list of days) of the month (1-31).
     *
     * Возвращает результат сравнения текущего дня с указанным днём (или перечнем дней) месяца (1-31).
     * @param int|array $md
     * @return bool
     */
    protected function givenMonthlyDay($md = [1]) {
        return $this->searchData($md, "j");
    }

    /**
     * Returns the result of comparing the current day with the specified day (or list of days) of the year (1-365).
     *
     * Возвращает результат сравнения текущего дня с указанным днём (или перечнем дней) года (1-365).
     * @param int|array $yd
     * @return bool
     */
    protected function givenYearDay($yd = [1]) {
        return $this->searchData($yd, "z");
    }

    // Set a constant value for the current date.
    // Установка постоянного значения текущей даты.
    protected function setDate(\DateTime $date) {
        $this->date = $date;
    }

    // Search for a match by date and take action if it is positive.
    // Поиск совпадения по дате и выполнение действий в положительном случае.
    private function searchData($values, string $format, $commands = []) {
        if (is_string($values) || is_int($values)) $values = [$values];
        $date = $this->getDate()->format($format);
        if (in_array(intval($date), $values)) {
            $this->run($commands);
            return true;
        }
        return false;
    }

    /**
     * @param string|array $commands
     * @return bool
     */
    private function run($commands) {
        if (is_string($commands)) {
            return $this->executeCommand($commands);
        }
        if (is_array($commands)) {
            $success = true;
            foreach ($commands as $cmd) {
                if (!$this->executeCommand($cmd)) $success = false;
            }
            return $success;
        }
        return false;
    }

    private function executeCommand(string $commands) {
        exec($commands, $output, $var);
        echo implode(PHP_EOL, $output);
        return $var;
    }

    private function getDate() {
        if (is_null($this->date)) {
            $this->date = new \DateTime('NOW');
        }
        return $this->date;
    }


}

