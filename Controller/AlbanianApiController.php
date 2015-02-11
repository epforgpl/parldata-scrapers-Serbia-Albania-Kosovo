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
        'QueleToSend'
    );

    public function index() {

    }

    public function organizationConvocation() {
        $this->AlbaniaChamber->recursive = -1;
        $content = $this->AlbaniaChamber->find('all', array(
            //'fields' => array('id', 'id'),
            'conditions' => array('api' => 0),
//            'limit' => 10
        ));
        if ($content) {
            foreach ($content as $c) {
                $combines = $this->AlbaniaChamber->combineToApiArray($c);
                $combine[] = $combines;
                if (isset($combines) && $combines) {
                    $result = $this->QueleToSend->putDataDB(array($combines), 'Albanian');
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
                'AlbaniaMpsDetail.id' => 17051,
                'AlbaniaMpsDetail.status' => 0
            ),
            'limit' => 1
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

}
