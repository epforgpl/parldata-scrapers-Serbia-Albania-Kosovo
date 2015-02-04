<?php

App::uses('CakeTime', 'Utility');

class SerbianSpeecheContent extends AppModel {

    public $belongsTo = 'SerbianSpeecheIndex';
    //public $formulaSpeaker = '/[\x{0410}-\x{042F}]{3,}+\s+[\x{0410}-\x{042F}]{3,}+:|[\x{0410}-\x{042F}]{3,}+:/u';
    //   public $formulaSpeaker = '/[А-Я]{3,}.\s+[А-Я]{3,}.:|[А-Я]{6,}.:/u';
    //public $formulaSpeaker = '/[А-Я\s\D]{3,20}.:/u';
    //public $formulaSpeaker = '/[А-Я].*?(:)/u';
    public $formulaSpeaker = '/[А-ЯЈ]{3,}\s+[А-ЯЈ]{3,}.:|ПРЕДСЕДНИК\:/u';
    public $eventId;

    public function combineSpeche($data) {

        $find_table = array
            (
            $this->formulaSpeaker,
            '/<span([^>]*)>/',
            "/<\/span>/",
            "/<p([^>]*)>/",
            "/<\/p>/",
        );

        $replace_table = array(
            '',
            '',
            '',
            '',
            "<br/>",
        );
        $data = preg_replace($find_table, $replace_table, $data);

        $data = preg_replace('/(<span[^>]*>)(.*?)(<\/span>)/i', '$1$3', $data);
        $formulaTmeStamp = '/\d{1}\/\d{1}\D.*?(<br\/>|<\/p>|<\/span>)/msxi';
        $formulaNarrative = '/\(.*?(\))/msxi';
        $logs = array();
        if (preg_match_all($formulaTmeStamp, $data, $matches)) {
            $results = reset($matches);
            $data = preg_replace($formulaTmeStamp, '', $data);
            $logs = $results;
        }
        $data = preg_replace($formulaNarrative, '', $data);
        $data = trim(preg_replace('/\s{2,}/msxi', "", $data));

        return array('speche' => $data, 'logs' => $logs);
    }

    public function combineToApiArray($content) {

        $this->eventId = $content['SerbianSpeecheIndex']['post_uid'];
        $combine = ':split: ' . $content['SerbianSpeecheContent']['content'] . ' :split: a';
        $combine = preg_replace($this->formulaSpeaker, " :split::split: $0", $combine);

        $formulaSplit = '/:split:.*?(:split:)/msxi';

        $results = $data = array();
        if (preg_match_all($formulaSplit, $combine, $matches)) {
            $results = reset($matches);
        }
        $getStartTime = $this->getTimeFromIntro($content['SerbianSpeecheContent']['intro']);
        if ($getStartTime) {
            $date = $this->toApiDate($content['SerbianSpeecheContent']['convert_date'] . ' ' . $getStartTime);
        } else {
            $date = $this->toApiDate($content['SerbianSpeecheContent']['convert_date']);
        }

        //  pr($topSpeaker);

        if (count($results) > 0) {
            foreach ($results as $key => $result) {
                $result = preg_replace('/:split:/i', "", $result);
                $result = preg_replace('/\s+/i', " ", $result);
                $text = $this->combineSpeche($result);

                $speaker = $this->getSpeaker($result);
                if ($speaker) {
                    $topSpeaker = $this->getTopSpeaker($content['SerbianSpeecheContent']['intro']);
                    $creatorId = $speaker['role'] == 'chair' && $topSpeaker ? $topSpeaker : $speaker['attribution_text'];


                    $data[$key]['speeches']['type'] = 'speech';
                    $data[$key]['speeches']['text'] = trim(strip_tags($text['speche']));
                    $data[$key]['speeches']['date'] = $date;
                    $data[$key]['speeches']['position'] = $key;
                    $data[$key]['speeches']['event_id'] = 'event_' . $content['SerbianSpeecheIndex']['post_uid'];
                    $data[$key]['speeches']['id'] = $content['SerbianSpeecheIndex']['post_uid'] . '-' . $key;
                    $data[$key]['speeches']['attribution_text'] = $speaker['attribution_text'];
                    $data[$key]['speeches']['creator_id'] = $creatorId;
                    $data[$key]['speeches']['role'] = $speaker['role'];
                    $data[$key]['speeches']['title'] = $content['SerbianSpeecheContent']['title'];
                } else {
                    $data[$key]['speeches']['id'] = $content['SerbianSpeecheIndex']['post_uid'] . '-' . $key;
                    $data[$key]['speeches']['type'] = 'narrative';
                    $data[$key]['speeches']['text'] = trim($text['speche']);
                    $data[$key]['speeches']['date'] = $date;
                    $data[$key]['speeches']['position'] = $key;
                    $data[$key]['speeches']['event_id'] = 'event_' . $content['SerbianSpeecheIndex']['post_uid'];
                }

                $narratives = $this->getNarrative($result);
                if ($narratives && count($narratives) > 0) {
                    foreach ($narratives as $nkey => $narrative) {
                        $data[$key . '-' . $nkey]['speeches']['id'] = $content['SerbianSpeecheIndex']['post_uid'] . '-' . $key . '-' . $nkey;
                        $data[$key . '-' . $nkey]['speeches']['type'] = 'narrative';
                        $data[$key . '-' . $nkey]['speeches']['text'] = $narrative;
                        $data[$key . '-' . $nkey]['speeches']['date'] = $date;
                        $data[$key . '-' . $nkey]['speeches']['position'] = $key;
                        $data[$key . '-' . $nkey]['speeches']['event_id'] = 'event_' . $content['SerbianSpeecheIndex']['post_uid'];
                    }
                }
                if (isset($text) && !empty($text['logs'])) {
                    $tlogs[] = $text['logs'];
                }
            }
        }
        if (!isset($narrative)) {
            $narrative = null;
        }
        $getEndTime = $this->getTimeFromIntro($narrative);
        if ($getEndTime) {
            $edate = $this->toApiDate($content['SerbianSpeecheContent']['convert_date'] . ' ' . $getEndTime);
        } else {
            $edate = $this->toApiDate($content['SerbianSpeecheContent']['convert_date']);
        }

        App::import('Model', 'SerbianMenuData');
        $this->SerbianMenuData = new SerbianMenuData();
        $organization_id = $this->SerbianMenuData->field(
                'start_date', array(
            'start_date <=' => $content['SerbianSpeecheContent']['convert_date'],
                ), 'start_date DESC'
        );

        $organization_id = $this->toChamber($organization_id);

        $data[]['events'] = array(
            'id' => 'event_' . $content['SerbianSpeecheIndex']['post_uid'],
            'name' => $content['SerbianSpeecheContent']['title'],
            'organization_id' => $organization_id,
            'start_date' => $date,
            'end_date' => $edate,
            'sources' => array(
                array(
                    'url' => $this->getSerbiaHost . $content['SerbianSpeecheIndex']['url']
                )
            ),
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

    public function getTimeFromIntro($data) {
        $formulaTime = '/у\s\d{2}\.\d{2}\sчас/i';

        if (preg_match($formulaTime, $data, $matches)) {
            $result = reset($matches);
            if ($result) {
                $result = preg_replace('/у|час|\s/i', "", $result);
                $result = preg_replace('/\./i', ":", $result);
                return $result;
            }
        }
        return false;
    }

    public function getNarrative($data) {
        $formulaNarrative = '/\(Седница.*?(\))/msxi';
        if (preg_match_all($formulaNarrative, $data, $matches)) {
            $results = reset($matches);
            $data = array();
            if ($results) {
                foreach ($results as $result) {
                    $data[] = preg_replace('/\(|\)/i', "", $result);
                }
            }
            return($data);
        }
        return false;
    }

    public function getTopSpeaker($data) {
        $formulaTopSpeaker = '/скупштине.*?(сазвала)/i';
        $formulaTopSpeakerReplace = '/скупштине|сазвала/';
        $data = trim($this->extractAndReplace($data, $formulaTopSpeaker, $formulaTopSpeakerReplace));
        $find = $this->checkPeopleExist($data);
        if ($find) {
            $data = $find;
        } else {
            $data = 'mp_' . $this->toCamelCase($data); //to CamelCase
        }
        return $data;
    }

    public function getSpeaker($data) {
        if (preg_match($this->formulaSpeaker, $data, $matches)) {
            $result = reset($matches);
            if ($result) {
                $name = $result;
                $data = array();
                $result = 'mp_' . $this->toCamelCase($result);

                $data['role'] = 'speaker';
                if ($result == 'Председник') {
                    $data['attribution_text'] = $result;
                    $data['role'] = 'chair';
                } else {
                    $data['attribution_text'] = $this->checkPeopleExist($name);
                }

                return($data);
            }
        }
        return false;
    }

    public function checkPeopleExist($name) {
        $name = trim(preg_replace('/\:/', '', $name));
        $searchs = explode(' ', $name);
//        pr($searchs);
        foreach ($searchs as $na) {
            $cond[] = array('SerbianDelegate.name LIKE' => '%' . $na . '%');
        }
        $conditions[] = array('AND' => $cond);

        App::import('Model', 'SerbianDelegate');
        $this->SerbianDelegate = new SerbianDelegate();
        $checkName = $this->SerbianDelegate->field(
                'SerbianDelegate.api_uid', $conditions
        );
        if ($checkName) {
            return 'mp_' . $checkName;
        } else {
            $newId = 'mp_' . $this->toCamelCase($name);
            $data[]['people']['id'] = $newId;
            $data[]['logs'] = array(
                'id' => 'people_' . $newId . '_eventId_' . $this->eventId . '_' . time() . '_' . rand(0, 999),
                'label' => 'not found: ' . $newId,
                'status' => 'finished',
//                        'params' => $t
            );
            App::import('Model', 'QueleToSend');
            $this->QueleToSend = new QueleToSend();
            $this->QueleToSend->putDataDB($data, 'Serbian');
            return $newId;
        }
    }

}
