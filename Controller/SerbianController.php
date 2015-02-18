<?php

App::uses('CakeTime', 'Utility');

class SerbianController extends AppController {

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

    public function beforeRender() {
        parent::beforeRender();
        $serbiaHost = $this->Serbia->getSerbiaHost;
        $this->set(compact('serbiaHost'));
    }

    public function combineToApiSpeeches($id = null) {
        $id = !is_null($id) ? $id : 540;
//        $content = 'lipa jak CHUJ: i wielka jak DUPA JANA: i kurwa chuj w dupe KUITAS i BABA jaga';

        $content = $this->SerbianSpeecheIndex->findById($id);
        if ($content) {
            $combine = $this->SerbianSpeecheContent->combineToApiArray($content);

            $content['pdfs'] = $this->SerbianPdf->find('all', array(
                'fields' => array('id', 'post_date', 'stamp_in_text'),
                'conditions' => array('SerbianPdf.post_date' => $content['SerbianSpeecheContent']['convert_date']),
                'order' => 'SerbianPdf.stamp_in_text ASC',
            ));
        }
        $this->set(compact('content', 'combine'));
    }

    public function getPlenarySpeeches($id = null) {
        $id = !is_null($id) ? $id : 540;
//        $content = 'lipa jak CHUJ: i wielka jak DUPA JANA: i kurwa chuj w dupe KUITAS i BABA jaga';

        $content = $this->Serbia->extraktContent('/Дванаеста_седница_Другог_редовног.23906.43.html');
        if ($content) {
//            $combine = $this->SerbianSpeecheContent->combineToApiArray($content);
        }
        $this->set(compact('content', 'combine'));
    }

    public function index() {
//        $this->QueleToSend->doRequest();
    }

    public function getMpsParty() {
        $listMenu = $this->SerbianMenuData->find('list', array(
            'fields' => array('id', 'name')
        ));

        $uids = $this->SerbianDelegate->find('list', array(
            'fields' => array('id', 'url_uid'),
            'conditions' => array(
                'SerbianDelegate.url_uid !=' => null,
                'SerbianDelegate.status' => 0,
            ),
            'order' => 'SerbianDelegate.modified ASC',
            'limit' => 1
        ));
        $uids = array('149');

        $content = $this->SerbianMps->getContactsInfoFromDelegates($uids);
        $this->set(compact('listMenu', 'content'));
//        do api
//        $content = $this->SerbianMenuData->find('all', array(
//            'order' => 'SerbianMenuData.start_date ASC'
//        ));
//        if ($content) {
//            $combine = $this->SerbianMenuData->combineToApiArray($content);
//        }
        $this->set(compact('listMenu', 'content', 'combine'));
    }

    public function listTable($id = null) {
        $listMenu = $this->SerbianMenuData->find('list', array(
            'fields' => array('id', 'name')
        ));
        $conditions = array();
        if (!is_null($id) && (int) $id) {
            $conditions = array('SerbianDelegate.serbian_menu_data_id' => $id);
        }

        $this->paginate = array(
            'conditions' => $conditions,
            // 'order' => 'SerbianDelegate.stamp_in_text ASC',
            'limit' => 50
        );
        $content = $this->paginate('SerbianDelegate');

        $this->set(compact('listMenu', 'content'));
    }

    public function getSchedules() {
        $this->paginate = array(
            'conditions' => array(
                'name' => 'serbia'
            ),
            'limit' => 50,
            'recursive' => -1
        );
        $content = $this->paginate('Schedule');

        $this->set(compact('content'));
    }

    public function getMpsContact() {

        $this->set(compact('content'));
    }

    public function mpsDelegate() {
        $this->paginate = array(
            'limit' => 50,
            'recursive' => -1
        );
        $content = $this->paginate('SerbianMpsDetail');

        $this->set(compact('content'));
    }

    public function mpsParliamentaryGroup() {
        $this->paginate = array(
            'limit' => 50,
            'contain' => array(
                'SerbianMpsDetail.id'
            )
        );
        $content = $this->paginate('SerbianParliamentaryGroup');

        $this->set(compact('content'));
    }

    public function mpsParty() {
        $this->paginate = array(
            'limit' => 50,
            'contain' => array(
                'SerbianMpsDetail.id'
            )
        );
        $content = $this->paginate('SerbianParty');

        $this->set(compact('content'));
    }

    public function mpsCommitte() {
        $this->paginate = array(
            'limit' => 50,
            'contain' => array(
                'SerbianMpsDetail.id'
            )
        );
        $content = $this->paginate('SerbianCommitte');

        $this->set(compact('content'));
    }

    public function mpsDelegationMembership() {
        $this->paginate = array(
            'limit' => 50,
            'contain' => array(
                'SerbianMpsDetail.id'
            )
        );
        $content = $this->paginate('SerbianDelegationMembership');

        $this->set(compact('content'));
    }

    public function mpsFriendship() {
        $this->paginate = array(
            'limit' => 50,
            'contain' => array(
                'SerbianMpsDetail.id'
            )
        );
        $content = $this->paginate('SerbianFriendship');

        $this->set(compact('content'));
    }

    public function mpsFunction() {

        $this->paginate = array(
            'limit' => 50,
            'contain' => array(
                'SerbianMpsDetail.id'
            )
        );
        $content = $this->paginate('SerbianFunction');

        $this->set(compact('content'));
    }

    public function mpsResidence() {

        $this->paginate = array(
            'limit' => 50,
            'contain' => array(
                'SerbianMpsDetail.id'
            )
        );
        $content = $this->paginate('SerbianResidence');

        $this->set(compact('content'));
    }

    public function getPlenaryVoting() {
//        $uids = $this->SerbianParty->find('list', array(
//            'fields' => array('id', 'uid'),
//            'conditions' => array(
////                'SerbianParty.modified <' => $now,
//                'SerbianParty.uid !=' => null,
//            ),
//            'order' => 'SerbianParty.modified ASC',
////            'limit' => 10
//        ));
//        $content = $this->SerbianParty->getContactsInfoUids($uids);
//        if ($content) {
//            foreach ($content as $c) {
//                $this->SerbianParty->save($c);
//            }
//        }
        $this->set(compact('content'));
    }

    public function listPdfs() {
        $this->paginate = array(
            'order' => 'SerbianPdf.stamp_in_text ASC',
        );
        $pdfs = $this->paginate('SerbianPdf');

        $this->set(compact('pdfs'));
    }

    public function viewPdf($id = null) {
        $content = $this->SerbianPdf->findById($id);
        $this->set(compact('content'));
    }

    public function listIndexPagin($lang = 'en') {
        $lang = in_array($lang, array('sr', 'en')) ? $lang : 'en';
        $conditions = array('SerbianSpeecheIndex.lang' => $lang);

        $this->paginate = array(
            'conditions' => $conditions,
            'order' => 'SerbianSpeecheIndex.created DESC',
            'limit' => 50,
            'contain' => array(
                'SerbianSpeecheContent' => array(
                //'SerbianPdf'
                )
            )
        );
        $content = $this->paginate('SerbianSpeecheIndex');

        foreach ($content as $k => $c) {
            $content[$k]['pdfs'] = $this->SerbianPdf->find('all', array(
                // 'fields' => array('id', 'post_date', 'stamp_in_text'),
                'conditions' => array('SerbianPdf.post_date' => $c['SerbianSpeecheContent']['convert_date']),
                'order' => 'SerbianPdf.stamp_in_text ASC',
            ));
        }

//        pr($results);
//        pr($pdfs);
        $this->set(compact('content'));
    }

    public function viewContent($id = null) {
        $content = $this->SerbianSpeecheContent->findById($id);
        $this->paginate = array(
            'conditions' => array('SerbianPdf.post_date' => $content['SerbianSpeecheContent']['convert_date']),
            'order' => 'SerbianPdf.stamp_in_text ASC',
        );
        $pdfs = $this->paginate('SerbianPdf');

        $this->set(compact('content', 'pdfs'));
    }

    public function listLogSpeches() {
        $this->paginate = array(
            'conditions' => array('SerbianLog.type' => array(0, 1, 2)),
            'order' => 'SerbianLog.created DESC',
            'limit' => 50,
        );
        $content = $this->paginate('SerbianLog');
        $this->set(compact('content'));
    }

    public function listLogPdfs() {
        $this->paginate = array(
            'conditions' => array('SerbianLog.type' => array(3)),
            'order' => 'SerbianLog.created DESC',
            'limit' => 50,
        );
        $content = $this->paginate('SerbianLog');
        $this->set(compact('content'));
    }

    public function listLogTable() {
        $listMenu = $this->SerbianMenuData->find('list', array(
            'fields' => array('id', 'name')
        ));
        $this->paginate = array(
            'conditions' => array('SerbianLog.type' => array(4, 5)),
            'order' => 'SerbianLog.created DESC',
            'limit' => 50,
        );
        $content = $this->paginate('SerbianLog');
        $this->set(compact('listMenu', 'content'));
    }

    public function listLogDelegate() {
        $this->paginate = array(
            'conditions' => array('SerbianLog.type' => array(6, 7)),
            'order' => 'SerbianLog.created DESC',
            'limit' => 50,
        );
        $content = $this->paginate('SerbianLog');
        $this->set(compact('content'));
    }

    public function viewLog($id = null) {
        $content = $this->SerbianLog->findById($id);
        $this->set(compact('content'));
    }

}
