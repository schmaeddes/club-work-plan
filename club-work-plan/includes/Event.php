<?php

class Event {

    public $id;
    public $name;
    public $description;
    public $date;
    public $creationDate;

    function __construct($array) {
        $this->id = $array[0];
        $this->name = $array[1];
        $this->description = $array[2];
        $this->date = $array[3];
        $this->creationDate = $array[4];
    }
}
