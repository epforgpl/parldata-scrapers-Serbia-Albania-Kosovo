<?php

//App::uses('CakeEmail', 'Network/Email');
App::uses('CakeTime', 'Utility');

class KosovanTask extends Shell {

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
        'KosovoCommitteFunc',
        'KosovoMps',
        'KosovoPdf',
        'KosovoTxt',
        'KosovoLog',
        'QueleToSend'
    );

    public function get_mps_index() {
        $info = CakeTime::toServer(time()) . ' Kosovo get_mps_index start | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog = $info . "\n";

        $listMenus = $this->KosovoMpsMenu->find('all', array(
            'fields' => array('id', 'url', 'name')
        ));
        if ($listMenus) {
            $data = array();
            foreach ($listMenus as $lm) {
                $data = $this->Kosovo->getMpsIndex($lm['KosovoMpsMenu']['url']);
                // pr($data['date']);
                if (count($data['data']) > 0) {
                    $info = 'find index | count: ' . count($data['data']);
                    $this->out($info);
                    $toLog .= $info . "\n";

                    foreach ($data['data'] as $d) {
                        if (!empty($d['name'])) {
                            $d['kosovo_mps_menu_id'] = $lm['KosovoMpsMenu']['id'];
                            $d = array_merge($d, $data['date']);
                            pr($d);
                            if ($this->KosovoMpsIndex->save($d)) {
                                $info = 'update index | menu_id: ' . $d['kosovo_mps_menu_id'] . ' | id: ' . $d['id'];
                                $this->out($info);
                                $toLog .= $info . "\n";
                            }
                        }
                    }
                } else {
                    $info = 'no data get fron site (index menu)';
                    $this->out($info);
                    $toLog .= $info . "\n";
                }
                usleep(15000);
            }
        } else {
            $info = 'no data from index menu';
            $this->out($info);
            $toLog .= $info . "\n";
        }

        $info = CakeTime::toServer(time()) . ' Kosovo get_mps_index end | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog .= $info . "\n";
        if ($toLog) {
            $this->KosovoLog->create();
            $this->KosovoLog->set(array(
                'type' => 7,
                'logcontent' => $toLog
                    )
            );
            if ($this->KosovoLog->save()) {
                $this->out('save log id: ' . $this->KosovoLog->id);
            }
        }
    }

    public function get_index_list_page() {
        $info = CakeTime::toServer(time()) . ' Kosovo getIndexListPage start | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog = $info . "\n";
        $content = $this->Kosovo->getIndexListPage();
        if ($content) {
            if ($this->KosovoMpsMenu->saveAll($content)) {
                $info = 'update index | count: ' . count($content);
                $this->out($info);
                $toLog .= $info . "\n";

                $change = true;
            }
        } else {
            $info = 'something is wrong, I can not retrieve the list of articles';
            $this->out($info);
            $toLog .= $info . "\n";
        }
        $info = CakeTime::toServer(time()) . ' Kosovo getIndexListPage end | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog .= $info . "\n";
        if ($toLog) {
            $this->KosovoLog->create();
            $this->KosovoLog->set(array(
                'type' => 6,
                'logcontent' => $toLog
                    )
            );
            if ($this->KosovoLog->save()) {
                $this->out('save log id: ' . $this->KosovoLog->id);
            }
        }
    }

    public function get_index() {
        $info = CakeTime::toServer(time()) . ' Kosovo get index start | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog = $info . "\n";

        $change = false;
        $link = 'http://www.kuvendikosoves.org/?cid=3,177';
        $content = $this->Kosovo->getPlenarySpeechesScrap($link);
        if (count($content)) {
            foreach ($content as $c) {
                $check = $this->KosovoSpeecheIndex->findById($c['id']);
                if ($check && $check['KosovoSpeecheIndex']['index_md5'] != $c['index_md5']) {
                    $c['status'] = 0;
                    if ($this->KosovoSpeecheIndex->save($c)) {
                        $info = 'diff md5 update index | postUid: ' . $c['post_uid'] . ' | postDate: ' . $c['post_date'];
                        $this->out($info);
                        $toLog .= $info . "\n";

                        $change = true;
                    }
                } elseif (!$check) {
                    if ($this->KosovoSpeecheIndex->save($c)) {
                        $info = 'save new index | postUid: ' . $c['post_uid'] . ' | postDate: ' . $c['post_date'];
                        $this->out($info);
                        $toLog .= $info . "\n";

                        $change = true;
                    }
                }
                if (!$change) {
                    $info = 'nothing changed';
                    $this->out($info);
                    $toLog .= $info . "\n";
                }
            }
        } else {
            $info = 'something is wrong, I can not retrieve the list of articles';
            $this->out($info);
            $toLog .= $info . "\n";
        }


        $info = CakeTime::toServer(time()) . ' Kosovo get index end | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog .= $info . "\n";
        if ($toLog) {
            $this->KosovoLog->create();
            $this->KosovoLog->set(array(
                'type' => 0,
                'logcontent' => $toLog
                    )
            );
            if ($this->KosovoLog->save()) {
                $this->out('save log id: ' . $this->KosovoLog->id);
            }
        }
    }

    public function get_content($limit = null) {
        $limit = !is_null($limit) && (int) $limit ? $limit : 30;
        $this->out($limit);
        $info = CakeTime::toServer(time()) . ' Kosovo get content start | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog = $toLogPdf = $toLogTxt = $info . "\n";

        $change = false;
        $listDataIndex = $this->KosovoSpeecheIndex->find('list', array(
            'fields' => array('KosovoSpeecheIndex.id', 'KosovoSpeecheIndex.url'),
            'conditions' => array(
                'KosovoSpeecheIndex.active' => 1,
                'KosovoSpeecheIndex.status' => 0
            ),
            'order' => 'KosovoSpeecheIndex.post_date ASC',
            'limit' => $limit
                )
        );

        if ($listDataIndex) {
            foreach ($listDataIndex as $key => $url) {
                $this->out($url);
                $content = $this->Kosovo->extractContent($key);
//                print_r($content['KosovoSpeecheContent']);

                if ($content['KosovoSpeecheContent']) {
                    $c = $this->KosovoSpeecheContent->findByKosovoSpeecheIndexId($key);
//                    print_r($content['KosovoSpeecheContent']);
//                    return;
                    if (!$c) {
                        if ($this->KosovoSpeecheContent->save($content['KosovoSpeecheContent'])) {

                            $info = 'save new content (key: ' . $key . ') ' . $url;
                            $this->out($info);
                            $toLog .= $info . "\n";

                            $change = true;
                        }
                    } else {
                        if ($c['KosovoSpeecheContent']['content_md5'] != $content['KosovoSpeecheContent']['content_md5']) {
                            $content['KosovoSpeecheContent']['status'] = 0;
                            if ($this->KosovoSpeecheContent->save($content['KosovoSpeecheContent'])) {

                                $info = 'diff md5 update content (key: ' . $key . ') ' . $url;
                                $this->out($info);
                                $toLog .= $info . "\n";

                                $change = true;
                            }
                        }
                    }
                    $this->KosovoSpeecheIndex->id = $key;
                    if ($change) {
                        $this->out($key);
                        $this->KosovoSpeecheIndex->saveField('status', 0);
                    }
                    $this->KosovoSpeecheIndex->saveField('status', 1);

                    if (isset($content['KosovoPdf'])) {
                        $changePdf = false;
                        $info = 'find content pdfs (key: ' . $key . ') pcs: ' . count($content['KosovoPdf']);
                        $this->out($info);
                        $toLog .= $info . "\n";
                        $toLogPdf .= $info . "\n";

                        foreach ($content['KosovoPdf'] as $pdf) {
                            $p = $this->KosovoPdf->findByPdfUrl($pdf['pdf_url']);

                            if (!$p) {
                                $this->KosovoPdf->create();
                                if ($this->KosovoPdf->save($pdf)) {

                                    $info = 'save new pdf (key: ' . $this->KosovoPdf->id . ')';
                                    $this->out($info);
                                    $toLogPdf .= $info . "\n";

                                    $changePdf = true;
                                }
                            }
                        }
                        if (!$changePdf) {
                            $info = 'pdfs nothing to do';
                            $this->out($info);
                            $toLog .= $info . "\n";
                            $toLogPdf .= $info . "\n";
                        }

                        $info = 'end content pdfs';
                        $this->out($info);
                        $toLog .= $info . "\n";
                        $toLogPdf .= $info . "\n";
                    }

                    if (isset($content['KosovoTxt'])) {
                        $changeTxt = false;
                        $info = 'find content Txts (key: ' . $key . ') pcs: ' . count($content['KosovoTxt']);
                        $this->out($info);
                        $toLog .= $info . "\n";
                        $toLogTxt .= $info . "\n";

                        foreach ($content['KosovoTxt'] as $txt) {
                            $t = $this->KosovoTxt->findByTxtUrl($txt['txt_url']);

                            if (!$t) {
                                $this->KosovoTxt->create();
                                if ($this->KosovoTxt->save($txt)) {

                                    $info = 'save new txt (key: ' . $this->KosovoTxt->id . ')';
                                    $this->out($info);
                                    $toLogTxt .= $info . "\n";

                                    $changeTxt = true;
                                }
                            }
                        }
                        if (!$changeTxt) {
                            $info = 'Txts nothing to do';
                            $this->out($info);
                            $toLog .= $info . "\n";
                            $toLogTxt .= $info . "\n";
                        }

                        $info = 'end content Txts';
                        $this->out($info);
                        $toLog .= $info . "\n";
                        $toLogTxt .= $info . "\n";
                    }
                } else {
                    $info = 'something is wrong, I can not get content from (key: ' . $key . ') ' . $url;
                    $this->out($info);
                    $toLog .= $info . "\n";
                }
                usleep(50000);
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
        $info = CakeTime::toServer(time()) . ' Kosovo get content end | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog .= $info . "\n";
        if ($toLog) {
            $this->KosovoLog->create();
            $this->KosovoLog->set(array(
                'type' => 1,
                'logcontent' => $toLog
                    )
            );
            if ($this->KosovoLog->save()) {
                $this->out('save content log id: ' . $this->KosovoLog->id);
            }
        }
        if (isset($toLogPdf)) {
            $this->KosovoLog->create();
            $this->KosovoLog->set(array(
                'type' => 2,
                'logcontent' => $toLogPdf
                    )
            );
            if ($this->KosovoLog->save()) {
                $this->out('save pdfs log id: ' . $this->KosovoLog->id);
            }
        }
        if (isset($toLogTxt)) {
            $this->KosovoLog->create();
            $this->KosovoLog->set(array(
                'type' => 3,
                'logcontent' => $toLogTxt
                    )
            );
            if ($this->KosovoLog->save()) {
                $this->out('save Txts log id: ' . $this->KosovoLog->id);
            }
        }
    }

    public function combine_txts($limit = null) {
        $limit = !is_null($limit) && (int) $limit ? $limit : 30;
        $info = CakeTime::toServer(time()) . ' Kosovo combine_txts start | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog = $info . "\n";

        $list = $this->KosovoTxt->find('all', array(
            'fields' => array('id', 'txt_url'),
            'conditions' => array('KosovoTxt.status' => 0),
            'limit' => $limit
        ));

        if (count($list)) {
            foreach ($list as $l) {
                $id = $l['KosovoTxt']['id'];
                $filePath = $l['KosovoTxt']['txt_url'];

                $info = 'get data to save: id ' . $id;
                $this->out($info);
                $toLog .= $info . "\n";

                if ($this->KosovoTxt->getContentTxtFromId($id)) {
                    $info = 'download txt: ' . $filePath;
                    $this->out($info);
                    $toLog .= $info . "\n";
                } else {
                    $info = '!!!ERROR download txt: ' . $filePath;
                    $this->out($info);
                    $toLog .= $info . "\n";
                }
            }
        } else {
            $info = 'txts nothing to do';
            $this->out($info);
            $toLog .= $info . "\n";
        }
        $info = CakeTime::toServer(time()) . ' Kosovo combine_txts end | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog .= $info . "\n";
        if ($toLog) {
            $this->KosovoLog->create();
            $this->KosovoLog->set(array(
                'type' => 4,
                'logcontent' => $toLog
                    )
            );
            if ($this->KosovoLog->save()) {
                $this->out('save combine_txts log id: ' . $this->KosovoLog->id);
            }
        }
    }

    public function combine_pdfs($limit = null) {
        $limit = !is_null($limit) && (int) $limit ? $limit : 30;
        $info = CakeTime::toServer(time()) . ' Kosovo combine_pdfs start | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog = $info . "\n";

        $list = $this->KosovoPdf->find('all', array(
            'fields' => array('id', 'pdf_url'),
            'conditions' => array('KosovoPdf.status' => 0),
            'limit' => $limit
        ));

        if (count($list)) {
            foreach ($list as $l) {
                $id = $l['KosovoPdf']['id'];
                $filePath = $l['KosovoPdf']['pdf_url'];

                $info = 'get data to save: id ' . $id;
                $this->out($info);
                $toLog .= $info . "\n";

                if ($this->KosovoPdf->getContentPdfFromId($id)) {
                    $info = 'download pdf: ' . $filePath;
                    $this->out($info);
                    $toLog .= $info . "\n";
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
        $info = CakeTime::toServer(time()) . ' Kosovo combine_pdfs end | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog .= $info . "\n";
        if ($toLog) {
            $this->KosovoLog->create();
            $this->KosovoLog->set(array(
                'type' => 5,
                'logcontent' => $toLog
                    )
            );
            if ($this->KosovoLog->save()) {
                $this->out('save combine_pdfs log id: ' . $this->KosovoLog->id);
            }
        }
    }

    public function get_mps_contacts($limit) {
        $limit = !is_null($limit) && (int) $limit ? $limit : 30;
        $info = CakeTime::toServer(time()) . ' Kosovo get_mps_contacts start | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog = $info . "\n";

        $data = $this->KosovoMpsIndex->find('all', array(
            'fields' => array('id', 'url', 'name', 'start_date', 'end_date'),
            'conditions' => array(
                'status' => 0
            ),
            'limit' => $limit,
            'recursive' => -1
        ));
        if ($data) {
            foreach ($data as $d) {
                $info = 'get data contact: id ' . $d['KosovoMpsIndex']['id'];
                $this->out($info);
                $toLog .= $info . "\n";

                $content = $this->KosovoMps->getMpsContact($d);
                if ($content) {
                    if ($this->KosovoMpsDetail->saveAll($content)) {
                        $info = 'success save data contact: id ' . $content['KosovoMpsDetail']['id'];
                        $this->out($info);
                        $toLog .= $info . "\n";
                        $this->KosovoMpsIndex->id = $d['KosovoMpsIndex']['id'];
                        $this->KosovoMpsIndex->saveField('status', 1);
                    }
                } else {
                    $info = 'no get data contact: id ' . $d['KosovoMpsIndex']['id'];
                    $this->out($info);
                    $toLog .= $info . "\n";
                }
                usleep(1500);
            }
        } else {
            $info = 'no get data index list';
            $this->out($info);
            $toLog .= $info . "\n";
        }

        $info = CakeTime::toServer(time()) . ' Kosovo get_mps_contacts end | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog .= $info . "\n";
        if ($toLog) {
            $this->KosovoLog->create();
            $this->KosovoLog->set(array(
                'type' => 8,
                'logcontent' => $toLog
                    )
            );
            if ($this->KosovoLog->save()) {
                $this->out('save get_mps_contacts log id: ' . $this->KosovoLog->id);
            }
        }
    }

    public function check_mps_contacts($now, $limit) {
        $limit = !is_null($limit) && (int) $limit ? $limit : 30;
        $info = CakeTime::toServer(time()) . ' Kosovo check_mps_contacts start | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog = $info . "\n";

        $data = $this->KosovoMpsDetail->find('all', array(
            'fields' => array('KosovoMpsDetail.id', 'KosovoMpsDetail.md5'),
            'conditions' => array(
                'KosovoMpsDetail.modified <' => $now,
            ),
            'contain' => array(
                'KosovoMpsIndex' => array(
                    'fields' => array('id', 'url', 'name', 'start_date', 'end_date'),
                )
            ),
            'limit' => $limit,
        ));
        if ($data) {
            foreach ($data as $d) {
                $info = 'get data contact: id ' . $d['KosovoMpsIndex']['id'];
                $this->out($info);
                $toLog .= $info . "\n";

                $content = $this->KosovoMps->getMpsContact($d);

                $this->out($content['KosovoMpsDetail']['md5']);
                $this->out($d['KosovoMpsDetail']['md5']);

                if ($content) {
                    if ($d['KosovoMpsDetail']['md5'] != $content['KosovoMpsDetail']['md5']) {
                        if ($this->KosovoMpsDetail->saveAll($content)) {
                            $info = 'success save data contact: id ' . $content['KosovoMpsDetail']['id'];
                            $this->out($info);
                            $toLog .= $info . "\n";
                        }
                    } else {
                        $info = 'nothing to do contact: id ' . $content['KosovoMpsDetail']['id'];
                        $this->out($info);
                        $toLog .= $info . "\n";
                        $this->KosovoMpsDetail->hint($content['KosovoMpsDetail']['id']);
                    }
                } else {
                    $info = 'no get data contact: id ' . $content['KosovoMpsDetail']['id'];
                    $this->out($info);
                    $toLog .= $info . "\n";
                }
                usleep(1500);
            }
        } else {
            $info = 'no get data index list';
            $this->out($info);
            $toLog .= $info . "\n";
        }


        $info = CakeTime::toServer(time()) . ' Kosovo check_mps_contacts end | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog .= $info . "\n";
        if ($toLog) {
            $this->KosovoLog->create();
            $this->KosovoLog->set(array(
                'type' => 9,
                'logcontent' => $toLog
                    )
            );
            if ($this->KosovoLog->save()) {
                $this->out('save check_mps_contacts log id: ' . $this->KosovoLog->id);
            }
        }
    }

    public function kosovo_combine_to_quelle() {
        ////////////////organizationConvocation
        $info = CakeTime::toServer(time()) . ' Kosovo organizationConvocation start | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog = $info . "\n";
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
                $info = 'KosovoMpsMenu start_date: ' . $c['KosovoMpsMenu']['start_date'];
                $this->out($info);
                $toLog .= $info . "\n";
                if (isset($combines) && $combines) {
                    $result = $this->QueleToSend->putDataDB(array($combines), 'Kosovan');
                    //  pr($result);
                    if ($result) {
                        $this->KosovoMpsMenu->id = $c['KosovoMpsMenu']['id'];
                        $this->KosovoMpsMenu->saveField('api', 1);
                    }
                }
            }
        } else {
            $info = 'organizationConvocation nothing to do';
            $this->out($info);
            $toLog .= $info . "\n";
        }
        $info = CakeTime::toServer(time()) . ' Kosovo organizationConvocation end | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog .= $info . "\n";

        ////////////////people
        $info = CakeTime::toServer(time()) . ' Kosovo people start | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog = $info . "\n";
        $content = $this->KosovoMpsDetail->find('all', array(
            'conditions' => array('KosovoMpsDetail.status' => 0),
//            'limit' => 1
        ));
        if ($content) {
            foreach ($content as $c) {
                $combines = $this->KosovoMpsDetail->combineToApiArray($c);
                $combine[] = $combines;
                $info = 'people id: ' . $c['KosovoMpsDetail']['id'];
                $this->out($info);
                $toLog .= $info . "\n";
                if (isset($combines) && $combines) {
                    $result = $this->QueleToSend->putDataDB(array($combines), 'Kosovan');
                    //  pr($result);
                    if ($result) {
                        $this->KosovoMpsDetail->id = $c['KosovoMpsDetail']['id'];
                        $this->KosovoMpsDetail->saveField('status', 1);
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
        $info = CakeTime::toServer(time()) . ' Kosovo people end | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog .= $info . "\n";

        ////////////////organizationParty
        $info = CakeTime::toServer(time()) . ' Kosovo organizationParty start | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog = $info . "\n";
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
                $info = 'organizationParty id: ' . $c['KosovoParty']['id'];
                $this->out($info);
                $toLog .= $info . "\n";
                if (isset($combines) && $combines) {
                    $result = $this->QueleToSend->putDataDB($combines, 'Kosovan');
                    //  pr($result);
                    if ($result) {
                        $this->KosovoParty->id = $c['KosovoParty']['id'];
                        $this->KosovoParty->saveField('api', 1);
                    }
                }
            }
        } else {
            $info = 'organizationParty nothing to do';
            $this->out($info);
            $toLog .= $info . "\n";
        }
        $info = CakeTime::toServer(time()) . ' Kosovo organizationParty end | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog .= $info . "\n";

        ////////////////organizationParliamentaryGroups
        $info = CakeTime::toServer(time()) . ' Kosovo organizationParliamentaryGroups start | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog = $info . "\n";
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
                $info = 'KosovoParliamentaryGroup uid: ' . $c['KosovoParliamentaryGroup']['uid'];
                $this->out($info);
                $toLog .= $info . "\n";
                if (isset($combines) && $combines) {
                    $result = $this->QueleToSend->putDataDB($combines, 'Kosovan');
                    //  pr($result);
                    if ($result) {
                        $this->KosovoParliamentaryGroup->id = $c['KosovoParliamentaryGroup']['id'];
                        $this->KosovoParliamentaryGroup->saveField('api', 1);
                    }
                }
            }
        } else {
            $info = 'organizationParliamentaryGroups nothing to do';
            $this->out($info);
            $toLog .= $info . "\n";
        }
        $info = CakeTime::toServer(time()) . ' Kosovo organizationParliamentaryGroups end | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog .= $info . "\n";

        ////////////////organizationCommitte
        $info = CakeTime::toServer(time()) . ' Kosovo organizationCommitte start | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog = $info . "\n";
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
                $info = 'KosovoCommittee uid: ' . $c['KosovoCommittee']['uid'];
                $this->out($info);
                $toLog .= $info . "\n";
                if (isset($combines) && $combines) {
                    $result = $this->QueleToSend->putDataDB($combines, 'Kosovan');
                    //  pr($result);
                    if ($result) {
                        $this->KosovoCommittee->id = $c['KosovoCommittee']['id'];
                        $this->KosovoCommittee->saveField('api', 1);
                    }
                }
            }
        } else {
            $info = 'organizationCommitte nothing to do';
            $this->out($info);
            $toLog .= $info . "\n";
        }
        $info = CakeTime::toServer(time()) . ' Kosovo organizationCommitte end | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog .= $info . "\n";

        ////////////////speecheEvents
        $info = CakeTime::toServer(time()) . ' Kosovo speecheEvents start | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog = $info . "\n";
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
//            'limit' => 1
        ));
//        pr($content);
        if ($content) {
            foreach ($content as $c) {
                $combines = $this->KosovoSpeecheContent->combineToApiArray($c);
                $combine[] = $combines;
                $info = 'KosovoSpeecheIndex post_uid: ' . $c['KosovoSpeecheIndex']['post_uid'];
                $this->out($info);
                $toLog .= $info . "\n";
                if (isset($combines) && $combines) {
                    $result = $this->QueleToSend->putDataDB($combines, 'Kosovan');
                    //  pr($result);
                    if ($result) {
                        $this->KosovoSpeecheIndex->id = $c['KosovoSpeecheIndex']['id'];
                        $this->KosovoSpeecheIndex->saveField('status', 2);
                    }
                }
            }
        } else {
            $info = 'speeches nothing to do';
            $this->out($info);
            $toLog .= $info . "\n";
        }
        $info = CakeTime::toServer(time()) . ' Kosovo speecheEvents end | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog .= $info . "\n";


        ////////////////votes
        $info = CakeTime::toServer(time()) . ' Kosovo votes start | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog = $info . "\n";
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
//                pr($c);
                $combines = $this->KosovoTxt->combineToApiArray($c);
                $combine[] = $combines;
                $info = 'KosovoTxt id: ' . $c['KosovoTxt']['id'];
                $this->out($info);
                $toLog .= $info . "\n";
                if (isset($combines) && $combines) {
                    $result = $this->QueleToSend->putDataDB(array($combines), 'Kosovan');
                    //  pr($result);
                    if ($result) {
                        $this->KosovoTxt->id = $c['KosovoTxt']['id'];
                        $this->KosovoTxt->saveField('status', 2);
                    }
                }
            }
        } else {
            $info = 'votes nothing to do';
            $this->out($info);
            $toLog .= $info . "\n";
        }
        $info = CakeTime::toServer(time()) . ' Kosovo votes end | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog .= $info . "\n";
        //
    }

    public function kosovo_send_to_quelle($limit = null) {
        $limit = !is_null($limit) && (int) $limit ? $limit : 100;
        $halfLimit = $limit / 2;
        $trinityLimit = $limit * 3;

        $info = CakeTime::toServer(time()) . ' Kosovo kosovo_send_to_quelle start | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
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
        $info = CakeTime::toServer(time()) . ' Kosovo kosovo_send_to_quelle end | pid:' . getmypid() . ' | mem: ' . $this->convert(memory_get_usage());
        $this->out($info);
        $toLog .= $info . "\n";
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
