<?php

class AlbaniaChamber extends AppModel {

    public function combineToApiArray($content) {
        $data['organizations']['id'] = 'chamber_' . $content['AlbaniaChamber']['name'];
        $data['organizations']['name'] = 'Kuvendi i Shqipërisë: Legjislatura ' . $content['AlbaniaChamber']['name'];
        $data['organizations']['classification'] = 'chamber';
        $data['organizations']['founding_date'] = CakeTime::format($content['AlbaniaChamber']['start_date'], '%Y');
        if (!empty($content['AlbaniaChamber']['end_date'])) {
            $data['organizations']['dissolution_date'] = CakeTime::format($content['AlbaniaChamber']['end_date'], '%Y');
        }
        return $data;
    }

}
