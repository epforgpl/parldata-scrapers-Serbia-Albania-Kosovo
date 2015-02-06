<?php

App::uses('CakeTime', 'Utility');

class SerbianApiController extends AppController {

    public $uses = array(
        'Schedule',
        'Serbia',
        'SerbianSpeecheIndex',
        'SerbianSpeecheContent',
        'SerbianPdf',
        'SerbianLog',
        'SerbianMps',
        'SerbianMpsDetail',
        'SerbianMenuData',
        'SerbianResidence',
        'SerbianParty',
        'SerbianParliamentaryGroup',
        'SerbianCommitte',
        'SerbianDelegate',
        'SerbianParliamentaryGroupFunc',
        'SerbianCommitteFunc',
        'SerbianDelegationMembership',
        'SerbianDelegationMembershipFunc',
        'SerbianFriendship',
        'SerbianFriendshipFunc',
        'SerbianFunction',
        'QueleToSend'
    );

    public function index() {

    }

    public function people() {
        $this->SerbianMpsDetail->recursive = -1;
        $content = $this->SerbianMpsDetail->find('all', array(
            'conditions' => array('status' => 0),
//            'limit' => 50
        ));
        if ($content) {
            foreach ($content as $c) {
                $combines = $this->SerbianMpsDetail->combineToApiArray($c['SerbianMpsDetail']);
                $combine[] = $combines;
                if (isset($combines) && $combines) {
                    $result = $this->QueleToSend->putDataDB(array($combines), 'Serbian');
                    //  pr($result);
                    if ($result) {
                        $this->SerbianMpsDetail->id = $c['SerbianMpsDetail']['id'];
                        $this->SerbianMpsDetail->saveField('status', 1);
                    }
                }
            }
        }

        $this->set(compact('content', 'combine'));
    }

    public function organizationConvocation() {
        $this->SerbianMenuData->recursive = -1;
        $content = $this->SerbianMenuData->find('all', array(
            //'fields' => array('id', 'id'),
            'conditions' => array('api' => 0),
//            'limit' => 10
        ));
        if ($content) {
            foreach ($content as $c) {
                $combines = $this->SerbianMenuData->combineToApiArray($c);
                $combine[] = $combines;
                if (isset($combines) && $combines) {
                    $result = $this->QueleToSend->putDataDB(array($combines), 'Serbian');
                    //  pr($result);
                    if ($result) {
                        $this->SerbianMenuData->id = $c['SerbianMenuData']['id'];
                        $this->SerbianMenuData->saveField('api', 1);
                    }
                }
            }
        }
        $this->set(compact('content', 'combine'));
    }

    public function organizationParty() {
        $content = $this->SerbianParty->find('all', array(
            //'fields' => array('id', 'id'),
            'conditions' => array(
                'api' => 0,
                'shortcut !=' => '-'
            ),
            'contain' => array(
                'SerbianMpsDetail' => array(
                    'SerbianMenuData'
                )
            ),
//            'limit' => 1
        ));
        if ($content) {
            foreach ($content as $c) {
                $combines = $this->SerbianParty->combineToApiArray($c);
                $combine[] = $combines;
                if (isset($combines) && $combines) {
                    $result = $this->QueleToSend->putDataDB($combines, 'Serbian');
                    //  pr($result);
                    if ($result) {
                        $this->SerbianParty->id = $c['SerbianParty']['id'];
                        $this->SerbianParty->saveField('api', 1);
                    }
                }
            }
        }
        $this->set(compact('content', 'combine'));
    }

    public function organizationParliamentaryGroups() {
        $content = $this->SerbianParliamentaryGroup->find('all', array(
            //'fields' => array('id', 'id'),
            'conditions' => array(
                'api' => 0,
            ),
//            'recursive' => -1,
//            'limit' => 2
        ));
        if ($content) {
            foreach ($content as $c) {
                $combines = $this->SerbianParliamentaryGroup->combineToApiArray($c);
                $combine[] = $combines;
                if (isset($combines) && $combines) {
                    $result = $this->QueleToSend->putDataDB($combines, 'Serbian');
                    //  pr($result);
                    if ($result) {
                        $this->SerbianParliamentaryGroup->id = $c['SerbianParliamentaryGroup']['id'];
                        $this->SerbianParliamentaryGroup->saveField('api', 1);
                    }
                }
            }
        }
        $this->set(compact('content', 'combine'));
    }

    public function organizationCommitte() {
        $content = $this->SerbianCommitte->find('all', array(
            //'fields' => array('id', 'id'),
            'conditions' => array(
                'api' => 0,
            ),
//            'recursive' => -1,
//            'limit' => 2
        ));
        if ($content) {
            foreach ($content as $c) {
                $combines = $this->SerbianCommitte->combineToApiArray($c);
                $combine[] = $combines;
                if (isset($combines) && $combines) {
                    $result = $this->QueleToSend->putDataDB($combines, 'Serbian');
                    //  pr($result);
                    if ($result) {
                        $this->SerbianCommitte->id = $c['SerbianCommitte']['id'];
                        $this->SerbianCommitte->saveField('api', 1);
                    }
                }
            }
        }
        $this->set(compact('content', 'combine'));
    }

    public function organizationDelegation() {
        $content = $this->SerbianDelegationMembership->find('all', array(
            //'fields' => array('id', 'id'),
            'conditions' => array(
                'api' => 0,
            ),
//            'recursive' => -1,
//            'limit' => 2
        ));
        if ($content) {
            foreach ($content as $c) {
                $combines = $this->SerbianDelegationMembership->combineToApiArray($c);
                $combine[] = $combines;
                if (isset($combines) && $combines) {
                    $result = $this->QueleToSend->putDataDB($combines, 'Serbian');
                    //  pr($result);
                    if ($result) {
                        $this->SerbianDelegationMembership->id = $c['SerbianDelegationMembership']['id'];
                        $this->SerbianDelegationMembership->saveField('api', 1);
                    }
                }
            }
        }
        $this->set(compact('content', 'combine'));
    }

    public function organizationFriendship() {
        $content = $this->SerbianFriendship->find('all', array(
            //'fields' => array('id', 'id'),
            'conditions' => array(
                'api' => 0,
            ),
//            'recursive' => -1,
//            'limit' => 2
        ));
        if ($content) {
            foreach ($content as $c) {
                $combines = $this->SerbianFriendship->combineToApiArray($c);
                $combine[] = $combines;
                if (isset($combines) && $combines) {
                    $result = $this->QueleToSend->putDataDB($combines, 'Serbian');
                    //  pr($result);
                    if ($result) {
                        $this->SerbianFriendship->id = $c['SerbianFriendship']['id'];
                        $this->SerbianFriendship->saveField('api', 1);
                    }
                }
            }
        }
        $this->set(compact('content', 'combine'));
    }

    public function organizationSpeaker() {
        $content = $this->SerbianFunction->find('all', array(
            //'fields' => array('id', 'id'),
            'conditions' => array(
//                'api' => 0,
            ),
//            'recursive' => -1,
//            'limit' => 2
        ));
        if ($content) {
            foreach ($content as $c) {
                $combines = $this->SerbianFunction->combineToApiArray($c);
                $combine[] = $combines;
                if (isset($combines) && $combines) {
                    $result = $this->QueleToSend->putDataDB($combines, 'Serbian');
                }
            }
        }
        $this->set(compact('content', 'combine'));
    }

    public function membershipConvocation() {
        $content = $this->SerbianDelegate->find('all', array(
            //'fields' => array('id', 'id'),
            'conditions' => array(
                'SerbianDelegate.status' => 0,
            // 'SerbianDelegate.url_uid' => null
            ),
            'contain' => array(
                'SerbianMenuData.start_date'
            ),
//            'limit' => 100
        ));

        if ($content) {
            foreach ($content as $c) {
                $combines = $this->SerbianDelegate->combineToApiArray($c);
                $combine[] = $combines;
                if (isset($combines) && $combines) {
                    $result = $this->QueleToSend->putDataDB(array($combines), 'Serbian');
                    //  pr($result);
                    if ($result) {
                        $this->SerbianDelegate->id = $c['SerbianDelegate']['id'];
                        $this->SerbianDelegate->saveField('status', 1);
                    }
                }
            }
        }

        $this->set(compact('content', 'combine'));
    }

    public function speeches() {

        $content = $this->SerbianSpeecheIndex->find('all', array(
            'conditions' => array(
                'SerbianSpeecheIndex.lang' => 'sr',
                'SerbianSpeecheIndex.status' => 1
            ),
            'order' => 'post_uid DESC',
//            'limit' => 50
        ));
//        pr($content);
        if ($content) {
            foreach ($content as $c) {
                $combines = $this->SerbianSpeecheContent->combineToApiArray($c);
                $combine[] = $combines;
                if (isset($combines) && $combines) {
                    $result = $this->QueleToSend->putDataDB($combines, 'Serbian');
                    //  pr($result);
                    if ($result) {
                        $this->SerbianSpeecheIndex->id = $c['SerbianSpeecheIndex']['id'];
                        $this->SerbianSpeecheIndex->saveField('status', 2);
                    }
                }
            }
        }
        $this->set(compact('content', 'combine'));
    }

    public function votes() {
        $content = $this->SerbianPdf->find('all', array(
            'fields' => array(
                'SerbianPdf.id',
                'SerbianPdf.post_date',
                'SerbianPdf.stamp_in_text',
                'SerbianPdf.pdf_url',
                'SerbianPdf.name_sr',
                'SerbianPdf.content_sr',
            ),
            'conditions' => array(
//                'SerbianPdf.lang' => 'sr',
                'SerbianPdf.status' => 1
            ),
//            'order' => 'post_uid DESC',
            'limit' => 5
        ));
        if ($content) {
            foreach ($content as $c) {
                $c['doEvent'] = $this->SerbianSpeecheIndex->find('first', array(
                    'fields' => array('SerbianSpeecheIndex.id', 'SerbianSpeecheIndex.post_uid', 'SerbianSpeecheIndex.post_date'),
                    'conditions' => array(
                        'SerbianSpeecheIndex.post_date' => $c['SerbianPdf']['post_date'],
                        'SerbianSpeecheIndex.lang' => 'sr',
                    ),
                    'contain' => array(
                        'SerbianSpeecheContent.id', 'SerbianSpeecheContent.serbian_speeche_index_id', 'SerbianSpeecheContent.convert_date',
                    )
                ));

//                pr($c);
                $combines = $this->SerbianPdf->combineToApiArray($c);
                $combine[] = $combines;
                if (isset($combines) && $combines) {
                    $result = $this->QueleToSend->putDataDB(array($combines), 'Serbian');
                    //  pr($result);
                    if ($result) {
                        $this->SerbianPdf->id = $c['SerbianPdf']['id'];
                        $this->SerbianPdf->saveField('status', 2);
                    }
                }
            }
        }
        $this->set(compact('content', 'combine'));
    }

    public function sendToApi() {
        $info[] = CakeTime::toServer(time()) . ' Serbia serbia_send_to_quelle start | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $ids = $this->getListQueleToSend('organizations', 50);
        $info[] = 'organizations count: ' . count($ids);
        if (!$ids) {
            $ids = $this->getListQueleToSend('people', 50);
            $info[] = 'people count: ' . count($ids);
        }
        if (!$ids) {
            $ids = $this->getListQueleToSend('events', 50);
            $info[] = 'events count: ' . count($ids);
        }
        if (!$ids) {
            $ids = $this->getListQueleToSend('speeches', 50);
            $info[] = 'speeches count: ' . count($ids);
        }
        if (!$ids) {
            $ids = $this->getListQueleToSend('motions', 50);
            $info[] = 'motions count: ' . count($ids);
        }
        if (!$ids) {
            $ids = $this->getListQueleToSend('vote-events', 50);
            $info[] = 'vote-events count: ' . count($ids);
        }
        if (!$ids) {
            $ids1 = $this->getListQueleToSend('votes', 1000);
            $info[] = 'votes count: ' . count($ids1);
            $ids2 = $this->getListQueleToSend('memberships', 1000);
            $info[] = 'memberships count: ' . count($ids2);
            $ids = array_merge($ids1, $ids2);
        }
//        if (!$ids) {
//            $ids = $this->getListQueleToSend('memberships', 50);
//            $info[] = 'memberships count: ' . count($ids);
//        }
        $result = null;
        $wait = $send = array();
        if ($ids) {
            foreach ($ids as $id) {
                $result = $this->QueleToSend->doRequest($id);
                if ($result) {
                    $this->QueleToSend->id = $id;
                    $this->QueleToSend->saveField('status', 1);
                    $send[] = $id;
                } else {
                    $hint = $this->QueleToSend->hint($id);
                    $wait[] = $id;
                    if ($hint == 1 || $hint == 1000) {
                        //$info[] = 'wait for id: ' . $id;
                    }
                }
            }
            $info[] = 'send count: ' . count($send);
            $info[] = 'wait count: ' . count($wait);
        } else {
            $info[] = 'votes nothing to do';
        }
        $info[] = CakeTime::toServer(time()) . ' Serbia serbia_send_to_quelle end | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
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

    public function testSend($id = null) {
        $this->autoRender = false;
        if (!is_null($id)) {
            $result = $this->QueleToSend->doRequest($id, true);
        } else {
            echo 'daj ID w url';
            $this->QueleToSend->deleteSerbiaAll();
        }
    }

    public function listQuele($type = null) {
        $this->paginate = array(
            'conditions' => array(
                'QueleToSend.direct' => 'Serbian',
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

}
