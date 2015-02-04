<?php

App::uses('CakeTime', 'Utility');

class Schedule extends AppModel {

    public function getAllSchedule($name) {
        return $this->find('all');
    }

}
