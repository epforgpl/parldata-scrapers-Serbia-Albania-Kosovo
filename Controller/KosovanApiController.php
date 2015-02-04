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
//        $this->SerbianMpsDetail->recursive = -1;
//        $content = $this->SerbianMpsDetail->find('all', array(
//            'conditions' => array('status' => 0),
//            'limit' => 50
//        ));
//        if ($content) {
//            foreach ($content as $c) {
//                $combines = $this->SerbianMpsDetail->combineToApiArray($c['SerbianMpsDetail']);
//                $combine[] = $combines;
//                if (isset($combines) && $combines) {
//                    $result = $this->QueleToSend->putDataDB(array($combines), 'Serbian');
//                    //  pr($result);
//                    if ($result) {
//                        $this->SerbianMpsDetail->id = $c['SerbianMpsDetail']['id'];
//                        $this->SerbianMpsDetail->saveField('status', 1);
//                    }
//                }
//            }
//        }

        $this->set(compact('content', 'combine'));
    }

}
