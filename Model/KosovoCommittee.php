<?php

class KosovoCommittee extends AppModel {

    public $hasAndBelongsToMany = array(
        'KosovoMpsDetail'
    );
    public $hasMany = array(
        'KosovoCommitteFunc' => array('dependent' => true),
    );

    public function getIdFromUidAndName($uid, $name) {
        $conditions = array('KosovoCommittee.name' => $name);
        if (!empty($uid)) {
            $conditions = array('KosovoCommittee.uid' => $uid);
        }
        $result = $this->find('first', array(
            'fields' => array('KosovoCommittee.id'),
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
        return $result['KosovoCommittee']['id'];
    }

    public function combineToApiArray($content) {
        $i = 0;
        $committeeId = 'committee_' . $content['KosovoCommittee']['uid'];
        $committee[$i]['organizations']['id'] = $committeeId;
        $committee[$i]['organizations']['name'] = $content['KosovoCommittee']['name'];
        $committee[$i]['organizations']['classification'] = 'committee';
        if (!empty($content['KosovoCommittee']['url'])) {
            $committee[$i]['organizations']['sources'] = array(
                array(
                    'url' => $this->getKosovoHost . '/' . trim($content['KosovoCommittee']['url']),
                )
            );
        }
//
        if (isset($content['KosovoMpsDetail']) && !empty($content['KosovoMpsDetail'])) {
            foreach ($content['KosovoMpsDetail'] as $key => $mp) {
                $i++;
//                $person = $i;
                $person = $this->checkKosovoPeopleExist($mp['name'], $mp['KosovoMpsIndex']['kosovo_mps_menu_id']);
                $committee[$i]['memberships']['id'] = $committeeId . '-' . $person;
                if (isset($mp['KosovoCommitteFunc']) && !empty($mp['KosovoCommitteFunc'])) {
                    foreach ($mp['KosovoCommitteFunc'] as $func) {
                        if ($func['kosovo_committee_id'] == $content['KosovoCommittee']['id']) {
                            $committee[$i]['memberships']['label'] = $func['name'];
                        }
                    }
                }

                $committee[$i]['memberships']['person_id'] = $person;
                $committee[$i]['memberships']['organization_id'] = $committeeId;
                if (!empty($mp['KosovoMpsIndex']['start_date'])) {
                    $committee[$i]['memberships']['start_date'] = $mp['KosovoMpsIndex']['start_date'];
                }
                if (!empty($mp['KosovoMpsIndex']['end_date'])) {
                    $committee[$i]['memberships']['end_date'] = $mp['KosovoMpsIndex']['end_date'];
                }
                if (!empty($content['KosovoCommittee']['url'])) {
                    $committee[$i]['memberships']['sources'][] = array(
                        'url' => $this->getKosovoHost . '/' . trim($content['KosovoCommittee']['url']),
                    );
                }
                if (!empty($mp['KosovoMpsIndex']['url'])) {
                    $committee[$i]['memberships']['sources'][] = array(
                        'url' => $this->getKosovoHost . '/' . trim($mp['KosovoMpsIndex']['url']),
                    );
                }
            }
        }
        return $committee;
    }

}
