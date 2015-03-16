<?php

//App::uses('CakeEmail', 'Network/Email');
App::uses('CakeTime', 'Utility');

class SerbianTask extends Shell {

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
    private $sebiaLink = array(
        'en' => 'http://www.parlament.gov.rs/activities/national-assembly/sessions.544.html',
        'sr' => 'http://www.parlament.gov.rs/%D0%B0%D0%BA%D1%82%D0%B8%D0%B2%D0%BD%D0%BE%D1%81%D1%82%D0%B8/%D0%BD%D0%B0%D1%80%D0%BE%D0%B4%D0%BD%D0%B0-%D1%81%D0%BA%D1%83%D0%BF%D1%88%D1%82%D0%B8%D0%BD%D0%B0/%D0%B7%D0%B0%D1%81%D0%B5%D0%B4%D0%B0%D1%9A%D0%B0.33.html'
    );
    private $sebiaLink2004 = array(
        'en' => 'http://www.parlament.gov.rs/activities/national-assembly/activities-archive/january-27,-2004-convocation/sessions.1298.html',
        'sr' => 'http://www.parlament.gov.rs/%D0%B0%D0%BA%D1%82%D0%B8%D0%B2%D0%BD%D0%BE%D1%81%D1%82%D0%B8/%D0%BD%D0%B0%D1%80%D0%BE%D0%B4%D0%BD%D0%B0-%D1%81%D0%BA%D1%83%D0%BF%D1%88%D1%82%D0%B8%D0%BD%D0%B0/%D0%B0%D1%80%D1%85%D0%B8%D0%B2%D0%B0-%D0%B0%D0%BA%D1%82%D0%B8%D0%B2%D0%BD%D0%BE%D1%81%D1%82%D0%B8/%D1%81%D0%B0%D0%B7%D0%B8%D0%B2-%D0%BE%D0%B4-27-%D1%98%D0%B0%D0%BD%D1%83%D0%B0%D1%80%D0%B0-2004/%D0%B7%D0%B0%D1%81%D0%B5%D0%B4%D0%B0%D1%9A%D0%B0.1273.html'
    );
    private $sebiaLink2007 = array(
        'en' => 'http://www.parlament.gov.rs/activities/national-assembly/activities-archive/february-14,-2007-convocation/sessions.1292.html',
        'sr' => 'http://www.parlament.gov.rs/%D0%B0%D0%BA%D1%82%D0%B8%D0%B2%D0%BD%D0%BE%D1%81%D1%82%D0%B8/%D0%BD%D0%B0%D1%80%D0%BE%D0%B4%D0%BD%D0%B0-%D1%81%D0%BA%D1%83%D0%BF%D1%88%D1%82%D0%B8%D0%BD%D0%B0/%D0%B0%D1%80%D1%85%D0%B8%D0%B2%D0%B0-%D0%B0%D0%BA%D1%82%D0%B8%D0%B2%D0%BD%D0%BE%D1%81%D1%82%D0%B8/%D1%81%D0%B0%D0%B7%D0%B8%D0%B2-%D0%BE%D0%B4-14-%D1%84%D0%B5%D0%B1%D1%80%D1%83%D0%B0%D1%80%D0%B0-2007/%D0%B7%D0%B0%D1%81%D0%B5%D0%B4%D0%B0%D1%9A%D0%B0.1267.html'
    );
    private $sebiaLink2008 = array(
        'en' => 'http://www.parlament.gov.rs/activities/national-assembly/activities-archive/june-11,-2008-convocation/sessions.1553.html',
        'sr' => 'http://www.parlament.gov.rs/%D0%B0%D0%BA%D1%82%D0%B8%D0%B2%D0%BD%D0%BE%D1%81%D1%82%D0%B8/%D0%BD%D0%B0%D1%80%D0%BE%D0%B4%D0%BD%D0%B0-%D1%81%D0%BA%D1%83%D0%BF%D1%88%D1%82%D0%B8%D0%BD%D0%B0/%D0%B0%D1%80%D1%85%D0%B8%D0%B2%D0%B0-%D0%B0%D0%BA%D1%82%D0%B8%D0%B2%D0%BD%D0%BE%D1%81%D1%82%D0%B8/%D1%81%D0%B0%D0%B7%D0%B8%D0%B2-%D0%BE%D0%B4-11-%D1%98%D1%83%D0%BD%D0%B0-2008/%D0%B7%D0%B0%D1%81%D0%B5%D0%B4%D0%B0%D1%9A%D0%B0.1539.html'
    );
    private $sebiaLink2012 = array(
        'en' => 'http://www.parlament.gov.rs/activities/national-assembly/activities-archive/may-31,-2012-convocation/sessions.2296.html',
        'sr' => 'http://www.parlament.gov.rs/%D0%B0%D0%BA%D1%82%D0%B8%D0%B2%D0%BD%D0%BE%D1%81%D1%82%D0%B8/%D0%BD%D0%B0%D1%80%D0%BE%D0%B4%D0%BD%D0%B0-%D1%81%D0%BA%D1%83%D0%BF%D1%88%D1%82%D0%B8%D0%BD%D0%B0/%D0%B0%D1%80%D1%85%D0%B8%D0%B2%D0%B0-%D0%B0%D0%BA%D1%82%D0%B8%D0%B2%D0%BD%D0%BE%D1%81%D1%82%D0%B8/%D1%81%D0%B0%D0%B7%D0%B8%D0%B2-%D0%BE%D0%B4-31-%D0%BC%D0%B0%D1%98%D0%B0-2012/%D0%B7%D0%B0%D1%81%D0%B5%D0%B4%D0%B0%D1%9A%D0%B0.2240.html'
    );

    public function get_schedules() {
        return $this->Schedule->find('all', array(
                        // 'conditions' => array('Schedule.name' => 'serbia'),
        ));
    }

    public function get_party_data($now) {
        $this->QueleToSend->doLog(
                array(
            'id' => time() . '_' . rand(4, 99999),
            'label' => 'get_party_data',
            'status' => 'running',
                ), 'Serbian'
        );
        $info = CakeTime::toServer(time()) . ' Serbia get_party_data start | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog = $info . "\n";

        $uids = $this->SerbianParty->find('list', array(
            'fields' => array('id', 'uid'),
            'conditions' => array(
//                'SerbianParty.modified <' => $now,
                'SerbianParty.uid !=' => null,
            ),
            'order' => 'SerbianParty.modified ASC',
//            'limit' => 10
        ));
        $info = 'geting uids get_party_data';
        $this->out($info);
        $toLog .= $info . "\n";
        print_r($uids);
        if ($uids) {
            foreach ($uids as $id => $uid) {
                $info = 'id: ' . $id;
                $this->out($info);
                $toLog .= $info . "\n";
            }
            $info = 'get data for select ids';
            $this->out($info);
            $toLog .= $info . "\n";
            $content = $this->SerbianParty->getContactsInfoUids($uids);
            if ($content) {
                foreach ($content as $c) {
                    if ($this->SerbianParty->save($c)) {
                        $info = 'update data for select ' . $c['id'];
                        $this->out($info);
                        $toLog .= $info . "\n";
                    }
                }
            }
        } else {
            $info = 'nothing to do';
            $this->out($info);
            $toLog .= $info . "\n";
        }

        $info = CakeTime::toServer(time()) . ' Serbia get_party_data end | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog .= $info . "\n";
        $this->QueleToSend->doLog(
                array(
            'id' => time() . '_' . rand(4, 99999),
            'label' => 'get_party_data',
            'status' => 'finished'
                ), 'Serbian'
        );
    }

    public function check_mps_contacts($now) {
        $this->QueleToSend->doLog(
                array(
            'id' => time() . '_' . rand(4, 99999),
            'label' => 'check_mps_contacts',
            'status' => 'running',
                ), 'Serbian'
        );
        $info = CakeTime::toServer(time()) . ' Serbia check_mps_contacts start | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog = $info . "\n";

        $uids = $this->SerbianMpsDetail->find('list', array(
            'fields' => array('id'),
            'conditions' => array(
                'SerbianMpsDetail.modified <' => $now,
            ),
            'order' => 'SerbianMpsDetail.modified ASC',
            'limit' => 3
        ));

        $info = 'geting uids mps contacts';
        $this->out($info);
        $toLog .= $info . "\n";
        if ($uids) {
            foreach ($uids as $uid) {
                $info = 'uid: ' . $uid;
                $this->out($info);
                $toLog .= $info . "\n";
            }
            $info = 'get data for select uids';
            $this->out($info);
            $toLog .= $info . "\n";
            $content = $this->SerbianMps->getContactsInfoFromDelegates($uids);
        } else {
            $info = 'nothing to do';
            $this->out($info);
            $toLog .= $info . "\n";
        }

        if (isset($content) && is_array($content)) {
            foreach ($content as $k => $c) {
                if (!empty($c)) {
                    if ($this->SerbianMpsDetail->saveAll($c)) {
                        $info = 'save data for uid: ' . $c['SerbianMpsDetail']['id'];
                        $this->out($info);
                        $toLog .= $info . "\n";
                    }
                } else {
                    $info = 'nothing to do uid: ' . $k;
                    $this->out($info);
                    $toLog .= $info . "\n";
                }
            }
        } else {
            $info = 'nothing to do';
            $this->out($info);
            $toLog .= $info . "\n";
        }

        $info = CakeTime::toServer(time()) . ' Serbia check_mps_contacts end | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog .= $info . "\n";
        $this->QueleToSend->doLog(
                array(
            'id' => time() . '_' . rand(4, 99999),
            'label' => 'check_mps_contacts',
            'status' => 'finished'
                ), 'Serbian'
        );
    }

    public function get_mps_table_delegates($limit = null) {
        $limit = !is_null($limit) && (int) $limit ? $limit : 30;
        $this->QueleToSend->doLog(
                array(
            'id' => time() . '_' . rand(4, 99999),
            'label' => 'get_mps_table_delegates',
            'status' => 'running',
                ), 'Serbian'
        );
        $info = CakeTime::toServer(time()) . ' Serbia get_mps_table_delegates start | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog = $info . "\n";

        $uids = $this->SerbianDelegate->find('list', array(
            'fields' => array('id', 'url_uid'),
            'conditions' => array(
                'SerbianDelegate.url_uid !=' => null,
                'SerbianDelegate.status' => 0,
            ),
            'order' => 'SerbianDelegate.modified ASC',
            'limit' => $limit
        ));

        $info = 'geting uids delegates';
        $this->out($info);
        $toLog .= $info . "\n";
        if ($uids) {
            foreach ($uids as $uid) {
                $info = 'uid: ' . $uid;
                $this->out($info);
                $toLog .= $info . "\n";
            }
            $info = 'update status for select uids';
            $this->out($info);
            $toLog .= $info . "\n";
            $this->SerbianDelegate->updateAll(
                    array('SerbianDelegate.status' => 1), array('SerbianDelegate.url_uid' => $uids)
            );
            $info = 'get data for select uids';
            $this->out($info);
            $toLog .= $info . "\n";
            $content = $this->SerbianMps->getContactsInfoFromDelegates($uids);
        } else {
            $info = 'nothing to do';
            $this->out($info);
            $toLog .= $info . "\n";
        }

        if (isset($content)) {
            foreach ($content as $c) {
                if (!empty($c)) {
                    if ($this->SerbianMpsDetail->saveAll($c)) {
                        $info = 'save data for uid: ' . $c['SerbianMpsDetail']['id'];
                        $this->out($info);
                        $toLog .= $info . "\n";
                    }
                }
            }
        } else {
            $info = 'nothing to do';
            $this->out($info);
            $toLog .= $info . "\n";
        }

        $info = CakeTime::toServer(time()) . ' Serbia get_mps_table_delegates end | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog .= $info . "\n";
        $this->QueleToSend->doLog(
                array(
            'id' => time() . '_' . rand(4, 99999),
            'label' => 'get_mps_table_delegates',
            'status' => 'finished'
                ), 'Serbian'
        );
    }

    public function get_mps_menu() {
        $this->QueleToSend->doLog(
                array(
            'id' => time() . '_' . rand(4, 99999),
            'label' => 'get_mps_menu',
            'status' => 'running',
                ), 'Serbian'
        );
        $info = CakeTime::toServer(time()) . ' Serbia get_mps_menu start | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog = $info . "\n";
        $menuData = $this->SerbianMps->getMenuFromLink();
        if ($menuData) {
            foreach ($menuData['SerbianMenuData'] as $md) {

                $check = $this->SerbianMenuData->findByMenuMd5($md['menu_md5']);
//                print_r($check);
                if (!$check) {
                    if ($this->SerbianMenuData->save($md)) {
                        $info = 'save menu id: ' . $md['id'];
                        $this->out($info);
                        $toLog .= $info . "\n";
                    }
                } else {
                    $info = 'nothing to change, menu id: ' . $md['id'];
                    $this->out($info);
                    $toLog .= $info . "\n";
                }
            }
        }
        $info = CakeTime::toServer(time()) . ' Serbia get_mps_menu end | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog .= $info . "\n";
        $this->QueleToSend->doLog(
                array(
            'id' => time() . '_' . rand(4, 99999),
            'label' => 'get_mps_menu',
            'status' => 'finished'
                ), 'Serbian'
        );
    }

    public function get_mps_tables() {
        $this->QueleToSend->doLog(
                array(
            'id' => time() . '_' . rand(4, 99999),
            'label' => 'get_mps_tables',
            'status' => 'running',
                ), 'Serbian'
        );
        $info = CakeTime::toServer(time()) . ' Serbia get_mps_tables start | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog = $info . "\n";
        $menuData = $this->SerbianMenuData->find('all', array(
            'fields' => array('id', 'name', 'url', 'start_date', 'menu_md5', 'table_md5'),
            'conditions' => array('status' => 0),
            'limit' => 1
        ));
        if ($menuData) {
            foreach ($menuData as $md) {
                //pr($md['SerbianMenuData']);
                $content = $this->SerbianMps->getTableFromLink($md['SerbianMenuData']);
                sleep(5);
                $data[] = $content;
                if ($content['SerbianMenuData']) {
                    $check = $this->SerbianMenuData->find('first', array(
                        'fields' => array('id'),
                        'conditions' => array(
                            'SerbianMenuData.menu_md5' => $md['SerbianMenuData']['menu_md5'],
                            'SerbianMenuData.table_md5' => $content['SerbianMenuData']['table_md5'],
                        )
                    ));
                    if (!$check) {
                        if ($this->SerbianMenuData->save($content['SerbianMenuData'])) {
                            $info = 'update menu id: ' . $md['SerbianMenuData']['id'];
                            $this->out($info);
                            $toLog .= $info . "\n";
                        }
                    } else {
                        $info = 'noting to change menu id: ' . $md['SerbianMenuData']['id'];
                        $this->out($info);
                        $toLog .= $info . "\n";
                    }
                }
                if (!empty($content['SerbianResidence'])) {
                    foreach ($content['SerbianResidence'] as $c) {
                        $this->SerbianResidence->getIdFromUidAndName($c['uid'], $c['name']);
                    }
                    $info = 'update Residences list';
                    $this->out($info);
                    $toLog .= $info . "\n";
                }
                if (!empty($content['SerbianParty'])) {
                    foreach ($content['SerbianParty'] as $c) {
                        $this->SerbianParty->getIdFromUidAndName($c['uid'], $c['name']);
                    }
                    $info = 'update Parties list';
                    $this->out($info);
                    $toLog .= $info . "\n";
                }
                if (!empty($content['SerbianParliamentaryGroup'])) {
                    foreach ($content['SerbianParliamentaryGroup'] as $c) {
                        $this->SerbianParliamentaryGroup->getIdFromUidAndName($c['uid'], $c['name']);
                    }
                    $info = 'update Parliamentary Groups list';
                    $this->out($info);
                    $toLog .= $info . "\n";
                }
                if (!empty($content['SerbianCommitte'])) {
                    foreach ($content['SerbianCommitte'] as $c) {
                        $this->SerbianCommitte->getIdFromUidAndName($c['uid'], $c['name']);
                    }
                    $info = 'update Committees list';
                    $this->out($info);
                    $toLog .= $info . "\n";
                }
                if (!empty($content['SerbianDelegate'])) {
//                    $this->SerbianDelegate->deleteAll(array('SerbianDelegate.serbian_menu_data_id' => $menuData[0]['SerbianMenuData']['id']), false);
//                    $info = 'delete Delegates tables';
//                    $this->out($info);
//                    $toLog .= $info . "\n";
//                    $this->SerbianDelegate->saveAll($content['SerbianDelegate']);
//                    $info = 'save Delegates tables';
//                    $this->out($info);
//                    $toLog .= $info . "\n";
                } else {
                    $info = 'not change Delegates tables';
                    $this->out($info);
                    $toLog .= $info . "\n";
                }
            }
            $this->SerbianMenuData->id = $menuData[0]['SerbianMenuData']['id'];
            $this->SerbianMenuData->saveField('status', 1);
        } else {
            $this->SerbianMenuData->updateAll(array('status' => 0), array('status' => 1));
        }
        $info = CakeTime::toServer(time()) . ' Serbia get_mps_tables end | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog .= $info . "\n";
        $this->QueleToSend->doLog(
                array(
            'id' => time() . '_' . rand(4, 99999),
            'label' => 'get_mps_tables',
            'status' => 'finished'
                ), 'Serbian'
        );
    }

    public function get_index() {
        $this->QueleToSend->doLog(
                array(
            'id' => time() . '_' . rand(4, 99999),
            'label' => 'get_index',
            'status' => 'running',
                ), 'Serbian'
        );
        $info = CakeTime::toServer(time()) . ' Serbia get_index start | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog = $info . "\n";

        foreach ($this->sebiaLink as $lang => $link) {

            $info = 'lang ' . $lang;
            $this->out($info);
            $toLog .= $info . "\n";

            $change = false;
            $getDataIndex = $this->Serbia->getPlenarySpeechesScrap($link);
            //$getDataIndex = null;
            if (count($getDataIndex)) {
                foreach ($getDataIndex as $d) {
                    // $this->out('index | postUid: ' . $d['post_uid'] . ' | postDate: ' . $d['post_date']);
                    $c = $this->SerbianSpeecheIndex->findByPostUid($d['post_uid']);
                    if (!$c) {
                        $this->SerbianSpeecheIndex->create();
                        $d['lang'] = $lang;
                        if ($this->SerbianSpeecheIndex->save($d)) {

                            $info = 'save new index | postUid: ' . $d['post_uid'] . ' | postDate: ' . $d['post_date'];
                            $this->out($info);
                            $toLog .= $info . "\n";

                            $change = true;
                        }
                    } else {
                        if ($c['SerbianSpeecheIndex']['index_md5'] != $d['index_md5']) {
                            $this->SerbianSpeecheIndex->id = $c['SerbianSpeecheIndex']['id'];
                            $d['lang'] = $lang;
                            $d['status'] = 0;
                            $this->SerbianSpeecheIndex->set($d);
                            if ($this->SerbianSpeecheIndex->save()) {

                                $info = 'diff md5 update index | postUid: ' . $d['post_uid'] . ' | postDate: ' . $d['post_date'];
                                $this->out($info);
                                $toLog .= $info . "\n";

                                $change = true;
                            }
                        }
                    }
                }
                if (!$change) {
                    $info = 'nothing changed';
                    $this->out($info);
                    $toLog .= $info . "\n";
                }
            } else {
                $info = 'something is wrong, I can not retrieve the list of articles';
                $this->out($info);
                $toLog .= $info . "\n";
            }
            usleep(350000);
        }
        $info = CakeTime::toServer(time()) . ' Serbia get_index end | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog .= $info . "\n";
        $this->QueleToSend->doLog(
                array(
            'id' => time() . '_' . rand(4, 99999),
            'label' => 'get_index',
            'status' => 'finished'
                ), 'Serbian'
        );
    }

    public function get_content($limit = null) {
        $limit = !is_null($limit) && (int) $limit ? $limit : 30;
        $this->QueleToSend->doLog(
                array(
            'id' => time() . '_' . rand(4, 99999),
            'label' => 'get_content',
            'status' => 'running',
                ), 'Serbian'
        );
        $info = CakeTime::toServer(time()) . ' Serbia get_content start | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog = $info . "\n";

        $change = false;
        $dataIndex = $this->SerbianSpeecheIndex->find('list', array(
            'fields' => array('SerbianSpeecheIndex.id', 'SerbianSpeecheIndex.url'),
            'conditions' => array(
                'SerbianSpeecheIndex.active' => 1,
                'SerbianSpeecheIndex.status' => 0
            ),
            'order' => 'SerbianSpeecheIndex.post_date ASC',
            'limit' => $limit
                )
        );
        $info = 'list index count: ' . count($dataIndex);
        $this->out($info);
        $toLog .= $info . "\n";

        $oldtime = CakeTime::format('-45 days', '%Y-%m-%d %H:%M:%S');
        $dataOldIndex = $this->SerbianSpeecheIndex->find('list', array(
            'fields' => array('SerbianSpeecheIndex.id', 'SerbianSpeecheIndex.url'),
            'conditions' => array(
//                'SerbianSpeecheIndex.id' => 546,
                'SerbianSpeecheIndex.active' => 1,
                'SerbianSpeecheIndex.post_date >' => $oldtime,
            ),
            'order' => 'SerbianSpeecheIndex.post_date ASC',
//            'limit' => $limit
                )
        );
        $info = 'list old (' . $oldtime . ') check index count: ' . count($dataOldIndex);
        $this->out($info);
        $toLog .= $info . "\n";
        $listDataIndex = array($dataIndex, $dataOldIndex);

        if ($listDataIndex) {
            foreach ($listDataIndex as $listData) {
                foreach ($listData as $key => $url) {
                    $content = $this->Serbia->extraktContent($url);
                    if ($content) {
                        $c = $this->SerbianSpeecheContent->findBySerbianSpeecheIndexId($key);
                        if (!$c) {
                            $this->SerbianSpeecheContent->create();
                            $content['serbian_speeche_index_id'] = $key;
                            if ($this->SerbianSpeecheContent->save($content)) {

                                $info = 'save new content (key: ' . $key . ') ' . $url;
                                $this->out($info);
                                $toLog .= $info . "\n";

                                $change = true;
                            }
                        } else {
                            $this->out($key);
                            $this->out($c['SerbianSpeecheContent']['content_md5']);
                            $this->out($content['content_md5']);

                            if ($c['SerbianSpeecheContent']['content_md5'] != $content['content_md5']) {
                                $this->SerbianSpeecheContent->id = $c['SerbianSpeecheContent']['id'];
                                $content['serbian_speeche_index_id'] = $key;
                                $content['status'] = 0;
//                        print_r($content);
                                if ($this->SerbianSpeecheContent->save($content)) {

                                    $info = 'diff md5 update content (key: ' . $key . ') ' . $url;
                                    $this->out($info);
                                    $toLog .= $info . "\n";

                                    $change = true;
                                }
                            }
                        }
                        if ($change) {
                            $this->SerbianSpeecheIndex->id = $key;
                            $this->SerbianSpeecheIndex->saveField('status', 1);
                        }
                        if (isset($content['pdfs'])) {
                            $changePdf = false;
                            $info = 'find content pdfs (key: ' . $key . ') pcs: ' . count($content['pdfs']);
                            $this->out($info);
                            $toLog .= $info . "\n";

                            foreach ($content['pdfs'] as $pdf) {
                                $p = $this->SerbianPdf->findByStampInText($pdf['stamp_in_text']);

                                if (!$p) {
                                    $this->SerbianPdf->create();
                                    if ($this->SerbianPdf->save($pdf)) {

                                        $info = 'save new pdf (key: ' . $pdf['stamp_in_text'] . ')';
                                        $this->out($info);
                                        $toLog .= $info . "\n";

                                        $changePdf = true;
                                    }
                                } else {

                                    if ($p['SerbianPdf']['pdf_md5'] != $pdf['pdf_md5']) {
                                        $this->SerbianPdf->id = $p['SerbianPdf']['id'];
                                        $pdf['status'] = 0;
                                        if ($this->SerbianPdf->save($pdf)) {

                                            $info = 'diff md5 update pdf (key: ' . $pdf['stamp_in_text'] . ')';
                                            $this->out($info);
                                            $toLog .= $info . "\n";

                                            $changePdf = true;
                                        }
                                    }
                                }
                            }
                            if (!$changePdf) {
                                $info = 'pdfs nothing to do';
                                $this->out($info);
                                $toLog .= $info . "\n";
                            }

                            $info = 'end content pdfs';
                            $this->out($info);
                            $toLog .= $info . "\n";
                        }
                    } else {
                        $info = 'something is wrong, I can not get content from (key: ' . $key . ') ' . $url;
                        $this->out($info);
                        $toLog .= $info . "\n";
                    }
                    usleep(50000);
                }
            }
            if (!$change) {
                $info = 'content nothing changed';
                $this->out($info);
                $toLog .= $info . "\n";
            }
        } else {
            $info = 'empty list indexes to check';
            $this->out($info);
            $toLog .= $info . "\n";
        }
        $info = CakeTime::toServer(time()) . ' Serbia get_content end | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog .= $info . "\n";
        $this->QueleToSend->doLog(
                array(
            'id' => time() . '_' . rand(4, 99999),
            'label' => 'get_content',
            'status' => 'finished'
                ), 'Serbian'
        );
    }

    public function combine_pdfs($limit = null) {
        $limit = !is_null($limit) && (int) $limit ? $limit : 30;
        $this->QueleToSend->doLog(
                array(
            'id' => time() . '_' . rand(4, 99999),
            'label' => 'combine_pdfs',
            'status' => 'running',
                ), 'Serbian'
        );
        $info = CakeTime::toServer(time()) . ' Serbia combine_pdfs start | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog = $info . "\n";

        $list = $this->SerbianPdf->find('all', array(
            'fields' => array('id', 'name_sr', 'pdf_url'),
            'conditions' => array('SerbianPdf.status' => 0),
            'limit' => $limit
        ));

        if (count($list)) {
            foreach ($list as $l) {
                $id = $l['SerbianPdf']['id'];

                $info = 'get data to transform: id ' . $id;
                $this->out($info);
                $toLog .= $info . "\n";

                $filePath = $this->SerbianPdf->downloadPdf($l);
                if ($filePath) {
                    $info = 'download pdf: ' . $filePath;
                    $this->out($info);
                    $toLog .= $info . "\n";

                    $combine = $this->SerbianPdf->combinePdfToHtml($filePath, $id);
                    if ($combine) {
                        $info = 'succes transform ' . $id . '.pdf to ' . $id . '.html (Serbian lang)';
                        $this->out($info);
                        $toLog .= $info . "\n";

                        $translate = $this->SerbianPdf->translateHtml($id);
//                        if ($translate) {
//
//                            $info = 'succes translate ' . $id . '.html (English lang)';
//                            $this->out($info);
//                            $toLog .= $info . "\n";
//                        } else {
//                            $info = '!!!ERROR  translate ' . $id . '.html (English lang)';
//                            $this->out($info);
//                            $toLog .= $info . "\n";
//                        }
                    } else {
                        $info = '!!!ERROR ' . $id . '.pdf to ' . $id . '.html (Serbian lang)';
                        $this->out($info);
                        $toLog .= $info . "\n";
                    }
                } else {
                    $info = '!!!ERROR download pdf: ' . $filePath;
                    $this->out($info);
                    $toLog .= $info . "\n";
                }
            }
        } else {
            $info = 'pdfs nothing to do';
            $this->out($info);
            $toLog .= $info . "\n";
        }
        $info = CakeTime::toServer(time()) . ' Serbia combine_pdfs end | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog .= $info . "\n";
        $this->QueleToSend->doLog(
                array(
            'id' => time() . '_' . rand(4, 99999),
            'label' => 'combine_pdfs',
            'status' => 'finished'
                ), 'Serbian'
        );
    }

    public function serbia_combine_to_quelle() {
        $this->QueleToSend->doLog(
                array(
            'id' => time() . '_' . rand(4, 99999),
            'label' => 'serbia_combine_to_quelle',
            'status' => 'running',
                ), 'Serbian'
        );
        ////////////////organizationConvocation
        $info = CakeTime::toServer(time()) . ' Serbia organizationConvocation start | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog = $info . "\n";
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
                $info = 'SerbianMenuData start_date: ' . $c['SerbianMenuData']['start_date'];
                $this->out($info);
                $toLog .= $info . "\n";
                if (isset($combines) && $combines) {
                    $result = $this->QueleToSend->putDataDB(array($combines), 'Serbian');
                    //  pr($result);
                    if ($result) {
                        $this->SerbianMenuData->id = $c['SerbianMenuData']['id'];
                        $this->SerbianMenuData->saveField('api', 1);
                    }
                }
            }
        } else {
            $info = 'organizationConvocation nothing to do';
            $this->out($info);
            $toLog .= $info . "\n";
        }
        $info = CakeTime::toServer(time()) . ' Serbia organizationConvocation end | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog .= $info . "\n";

        ////////////////people
        $info = CakeTime::toServer(time()) . ' Serbia people start | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog = $info . "\n";
        $this->SerbianMpsDetail->recursive = -1;
        $content = $this->SerbianMpsDetail->find('all', array(
            'conditions' => array('status' => 0),
//            'limit' => 50
        ));
        if ($content) {
            foreach ($content as $c) {
                $combines = $this->SerbianMpsDetail->combineToApiArray($c['SerbianMpsDetail']);
                $combine[] = $combines;
                $info = 'people id: ' . $c['SerbianMpsDetail']['id'];
                $this->out($info);
                $toLog .= $info . "\n";
                if (isset($combines) && $combines) {
                    $result = $this->QueleToSend->putDataDB(array($combines), 'Serbian');
                    //  pr($result);
                    if ($result) {
                        $this->SerbianMpsDetail->id = $c['SerbianMpsDetail']['id'];
                        $this->SerbianMpsDetail->saveField('status', 1);
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
        $info = CakeTime::toServer(time()) . ' Serbia people end | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog .= $info . "\n";

        ////////////////organizationParty
        $info = CakeTime::toServer(time()) . ' Serbia organizationParty start | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog = $info . "\n";
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
//            'limit' => 2
        ));
        if ($content) {
            foreach ($content as $c) {
                $combines = $this->SerbianParty->combineToApiArray($c);
                $combine[] = $combines;
                $info = 'organizationParty uid: ' . $c['SerbianParty']['uid'];
                $this->out($info);
                $toLog .= $info . "\n";
                if (isset($combines) && $combines) {
                    $result = $this->QueleToSend->putDataDB($combines, 'Serbian');
                    //  pr($result);
                    if ($result) {
                        $this->SerbianParty->id = $c['SerbianParty']['id'];
                        $this->SerbianParty->saveField('api', 1);
                    }
                }
            }
        } else {
            $info = 'organizationParty nothing to do';
            $this->out($info);
            $toLog .= $info . "\n";
        }
        $info = CakeTime::toServer(time()) . ' Serbia organizationParty end | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog .= $info . "\n";

        ////////////////organizationParliamentaryGroups
        $info = CakeTime::toServer(time()) . ' Serbia organizationParliamentaryGroups start | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog = $info . "\n";
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
                $info = 'SerbianParliamentaryGroup uid: ' . $c['SerbianParliamentaryGroup']['uid'];
                $this->out($info);
                $toLog .= $info . "\n";
                if (isset($combines) && $combines) {
                    $result = $this->QueleToSend->putDataDB($combines, 'Serbian');
                    //  pr($result);
                    if ($result) {
                        $this->SerbianParliamentaryGroup->id = $c['SerbianParliamentaryGroup']['id'];
                        $this->SerbianParliamentaryGroup->saveField('api', 1);
                    }
                }
            }
        } else {
            $info = 'organizationParliamentaryGroups nothing to do';
            $this->out($info);
            $toLog .= $info . "\n";
        }
        $info = CakeTime::toServer(time()) . ' Serbia organizationParliamentaryGroups end | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog .= $info . "\n";

        ////////////////organizationCommitte
        $info = CakeTime::toServer(time()) . ' Serbia organizationCommitte start | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog = $info . "\n";
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
                $info = 'SerbianCommitte uid: ' . $c['SerbianCommitte']['uid'];
                $this->out($info);
                $toLog .= $info . "\n";
                if (isset($combines) && $combines) {
                    $result = $this->QueleToSend->putDataDB($combines, 'Serbian');
                    //  pr($result);
                    if ($result) {
                        $this->SerbianCommitte->id = $c['SerbianCommitte']['id'];
                        $this->SerbianCommitte->saveField('api', 1);
                    }
                }
            }
        } else {
            $info = 'organizationCommitte nothing to do';
            $this->out($info);
            $toLog .= $info . "\n";
        }
        $info = CakeTime::toServer(time()) . ' Serbia organizationCommitte end | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog .= $info . "\n";

        ////////////////organizationDelegation
        $info = CakeTime::toServer(time()) . ' Serbia organizationDelegation start | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog = $info . "\n";
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
                $info = 'SerbianDelegationMembership uid: ' . $c['SerbianDelegationMembership']['uid'];
                $this->out($info);
                $toLog .= $info . "\n";
                if (isset($combines) && $combines) {
                    $result = $this->QueleToSend->putDataDB($combines, 'Serbian');
                    //  pr($result);
                    if ($result) {
                        $this->SerbianDelegationMembership->id = $c['SerbianDelegationMembership']['id'];
                        $this->SerbianDelegationMembership->saveField('api', 1);
                    }
                }
            }
        } else {
            $info = 'organizationDelegation nothing to do';
            $this->out($info);
            $toLog .= $info . "\n";
        }
        $info = CakeTime::toServer(time()) . ' Serbia organizationDelegation end | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog .= $info . "\n";

        ////////////////organizationFriendship
        $info = CakeTime::toServer(time()) . ' Serbia organizationFriendship start | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog = $info . "\n";
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
                $info = 'SerbianFriendship uid: ' . $c['SerbianFriendship']['uid'];
                $this->out($info);
                $toLog .= $info . "\n";
                if (isset($combines) && $combines) {
                    $result = $this->QueleToSend->putDataDB($combines, 'Serbian');
                    //  pr($result);
                    if ($result) {
                        $this->SerbianFriendship->id = $c['SerbianFriendship']['id'];
                        $this->SerbianFriendship->saveField('api', 1);
                    }
                }
            }
        } else {
            $info = 'organizationFriendship nothing to do';
            $this->out($info);
            $toLog .= $info . "\n";
        }
        $info = CakeTime::toServer(time()) . ' Serbia organizationFriendship end | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog .= $info . "\n";

        ////////////////organizationSpeaker
        $tmpTime = CakeTime::format('-' . 7 . ' days', '%Y-%m-%d %H:%M:%S');
        $info = CakeTime::toServer(time()) . ' Serbia organizationSpeaker start | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog = $info . "\n";
        $content = $this->SerbianFunction->find('all', array(
            //'fields' => array('id', 'id'),
            'conditions' => array(
                'SerbianFunction.modified <' => $tmpTime,
            ),
//            'recursive' => -1,
//            'limit' => 2
        ));
        if ($content) {
            foreach ($content as $c) {
                $combines = $this->SerbianFunction->combineToApiArray($c);
                $combine[] = $combines;
                $info = 'SerbianFunction id: ' . $c['SerbianFunction']['id'];
                $this->out($info);
                $this->out($this->SerbianFunction->hint($c['SerbianFunction']['id']));
                $toLog .= $info . "\n";
                if (isset($combines) && $combines) {
                    $result = $this->QueleToSend->putDataDB($combines, 'Serbian');
                }
            }
        } else {
            $info = 'organizationSpeaker nothing to do';
            $this->out($info);
            $toLog .= $info . "\n";
        }
        $info = CakeTime::toServer(time()) . ' Serbia organizationSpeaker end | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog .= $info . "\n";

        ////////////////membershipConvocation
        $info = CakeTime::toServer(time()) . ' Serbia membershipConvocation start | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog = $info . "\n";
        $content = $this->SerbianDelegate->find('all', array(
            //'fields' => array('id', 'id'),
            'conditions' => array(
                'SerbianDelegate.status' => 0,
            // 'SerbianDelegate.url_uid' => null
            ),
            'contain' => array(
                'SerbianMenuData'
            ),
//            'limit' => 100
        ));

        if ($content) {
            foreach ($content as $c) {
                $combines = $this->SerbianDelegate->combineToApiArray($c);
                $combine[] = $combines;
                $info = 'SerbianDelegate id: ' . $c['SerbianDelegate']['id'];
                $this->out($info);
                $toLog .= $info . "\n";
                if (isset($combines) && $combines) {
                    $result = $this->QueleToSend->putDataDB(array($combines), 'Serbian');
                    //  pr($result);
                    if ($result) {
                        $this->SerbianDelegate->id = $c['SerbianDelegate']['id'];
                        $this->SerbianDelegate->saveField('status', 1);
                    }
                }
            }
        } else {
            $info = 'membershipConvocation nothing to do';
            $this->out($info);
            $toLog .= $info . "\n";
        }
        $info = CakeTime::toServer(time()) . ' Serbia membershipConvocation end | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog .= $info . "\n";

        ////////////////speeches
        $info = CakeTime::toServer(time()) . ' Serbia speeches start | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog = $info . "\n";
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
                $info = 'SerbianSpeecheContent post_uid: ' . $c['SerbianSpeecheIndex']['post_uid'];
                $this->out($info);
                $toLog .= $info . "\n";
                if (isset($combines) && $combines) {
                    $result = $this->QueleToSend->putDataDB($combines, 'Serbian');
                    //  pr($result);
                    if ($result) {
                        $this->SerbianSpeecheIndex->id = $c['SerbianSpeecheIndex']['id'];
                        $this->SerbianSpeecheIndex->saveField('status', 2);
                    }
                }
            }
        } else {
            $info = 'speeches nothing to do';
            $this->out($info);
            $toLog .= $info . "\n";
        }
        $info = CakeTime::toServer(time()) . ' Serbia speeches end | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog .= $info . "\n";


        ////////////////votes
        $info = CakeTime::toServer(time()) . ' Serbia votes start | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog = $info . "\n";
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
                $info = 'SerbianPdf stamp_in_text: ' . $c['SerbianPdf']['stamp_in_text'];
                $this->out($info);
                $toLog .= $info . "\n";
                if (isset($combines) && $combines) {
                    $result = $this->QueleToSend->putDataDB(array($combines), 'Serbian');
                    //  pr($result);
                    if ($result) {
                        $this->SerbianPdf->id = $c['SerbianPdf']['id'];
                        $this->SerbianPdf->saveField('status', 2);
                    }
                }
            }
        } else {
            $info = 'votes nothing to do';
            $this->out($info);
            $toLog .= $info . "\n";
        }
        $info = CakeTime::toServer(time()) . ' Serbia votes end | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog .= $info . "\n";
        $this->QueleToSend->doLog(
                array(
            'id' => time() . '_' . rand(4, 99999),
            'label' => 'serbia_combine_to_quelle',
            'status' => 'finished'
                ), 'Serbian'
        );
    }

    public function serbia_send_to_quelle($limit = null) {
        $limit = !is_null($limit) && (int) $limit ? $limit : 100;
        $halfLimit = $limit / 2;
        $trinityLimit = $limit * 3;
        $this->QueleToSend->doLog(
                array(
            'id' => time() . '_' . rand(4, 99999),
            'label' => 'serbia_send_to_quelle',
            'status' => 'running',
                ), 'Serbian'
        );
        $info = CakeTime::toServer(time()) . ' Serbia serbia_send_to_quelle start | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
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
            $speeches = $this->getListQueleToSend('speeches', $limit);
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
        $info = CakeTime::toServer(time()) . ' Serbia serbia_send_to_quelle end | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog .= $info . "\n";
        $this->QueleToSend->doLog(
                array(
            'id' => time() . '_' . rand(4, 99999),
            'label' => 'serbia_combine_to_quelle',
            'status' => 'finished'
                ), 'Serbian'
        );
    }

    public function getListQueleToSend($type = null, $limit = null) {
        $limit = !is_null($limit) && (int) $limit ? $limit : 100;
        if (!is_null($type)) {
            return $this->QueleToSend->find('list', array(
                        'fielsd' => array('id', 'id'),
                        'conditions' => array(
                            'direct' => 'Serbian',
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
