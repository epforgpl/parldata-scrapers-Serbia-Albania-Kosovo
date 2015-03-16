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
        $nname = $this->combineKosovoPeopleName($name);
        if (!empty($data['KosovoMpsDetail']['image'])) {
            $nname['image'] = $this->getKosovoHost . '/' . $data['KosovoMpsDetail']['image'];
        }
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
        if (!empty($data['KosovoMpsIndex']['start_date'])) {
//            $nname['effective_date'] = $this->toApiDate($data['KosovoMpsIndex']['start_date']);
//            $nname['effective_date'] = $data['KosovoMpsIndex']['start_date'];
//            $nname['effective_date'] = $data['KosovoMpsIndex']['start_date'];
        }

        return array('people' => $nname);
    }

}
