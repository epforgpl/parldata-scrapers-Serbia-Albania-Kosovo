<?php

class SerbianDelegate extends AppModel {

    public $belongsTo = array(
        'SerbianMenuData'
    );

    public function combineToApiArray($content) {
        $personId = $this->checkPeopleExist($content['SerbianDelegate']['name']);
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
        $sources[] = array(
            'url' => $this->getSerbiaHost . trim($content['SerbianMenuData']['url']),
        );
        if (!empty($content['SerbianDelegate']['url'])) {
            $sources[] = array(
                'url' => $this->getSerbiaHost . trim($content['SerbianDelegate']['url']),
            );
        }
        $data['memberships']['sources'] = $sources;

        return $data;
    }

}
