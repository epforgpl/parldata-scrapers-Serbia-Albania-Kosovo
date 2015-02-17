<?php

class AlbaniaChamber extends AppModel {

    public $hasMany = array(
        'AlbaniaDeputet'
    );

    public function combineToApiArray($content) {
        $i = 0;
        $id = 'chamber_' . $content['AlbaniaChamber']['name'];
        $data[$i]['organizations']['id'] = $id;
        $data[$i]['organizations']['name'] = 'Kuvendi i Shqipërisë: Legjislatura ' . $content['AlbaniaChamber']['name'];
        $data[$i]['organizations']['classification'] = 'chamber';
        $founding_date = CakeTime::format($content['AlbaniaChamber']['start_date'], '%Y');
        $data[$i]['organizations']['founding_date'] = $founding_date;
        if (!empty($content['AlbaniaChamber']['end_date'])) {
            $dissolution_date = CakeTime::format($content['AlbaniaChamber']['end_date'], '%Y');
            $data[$i]['organizations']['dissolution_date'] = $dissolution_date;
        }
        if (!empty($content['AlbaniaDeputet'])) {
            foreach ($content['AlbaniaDeputet'] as $key => $mp) {
                $i++;
                $person = $this->checkAlbaniaPeopleExist($mp['surname'] . ' ' . $mp['name']);
                $data[$i]['memberships']['id'] = $id . '-' . $person;
                $data[$i]['memberships']['label'] = 'Deputet';
                $data[$i]['memberships']['person_id'] = $person;
                $data[$i]['memberships']['organization_id'] = $id;
                if ($founding_date) {
                    $data[$i]['memberships']['start_date'] = $founding_date;
                }
                if (isset($dissolution_date)) {
                    $data[$i]['memberships']['end_date'] = $dissolution_date;
                }

                $i++;
                $partyId = 'party_' . $this->toCamelCase($mp['party']);
                $data[$i]['organizations']['id'] = $partyId;
                $data[$i]['organizations']['name'] = $mp['party'];
                $data[$i]['organizations']['classification'] = 'party';

                $i++;
                $data[$i]['memberships']['id'] = $partyId . '-' . $person;
                $data[$i]['memberships']['label'] = 'Anëtar';
                $data[$i]['memberships']['person_id'] = $person;
                $data[$i]['memberships']['organization_id'] = $partyId;
                if ($founding_date) {
                    $data[$i]['memberships']['start_date'] = $founding_date;
                }
                if (isset($dissolution_date)) {
                    $data[$i]['memberships']['end_date'] = $dissolution_date;
                }
            }
        }
        return $data;
    }

}
