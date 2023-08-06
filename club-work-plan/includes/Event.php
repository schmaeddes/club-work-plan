<?php

class Event {

    public $id;
    public $name;
    public $description;
    public $date;
    public $creationDate;

    function __construct($array) {
        $dateFormat = get_option('date_format');
        $timeFormat = get_option('time_format');
        $formattedDate = sprintf("%s %s",
            date($dateFormat, strtotime($array[3])),
            date($timeFormat, strtotime($array[3]))
        );

        $this->id = $array[0];
        $this->name = $array[1];
        $this->description = $array[2];
        $this->date = $formattedDate;
        $this->creationDate = $array[4];
    }
}
