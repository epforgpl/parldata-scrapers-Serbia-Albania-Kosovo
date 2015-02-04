<?php

App::uses('CakeTime', 'Utility');

class KosovoMpsMenu extends AppModel {

    public function combineToApiArray($content) {

        $id = $this->toChamber($content['KosovoMpsMenu']['start_date']);
        $data['organizations']['id'] = $id;
        $data['organizations']['name'] = 'Kuvendit të Kosovës' . ' - ' . $content['KosovoMpsMenu']['name'];
        $data['organizations']['classification'] = 'chamber';
        $data['organizations']['founding_date'] = $content['KosovoMpsMenu']['start_date'];
        if (!empty($content['KosovoMpsMenu']['end_date'])) {
            $data['organizations']['dissolution_date'] = $content['KosovoMpsMenu']['end_date'];
        }
        return $data;
    }

}
