<?php

class Event {

    public $id;
    public $name;
    public $description;
    public $date;
    public $creationDate;

    function __construct($row) {
        $dateFormat = get_option('date_format');
        $timeFormat = get_option('time_format');
        $formattedDate = sprintf("%s %s",
            date($dateFormat, strtotime($row->date_of_event)),
            date($timeFormat, strtotime($row->date_of_event))
        );

        $this->id = $row->id;
        $this->name = $row->event_name;
        $this->description = $row->event_description;
        $this->date = $formattedDate;
        $this->creationDate = $row->date_of_creation;
    }
}
