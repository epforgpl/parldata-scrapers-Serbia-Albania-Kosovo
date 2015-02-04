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

}
