<?php

class KosovoMpsDetail extends AppModel {

    public $belongsTo = array(
        'KosovoMpsIndex'
    );
    public $hasMany = array(
        'KosovoCommitteFunc' => array('dependent' => true),
        'KosovoMpsPersonalData' => array('dependent' => true),
        'KosovoMpsEducation' => array('dependent' => true),
        'KosovoMpsActivity' => array('dependent' => true),
        'KosovoMpsLanguage' => array('dependent' => true),
        'KosovoMpsAddress' => array('dependent' => true),
    );
    public $hasAndBelongsToMany = array(
        'KosovoCommittee',
        'KosovoParliamentaryGroup',
        'KosovoParty',
    );

    public function combineToApiArray($data) {

        $name = trim($data['KosovoMpsDetail']['name']);
        $nname['id'] = 'mp_' . $data['KosovoMpsIndex']['kosovo_mps_menu_id'] . '_' . $this->toCamelCase($name);

        $nname['name'] = $name;
        $name = preg_replace('/\s\-\s/', '-', $name);
        $name = explode(' ', $name);
        $name = array_values($name);

        $nname['given_name'] = array_shift($name);

        if (count($name) > 0) {
            $nname['family_name'] = null;
            foreach ($name as $nn) {
                $nname['family_name'] .= ' ' . $nn;
            }
            $nname['family_name'] = trim($nname['family_name']);
        }

        if (isset($nname['family_name']) && (is_null($nname['family_name']) || $nname['family_name'] == '' || empty($nname['family_name']))) {
            unset($nname['family_name']);
        }

        $nname['image'] = $this->getKosovoHost . '/' . $data['KosovoMpsDetail']['image'];
        if (isset($data['KosovoMpsPersonalData']) && !empty($data['KosovoMpsPersonalData'])) {

            foreach ($data['KosovoMpsPersonalData'] as $d) {
                $date = $this->extractDateAndTrim($d['name']);
                if ($date) {
                    $birth_date = $date;
                }
            }
            if (isset($birth_date) && $birth_date) {
                $nname['birth_date'] = $birth_date;
            }
        }
//
        if (!empty($data['KosovoMpsDetail']['phone'])) {
            $contactDetails[] = array(
                'label' => 'Phone',
                'type' => 'tel',
                'value' => $data['KosovoMpsDetail']['phone']
            );
        }
        if (isset($contactDetails)) {
            $nname['contact_details'] = $contactDetails;
        }
        if (!empty($data['KosovoMpsIndex']['url'])) {
            $nname['sources'] = array(
                array(
                    'url' => $this->getKosovoHost . '/' . $data['KosovoMpsIndex']['url'],
                )
            );
        }

        return array('people' => $nname);
    }

}
