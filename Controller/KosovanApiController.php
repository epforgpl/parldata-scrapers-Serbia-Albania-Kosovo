<?php

App::uses('CakeTime', 'Utility');

class KosovanApiController extends AppController {

    public $uses = array(
        'Schedule',
        'Kosovo',
        'KosovoMpsMenu',
        'KosovoMpsIndex',
        'KosovoMpsDetail',
        'KosovoSpeecheIndex',
        'KosovoSpeecheContent',
        'KosovoParliamentaryGroup',
        'KosovoParty',
        'KosovoMpsPersonalData',
        'KosovoMpsEducation',
        'KosovoMpsActivity',
        'KosovoMpsLanguage',
        'KosovoMpsAddress',
        'KosovoCommittee',
        'KosovoMps',
        'KosovoPdf',
        'KosovoTxt',
        'KosovoLog',
        'QueleToSend'
    );

    public function index() {

    }

    public function organizationConvocation() {
        $this->KosovoMpsMenu->recursive = -1;
        $content = $this->KosovoMpsMenu->find('all', array(
            //'fields' => array('id', 'id'),
            'conditions' => array('api' => 0),
//            'limit' => 10
        ));
        if ($content) {
            foreach ($content as $c) {
                $combines = $this->KosovoMpsMenu->combineToApiArray($c);
                $combine[] = $combines;
                if (isset($combines) && $combines) {
                    $result = $this->QueleToSend->putDataDB(array($combines), 'Kosovan');
                    //  pr($result);
                    if ($result) {
                        $this->KosovoMpsMenu->id = $c['KosovoMpsMenu']['id'];
                        $this->KosovoMpsMenu->saveField('api', 1);
                    }
                }
            }
        }
        $this->set(compact('content', 'combine'));
    }

    public function people() {
//        $this->KosovoMpsDetail->recursive = -1;
        $content = $this->KosovoMpsDetail->find('all', array(
            'conditions' => array(
                'KosovoMpsDetail.status' => 0
            ),
//            'limit' => 1
        ));
        if ($content) {
            foreach ($content as $c) {
                $combines = $this->KosovoMpsDetail->combineToApiArray($c);
                $combine[] = $combines;
                if (isset($combines) && $combines) {
                    $result = $this->QueleToSend->putDataDB(array($combines), 'Kosovan');
//                    //  pr($result);
                    if ($result) {
                        $this->KosovoMpsDetail->id = $c['KosovoMpsDetail']['id'];
                        $this->KosovoMpsDetail->saveField('status', 1);
                    }
                }
            }
        }

        $this->set(compact('content', 'combine'));
    }

    public function organizationParty() {
        $content = $this->KosovoParty->find('all', array(
            //'fields' => array('id', 'id'),
            'conditions' => array(
                'api' => 0,
                'shortcut !=' => '-'
            ),
            'contain' => array(
                'KosovoMpsDetail' => array(
                    'KosovoMpsIndex'
                )
            ),
//            'limit' => 1
        ));
        if ($content) {
            foreach ($content as $c) {
                $combines = $this->KosovoParty->combineToApiArray($c);
                $combine[] = $combines;
                if (isset($combines) && $combines) {
//                    pr($combines);
                    $result = $this->QueleToSend->putDataDB($combines, 'Kosovan');
//                    //  pr($result);
                    if ($result) {
                        $this->KosovoParty->id = $c['KosovoParty']['id'];
                        $this->KosovoParty->saveField('api', 1);
                    }
                }
            }
        }
        $this->set(compact('content', 'combine'));
    }

    public function organizationParliamentaryGroups() {
        $content = $this->KosovoParliamentaryGroup->find('all', array(
            //'fields' => array('id', 'id'),
            'conditions' => array(
                'api' => 0,
            ),
            'contain' => array(
                'KosovoMpsDetail' => array(
                    'KosovoMpsIndex'
                )
            ),
//            'limit' => 1
        ));
        if ($content) {
            foreach ($content as $c) {
                $combines = $this->KosovoParliamentaryGroup->combineToApiArray($c);
                $combine[] = $combines;
                if (isset($combines) && $combines) {
                    $result = $this->QueleToSend->putDataDB($combines, 'Kosovan');
                    //  pr($result);
                    if ($result) {
                        $this->KosovoParliamentaryGroup->id = $c['KosovoParliamentaryGroup']['id'];
                        $this->KosovoParliamentaryGroup->saveField('api', 1);
                    }
                }
            }
        }
        $this->set(compact('content', 'combine'));
    }

    public function organizationCommitte() {
        $content = $this->KosovoCommittee->find('all', array(
            //'fields' => array('id', 'id'),
            'conditions' => array(
                'api' => 0,
                'KosovoCommittee.uid >' => 0
            ),
            'contain' => array(
                'KosovoMpsDetail' => array(
                    'KosovoMpsIndex',
                    'KosovoCommitteFunc'
                )
            ),
//            'limit' => 1
        ));
        if ($content) {
            foreach ($content as $c) {
                $combines = $this->KosovoCommittee->combineToApiArray($c);
                $combine[] = $combines;
                if (isset($combines) && $combines) {
                    $result = $this->QueleToSend->putDataDB($combines, 'Kosovan');
                    //  pr($result);
                    if ($result) {
                        $this->KosovoCommittee->id = $c['KosovoCommittee']['id'];
                        $this->KosovoCommittee->saveField('api', 1);
                    }
                }
            }
        }
        $this->set(compact('content', 'combine'));
    }

    public function speecheEvents() {
        $content = $this->KosovoSpeecheIndex->find('all', array(
            'conditions' => array(
                'KosovoSpeecheIndex.status' => 1
            ),
            'contain' => array(
                'KosovoSpeecheContent' => array(
                    'KosovoPdf' => array(
                        'fields' => array('id', 'kosovo_speeche_content_id', 'pdf_url')
                    )
                )
            ),
            'order' => 'post_uid DESC',
        ));
//        pr($content);
        if ($content) {
            foreach ($content as $c) {
                $combines = $this->KosovoSpeecheContent->combineToApiArray($c);
                $combine[] = $combines;
                if (isset($combines) && $combines) {
                    $result = $this->QueleToSend->putDataDB($combines, 'Kosovan');
//                    //  pr($result);
                    if ($result) {
                        $this->KosovoSpeecheIndex->id = $c['KosovoSpeecheIndex']['id'];
                        $this->KosovoSpeecheIndex->saveField('status', 2);
                    }
                }
            }
        }
        $this->set(compact('content', 'combine'));
    }

    public function votes() {
        $content = $this->KosovoTxt->find('all', array(
            'conditions' => array(
                'KosovoTxt.status' => 1
            ),
            'contain' => array(
                'KosovoSpeecheContent' => array(
                    'KosovoSpeecheIndex'
                )
            ),
//            'order' => 'post_uid DESC',
            'limit' => 5
        ));
        if ($content) {
            foreach ($content as $c) {

                $combines = $this->KosovoTxt->combineToApiArray($c);
                $combine[] = $combines;
                if (isset($combines) && $combines) {
                    $result = $this->QueleToSend->putDataDB(array($combines), 'Kosovan');
                    //  pr($result);
                    if ($result) {
                        $this->KosovoTxt->id = $c['KosovoTxt']['id'];
                        $this->KosovoTxt->saveField('status', 2);
                    }
                }
            }
        }
        $this->set(compact('content', 'combine'));
    }

    public function listQuele($type = null) {
        $this->paginate = array(
            'conditions' => array(
                'QueleToSend.direct' => 'Kosovan',
                'QueleToSend.type' => $type
            ),
            'order' => 'QueleToSend.created DESC',
            'limit' => 50,
        );
        $content = $this->paginate('QueleToSend');
        $this->set(compact('content'));
    }

    public function viewData($id = null) {
        $content = $this->QueleToSend->findById($id);
        $this->set(compact('content'));
    }

    public function sendToApi($limit = null) {
        $limit = !is_null($limit) && (int) $limit ? $limit : 5;
        $halfLimit = $limit / 2;
        $trinityLimit = $limit * 3;

        $info[] = CakeTime::toServer(time()) . ' Kosovo kosovo_send_to_quelle start | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $ids = $this->getListQueleToSend('organizations', $limit);
        $info[] = 'organizations count: ' . count($ids);
        if (!$ids || count($ids) < $trinityLimit) {
            $people = $this->getListQueleToSend('people', $trinityLimit);
            $info[] = 'people count: ' . count($people);
            $ids = array_merge($ids, $people);
        }
        if (!$ids || count($ids) < $trinityLimit) {
            $events = $this->getListQueleToSend('events', $limit);
            $info[] = 'events count: ' . count($events);
            $ids = array_merge($ids, $events);
        }
        if (!$ids || count($ids) < $trinityLimit) {
            $speeches = $this->getListQueleToSend('speeches', $limit);
            $info[] = 'speeches count: ' . count($speeches);
            $ids = array_merge($ids, $speeches);
        }
        if (!$ids || count($ids) < $trinityLimit) {
            $motions = $this->getListQueleToSend('motions', $limit);
            $info[] = 'motions count: ' . count($motions);
            $ids = array_merge($ids, $motions);
        }
        if (!$ids || count($ids) < $trinityLimit) {
            $vote_events = $this->getListQueleToSend('vote-events', $limit);
            $info[] = 'vote-events count: ' . count($vote_events);
            $ids = array_merge($ids, $vote_events);
        }
        if (!$ids || count($ids) < $trinityLimit) {
            $memberships = $this->getListQueleToSend('memberships', $trinityLimit);
            $info[] = 'memberships count: ' . count($memberships);
            $ids = array_merge($ids, $memberships);
        }
        if (!$ids || count($ids) < $trinityLimit) {
            $votes = $this->getListQueleToSend('votes', $trinityLimit);
            $info[] = 'votes count: ' . count($votes);
            $ids = array_merge($ids, $votes);
        }

        $result = null;
        $wait = $send = array();
        if ($ids) {
            foreach ($ids as $id) {
                $result = $this->QueleToSend->doRequest($id);
                $this->QueleToSend->id = $id;
                if ($result['status'] == 1) {
                    $this->QueleToSend->saveField('status', 1);
                    $send[] = $id;
                } else {
                    $hint = $this->QueleToSend->hint($id);
                    $wait[] = $id;
                    if ($hint == 1 || $hint == 100) {
//                        $info[] = 'wait for id: ' . $id;
                    }
                }
                $this->QueleToSend->saveField('code', $result['code']);
            }
            $info[] = 'send count: ' . count($send);
            $info[] = 'wait count: ' . count($wait);
        } else {
            $info[] = 'votes nothing to do';
        }
        $info[] = CakeTime::toServer(time()) . ' Kosovo kosovo_send_to_quelle end | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $combine = $info;
        $content = $ids;
        $this->set(compact('content', 'combine'));
    }

    public function speeches() {
        $content = $this->KosovoPdf->find('all', array(
            'conditions' => array(
//                'KosovoPdf.id' => 310,
                'KosovoPdf.status' => 1
            ),
            'contain' => array(
                'KosovoSpeecheContent' => array(
                    'KosovoSpeecheIndex'
                )
            ),
//            'order' => 'post_uid DESC',
            'limit' => 10
        ));
        if ($content) {
            foreach ($content as $c) {
//
                $combines = $this->KosovoPdf->combineToApiArray($c);
                $combine[] = $combines;
                if (isset($combines) && $combines) {
                    $result = $this->QueleToSend->putDataDB($combines, 'Kosovan');
//                    //  pr($result);
                    if ($result) {
                        $this->KosovoPdf->id = $c['KosovoPdf']['id'];
                        $this->KosovoPdf->saveField('status', 2);
                    }
                }
            }
        }
        $this->set(compact('content', 'combine'));
    }

    public function getListQueleToSend($type = null, $limit = null) {
        $limit = !is_null($limit) && (int) $limit ? $limit : 100;
        if (!is_null($type)) {
            return $this->QueleToSend->find('list', array(
                        'fielsd' => array('id', 'id'),
                        'conditions' => array(
                            'direct' => 'Kosovan',
                            'type' => $type, //organizations //people
                            'status' => 0,
                            'hints <' => 1000,
                        // 'modified <' => CakeTime::format('-' . 30 . ' minutes', '%Y-%m-%d %H:%M:%S')
                        ),
                        'orders' => 'modified DESC',
                        'limit' => $limit
            ));
        }
    }

    public function testPeople($name = 'Džezair Murati') {
        $this->autoRender = false;
        $combines = $this->KosovoPdf->checkKosovoPeopleExist($name, 3);
        pr($combines);
    }

}
