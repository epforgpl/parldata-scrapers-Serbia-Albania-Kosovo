<?php

class AlbaniaSpeecheIndex extends AppModel {

    public $hasOne = array(
        'AlbaniaDoc',
    );
    public $belongsTo = array(
        'AlbaniaSpecheSession'
    );

}
