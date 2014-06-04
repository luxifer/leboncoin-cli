<?php
namespace Luxifer\Leboncoin\Datetime;

class LeboncoinDatetime extends \DateTime
{
    public function __construct($date, $time)
    {
        parent::__construct();
        $this->setTimezone(new \DateTimeZone('Europe/Paris'));

        $this->fullMatch($date);

        list($hour, $second) = explode(':', $time);
        $this->setTime($hour, $second);
    }

    private function fullMatch($key)
    {
        $config = array(
            "Aujourd'hui" => 'now',
            'Hier'        => '-1 day'
        );

        if (!isset($config[$key])) {
            list($day, $month) = explode(' ', $key);

            $this->setDate(date('Y'), $this->monthMatch($month), (int) $day);

            return;
        }

        $this->setTimestamp(strtotime($config[$key]));
    }

    private function monthMatch($key)
    {
        $months = array(
            'janvier'   => 1,
            'février'   => 2,
            'mars'      => 3,
            'avril'     => 4,
            'mai'       => 5,
            'juin'      => 6,
            'juillet'   => 7,
            'août'      => 8,
            'septembre' => 9,
            'octobre'   => 10,
            'novembre'  => 11,
            'décembre'  => 12
        );

        return $months[$key];
    }
}
