<?php

class KosovoCommitteFunc extends AppModel {

    public $belongsTo = array(
        'KosovoMpsDetail',
        'KosovoCommittee'
    );

}
