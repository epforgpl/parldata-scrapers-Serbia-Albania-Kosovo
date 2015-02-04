<?php

class KosovoMpsDetail extends AppModel {

    public $belongsTo = array(
        'KosovoMpsIndex'
    );
    public $hasMany = array(
        'KosovoMpsPersonalData' => array('dependent' => true),
        'KosovoMpsEducation' => array('dependent' => true),
        'KosovoMpsActivity' => array('dependent' => true),
        'KosovoMpsLanguage' => array('dependent' => true),
        'KosovoMpsAddress' => array('dependent' => true),
    );
    public $hasAndBelongsToMany = array(
        'KosovoParliamentaryGroup',
        'KosovoParty',
    );

}
