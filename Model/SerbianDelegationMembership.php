<?php

class SerbianDelegationMembership extends AppModel {

    public $hasAndBelongsToMany = array(
        'SerbianMpsDetail'
    );

    public function getIdFromUidAndName($uid, $name) {
        $conditions = array('SerbianDelegationMembership.name' => $name);
        if (!empty($uid)) {
            $conditions = array('SerbianDelegationMembership.uid' => $uid);
        }
        $result = $this->find('first', array(
            'fields' => array('SerbianDelegationMembership.id'),
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
        return $result['SerbianDelegationMembership']['id'];
    }

    public function combineToApiArray($content) {
        $i = 0;
        $valId = 'delegation_' . $content['SerbianDelegationMembership']['uid'];
        $group[$i]['organizations']['id'] = $valId;
        $group[$i]['organizations']['name'] = $content['SerbianDelegationMembership']['name'];
        $group[$i]['organizations']['classification'] = 'delegation';
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
