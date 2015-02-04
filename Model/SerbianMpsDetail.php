<?php

class SerbianMpsDetail extends AppModel {

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
        $nname['id'] = 'mp_' . $this->toCamelCase($name);
        $nname['name'] = $name;
        $name = preg_replace('/\s\-\s/', '-', $name);
        $find_table = array(
            '/ ДОЦ. ДР /',
            '/ Проф. Др /',
            '/ проф. др /',
            '/ ПРОФ. ДР /',
            '/ ДР /',
            '/ Др /',
            '/ др /',
            '/ МР /',
            '/ Мр /',
            '/ мр /'
        );

        $replace_table = array(
            ' dd ',
            ' pd ',
            ' pd ',
            ' pd ',
            ' dr ',
            ' dr ',
            ' dr ',
            ' mgr ',
            ' mgr ',
            ' mgr ',
        );

        $honorific_prefix = array(
            'dd' => 'Доц. Др',
            'pd' => 'Проф. Др',
            'dr' => 'Др',
            'dr' => 'Мр',
        );
        $name = preg_replace($find_table, $replace_table, $name);
        $name = explode(' ', $name);
        foreach ($name as $key => $nn) {
            if (array_key_exists($nn, $honorific_prefix)) {
                $nname['honorific_prefix'] = $honorific_prefix[$nn];
                unset($name[$key]);
            }
        }
        $name = array_values($name);
//        if (count($name) == 2) {
//            $nname['given_name'] = $name[0];
//            //  $nname['last_name'] = $name[1];
//        } else {
        $nname['given_name'] = array_shift($name);
//        $nname['last_name'] = array_pop($name);
        if (count($name) > 0) {
            $nname['additional_name'] = null;
            foreach ($name as $nn) {
                $nname['additional_name'] .= ' ' . $nn;
            }
            $nname['additional_name'] = trim($nname['additional_name']);
        }
//        }
        if (isset($nname['additional_name']) && (is_null($nname['additional_name']) || $nname['additional_name'] == '' || empty($nname['additional_name']))) {
            unset($nname['additional_name']);
        }
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

//        $nname = array(
//            'name' => $name
//        );
        return array('people' => $nname);
    }

}
