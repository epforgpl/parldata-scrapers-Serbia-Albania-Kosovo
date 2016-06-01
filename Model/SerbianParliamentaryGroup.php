<?php

class SerbianParliamentaryGroup extends AppModel {

    public $hasAndBelongsToMany = array(
        'SerbianMpsDetail'
    );

    public function getIdFromUidAndName($uid, $name) {
        $conditions = array('SerbianParliamentaryGroup.name' => $name);
        if (!empty($uid)) {
            $conditions = array('SerbianParliamentaryGroup.uid' => $uid);
        }
        $result = $this->find('first', array(
            'fields' => array('SerbianParliamentaryGroup.id'),
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
        return $result['SerbianParliamentaryGroup']['id'];
    }

    public function combineToApiArray($content) {
        $i = 0;
        $parliamentaryGroupId = 'parliamentary_group_' . $content['SerbianParliamentaryGroup']['uid'];
        $group[$i]['organizations']['id'] = $parliamentaryGroupId;
        $group[$i]['organizations']['name'] = $content['SerbianParliamentaryGroup']['name'];
        $group[$i]['organizations']['classification'] = 'parliamentary_group';
        $group[$i]['organizations']['sources'] = array(
            array(
                'url' => $this->getSerbiaHost . $content['SerbianParliamentaryGroup']['url'],
            )
        );
        if (isset($content['SerbianMpsDetail']) && !empty($content['SerbianMpsDetail'])) {
            foreach ($content['SerbianMpsDetail'] as $key => $mp) {
                $i++;
                $person = $this->checkPeopleExist($mp['name']);
                $group[$i]['memberships']['id'] = $parliamentaryGroupId . '-' . $person;
                $group[$i]['memberships']['label'] = 'MP';
                $group[$i]['memberships']['person_id'] = $person;
                $group[$i]['memberships']['organization_id'] = $parliamentaryGroupId;
                $group[$i]['memberships']['sources'] = array(
                    array(
                        'url' => $this->getSerbiaHost . $content['SerbianParliamentaryGroup']['url'],
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
