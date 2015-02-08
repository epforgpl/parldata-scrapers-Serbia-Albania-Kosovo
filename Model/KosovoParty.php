<?php

class KosovoParty extends AppModel {

    public $hasAndBelongsToMany = array(
        'KosovoMpsDetail'
    );

    public function getIdFromUidAndName($uid, $name) {
        $conditions = array('KosovoParty.name' => $name);
        if (!empty($uid)) {
            $conditions = array('KosovoParty.uid' => $uid);
        }
        $result = $this->find('first', array(
            'fields' => array('KosovoParty.id'),
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
        return $result['KosovoParty']['id'];
    }

    public function combineToApiArray($content) {
        $i = 0;
        $partyId = 'party_' . $this->toCamelCase($content['KosovoParty']['name']);
        $party[$i]['organizations']['id'] = $partyId;
        $party[$i]['organizations']['name'] = $content['KosovoParty']['name'];
        $party[$i]['organizations']['classification'] = 'party';
        if (!empty($data['KosovoParty']['shortcut'])) {
            $party['organizations']['other_names'] = array(
                array(
                    'name' => $data['KosovoParty']['shortcut'],
                    'note' => 'shortcut'
                )
            );
        }
        $l = $i;
        if (isset($content['KosovoMpsDetail']) && !empty($content['KosovoMpsDetail'])) {
            foreach ($content['KosovoMpsDetail'] as $key => $mp) {
                $i++;
//                $person = $i;
                $person = $this->checkKosovoPeopleExist($mp['name'], $mp['KosovoMpsIndex']['kosovo_mps_menu_id']);
                $party[$i]['memberships']['id'] = $partyId . '-' . $person;
                $party[$i]['memberships']['label'] = 'MP';
                $party[$i]['memberships']['person_id'] = $person;
                $party[$i]['memberships']['organization_id'] = $partyId;
                if (!empty($mp['KosovoMpsIndex']['start_date'])) {
                    $party[$i]['memberships']['start_date'] = $mp['KosovoMpsIndex']['start_date'];
                }
                if (!empty($mp['KosovoMpsIndex']['end_date'])) {
                    $party[$i]['memberships']['end_date'] = $mp['KosovoMpsIndex']['end_date'];
                }
                $party[$l]['organizations']['sources'][] = array(
                    'url' => $this->getKosovoHost . '/' . $mp['KosovoMpsIndex']['url'],
                );
//                break;
            }
        }
        return $party;
    }

}
