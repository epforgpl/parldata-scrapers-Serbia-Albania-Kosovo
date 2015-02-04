<?php

class SerbianResidence extends AppModel {

    public $hasAndBelongsToMany = array(
        'SerbianMpsDetail'
    );

    public function getIdFromUidAndName($uid, $name) {
        $conditions = array('SerbianResidence.name' => $name);
        if (!empty($uid)) {
            $conditions = array('SerbianResidence.uid' => $uid);
        }
        $result = $this->find('first', array(
            'fields' => array('SerbianResidence.id'),
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
        return $result['SerbianResidence']['id'];
    }

}
