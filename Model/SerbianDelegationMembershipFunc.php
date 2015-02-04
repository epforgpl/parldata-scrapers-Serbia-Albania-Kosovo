<?php

class SerbianDelegationMembershipFunc extends AppModel {

    public $belongsTo = array(
        'SerbianMpsDetail',
        'SerbianDelegationMembership'
    );

}
