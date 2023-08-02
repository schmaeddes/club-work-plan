<?php 

class Duty {

    public $id;
    public $eventID;
    public $duty;
    public $startTime;
    public $endTime;
    public $member;
    public $dateOfEntry;

    function __construct($array) {
        $this->id = $array[0];
        $this->eventID = $array[1];
        $this->duty = $array[2];
        $this->startTime = $array[3];
        $this->endTime = $array[4];
        $this->member = $array[5];
        $this->dateOfEntry = $array[6];
    }
}

?>