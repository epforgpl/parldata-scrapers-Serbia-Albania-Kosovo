<?php

class SerbianDelegate extends AppModel {

    public $belongsTo = array(
        'SerbianMenuData'
    );

    public function combineToApiArray($content) {
        $personId = 'mp_' . $this->toCamelCase($content['SerbianDelegate']['name']);
        $chamberId = 'chamber_' . $content['SerbianMenuData']['start_date'];
        $data['memberships']['id'] = $chamberId . '-' . $personId;
        $data['memberships']['label'] = 'MP';
        $data['memberships']['person_id'] = $personId;
        $data['memberships']['organization_id'] = $chamberId;

        if (!empty($content['SerbianDelegate']['start_date'])) {
            $data['memberships']['start_date'] = $content['SerbianDelegate']['start_date'];
        }
        if (!empty($content['SerbianDelegate']['end_date'])) {
            $data['memberships']['end_date'] = $content['SerbianDelegate']['end_date'];
        }
        if (empty($content['SerbianDelegate']['url_uid'])) {
            $data['people']['id'] = $personId;
            $data['logs'] = array(
                'id' => 'people_memberships_' . $data['memberships']['id'] . '_' . time() . '_' . rand(0, 999),
                'label' => 'people not data exists: ' . $personId,
                'status' => 'finished',
//                        'params' => $t
            );
        }

        return $data;
    }

}
