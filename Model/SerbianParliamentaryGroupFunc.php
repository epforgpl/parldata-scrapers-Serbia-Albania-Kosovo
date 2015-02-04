<?php

class SerbianParliamentaryGroupFunc extends AppModel {

    public $belongsTo = array(
        'SerbianMpsDetail',
        'SerbianParliamentaryGroup'
    );

}
