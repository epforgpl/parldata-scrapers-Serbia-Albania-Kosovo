<?php

class KosovoMpsAddress extends AppModel {

    public $belongsTo = array(
        'KosovoMpsDetail',
    );

    public function getIdFromUidAndName($id, $name) {
        $conditions = array('KosovoMpsAddress.name' => $name);
        if (!empty($uid)) {
            $conditions = array('KosovoMpsAddress.kosovo_mps_detail_id' => $id);
        }
        $result = $this->find('first', array(
            'fields' => array('KosovoMpsAddress.id'),
            'conditions' => $conditions,
            'recursive' => -1
        ));

        if (!$result) {
            $this->create();
            $this->set(array(
                'kosovo_mps_detail_id' => $id,
                'name' => $name
            ));
            $this->save();
            return $this->getLastInsertID();
        }
        return $result['KosovoMpsAddress']['id'];
    }

}
