<?php

class KosovoMpsActivity extends AppModel {

    public $belongsTo = array(
        'KosovoMpsDetail',
    );

    public function getIdFromUidAndName($id, $name) {
        $conditions = array('KosovoMpsActivity.name' => $name);
        if (!empty($uid)) {
            $conditions = array('KosovoMpsActivity.kosovo_mps_detail_id' => $id);
        }
        $result = $this->find('first', array(
            'fields' => array('KosovoMpsActivity.id'),
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
        return $result['KosovoMpsActivity']['id'];
    }

}
