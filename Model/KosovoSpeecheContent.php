<?php

class KosovoSpeecheContent extends AppModel {

    public $hasMany = array(
        'KosovoPdf',
        'KosovoTxt'
    );

}
