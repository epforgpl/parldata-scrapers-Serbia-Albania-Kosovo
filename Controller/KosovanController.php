<?php

App::uses('CakeTime', 'Utility');

class KosovanController extends AppController {

    public $uses = array(
        'Schedule',
        'Kosovo',
        'KosovoMpsMenu',
        'KosovoMpsIndex',
        'KosovoMpsDetail',
        'KosovoSpeecheIndex',
        'KosovoSpeecheContent',
        'KosovoCommittee',
        'KosovoCommitteFunc',
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
        'KosovoLog'
    );

    public function beforeRender() {
        parent::beforeRender();
        $kosovoHost = $this->Kosovo->getKosovoHost;
        $this->set(compact('kosovoHost'));
    }

    public function index() {

    }

    public function getPlenarySpeeches() {
        $ids = $this->KosovoPdf->find('list', array(
            'fields' => array('id', 'id'),
            'conditions' => array(
                'KosovoPdf.status' => 1
            ),
            'limit' => 30
        ));
//        $id = 176;
//        if ($ids) {
//            foreach ($ids as $id) {
//                $fileFolder = WWW_ROOT . 'files' . DS . 'kosovo' . DS;
//                $content = (file_get_contents($fileFolder . $id . '.html'));
//                $content = preg_replace('/&nbsp;/i', ' ', $content);
//                $content = preg_replace('/|<hr>|<\!DOCTYPE.*?(>)|<BODY.*?(>)|<\/BODY>|<\/HTML>/i', '', $content);
//                $content = preg_replace('/<HTML>.*?(<\/HEAD>)/ism', '', $content);
//                $content = preg_replace('/\s<br>\\n(?=[Ј,с,\(])/ism', ' ', $content);
//                $content = preg_replace('/\\n<A.*?(<\/a>)/ism', '', $content);
//                $content = preg_replace('/(\xC2\xA0|&nbsp;)/', ' ', $content);
////        $content = preg_replace('/<br>\s<br>|<b>\s<\/b>/ism', '', $content);
//                $content = preg_replace('/<br><br><br><br>|<br><br><br>|<br><br>/ism', '<br>', $content);
//                $content = preg_replace('/<br><b>\s\s+<\/b><br>/i', '', $content);
//                $content = preg_replace('/\s\s+/i', ' ', $content);
//                $content = trim($content);
//                if ($content) {
//                    $this->KosovoPdf->id = $id;
//                    $this->KosovoPdf->saveField('content_sr', $content);
//                    $this->KosovoPdf->saveField('status', 2);
//                }
//            }
//        }
        $content = $ids;
        $this->set(compact('content'));
    }

    public function listIndex($lang = 'en') {
        $conditions = array();
        $this->paginate = array(
            'conditions' => $conditions,
            'order' => 'KosovoSpeecheIndex.created DESC',
            'limit' => 20,
            'contain' => array(
                'KosovoSpeecheContent' => array(
                    'KosovoPdf.id',
                    'KosovoTxt.id'
                )
            )
        );
        $content = $this->paginate('KosovoSpeecheIndex');
        $this->set(compact('content'));
    }

    public function getPlenaryVoting() {

    }

    public function getMpsParty() {
        $listMenu = $this->KosovoMpsMenu->find('list', array(
            'fields' => array('id', 'name')
        ));
        $this->set(compact('listMenu', 'content'));
    }

    public function listMpsIndex($type = null) {
        $listMenu = $this->KosovoMpsMenu->find('list', array(
            'fields' => array('id', 'name')
        ));
        $this->paginate = array(
            'conditions' => array('KosovoMpsIndex.kosovo_mps_menu_id' => $type),
            'order' => 'KosovoMpsIndex.created DESC',
            'limit' => 50,
        );
        $content = $this->paginate('KosovoMpsIndex');
        $this->set(compact('listMenu', 'content'));
    }

    public function getMpsContact() {

    }

    public function mpsDelegate() {
        $this->paginate = array(
            'contain' => array('KosovoMpsIndex'),
            'order' => 'KosovoMpsDetail.created DESC',
            'limit' => 30,
        );
        $content = $this->paginate('KosovoMpsDetail');
        // pr($content);
        $this->set(compact('listMenu', 'content'));
    }

    public function mpsParliamentaryGroup() {
        $this->paginate = array(
            'limit' => 50,
            'contain' => array(
                'KosovoMpsDetail.id'
            )
        );
        $content = $this->paginate('KosovoParliamentaryGroup');

        $this->set(compact('content'));
    }

    public function mpsCommittee() {
        $this->paginate = array(
            'limit' => 50,
            'contain' => array(
                'KosovoMpsDetail.id'
            )
        );
        $content = $this->paginate('KosovoCommittee');

        $this->set(compact('content'));
    }

    public function mpsParty() {
        $this->paginate = array(
            'limit' => 50,
            'contain' => array(
                'KosovoMpsDetail.id'
            )
        );
        $content = $this->paginate('KosovoParty');

        $this->set(compact('content'));
    }

    public function viewDelegate($id = null) {
        $content = $this->KosovoMpsDetail->find('first', array(
            'conditions' => array('KosovoMpsDetail.id' => $id),
            'contain' => array(
                'KosovoMpsIndex' => array('KosovoMpsMenu'),
                'KosovoMpsPersonalData',
                'KosovoMpsEducation',
                'KosovoMpsActivity',
                'KosovoMpsLanguage',
                'KosovoMpsAddress',
                'KosovoParliamentaryGroup',
                'KosovoParty'
            )
                )
        );
        // pr($content);
        $this->set(compact('content'));
    }

    public function listTxts() {
        $this->paginate = array(
            'order' => 'KosovoTxt.kosovo_speeche_content_id ASC',
        );
        $txts = $this->paginate('KosovoTxt');

        $this->set(compact('txts'));
    }

    public function viewTxt($id = null) {
        $content = $this->KosovoTxt->findById($id);
        $this->set(compact('content'));
    }

    public function listLogTxts() {
        $this->paginate = array(
            'conditions' => array('KosovoLog.type' => array(3)),
            'order' => 'KosovoLog.created DESC',
            'limit' => 50,
        );
        $content = $this->paginate('KosovoLog');
        $this->set(compact('content'));
    }

    public function listPdf() {
        $this->paginate = array(
            'order' => 'KosovoPdf.kosovo_speeche_content_id ASC',
        );
        $pdfs = $this->paginate('KosovoPdf');

        $this->set(compact('pdfs'));
    }

    public function viewPdf($id = null) {
        $content = $this->KosovoPdf->findById($id);
        $this->set(compact('content'));
    }

    public function listLogDelegate() {
        $this->paginate = array(
            'conditions' => array('KosovoLog.type' => array(8, 9)),
            'order' => 'KosovoLog.created DESC',
            'limit' => 50,
        );
        $content = $this->paginate('KosovoLog');
        $this->set(compact('content'));
    }

    public function listLogMpsIndex() {
        $listMenu = $this->KosovoMpsMenu->find('list', array(
            'fields' => array('id', 'name')
        ));
        $this->paginate = array(
            'conditions' => array('KosovoLog.type' => array(6, 7)),
            'order' => 'KosovoLog.created DESC',
            'limit' => 50,
        );
        $content = $this->paginate('KosovoLog');
        $this->set(compact('listMenu', 'content'));
    }

    public function listLogSpeches() {
        $this->paginate = array(
            'conditions' => array('KosovoLog.type' => array(0, 1, 5)),
            'order' => 'KosovoLog.created DESC',
            'limit' => 50,
        );
        $content = $this->paginate('KosovoLog');
        $this->set(compact('content'));
    }

    public function viewContent($id = null) {
        $content = $this->KosovoSpeecheContent->findById($id);
        $this->set(compact('content'));
    }

    public function viewLog($id = null) {
        $content = $this->KosovoLog->findById($id);
        $this->set(compact('content'));
    }

    public function getSchedules() {
        $this->paginate = array(
            'conditions' => array(
                'name' => 'kosovo'
            ),
            'limit' => 50,
            'recursive' => -1
        );
        $content = $this->paginate('Schedule');

        $this->set(compact('content'));
    }

}
