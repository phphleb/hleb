<?php

declare(strict_types=1);

namespace Hleb\Main\Commands;

class MainLaunchTask  extends \MainTask
{
    private $date = null;


    protected function everyMinute($cms = []) // XX:XX:00
    {
        $this->run($cms);
    }


    protected function everyHour($cms = []) // XX:00:00
    {
        if ($this->getDate()->format('i') === '00')  $this->run($cms);
    }


    protected function everyDay($cms = []) // 00:00:00
    {
        if ($this->getDate()->format('H:i') === '00:00') $this->run($cms);
    }


    protected function every5Minutes($cmds = [])
    {
        $date = $this->getDate()->format('i');
        if ($date[1] == '0' || $date[1] === '5')  $this->run($cmds);
    }


    protected function every10Minutes($cmds = [])
    {
        $date = $this->getDate()->format('i');
        if ($date[1] == '0') $this->run($cmds);
    }


    protected function every15Minutes($cmds = [])
    {
        $date = $this->getDate()->format('i');
        if (in_array($date, ['00', '15', '30', '45'])) $this->run($cmds);
    }


    protected function every20Minutes($cmds = [])
    {
        $date = $this->getDate()->format('i');
        if (in_array($date, ['00', '20', '40'])) $this->run($cmds);
    }


    protected function givenMinutes($m = [0], $cmds = [])  // 0-60
    {
        return $this->searchData($m,  'i', $cmds);
    }


    protected function givenHour($h = [0])  // 0-24
    {
        return $this->searchData($h, 'H');
    }


    protected function givenMonth($mn = [1])  // 1-12
    {
        return $this->searchData($mn, 'm');
    }


    protected function givenWeeklyDay($wd = [1])  // 1-7
    {
        return $this->searchData($wd, 'N');
    }

    protected function givenMonthlyDay($md = [1])  // 1-31
    {
        return $this->searchData($md, 'j');
    }

    protected function givenYearDay($yd = [1])  // 1-365
    {
        return $this->searchData($yd, 'z');
    }

    protected function changeLeapYear()  // Високосный год
    {
        return $this->getDate()->format('L') === 1;
    }

    protected function changeAm()  // До полудня
    {
        return $this->getDate()->format('a') === 'am';
    }

    protected function changePm()  // После полудня
    {
        return $this->getDate()->format('a') === 'ap';
    }

    // Понедельник
    protected function givenMonday()
    {
        return $this->givenWeeklyDay(1);
    }

    // Вторник
    protected function givenTuesday()
    {
        return $this->givenWeeklyDay(2);
    }

    // Среда
    protected function givenWednesday()
    {
        return $this->givenWeeklyDay(3);
    }

    // Четверг
    protected function givenThursday()
    {
        return $this->givenWeeklyDay(4);
    }

    // Пятница
    protected function givenFriday()
    {
        return $this->givenWeeklyDay(5);
    }

    // Суббота
    protected function givenSaturday()
    {
        return $this->givenWeeklyDay(6);
    }

    // Воскресенье
    protected function givenSunday()
    {
        return $this->givenWeeklyDay(7);
    }


    protected function byPattern(string $format = 'Y-m-d H:i:s', string $date = '0000-00-00 00:00:00', $cmds = [])
    {
        if ($this->getDate()->format($format) === $date){
            $this->run($cmds);
            return true;
        }
        return false;
    }

    protected function inNewYearDay()
    {
        return $this->byPattern('m-d', '12-31');
    }

    protected function inHalloweenDay()
    {
        return $this->byPattern('m-d', '10-31');
    }

    protected function setDate(\DateTime $date){
        $this->date = $date;
    }

    private function searchData($values, string $format, $cms = [])
    {
        if(is_string($values) || is_int($values)) $values = [$values];
        $date = $this->getDate()->format($format);
        if(in_array(intval($date), $values)){
            $this->run($cms);
            return true;
        }
        return false;
    }


    /**
     * @param string|array $cms
     * @return bool
     */
    private function run($cms)
    {
        if (is_string($cms)){
            return $this->execute_command($cms);
        }
        if (is_array($cms)) {
            $success = true;
            foreach ($cms as $cmd) {
                if(!$this->execute_command($cmd)) $success = false;
            }
            return $success;
        }
        return false;
    }

    private function execute_command(string $cmd)
    {
        exec($cmd, $output, $var);
        echo implode("\n", $output);
        return $var;
    }

    private function getDate()
    {
        if(is_null($this->date)){
            $this->date = new \DateTime('NOW');
        }
        return $this->date;
    }


}

