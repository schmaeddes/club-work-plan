<?php 
class Duty {

    public $id;
    public $event;
    public $duty;
    public $startTime;
    public $endtime;
    public $member;
    public $dateOfEntry;

    function __construct($array) {
        $this->id = $array[0];
        $this->event = $array[1];
        $this->duty = $array[2];
        $this->startTime = $array[3];
        $this->endtime = $array[4];
        $this->member = $array[5];
        $this->dateOfEntry = $array[6];
    }
}
?>