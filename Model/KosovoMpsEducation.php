<?php

class KosovoMpsEducation extends AppModel {

    public $belongsTo = array(
        'KosovoMpsDetail',
    );

    public function getIdFromUidAndName($id, $name) {
        $conditions = array('KosovoMpsEducation.name' => $name);
        if (!empty($uid)) {
            $conditions = array('KosovoMpsEducation.kosovo_mps_detail_id' => $id);
        }
        $result = $this->find('first', array(
            'fields' => array('KosovoMpsEducation.id'),
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
        return $result['KosovoMpsEducation']['id'];
    }

}
