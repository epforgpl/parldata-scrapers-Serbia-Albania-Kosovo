<?php

class KosovoParliamentaryGroup extends AppModel {

    public $hasAndBelongsToMany = array(
        'KosovoMpsDetail'
    );

    public function getIdFromUidAndName($uid, $name) {
        $conditions = array('KosovoParliamentaryGroup.name' => $name);
        if (!empty($uid)) {
            $conditions = array('KosovoParliamentaryGroup.uid' => $uid);
        }
        $result = $this->find('first', array(
            'fields' => array('KosovoParliamentaryGroup.id'),
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
        return $result['KosovoParliamentaryGroup']['id'];
    }

    public function combineToApiArray($content) {
        $i = 0;
        $groupId = 'parliamentary_group_' . $content['KosovoParliamentaryGroup']['uid'];
        $group[$i]['organizations']['id'] = $groupId;
        $group[$i]['organizations']['name'] = $content['KosovoParliamentaryGroup']['name'];
        $group[$i]['organizations']['classification'] = 'parliamentary_group';
        $group[$i]['organizations']['sources'] = array(
            array(
                'url' => $this->getKosovoHost . '/' . $content['KosovoParliamentaryGroup']['url'],
            )
        );

        if (isset($content['KosovoMpsDetail']) && !empty($content['KosovoMpsDetail'])) {
            foreach ($content['KosovoMpsDetail'] as $key => $mp) {
                $i++;
//                $person = $i;
                $person = $this->checkKosovoPeopleExist($mp['name'], $mp['KosovoMpsIndex']['kosovo_mps_menu_id']);
                $group[$i]['memberships']['id'] = $groupId . '-' . $person;
                $group[$i]['memberships']['label'] = 'MP';
                $group[$i]['memberships']['person_id'] = $person;
                $group[$i]['memberships']['organization_id'] = $groupId;
                if (!empty($mp['KosovoMpsIndex']['start_date'])) {
                    $group[$i]['memberships']['start_date'] = $mp['KosovoMpsIndex']['start_date'];
                }
                if (!empty($mp['KosovoMpsIndex']['end_date'])) {
                    $group[$i]['memberships']['end_date'] = $mp['KosovoMpsIndex']['end_date'];
                }
//                break;
            }
        }
        return $group;
    }

}
