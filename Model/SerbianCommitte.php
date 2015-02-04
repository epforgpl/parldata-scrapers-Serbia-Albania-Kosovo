<?php

class SerbianCommitte extends AppModel {

    public $hasAndBelongsToMany = array(
        'SerbianMpsDetail'
    );

    public function getIdFromUidAndName($uid, $name) {
        $conditions = array('SerbianCommitte.name' => $name);
        if (!empty($uid)) {
            $conditions = array('SerbianCommitte.uid' => $uid);
        }
        $result = $this->find('first', array(
            'fields' => array('SerbianCommitte.id'),
            'conditions' => $conditions,
            'recursive' => -1
        ));

        if (!$result) {
            $this->create();
            $this->set(array(
                'uid' => $uid,
                'name' => $name
            ));
            $this->save();
            return $this->getLastInsertID();
        }
        return $result['SerbianCommitte']['id'];
    }

    public function combineToApiArray($content) {
        $i = 0;
        $valId = 'committee_' . $content['SerbianCommitte']['uid'];
        $group[$i]['organizations']['id'] = $valId;
        $group[$i]['organizations']['name'] = $content['SerbianCommitte']['name'];
        $group[$i]['organizations']['classification'] = 'committee';
        if (isset($content['SerbianMpsDetail']) && !empty($content['SerbianMpsDetail'])) {
            foreach ($content['SerbianMpsDetail'] as $key => $mp) {
                $i++;
                $person = $this->checkPeopleExist($mp['name']);
                $group[$i]['memberships']['id'] = $valId . '-' . $person;
                $group[$i]['memberships']['label'] = 'MP';
                $group[$i]['memberships']['person_id'] = $person;
                $group[$i]['memberships']['organization_id'] = $valId;
//                $group['toMemberships'][$key]['memberships']['all'] = $mp;
            }
        }
        return $group;
    }

}
