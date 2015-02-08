<?php

App::uses('HttpSocket', 'Network/Http');

class KosovoTxt extends AppModel {

    public $belongsTo = 'KosovoSpeecheContent';
    public $eventId;
    public $menuId = 0;

    public function getContentTxtFromId($id) {
        $this->recursive = -1;
        $txt = $this->findById($id);

        $url = $this->getKosovoHost . $txt['KosovoTxt']['txt_url'];

        if (@ $content = file_get_contents($url)) {
            $txt_md5 = md5($content);
            if ($txt['KosovoTxt']['txt_md5'] != $txt_md5) {
                $this->id = $id;
                $this->set(array(
                    'txt_md5' => $txt_md5,
                    'status' => 1,
                    'content_sr' => utf8_encode($content)
                ));
                if ($this->save()) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
    }

    public function combineToApiArray($content) {
        $formulaName = '/vot\_.*(?=\.txt)/i';
        if (!empty($content['KosovoSpeecheContent']['KosovoSpeecheIndex']['post_uid'])) {
            $this->eventId = 'event_' . $content['KosovoSpeecheContent']['KosovoSpeecheIndex']['post_uid'];
        }
        $this->voteId = $data['vote-events']['id'] = $this->extractTrimStrip($content['KosovoTxt']['txt_url'], $formulaName);
        $this->txtUrl = $this->getKosovoHost . $content['KosovoTxt']['txt_url'];

        App::import('Model', 'KosovoMpsMenu');
        $this->KosovoMpsMenu = new KosovoMpsMenu();

        $organization = $this->KosovoMpsMenu->find('first', array(
            'fields' => array('id', 'start_date'),
            'conditions' => array(
                'start_date <=' => $content['KosovoSpeecheContent']['post_date'],
            ),
            'order' => 'start_date DESC'
        ));
        $this->menuId = $organization['KosovoMpsMenu']['id'];
        $organization_id = $organization['KosovoMpsMenu']['start_date'];


        $organization_id = $this->toChamber($organization_id);
        $data['vote-events']['organization_id'] = $organization_id;
        if (!empty($this->eventId)) {
            $data['vote-events']['legislative_session_id'] = $this->eventId;
        }
        $data['vote-events']['motion_id'] = $data['vote-events']['id'];
        $data['vote-events']['start_date'] = $this->toApiDate($this->getDateFromTxt($content['KosovoTxt']['content_sr']));

        $data['motions']['id'] = $data['vote-events']['id'];
        $data['motions']['organization_id'] = $organization_id;
        $data['motions']['text'] = $this->findTitleFromTxt($content['KosovoTxt']['content_sr']);

        $data['vote-events']['sources'] = $data['motions']['sources'] = array(
            array(
                'url' => $this->txtUrl
            )
        );
        $vote['AllVotes'] = $this->combineToVote($content['KosovoTxt']['content_sr']);
        $result = $vote['AllVotes']['votesYes'] > $vote['AllVotes']['votesNo'] ? 'pass' : 'fail';
        $data['motions']['result'] = $data['vote-events']['result'] = $result;
        $data['vote-events']['counts'] = array(
            array(
                'option' => 'yes', 'value' => $vote['AllVotes']['votesYes']
            ),
            array(
                'option' => 'no', 'value' => $vote['AllVotes']['votesNo']
            ),
            array(
                'option' => 'abstain', 'value' => $vote['AllVotes']['votesAbstain']
            ),
            array(
                'option' => 'not voting', 'value' => $vote['AllVotes']['votesNotVoting']
            )
        );
        $data['vote-events']['sources'] = $data['motions']['sources'];
        return $data;
    }

    public function getDateFromTxt($content_sr) {
        $formulaDate = '/(\d{2}\-\d{2}\-\d{4}|\d{2}:\d{2})/i';
        if (preg_match_all($formulaDate, $content_sr, $matches)) {
            $results = reset($matches);
            $results = implode(' ', $results);
            return $results;
        }
        return null;
    }

    public function findTitleFromTxt($content_sr) {
        $formulaTitle = '/\d\,.*?(\,\d)/i';
//        $formulaTitleReplace = '/\d\,|\,\d|\s+/i';
        $formulaTitleReplace = '/\d\,|\,\d/i';
        $result = $this->extractAndReplace($content_sr, $formulaTitle, $formulaTitleReplace);
        $result = preg_replace('/\,/', ', ', $result);
        $result = preg_replace('/\s+/', ' ', $result);

        return $result;
    }

    public function combineToVote($content_sr) {
        $formulaLine = '/\d+\,.*?(\n)/i';

        if (preg_match_all($formulaLine, $content_sr, $matches)) {
            $results = reset($matches);

            $votesYes = $votesNo = $votesAbstain = $votesNotVoting = array();
            foreach ($results as $result) {
                $tmpArray = explode(',', $result);

                if (count($tmpArray) == 4 && !empty($tmpArray[1]) && strpos($tmpArray[3], 'Yes') !== false) {
                    $person = preg_replace('/\s+/', ' ', $tmpArray[1]);
                    $person = $this->checkKosovoPeopleExist($person, $this->menuId);

                    $votesYes[]['votes'] = array(
                        'id' => 'yes-' . $this->voteId . '-' . $person,
//                            'name' => $yes[1],
                        'vote_event_id' => $this->voteId,
                        'voter_id' => $person,
                        'option' => 'yes',
//                            'group_id' => $group_id,
                    );
                }


                if (count($tmpArray) == 4 && !empty($tmpArray[1]) && strpos($tmpArray[3], 'No') !== false) {
                    $person = preg_replace('/\s+/', ' ', $tmpArray[1]);
                    $person = $this->checkKosovoPeopleExist($person, $this->menuId);

                    $votesNo[]['votes'] = array(
                        'id' => 'no-' . $this->voteId . '-' . $person,
                        'vote_event_id' => $this->voteId,
                        'voter_id' => $person,
                        'option' => 'no',
//                            'group_id' => $group_id,
                    );
                }

                if (count($tmpArray) == 4 && !empty($tmpArray[1]) && strpos($tmpArray[3], 'Abstain') !== false) {
                    $person = preg_replace('/\s+/', ' ', $tmpArray[1]);
                    $person = $this->checkKosovoPeopleExist($person, $this->menuId);

                    $votesAbstain[]['votes'] = array(
                        'id' => 'abstain-' . $this->voteId . '-' . $person,
                        'vote_event_id' => $this->voteId,
                        'voter_id' => $person,
                        'option' => 'abstain',
//                            'group_id' => $group_id,
                    );
                }

                if (count($tmpArray) == 4 && !empty($tmpArray[1]) && strpos($tmpArray[3], 'Not Voted') !== false) {
                    $person = preg_replace('/\s+/', ' ', $tmpArray[1]);
                    $person = $this->checkKosovoPeopleExist($person, $this->menuId);

                    $votesNotVoting[]['votes'] = array(
                        'id' => 'not-voting-' . $this->voteId . '-' . $person,
                        'vote_event_id' => $this->voteId,
                        'voter_id' => $person,
                        'option' => 'not voting',
//                            'group_id' => $group_id,
                    );
                }
            }
            $all = array_merge($votesYes, $votesNo, $votesAbstain, $votesNotVoting);

            App::import('Model', 'QueleToSend');
            $this->QueleToSend = new QueleToSend();
            $this->QueleToSend->putDataDB($all, 'Kosovan');

            $all = array(
                'votesYes' => count($votesYes),
                'votesNo' => count($votesNo),
                'votesAbstain' => count($votesAbstain),
                'votesNotVoting' => count($votesNotVoting)
            );
            return $all;
        }
    }

}
