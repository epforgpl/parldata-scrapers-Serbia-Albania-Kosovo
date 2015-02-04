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

}
