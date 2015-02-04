<?php

App::uses('HttpSocket', 'Network/Http');
App::import('Vendor', 'GoogleTranslate', array('file' => 'Stichoza' . DS . 'Google' . DS . 'GoogleTranslate.php'));
App::import('Vendor', 'PdfToHtml', array('file' => 'Gufy' . DS . 'PdfToHtml.php'));

class SerbianPdf extends AppModel {

    public $translate_from = 'sr';
    public $translate_into = 'en';
    public $voteId;

    public function downloadPdf($l) {
        //  pr($l['SerbianPdf']['id']);
        $fileFolder = WWW_ROOT . 'files' . DS . 'serbia' . DS;
        $this->id = $l['SerbianPdf']['id'];

        $this->GoogleTranslate = new GoogleTranslate();
        $name_en = $this->GoogleTranslate->setLangFrom(
                        $this->translate_from)
                ->setLangTo($this->translate_into)
                ->translate(strip_tags($l['SerbianPdf']['name_sr'])
        );
        if ($name_en) {
            $this->saveField('name_en', $name_en);
        }
        $url = ($this->getSerbiaHost . rawurlencode($l['SerbianPdf']['pdf_url']));
        $url = str_replace('%2F', '/', $url);
        $httpSocket = new HttpSocket();
        $filePdfName = $fileFolder . $l['SerbianPdf']['id'] . '.pdf';
        $f = fopen($filePdfName, 'w');
        chmod($filePdfName, 0666);
        $httpSocket->setContentResource($f);
        $httpSocket->get($url);
        if ($httpSocket->response->code != 200) {
            $httpSocket->get($l['SerbianPdf']['pdf_url']);
        }
        fclose($f);
        usleep(800);
        if (file_exists($filePdfName)) {
            return $filePdfName;
            // return $httpSocket->response->code;
        } else {
            return false;
        }
    }

    public function combinePdfToHtml($filePath, $id) {
        $fileFolder = WWW_ROOT . 'files' . DS . 'serbia' . DS;
        if (file_exists($filePath)) {
            $pdf = new \Gufy\PdfToHtml;
            $pdf->open($filePath);
            $pdf->generateOptions('singlePage');
            $pdf->setOutputDirectory($fileFolder);
            if ($pdf->generate()) {
                $content = (file_get_contents($fileFolder . $id . '.html'));
                $content = preg_replace('/&nbsp;/i', ' ', $content);
                $content = preg_replace('/|<hr>|<\!DOCTYPE.*?(>)|<BODY.*?(>)|<\/BODY>|<\/HTML>/i', '', $content);
                $content = preg_replace('/<HTML>.*?(<\/HEAD>)/ism', '', $content);
                $content = preg_replace('/\s<br>\\n(?=[Ј,с,\(])/ism', ' ', $content);
                $content = preg_replace('/\\n<A.*?(<\/a>)/ism', '', $content);
                $content = preg_replace('/\s<br><b>\s<\/b><br>|<br>/i', '<br \>', $content);
                $content = trim($content);
                //  pr($content);
                if ($content) {
                    $this->saveField('content_sr', $content, array('id' => $id));
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function translateHtml($id) {
        $vowels = array("< b >", "< / b>", "< / b >");
        $vowels1 = array("<b>", "</b>", "</b>");
        if ($id) {
            $content = $this->field('content_sr', array('id' => $id));
            if (!empty($content)) {
                $translate = null;
                foreach (preg_split("/((\r?\n)|(\r\n?))/", $content) as $line) {
                    //  if (!empty($line)) {
                    $translates = $this->GoogleTranslate->setLangFrom(
                                    $this->translate_from)
                            ->setLangTo($this->translate_into)
                            ->translate($line);
                    $translate .= str_replace($vowels, $vowels1, $translates);
                    $translate .= "\n";
                    //  }
                    usleep(1500);
                }
                if ($translate) {
                    $this->saveField('content_en', $translate);
                    $this->saveField('status', 1);
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function combinePdfs($list) {
        $fileFolder = WWW_ROOT . 'files' . DS . 'serbia' . DS;
        $vowels = array("< b >", "< / b>", "< / b >");
        $vowels1 = array("<b>", "</b>", "</b>");

        if (count($list)) {

            foreach ($list as $l) {
                $this->id = $l['SerbianPdf']['id'];

                $this->GoogleTranslate = new GoogleTranslate();
                $name_en = $this->GoogleTranslate->setLangFrom(
                                $this->translate_from)
                        ->setLangTo($this->translate_into)
                        ->translate(strip_tags($l['SerbianPdf']['name_sr'])
                );
                if ($name_en) {
                    $this->saveField('name_en', $name_en);
                }
                $url = ($this->getSerbiaHost . rawurlencode($l['SerbianPdf']['pdf_url']));
                $url = str_replace('%2F', '/', $url);


                $httpSocket = new HttpSocket();
                $filePdfName = $fileFolder . $l['SerbianPdf']['id'] . '.pdf';
                $f = fopen($filePdfName, 'w');
                $httpSocket->setContentResource($f);
                $httpSocket->get($url);
                fclose($f);
                usleep(800);
                if (file_exists($filePdfName)) {
                    $pdf = new \Gufy\PdfToHtml;
                    $pdf->open($filePdfName);
                    $pdf->generateOptions('singlePage');
                    $pdf->setOutputDirectory($fileFolder);
                    if ($pdf->generate()) {
                        $content = (file_get_contents($fileFolder . $l['SerbianPdf']['id'] . '.html'));
                        $content = preg_replace('/&nbsp;/i', ' ', $content);
                        $content = preg_replace('/|<hr>|<\!DOCTYPE.*?(>)|<BODY.*?(>)|<\/BODY>|<\/HTML>/i', '', $content);
                        $content = preg_replace('/<HTML>.*?(<\/HEAD>)/ism', '', $content);
                        $content = preg_replace('/\s<br>\\n(?=[Ј,с,\(])/ism', ' ', $content);
                        $content = preg_replace('/\\n<A.*?(<\/a>)/ism', '', $content);
                        $content = preg_replace('/\s<br><b>\s<\/b><br>|<br>/i', '<br \>', $content);
                        $content = trim($content);
                        pr($content);
                        if ($content) {
                            $this->saveField('content_sr', $content);
                            $translate = null;
                            foreach (preg_split("/((\r?\n)|(\r\n?))/", $content) as $line) {
                                //  if (!empty($line)) {
                                $translates = $this->GoogleTranslate->setLangFrom(
                                                $this->translate_from)
                                        ->setLangTo($this->translate_into)
                                        ->translate($line);
                                $translate .= str_replace($vowels, $vowels1, $translates);
                                $translate .= "\n";
                                //  }
                                usleep(1500);
                            }
                            if ($translate) {
                                $this->saveField('content_en', $translate);
                                $this->saveField('status', 1);
                            }
                            pr($translate);
                        }
                    }
                }
                // usleep(1500);
            }
        }
    }

    public function combineToApiArray($content) {
        $eventId = 'event_' . $content['doEvent']['SerbianSpeecheIndex']['post_uid'];

        $this->voteId = $data['vote-events']['id'] = $eventId . '-' . $content['SerbianPdf']['stamp_in_text'];
        $this->pdfUrl = $this->getSerbiaHost . $content['SerbianPdf']['pdf_url'];
        App::import('Model', 'SerbianMenuData');
        $this->SerbianMenuData = new SerbianMenuData();
        $organization_id = $this->SerbianMenuData->field(
                'start_date', array(
            'start_date <' => $content['SerbianPdf']['post_date'],
                ), 'start_date DESC'
        );

        $organization_id = $this->toChamber($organization_id);
        $data['vote-events']['organization_id'] = $organization_id;
        $data['vote-events']['legislative_session_id'] = $eventId;
        $data['vote-events']['motion_id'] = $data['vote-events']['id'];
        $data['vote-events']['start_date'] = $this->toApiDate($this->getDateFromPdf($content['SerbianPdf']['content_sr']));

        $data['motions']['id'] = $data['vote-events']['id'];
        $data['motions']['organization_id'] = $organization_id;
        $data['motions']['text'] = $content['SerbianPdf']['name_sr'];

        $data['motions']['sources'] = array(
            array(
                'url' => $this->pdfUrl
            )
        );
        $vote['AllVotes'] = $this->combineToVote($content['SerbianPdf']['content_sr']);
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

//        $data['all'] = $content;
        return $data;
    }

    public function getDateFromPdf($content_sr) {
        $formulaDate = '/(\d{2}\.\d{2}\.\d{4}|\d{2}:\d{2}:\d{2})/i';
        if (preg_match_all($formulaDate, $content_sr, $matches)) {
            $results = reset($matches);
            $results = implode(' ', $results);
            return $results;
        }
        return $content_sr;
    }

    public function combineToVote($content_sr) {

        $formulaYes = '/ГЛАСАЛИ\sЗА\:.*(?=ГЛАСАЛИ\sПРОТИВ\:)/msxi';
        $formulaNo = '/ГЛАСАЛИ\sПРОТИВ\:.*(?=УЗДРЖАНИ\:)/msxi';
        $formulaAbstain = '/УЗДРЖАНИ\:.*(?=НИСУ\sГЛАСАЛИ\:)/msxi';
        $formulaNotVoting = '/НИСУ\sГЛАСАЛИ\:.*(?=$)/msxi';
        $formulaLine = '/\d+.*?(\\>)/u';
        $formulaParty = '/(\s|\()[А-ЯЈ]{2,}.*?(\\>)/u';
        $find_table = array(
            '/ ДОЦ. ДР /',
            '/ Проф. Др /',
            '/ проф. др /',
            '/ ПРОФ. ДР /',
            '/ ДР /',
            '/ Др /',
            '/ др /',
            '/ МР /',
            '/ Мр /',
            '/ мр /',
            $formulaParty,
            '/\d+\./'
        );
        $votesYes = $votesNo = $votesAbstain = $votesNotVoting = array();

        if (preg_match($formulaYes, $content_sr, $matches)) {
            $results = reset($matches);
            if (preg_match_all($formulaLine, $results, $matches)) {
                $result = reset($matches);
                foreach ($result as $r) {
                    if (preg_match($formulaParty, $r, $matches)) {
                        $shortcut = trim(strip_tags(reset($matches)));
                        $person = preg_replace($find_table, '', $r);
                        $person = $this->checkPeopleExist($person);
//                        $group_id = $this->checkPartyeExist($shortcut);
                        $votesYes[]['votes'] = array(
                            'id' => 'yes-' . $this->voteId . '-' . $person,
                            'vote_event_id' => $this->voteId,
                            'voter_id' => $person,
                            'option' => 'yes',
//                            'group_id' => $group_id,
                        );
                    }
                }
            }
        }
        if (preg_match($formulaNo, $content_sr, $matches)) {
            $results = reset($matches);
            if (preg_match_all($formulaLine, $results, $matches)) {
                $result = reset($matches);
                foreach ($result as $r) {
                    if (preg_match($formulaParty, $r, $matches)) {
                        $shortcut = trim(strip_tags(reset($matches)));
                        $person = preg_replace($find_table, '', $r);
                        $person = $this->checkPeopleExist($person);
//                        $group_id = $this->checkPartyeExist($shortcut);
                        $votesNo[]['votes'] = array(
                            'id' => 'no-' . $this->voteId . '-' . $person,
                            'vote_event_id' => $this->voteId,
                            'voter_id' => $person,
                            'option' => 'no',
//                            'group_id' => $group_id,
                        );
                    }
                }
            }
        }
        if (preg_match($formulaAbstain, $content_sr, $matches)) {
            $results = reset($matches);
            if (preg_match_all($formulaLine, $results, $matches)) {
                $result = reset($matches);
                foreach ($result as $r) {
                    if (preg_match($formulaParty, $r, $matches)) {
                        $shortcut = trim(strip_tags(reset($matches)));
                        $person = preg_replace($find_table, '', $r);
                        $person = $this->checkPeopleExist($person);
//                        $group_id = $this->checkPartyeExist($shortcut);
                        $votesAbstain[]['votes'] = array(
                            'id' => 'abstain-' . $this->voteId . '-' . $person,
                            'vote_event_id' => $this->voteId,
                            'voter_id' => $person,
                            'option' => 'abstain',
//                            'group_id' => $group_id,
                        );
                    }
                }
            }
        }
        if (preg_match($formulaNotVoting, $content_sr, $matches)) {
            $results = reset($matches);
            if (preg_match_all($formulaLine, $results, $matches)) {
                $result = reset($matches);
                foreach ($result as $r) {
                    if (preg_match($formulaParty, $r, $matches)) {
                        $shortcut = trim(strip_tags(reset($matches)));
                        $person = preg_replace($find_table, '', $r);
                        $person = $this->checkPeopleExist($person);
//                        $group_id = $this->checkPartyeExist($shortcut);
                        $votesNotVoting[]['votes'] = array(
                            'id' => 'not-voting-' . $this->voteId . '-' . $person,
                            'vote_event_id' => $this->voteId,
                            'voter_id' => $person,
                            'option' => 'not voting',
//                            'group_id' => $group_id,
                        );
                    }
                }
            }
        }
        // 'group_id' => $group_id,
        // TODO match with http://api.parldata.eu/rs/parlament/organizations?where={%22other_names.name%22:%22SKR%C3%93T%20PPG%22,%22other_names.note%22:%22shortcut%22,%22classification%22:%20%22parliamentary_group%22}

        $all = array_merge($votesYes, $votesNo, $votesAbstain, $votesNotVoting);

        App::import('Model', 'QueleToSend');
        $this->QueleToSend = new QueleToSend();
        $this->QueleToSend->putDataDB($all, 'Serbian');

        $all = array(
            'votesYes' => count($votesYes),
            'votesNo' => count($votesNo),
            'votesAbstain' => count($votesAbstain),
            'votesNotVoting' => count($votesNotVoting)
        );
        return $all;
    }

}
