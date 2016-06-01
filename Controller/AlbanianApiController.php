<?php

App::uses('CakeTime', 'Utility');

class AlbanianApiController extends AppController {

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

    public function index() {

    }

    public function organizationConvocation() {
        $this->AlbaniaChamber->recursive = -1;
        $content = $this->AlbaniaChamber->find('all', array(
            //'fields' => array('id', 'id'),
            'conditions' => array('api' => 0),
            'contain' => array(
                'AlbaniaDeputet'
            ),
            'order' => 'AlbaniaChamber.id DESC',
            'limit' => 1
        ));
        if ($content) {
            foreach ($content as $c) {
                $combines = $this->AlbaniaChamber->combineToApiArray($c);
                $combine[] = $combines;
                if (isset($combines) && $combines) {
                    $result = $this->QueleToSend->putDataDB($combines, 'Albanian');
//                    //  pr($result);
                    if ($result) {
                        $this->AlbaniaChamber->id = $c['AlbaniaChamber']['id'];
                        $this->AlbaniaChamber->saveField('api', 1);
                    }
                }
            }
        }
        $this->set(compact('content', 'combine'));
    }

    public function people() {
//        $this->KosovoMpsDetail->recursive = -1;
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
        }
//        foreach ($content as $conten) {
//            $aaa[] = $conten['AlbaniaMpsDetail']['group_parliamentary_committees'];
//        }
//        $content = $aaa;
        $this->set(compact('content', 'combine'));
    }

    public function speeches() {

        $content = $this->AlbaniaDoc->find('all', array(
            'conditions' => array(
                'AlbaniaDoc.status' => 0
            ),
            'contain' => array(
                'AlbaniaSpeecheIndex' => array(
                    'AlbaniaSpecheSession'
                )
            ),
            'order' => 'AlbaniaDoc.id ASC',
            'limit' => 1
        ));
//        pr($content);
        if ($content) {
            foreach ($content as $c) {
                $combines = $this->AlbaniaDoc->combineToApiArray($c);
                $combine[] = $combines;
                if (isset($combines) && $combines) {
                    $result = $this->QueleToSend->putDataDB($combines, 'Albanian');
//                    //  pr($result);
                    if ($result) {
                        $this->AlbaniaDoc->id = $c['AlbaniaDoc']['id'];
                        $this->AlbaniaDoc->saveField('status', 1);
                    }
                }
            }
        }
        $this->set(compact('content', 'combine'));
    }

    public function listQuele($type = null) {
        $this->paginate = array(
            'conditions' => array(
                'QueleToSend.direct' => 'Albanian',
                'QueleToSend.type' => $type
            ),
            'order' => 'QueleToSend.created DESC',
            'limit' => 50,
        );
        $content = $this->paginate('QueleToSend');
        $this->set(compact('content'));
    }

    public function sendToApi($limit = null) {
        $limit = !is_null($limit) && (int) $limit ? $limit : 5;
        $halfLimit = $limit / 2;
        $trinityLimit = $limit * 3;

        $info[] = CakeTime::toServer(time()) . ' Albania albania_send_to_quelle start | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
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
        $info[] = CakeTime::toServer(time()) . ' Albania albania_send_to_quelle end | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $combine = $info;
        $content = $ids;
        $this->set(compact('content', 'combine'));
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
                            'hints <' => 1000,
                        // 'modified <' => CakeTime::format('-' . 30 . ' minutes', '%Y-%m-%d %H:%M:%S')
                        ),
                        'orders' => 'modified DESC',
                        'limit' => $limit
            ));
        }
    }

}
