<?php

App::uses('CakeTime', 'Utility');

class SerbianMenuData extends AppModel {

    public function combineToApiArray($content) {

        $id = 'chamber_' . $content['SerbianMenuData']['start_date'];
        $data['organizations']['id'] = $id;
        $data['organizations']['name'] = 'Народна скупштина Републике Србије';
        $data['organizations']['classification'] = 'chamber';
        $data['organizations']['founding_date'] = $content['SerbianMenuData']['start_date'];
        $dissolution_date = $this->field('start_date', array('start_date >' => $content['SerbianMenuData']['start_date'], 'id !=' => $content['SerbianMenuData']['id']), 'start_date ASC');
        if ($dissolution_date) {
            $dissolution_date = CakeTime::format($dissolution_date . ' -1 day', '%Y-%m-%d');
            $data['organizations']['dissolution_date'] = $dissolution_date;
        }

        return $data;
    }

}
