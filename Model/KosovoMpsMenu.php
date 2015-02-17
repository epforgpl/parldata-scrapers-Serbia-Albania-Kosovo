<?php

App::uses('CakeTime', 'Utility');

class KosovoMpsMenu extends AppModel {

    public $hasMany = array(
        'KosovoMpsIndex',
    );

    public function combineToApiArray($content) {
        $i = 0;
        $id = $this->toChamber($content['KosovoMpsMenu']['start_date']);
        $data[$i]['organizations']['id'] = $id;
        $data[$i]['organizations']['name'] = 'Kuvendit të Kosovës' . ' - ' . $content['KosovoMpsMenu']['name'];
        $data[$i]['organizations']['classification'] = 'chamber';
        $data[$i]['organizations']['founding_date'] = $content['KosovoMpsMenu']['start_date'];
        if (!empty($content['KosovoMpsMenu']['end_date'])) {
            $data[$i]['organizations']['dissolution_date'] = $content['KosovoMpsMenu']['end_date'];
        }
        if (!empty($content['KosovoMpsIndex'])) {
            foreach ($content['KosovoMpsIndex'] as $key => $mp) {
                $i++;
                $person = $this->checkKosovoPeopleExist($mp['name'], $mp['kosovo_mps_menu_id']);
                $data[$i]['memberships']['id'] = $id . '-' . $person;
                $data[$i]['memberships']['label'] = 'MP';
                $data[$i]['memberships']['person_id'] = $person;
                $data[$i]['memberships']['organization_id'] = $id;
                if (!empty($mp['KosovoMpsIndex']['start_date'])) {
                    $data[$i]['memberships']['start_date'] = $mp['start_date'];
                }
                if (!empty($mp['KosovoMpsIndex']['end_date'])) {
                    $data[$i]['memberships']['end_date'] = $mp['end_date'];
                }
            }
        }
        return $data;
    }

}
