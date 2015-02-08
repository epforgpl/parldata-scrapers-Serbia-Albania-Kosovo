<?php

class KosovoSpeecheContent extends AppModel {

    public $hasMany = array(
        'KosovoPdf',
        'KosovoTxt'
    );
    public $belongsTo = array(
        'KosovoSpeecheIndex'
    );

    public function combineToApiArray($content) {
//        return $content;

        $date = $this->toApiDate($content['KosovoSpeecheIndex']['post_date']);

        App::import('Model', 'KosovoMpsMenu');
        $this->KosovoMpsMenu = new KosovoMpsMenu();
        $organization_id = $this->KosovoMpsMenu->field(
                'start_date', array(
            'start_date <=' => $content['KosovoSpeecheIndex']['post_date'],
                ), 'start_date DESC'
        );

        $organization_id = $this->toChamber($organization_id);

        $sources[] = array(
            'url' => $this->getKosovoHost . '/' . $content['KosovoSpeecheIndex']['url'],
            'note' => 'webpage'
        );
        $combine = null;
        if (!empty($content['KosovoSpeecheContent']['KosovoPdf']) && isset($content['KosovoSpeecheContent']['KosovoPdf'][0])) {
            foreach ($content['KosovoSpeecheContent']['KosovoPdf'] as $pdf) {
                if (strpos($pdf['pdf_url'], 'trans_') !== false) {
                    $note = 'transcript';
                } else {
                    $note = 'procesverbali';
                }
                $sources[] = array(
                    'url' => $this->getKosovoHost . $pdf['pdf_url'],
                    'note' => $note
                );
            }
        }
        $data[]['events'] = array(
            'id' => 'event_' . $content['KosovoSpeecheIndex']['post_uid'],
            'name' => $content['KosovoSpeecheContent']['title'],
            'organization_id' => $organization_id,
            'start_date' => $date,
            'sources' => $sources,
        );


        if (isset($tlogs) && count($tlogs)) {
            foreach ($tlogs as $tl) {
                foreach ($tl as $t) {
//                    $data[]['logs'] = array(
//                        'id' => 'events_' . $content['SerbianSpeecheIndex']['post_uid'] . '_' . time() . '_' . rand(0, 999),
//                        'label' => 'remove: ' . trim(strip_tags($t)),
//                        'status' => 'finished',
////                        'params' => $t
//                    );
                }
            }
        }

        return $data;
    }

}
