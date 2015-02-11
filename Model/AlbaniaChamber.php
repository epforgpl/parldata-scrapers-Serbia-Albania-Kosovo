<?php

class AlbaniaChamber extends AppModel {

    public function combineToApiArray($content) {
        $data['organizations']['id'] = 'chamber_' . $content['AlbaniaChamber']['name'];
        $data['organizations']['name'] = 'Kuvendi i Shqipërisë: Legjislatura ' . $content['AlbaniaChamber']['name'];
        $data['organizations']['classification'] = 'chamber';
        $data['organizations']['founding_date'] = $content['AlbaniaChamber']['start_date'];
        if (!empty($content['AlbaniaChamber']['end_date'])) {
            $data['organizations']['dissolution_date'] = $content['AlbaniaChamber']['end_date'];
        }
        return $data;
    }

}
