<?php

namespace includes;

class Duty {

    public $id;
    public $eventID;
    public $duty;
    public $startTime;
    public $endTime;
    public $member;
    public $dateOfEntry;

    private function __construct() {
		// empty
    }

    public static function from_row($row): Duty {
        $instance = new self();
        $instance->id = $row->id;
        $instance->eventID = $row->event_id;
        $instance->duty = $row->duty;
        $instance->startTime = $row->start_time;
        $instance->endTime = $row->end_time;
        $instance->member = $row->member;
        $instance->dateOfEntry = $row->date_of_entry;

        return $instance;
    }

    public static function from_array($array): Duty {
        $instance = new self();
        $instance->id = $array[0];
        $instance->eventID = $array[1];
        $instance->duty = $array[2];
        $instance->startTime = $array[3];
        $instance->endTime = $array[4];
        $instance->member = $array[5];
        $instance->dateOfEntry = $array[6];

        return $instance;
    }
}
