<?php
namespace Luxifer\Leboncoin\Datetime;

class LeboncoinDatetime extends \DateTime
{
    /**
     * @param string $date date as displayed on the website
     * @param string $time time as displayed on the website
     */
    public function __construct($date, $time)
    {
        parent::__construct();
        $this->setTimezone(new \DateTimeZone('Europe/Paris'));

        $this->fullMatch($date);

        list($hour, $second) = explode(':', $time);
        $this->setTime($hour, $second);
    }

    /**
     * Transform the date part into a php on
     *
     * @param  string $key date as displayed on the website
     */
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

    /**
     * Transform the month displayed on the website into the corresponding integer value
     * @param  string $key month
     * @return integer      month
     */
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

    /**
     * Format the object to a string corresponding at the SQL format
     *
     * @return string datetime
     */
    public function __toString()
    {
        return $this->format('Y-m-d H:i:s');
    }
}
