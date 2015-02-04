<?php

App::uses('CakeTime', 'Utility');

class AlbanianController extends AppController {

    public $uses = array(
        'Schedule',
        'Albania',
        'AlbaniaSpeecheIndex',
        'AlbaniaVoteIndex',
        'AlbaniaMpsIndex',
        'AlbaniaMpsDetail',
        'AlbaniaDoc',
        'AlbaniaPdf',
        'AlbaniaLog'
    );

    public function index() {

    }

    public function getMpsParty() {

        $this->set(compact('content'));
    }

    public function listMpsIndex() {
        $conditions = array();
        $this->paginate = array(
            'conditions' => $conditions,
            'order' => 'AlbaniaMpsIndex.uid DESC',
            'limit' => 50,
        );
        $content = $this->paginate('AlbaniaMpsIndex');
        $this->set(compact('content'));
    }

    public function getPlenaryVoting() {

    }

    public function getMpsContact() {
//        $content = $this->Albania->getMpsContent('http://www.parlament.al/web/KLOSI_Blendi_10696_1.php', 10696);
//        $this->set(compact('content'));
    }

    public function listMpsDetails() {
        $conditions = array();
        $this->paginate = array(
            'conditions' => $conditions,
            'order' => 'AlbaniaMpsDetail.id DESC',
            'limit' => 20,
        );
        $content = $this->paginate('AlbaniaMpsDetail');
        $this->set(compact('content'));
    }

    public function viewDelegate($id = null) {
        $content = $this->AlbaniaMpsDetail->findById($id);
        $this->set(compact('content'));
    }

    public function getPlenarySpeeches() {
//        $limit = 1;
//        $limit = !is_null($limit) && (int) $limit ? $limit : 2;
//        $link = 'www.parlament.al/web/pub/proc_30_05_2013_13782_1.doc';
//        $links = $this->AlbaniaSpeecheIndex->find('list', array(
//            'fields' => array('id', 'url'),
//            'conditions' => array('status' => 0),
//            'limit' => $limit
//        ));
//        if ($links) {
//            foreach ($links as $k => $l) {
//                $content = $this->AlbaniaDoc->getDocFromLink($l, $k);
//                if ($content) {
//                    $this->AlbaniaSpeecheIndex->id = $k;
//                    $this->AlbaniaSpeecheIndex->saveField('status', 1);
//                }
//                sleep(10);
//            }
//        }
//        pr($links);
//        // $content = $this->AlbaniaDoc->getDocFromLink($link, 123);
//        $this->set(compact('content'));
    }

    public function listIndex() {
        $conditions = array();
        $this->paginate = array(
            'conditions' => $conditions,
            'order' => 'AlbaniaSpeecheIndex.post_date DESC',
            'contain' => array('AlbaniaDoc.id'),
            'limit' => 50,
        );
        $content = $this->paginate('AlbaniaSpeecheIndex');
        $this->set(compact('content'));
    }

    public function viewContent($id = null) {
        $content = $this->AlbaniaDoc->findById($id);
        $this->set(compact('content'));
    }

    public function listVoteIndex() {
        $conditions = array();
        $this->paginate = array(
            'conditions' => $conditions,
            'order' => 'AlbaniaVoteIndex.id DESC',
            'limit' => 50,
        );
        $content = $this->paginate('AlbaniaVoteIndex');
        $this->set(compact('content'));
    }

    public function listLogSpeches() {
        $this->paginate = array(
            'conditions' => array('AlbaniaLog.type' => array(0)),
            'order' => 'AlbaniaLog.created DESC',
            'limit' => 50,
        );
        $content = $this->paginate('AlbaniaLog');
        $this->set(compact('content'));
    }

    public function listLogVoteSpeches() {
        $this->paginate = array(
            'conditions' => array('AlbaniaLog.type' => array(1)),
            'order' => 'AlbaniaLog.created DESC',
            'limit' => 50,
        );
        $content = $this->paginate('AlbaniaLog');
        $this->set(compact('content'));
    }

    public function listLogMps() {
        $this->paginate = array(
            'conditions' => array('AlbaniaLog.type' => array(2)),
            'order' => 'AlbaniaLog.created DESC',
            'limit' => 50,
        );
        $content = $this->paginate('AlbaniaLog');
        $this->set(compact('content'));
    }

    public function listLogMpsDetails() {
        $this->paginate = array(
            'conditions' => array('AlbaniaLog.type' => array(3, 4)),
            'order' => 'AlbaniaLog.created DESC',
            'limit' => 50,
        );
        $content = $this->paginate('AlbaniaLog');
        $this->set(compact('content'));
    }

    public function viewLog($id = null) {
        $content = $this->AlbaniaLog->findById($id);
        $this->set(compact('content'));
    }

    public function getSchedules() {
        $this->paginate = array(
            'conditions' => array(
                'name' => 'albania'
            ),
            'limit' => 50,
            'recursive' => -1
        );
        $content = $this->paginate('Schedule');

        $this->set(compact('content'));
    }

}
