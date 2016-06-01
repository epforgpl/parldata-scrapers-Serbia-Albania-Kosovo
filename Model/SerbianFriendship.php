<?php

class SerbianFriendship extends AppModel {

    public $hasAndBelongsToMany = array(
        'SerbianMpsDetail'
    );

    public function getIdFromUidAndName($uid, $name) {
        $conditions = array('SerbianFriendship.name' => $name);
        if (!empty($uid)) {
            $conditions = array('SerbianFriendship.uid' => $uid);
        }
        $result = $this->find('first', array(
            'fields' => array('SerbianFriendship.id'),
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
        return $result['SerbianFriendship']['id'];
    }

    public function combineToApiArray($content) {
        $i = 0;
        $valId = 'friendship_group_' . $content['SerbianFriendship']['uid'];
        $group[$i]['organizations']['id'] = $valId;
        $group[$i]['organizations']['name'] = $content['SerbianFriendship']['name'];
        $group[$i]['organizations']['classification'] = 'friendship_group';
        $group[$i]['organizations']['sources'] = array(
            array(
                'url' => $this->getSerbiaHost . trim($content['SerbianFriendship']['url']),
            )
        );
        if (isset($content['SerbianMpsDetail']) && !empty($content['SerbianMpsDetail'])) {
            foreach ($content['SerbianMpsDetail'] as $key => $mp) {
                $i++;
                $person = $this->checkPeopleExist($mp['name']);
                $group[$i]['memberships']['id'] = $valId . '-' . $person;
                $group[$i]['memberships']['label'] = 'MP';
                $group[$i]['memberships']['person_id'] = $person;
                $group[$i]['memberships']['organization_id'] = $valId;
                $group[$i]['memberships']['sources'] = array(
                    array(
                        'url' => $this->getSerbiaHost . trim($content['SerbianFriendship']['url']),
                    ),
                    array(
                        'url' => 'http://www.parlament.gov.rs/national-assembly/composition/members-of-parliament.' . $mp['id'] . '.245.html',
                    )
                );
            }
        }
        return $group;
    }

}
