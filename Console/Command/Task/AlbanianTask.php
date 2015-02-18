<?php

//App::uses('CakeEmail', 'Network/Email');
App::uses('CakeTime', 'Utility');

class AlbanianTask extends Shell {

    public $uses = array(
        'Schedule',
        'Albania',
        'AlbaniaChamber',
        'AlbaniaSpeecheIndex',
        'AlbaniaVoteIndex',
        'AlbaniaMpsIndex',
        'AlbaniaMpsDetail',
        'AlbaniaDoc',
        'AlbaniaPdf',
        'AlbaniaLog',
        'AlbaniaDeputet',
        'QueleToSend'
    );

    public function check_mps_contacts($now) {
        $this->QueleToSend->doLog(
                array(
            'id' => time() . '_' . rand(4, 99999),
            'label' => 'check_mps_contacts',
            'status' => 'running',
                ), 'Albanian'
        );
        $info = CakeTime::toServer(time()) . ' Albania check_mps_contacts start | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog = $info . "\n";

        $lists = $this->AlbaniaMpsDetail->find('all', array(
            'fields' => array('id', 'url', 'md5'),
            'conditions' => array(
                'AlbaniaMpsDetail.modified <' => $now,
            ),
            'order' => 'AlbaniaMpsDetail.modified ASC',
            'limit' => 3
        ));

        $info = 'geting uids mps contacts';
        $this->out($info);
        $toLog .= $info . "\n";
        if ($lists) {
            foreach ($lists as $list) {
                $info = 'id: ' . $list['AlbaniaMpsDetail']['id'];
                $this->out($info);
                $toLog .= $info . "\n";

                $info = 'get data for select url: ' . $list['AlbaniaMpsDetail']['url'];
                $this->out($info);
                $toLog .= $info . "\n";

                $mpsDetails = $this->Albania->getMpsContent($list['AlbaniaMpsDetail']['url'], $list['AlbaniaMpsDetail']['id']);
                $info = 'get data for select md5: ' . $mpsDetails['md5'];
                $this->out($info);
                $toLog .= $info . "\n";
                if ($mpsDetails && count($mpsDetails) > 0) {
                    if ($list['AlbaniaMpsDetail']['md5'] != $mpsDetails['md5']) {
                        if ($this->AlbaniaMpsDetail->save($mpsDetails)) {
                            $info = 'update new Mps Detail | id: ' . $mpsDetails['id'];
                            $this->out($info);
                            $toLog .= $info . "\n";
                            $change = true;
                        }
                    } else {
                        $info = 'nothing to change | id: ' . $mpsDetails['id'];
                        $this->out($info);
                        $toLog .= $info . "\n";
                        $this->AlbaniaMpsDetail->hint($mpsDetails['id']);
                    }
                } else {
                    $info = 'something is wrong, I can not get data from index link uid: ' . $list['AlbaniaMpsDetail']['id'];
                    $this->out($info);
                    $toLog .= $info . "\n";
                }
                sleep(3);
            }
        } else {
            $info = 'nothing to do';
            $this->out($info);
            $toLog .= $info . "\n";
        }



        $info = CakeTime::toServer(time()) . ' Albania check_mps_contacts end | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog .= $info . "\n";
        $this->QueleToSend->doLog(
                array(
            'id' => time() . '_' . rand(4, 99999),
            'label' => 'check_mps_contacts',
            'status' => 'finished',
            'params' => explode("\n", $toLog)
                ), 'Albanian'
        );
    }

    public function get_mps_details_from_index($limit = null) {
        $limit = !is_null($limit) && (int) $limit ? $limit : 2;
        $this->QueleToSend->doLog(
                array(
            'id' => time() . '_' . rand(4, 99999),
            'label' => 'get_mps_details_from_index',
            'status' => 'running',
                ), 'Albanian'
        );
        $info = CakeTime::toServer(time()) . ' Albania get_mps_details_from_index start | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog = $info . "\n";

        $mpsList = $this->AlbaniaMpsIndex->find('all', array(
            'fields' => array('AlbaniaMpsIndex.id', 'AlbaniaMpsIndex.uid', 'AlbaniaMpsIndex.url'),
            'conditions' => array(
                'AlbaniaMpsIndex.status' => 0
            ),
            'order' => 'AlbaniaMpsIndex.uid ASC',
            'limit' => $limit
        ));
        $change = false;
        if (count($mpsList) > 0) {
            foreach ($mpsList as $l) {
                $mpsDetails = $this->Albania->getMpsContent($l['AlbaniaMpsIndex']['url'], $l['AlbaniaMpsIndex']['uid']);
                if ($mpsDetails && count($mpsDetails) > 0) {
                    $check = $this->AlbaniaMpsDetail->findById($mpsDetails['id']);
                    if (!$check) {
                        if ($this->AlbaniaMpsDetail->save($mpsDetails)) {
                            $info = 'save new Mps Detail | id: ' . $mpsDetails['id'];
                            $this->out($info);
                            $toLog .= $info . "\n";
                            $change = true;
                        }
                    } else {
                        if ($check['AlbaniaMpsDetail']['md5'] != $mpsDetails['md5']) {
                            if ($this->AlbaniaMpsDetail->save($mpsDetails)) {
                                $info = 'update new Mps Detail | id: ' . $mpsDetails['id'];
                                $this->out($info);
                                $toLog .= $info . "\n";
                                $change = true;
                            }
                        }
                    }
                    if (!$change) {
                        $info = 'exists, nothing changed | id: ' . $mpsDetails['id'];
                        $this->out($info);
                        $toLog .= $info . "\n";
                    }
                    $this->AlbaniaMpsIndex->id = $l['AlbaniaMpsIndex']['id'];
                    $this->AlbaniaMpsIndex->saveField('status', 1);
                    $info = 'change status index | id: ' . $l['AlbaniaMpsIndex']['id'];
                    $this->out($info);
                    $toLog .= $info . "\n";
                } else {
                    $info = 'something is wrong, I can not get data from index link uid: ' . $l['AlbaniaMpsIndex']['uid'];
                    $this->out($info);
                    $toLog .= $info . "\n";
                }
                sleep(3);
            }
        }

        $info = CakeTime::toServer(time()) . ' Albania get_mps_details_from_index end | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog .= $info . "\n";
        $this->QueleToSend->doLog(
                array(
            'id' => time() . '_' . rand(4, 99999),
            'label' => 'get_mps_details_from_index',
            'status' => 'finished',
            'params' => explode("\n", $toLog)
                ), 'Albanian'
        );
    }

    public function get_deputed() {
        $this->QueleToSend->doLog(
                array(
            'id' => time() . '_' . rand(4, 99999),
            'label' => 'get_deputed',
            'status' => 'running',
                ), 'Albanian'
        );
        $info = CakeTime::toServer(time()) . ' Albania get_deputed start | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog = $info . "\n";

        $chamber = $this->AlbaniaChamber->find('first', array(
            'fields' => array('id', 'uid', 'name'),
            'conditions' => array(
                'AlbaniaChamber.status' => 1,
                'AlbaniaChamber.uid !=' => null,
            )
        ));
        if ($chamber) {
//            $filePdfName = $this->AlbaniaPdf->getPdfFromIs($id);
            $filePdfName = '/home/scrapper/domains/scrapper.cakephp.com.pl/public_html/app/webroot/files/albania/20230.pdf';
            if ($filePdfName) {
                $content = $this->AlbaniaPdf->combinePdfToHtml($filePdfName, $chamber['AlbaniaChamber']['uid'], $chamber['AlbaniaChamber']['id']);
                if ($content) {
                    $change = false;
                    foreach ($content as $c) {
                        $check = $this->AlbaniaDeputet->find('first', array(
                            'fields' => array('id', 'name'),
                            'conditions' => array(
                                'AlbaniaDeputet.md5' => $c['md5'],
                                'AlbaniaDeputet.name LIKE' => $c['name'],
                            )
                        ));
                        if (!$check) {
                            $this->AlbaniaDeputet->create();
                            if ($this->AlbaniaDeputet->save($c)) {
                                $info = 'save new id: ' . $this->AlbaniaDeputet->getLastInsertId();
                                $this->out($info);
                                $toLog .= $info . "\n";
                                $change = true;
                            }
                        } else {
                            $info = 'exists, nothing changed | id: ' . $check['AlbaniaDeputet']['id'];
                            $this->out($info);
                            $toLog .= $info . "\n";
                        }
                    }
                }
                $this->AlbaniaChamber->id = $chamber['AlbaniaChamber']['id'];
                $this->AlbaniaChamber->saveField('status', 1);
            }
        }
        $info = CakeTime::toServer(time()) . ' Albania get_deputed end | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog .= $info . "\n";
        $this->QueleToSend->doLog(
                array(
            'id' => time() . '_' . rand(4, 99999),
            'label' => 'get_deputed',
            'status' => 'finished',
            'params' => explode("\n", $toLog)
                ), 'Albanian'
        );
    }

    public function get_index() {
        $this->QueleToSend->doLog(
                array(
            'id' => time() . '_' . rand(4, 99999),
            'label' => 'get_index',
            'status' => 'running',
                ), 'Albanian'
        );
        $info = CakeTime::toServer(time()) . ' Albania get_index start | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog = $info . "\n";

        $link = 'http://www.parlament.al/web/Procesverbalet_e_Seancave_Plenare_te_Kuvendit_Legjislatura_XVIII_6643_1.php';
        $content = $this->Albania->getMenuListFromLink($link);
        if ($content) {
            foreach ($content as $c) {
                $check = $this->AlbaniaSpeecheIndex->findByUrl($c['url']);
                if (!$check) {
                    $this->AlbaniaSpeecheIndex->create();
                    if ($this->AlbaniaSpeecheIndex->save($c)) {
                        $info = 'save new index | postDate: ' . $c['post_date'];
                        $this->out($info);
                        $toLog .= $info . "\n";
                    }
                } else {
                    $info = 'exists, nothing changed | postDate: ' . $c['post_date'];
                    $this->out($info);
                    $toLog .= $info . "\n";
                }
            }
//            // $this->KosovoSpeecheContent->saveAll($content);
        } else {
            $info = 'something is wrong, I can not retrieve the list of articles';
            $this->out($info);
            $toLog .= $info . "\n";
        }

        $info = CakeTime::toServer(time()) . ' Albania get_index end | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog .= $info . "\n";
        $this->QueleToSend->doLog(
                array(
            'id' => time() . '_' . rand(4, 99999),
            'label' => 'get_index',
            'status' => 'finished',
            'params' => explode("\n", $toLog)
                ), 'Albanian'
        );
    }

    public function get_vote_index() {
        $this->QueleToSend->doLog(
                array(
            'id' => time() . '_' . rand(4, 99999),
            'label' => 'get_vote_index',
            'status' => 'running',
                ), 'Albanian'
        );
        $info = CakeTime::toServer(time()) . ' Albania get_vote_index start | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog = $info . "\n";

        $link = 'http://www.parlament.al/web/Legjislatura_VII_12399_1.php';
        $content = $this->Albania->getPaginPage($link);
        if ($content) {
            foreach ($content as $c) {
                $check = $this->AlbaniaVoteIndex->findByUrl($c['url']);
                if (!$check) {
                    $this->AlbaniaVoteIndex->create();
                    if ($this->AlbaniaVoteIndex->save($c)) {
                        $info = 'save new index | uid: ' . $c['uid'];
                        $this->out($info);
                        $toLog .= $info . "\n";
                    }
                } else {
                    $info = 'exists, nothing changed | uid: ' . $c['uid'];
                    $this->out($info);
                    $toLog .= $info . "\n";
                }
            }
//            // $this->KosovoSpeecheContent->saveAll($content);
        } else {
            $info = 'something is wrong, I can not retrieve the list of articles';
            $this->out($info);
            $toLog .= $info . "\n";
        }

        $info = CakeTime::toServer(time()) . ' Albania get_vote_index end | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog .= $info . "\n";
        $this->QueleToSend->doLog(
                array(
            'id' => time() . '_' . rand(4, 99999),
            'label' => 'get_vote_index',
            'status' => 'finished',
            'params' => explode("\n", $toLog)
                ), 'Albanian'
        );
    }

    public function get_mps_index() {
        $this->QueleToSend->doLog(
                array(
            'id' => time() . '_' . rand(4, 99999),
            'label' => 'get_mps_index',
            'status' => 'running',
                ), 'Albanian'
        );
        $info = CakeTime::toServer(time()) . ' Albania get_mps_index start | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog = $info . "\n";

        $link = 'http://www.parlament.al/web/Jeteshkrime_7033_1.php';
        $content = $this->Albania->checkPagin($link);
        if ($content) {
            foreach ($content as $c) {
                $list = $this->Albania->getPaginPage($c);
                if (!empty($list)) {
                    foreach ($list as $l) {
                        $check = $this->AlbaniaMpsIndex->findByUrl($l['url']);
                        if (!$check) {
                            $this->AlbaniaMpsIndex->create();
                            if ($this->AlbaniaMpsIndex->save($l)) {
                                $info = 'save new index | uid: ' . $l['uid'];
                                $this->out($info);
                                $toLog .= $info . "\n";
                            }
                        } else {
                            $info = 'exists, nothing changed | uid: ' . $l['uid'];
                            $this->out($info);
                            $toLog .= $info . "\n";
                        }
                    }
                    usleep(3000);
                }
                sleep(1);
            }
        } else {
            $info = 'something is wrong, I can not retrieve the list of articles';
            $this->out($info);
            $toLog .= $info . "\n";
        }

        $info = CakeTime::toServer(time()) . ' Albania get_mps_index end | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog .= $info . "\n";
        $this->QueleToSend->doLog(
                array(
            'id' => time() . '_' . rand(4, 99999),
            'label' => 'get_mps_index',
            'status' => 'finished',
            'params' => explode("\n", $toLog)
                ), 'Albanian'
        );
    }

    public function get_doc_from_link($limit) {
        $this->QueleToSend->doLog(
                array(
            'id' => time() . '_' . rand(4, 99999),
            'label' => 'get_doc_from_link',
            'status' => 'running',
                ), 'Albanian'
        );
        $info = CakeTime::toServer(time()) . ' Albania get_doc_from_link start | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog = $info . "\n";

        $limit = !is_null($limit) && (int) $limit ? $limit : 2;
        $link = 'www.parlament.al/web/pub/proc_30_05_2013_13782_1.doc';
        $links = $this->AlbaniaSpeecheIndex->find('list', array(
            'fields' => array('id', 'url'),
            'conditions' => array('status' => 0),
            'limit' => $limit
        ));
        if ($links) {
            foreach ($links as $k => $l) {
                $content = $this->AlbaniaDoc->getDocFromLink($l, $k);
                if ($content) {
                    $info = 'save new docToHtml | id: ' . $k;
                    $this->out($info);
                    $toLog .= $info . "\n";
                    $this->AlbaniaSpeecheIndex->id = $k;
                    $this->AlbaniaSpeecheIndex->saveField('status', 1);
                } else {
                    $info = 'something is wrong, I can not retrieve docToHtml | id: ' . $k;
                    $this->out($info);
                    $toLog .= $info . "\n";
                }
                sleep(10);
            }
        }

        $info = CakeTime::toServer(time()) . ' Albania get_doc_from_link end | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog .= $info . "\n";
        $this->QueleToSend->doLog(
                array(
            'id' => time() . '_' . rand(4, 99999),
            'label' => 'get_doc_from_link',
            'status' => 'finished',
            'params' => explode("\n", $toLog)
                ), 'Albanian'
        );
    }

    public function albania_combine_to_quelle() {
        $this->QueleToSend->doLog(
                array(
            'id' => time() . '_' . rand(4, 99999),
            'label' => 'albania_combine_to_quelle',
            'status' => 'running',
                ), 'Albanian'
        );
        ////////////////organizationConvocation
        $info = CakeTime::toServer(time()) . ' Albania organizationConvocation start | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog = $info . "\n";
        $this->AlbaniaChamber->recursive = -1;
        $content = $this->AlbaniaChamber->find('all', array(
            //'fields' => array('id', 'id'),
            'conditions' => array('api' => 0),
            'contain' => array(
                'AlbaniaDeputet'
            ),
//            'limit' => 10
        ));
        if ($content) {
            foreach ($content as $c) {
                $combines = $this->AlbaniaChamber->combineToApiArray($c);
                $combine[] = $combines;
                $info = 'AlbaniaChamber name: ' . $c['AlbaniaChamber']['name'];
                $this->out($info);
                $toLog .= $info . "\n";
                if (isset($combines) && $combines) {
                    $result = $this->QueleToSend->putDataDB($combines, 'Albanian');
                    //  pr($result);
                    if ($result) {
                        $this->AlbaniaChamber->id = $c['AlbaniaChamber']['id'];
                        $this->AlbaniaChamber->saveField('api', 1);
                    }
                }
            }
        } else {
            $info = 'organizationConvocation nothing to do';
            $this->out($info);
            $toLog .= $info . "\n";
        }
        $info = CakeTime::toServer(time()) . ' Albania organizationConvocation end | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog .= $info . "\n";

        ////////////////people organizationParty organizationParliamentaryGroups organizationCommitte
        $info = CakeTime::toServer(time()) . ' Albania people start | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog .= $info . "\n";
        $content = $this->AlbaniaMpsDetail->find('all', array(
            'conditions' => array(
                'AlbaniaMpsDetail.status' => 0
            ),
//            'limit' => 10
        ));

        if ($content) {
            foreach ($content as $c) {
                $combines = $this->AlbaniaMpsDetail->combineToApiArray($c);
                $combine[] = $combines;
                $info = 'people id: ' . $c['AlbaniaMpsDetail']['id'];
                $this->out($info);
                $toLog .= $info . "\n";
                if (isset($combines) && $combines) {
                    foreach ($combines as $cc) {
                        $result = $this->QueleToSend->putDataDB($cc, 'Albanian');
                    }
                    if ($result) {
                        $this->AlbaniaMpsDetail->id = $c['AlbaniaMpsDetail']['id'];
                        $this->AlbaniaMpsDetail->saveField('status', 1);
                    }
                }
            }
            $info = 'people count: ' . count($content);
            $this->out($info);
            $toLog .= $info . "\n";
        } else {
            $info = 'people nothing to do';
            $this->out($info);
            $toLog .= $info . "\n";
        }
        $info = CakeTime::toServer(time()) . ' Albania people end | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog .= $info . "\n";


        //////////////speeche
        $info = CakeTime::toServer(time()) . ' Albania speecheEvents start | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog .= $info . "\n";
        $content = $this->AlbaniaDoc->find('all', array(
            'conditions' => array(
                'AlbaniaDoc.status' => 0
            ),
            'order' => 'AlbaniaDoc.id ASC',
            'limit' => 10
        ));
//        pr($content);
        if ($content) {
            foreach ($content as $c) {
                $combines = $this->AlbaniaDoc->combineToApiArray($c);
                $combine[] = $combines;
                $info = 'AlbaniaDoc id: ' . $c['AlbaniaDoc']['id'];
                $this->out($info);
                $toLog .= $info . "\n";
                if (isset($combines) && $combines) {
                    $result = $this->QueleToSend->putDataDB($combines, 'Albanian');
//                    //  pr($result);
                    if ($result) {
                        $this->AlbaniaDoc->id = $c['AlbaniaDoc']['id'];
                        $this->AlbaniaDoc->saveField('status', 1);
                    }
                }
            }
        } else {
            $info = 'speeches nothing to do';
            $this->out($info);
            $toLog .= $info . "\n";
        }
        $info = CakeTime::toServer(time()) . ' Albania speecheEvents end | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog .= $info . "\n";
        $this->QueleToSend->doLog(
                array(
            'id' => time() . '_' . rand(4, 99999),
            'label' => 'albania_combine_to_quelle',
            'status' => 'finished',
            'params' => explode("\n", $toLog)
                ), 'Albanian'
        );
        //////
    }

    public function albania_send_to_quelle($limit = null) {
        $limit = !is_null($limit) && (int) $limit ? $limit : 100;
        $halfLimit = $limit / 2;
        $trinityLimit = $limit * 3;

        $this->QueleToSend->doLog(
                array(
            'id' => time() . '_' . rand(4, 99999),
            'label' => 'albania_send_to_quelle',
            'status' => 'running',
                ), 'Albanian'
        );
        $info = CakeTime::toServer(time()) . ' Albania albania_send_to_quelle start | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog = $info . "\n";

        $ids = $this->getListQueleToSend('organizations', $limit);
        $info = 'organizations count: ' . count($ids);
        $this->out($info);
        $toLog .= $info . "\n";
        if (!$ids || count($ids) < $trinityLimit) {
            $people = $this->getListQueleToSend('people', $trinityLimit);
            $info = 'people count: ' . count($people);
            $this->out($info);
            $toLog .= $info . "\n";
            $ids = array_merge($ids, $people);
        }
        if (!$ids || count($ids) < $trinityLimit) {
            $events = $this->getListQueleToSend('events', $limit);
            $info = 'events count: ' . count($events);
            $this->out($info);
            $toLog .= $info . "\n";
            $ids = array_merge($ids, $events);
        }
        if (!$ids || count($ids) < $trinityLimit) {
            $speeches = $this->getListQueleToSend('speeches', $trinityLimit);
            $info = 'speeches count: ' . count($speeches);
            $this->out($info);
            $toLog .= $info . "\n";
            $ids = array_merge($ids, $speeches);
        }
        if (!$ids || count($ids) < $trinityLimit) {
            $motions = $this->getListQueleToSend('motions', $limit);
            $info = 'motions count: ' . count($motions);
            $this->out($info);
            $toLog .= $info . "\n";
            $ids = array_merge($ids, $motions);
        }
        if (!$ids || count($ids) < $trinityLimit) {
            $vote_events = $this->getListQueleToSend('vote-events', $limit);
            $info = 'vote-events count: ' . count($vote_events);
            $this->out($info);
            $toLog .= $info . "\n";
            $ids = array_merge($ids, $vote_events);
        }
        if (!$ids || count($ids) < $trinityLimit) {
            $memberships = $this->getListQueleToSend('memberships', $trinityLimit);
            $info = 'memberships count: ' . count($memberships);
            $this->out($info);
            $toLog .= $info . "\n";
            $ids = array_merge($ids, $memberships);
        }
        if (!$ids || count($ids) < $trinityLimit) {
            $votes = $this->getListQueleToSend('votes', $trinityLimit);
            $info = 'votes count: ' . count($votes);
            $this->out($info);
            $toLog .= $info . "\n";
            $ids = array_merge($ids, $votes);
        }


        $result = null;
        $wait = $send = array();
        if ($ids) {
            foreach ($ids as $id) {
                $result = $this->QueleToSend->doRequest($id);
//                print_r($result);
                $this->QueleToSend->id = $id;
                if ($result['status'] == 1) {
                    $this->QueleToSend->saveField('status', 1);
                    $send[] = $id;
                } else {
                    $hint = $this->QueleToSend->hint($id);
                    $wait[] = $id;
                    if ($hint == 1 || $hint == 100) {
//                        $info = 'wait for id: ' . $id;
//                        $this->out($info);
//                        $toLog .= $info . "\n";
                    }
                }
                $this->QueleToSend->saveField('code', $result['code']);
            }
            $info = 'send count: ' . count($send);
            $this->out($info);
            $toLog .= $info . "\n";
            $info = 'wait count: ' . count($wait);
            $this->out($info);
            $toLog .= $info . "\n";
        } else {
            $info = 'votes nothing to do';
            $this->out($info);
            $toLog .= $info . "\n";
        }
        $info = CakeTime::toServer(time()) . ' Albania albania_send_to_quelle end | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog .= $info . "\n";
        $this->QueleToSend->doLog(
                array(
            'id' => time() . '_' . rand(4, 99999),
            'label' => 'albania_send_to_quelle',
            'status' => 'finished',
            'params' => explode("\n", $toLog)
                ), 'Albanian'
        );
    }

    public function getListQueleToSend($type = null, $limit = null) {
        $limit = !is_null($limit) && (int) $limit ? $limit : 100;
        if (!is_null($type)) {
            return $this->QueleToSend->find('list', array(
                        'fielsd' => array('id', 'id'),
                        'conditions' => array(
                            'direct' => 'Albanian',
                            'type' => $type, //organizations //people
                            'status' => 0,
                            'hints <' => 100,
//                            'modified <' => CakeTime::format('-' . 30 . ' minutes', '%Y-%m-%d %H:%M:%S')
                        ),
                        'orders' => 'modified DESC',
                        'limit' => $limit
            ));
        }
    }

    function convert($size) {
        $unit = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');
        return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
    }

}
