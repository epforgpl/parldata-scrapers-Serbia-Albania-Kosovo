<?php

class SerbianCommitteFunc extends AppModel {

    public $belongsTo = array(
        'SerbianMpsDetail',
        'SerbianCommitte'
    );

}
