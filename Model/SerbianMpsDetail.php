<?php

class SerbianMpsDetail extends AppModel {

    public $belongsTo = array(
        'SerbianMenuData'
    );
    public $hasMany = array(
        'SerbianParliamentaryGroupFunc' => array('dependent' => true),
        'SerbianCommitteFunc' => array('dependent' => true),
        'SerbianDelegationMembershipFunc' => array('dependent' => true),
        'SerbianFriendshipFunc' => array('dependent' => true)
    );
    public $hasAndBelongsToMany = array(
        'SerbianResidence',
        'SerbianParliamentaryGroup',
        'SerbianParty',
        'SerbianCommitte',
        'SerbianDelegationMembership',
        'SerbianFriendship',
        'SerbianFunction'
    );

    public function combineToApiArray($data) {

        $name = trim($data['name']);
        $nname = $this->combineSerbianPeopleName($name);
        $nname['image'] = $this->getSerbiaHost . $data['image'];
        $nname['birth_date'] = $data['year_of_birth'];
        $nname['summary'] = $data['occupation'];
        if (!empty($data['www'])) {
            $nname['links'][] = array(
                'note' => 'www',
                'url' => $data['www']
            );
        }
        if (!empty($data['facebook'])) {
            $nname['links'][] = array(
                'note' => 'facebook',
                'url' => $data['facebook']
            );
        }
        if (!empty($data['twitter'])) {
            $nname['links'][] = array(
                'note' => 'twitter',
                'url' => $data['twitter']
            );
        }
        if (!empty($data['biography'])) {
            $nname['biography'] = $data['biography'];
        }
        $nname['sources'] = array(
            array(
                'url' => 'http://www.parlament.gov.rs/national-assembly/composition/members-of-parliament.' . $data['id'] . '.245.html',
            )
        );
//        $nname = array(
//            'name' => $name
//        );
        return array('people' => $nname);
    }

}
