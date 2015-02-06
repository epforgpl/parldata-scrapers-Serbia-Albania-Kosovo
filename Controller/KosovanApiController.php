<?php

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
            'limit' => 10
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
            'conditions' => array('KosovoMpsDetail.status' => 0),
//            'limit' => 10
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

}
