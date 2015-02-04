<?php

class SerbianFunction extends AppModel {

    public $hasAndBelongsToMany = array(
        'SerbianMpsDetail'
    );

    public function getIdFromUidAndName($uid, $name) {
        $conditions = array('SerbianFunction.name' => $name);
        if (!empty($uid)) {
            $conditions = array('SerbianFunction.uid' => $uid);
        }
        $result = $this->find('first', array(
            'fields' => array('SerbianFunction.id'),
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
        return $result['SerbianFunction']['id'];
    }

    public function combineToApiArray($content) {
        $i = 0;
        if (isset($content['SerbianMpsDetail']) && !empty($content['SerbianMpsDetail'])) {
            $type = $classification = null;
            if (preg_match('/Председник/i', $content['SerbianFunction']['name'], $matches)) {
                $type = 'speaker';
                $classification = 'speaker';
            }
            if (preg_match('/Потпредседник/i', $content['SerbianFunction']['name'], $matches)) {
                $type = 'deputy';
                $classification = 'deputy_speaker';
            }
            foreach ($content['SerbianMpsDetail'] as $key => $mp) {
                $i++;
                $chamber = $this->getChamber($mp['created']);
                $person = $this->checkPeopleExist($mp['name']);
                $group[$i]['memberships']['id'] = $chamber . '-speaker-' . $person;
                $group[$i]['memberships']['organization_id'] = $chamber . '-' . $type;
                $group[$i]['memberships']['person_id'] = $person;
//                $group['toMemberships'][$key]['memberships']['all'] = $mp;

                $group[$i]['organizations']['id'] = $chamber . '-' . $type;
                $group[$i]['organizations']['classification'] = $classification;
                $group[$i]['organizations']['parent_id'] = $chamber;
                $group[$i]['organizations']['name'] = $content['SerbianFunction']['name'];
            }
        }

        return $group;
    }

}
