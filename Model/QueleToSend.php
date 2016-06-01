<?php

App::uses('HttpSocket', 'Network/Http');

class QueleToSend extends AppModel {

    public $username;
    public $password;
    public $baseUrl;

    public function putDataDB($data, $direct, $checkMd5 = true) {

        $puts = true;
        foreach ($data as $d) {
            foreach ($d as $k => $v) {
                $type = $k;
                $uid = $v['id'];
                $serialize = serialize($v);
                $md5 = md5($serialize);
                $conditions = array();

                $conditions[] = array('direct' => $direct);
                $conditions[] = array('type' => $type);
                $conditions[] = array('uid' => $uid);

                if ($checkMd5) {
                    $conditions[] = array('md5' => $md5);
                }
                $check = $this->find('first', array(
                    'fields' => array('id'),
                    'conditions' => $conditions
                ));
                //  pr($check);
                if (!$check) {
                    $this->create();
                    $this->set(array(
                        'direct' => $direct,
                        'uid' => $uid,
                        'type' => $type,
                        'data' => $serialize,
                        'md5' => $md5
                    ));
                    if (!$this->save()) {
                        $puts = false;
                    }
                }

//                pr($k);
//                pr($v);
            }
        }
        return $puts;
    }

    public function doRequest($id, $test = false) {

        $data = $this->findById($id);
        //set connect config
        $direct = $data['QueleToSend']['direct'];
        $getSet = Configure::read('api_server');

        $this->username = $getSet[$direct]['username'];
        $this->password = $getSet[$direct]['password'];
        $this->baseUrl = $getSet[$direct]['base_url'];

        $HttpSocket = new HttpSocket(array(
            'ssl_allow_self_signed' => true
        ));
        $HttpSocket->configAuth('Basic', $this->username, $this->password);
        $request = array(
            'header' => array('Content-Type' => 'application/json'),
            'raw' => null,
        );


        $postSend = unserialize($data['QueleToSend']['data']);
        $putSend = $postSend;
        if ($test) {
            pr($putSend);
            pr(json_encode($postSend));
        }
        unset($putSend['id']);

        $delete = false;
        $combine = array(
            'url_post' => $this->baseUrl . $data['QueleToSend']['type'],
            'post_send' => json_encode($postSend),
            'url_put' => $this->baseUrl . $data['QueleToSend']['type'] . '/' . $data['QueleToSend']['uid'],
            'put_send' => json_encode($putSend),
//            'delete' => 'https://api.parldata.eu/rs/skupstina/' . $data['QueleToSend']['type'],
        );

        usleep(300);
        $results = $HttpSocket->post($combine['url_post'], $combine['post_send'], $request);
        if ($test) {
            pr($results);
        }
        $result = json_decode($results->body);
        $status['status'] = false;
        $status['code'] = $results->code;

        if ($status['code'] == 500) {
            sleep(5);
        }

        if ($result->_status == 'ERR') {
            $results = null;
            $results = $HttpSocket->put($combine['url_put'], $combine['put_send'], $request);
            $status['code'] = $results->code;
            $result = json_decode($results->body);
            if ($test) {
                pr($results);
            }
            if ($status['code'] == 500) {
                sleep(5);
            } elseif ($result->_status == 'OK') {
                $status['status'] = true;
            }
        } elseif ($result->_status == 'OK') {
            $status['status'] = true;
        }

        if ($test) {
            pr($status);
        }
        return $status;
    }

    public function doLog($log, $direct) {
        $getSet = Configure::read('api_server');

        $this->username = $getSet[$direct]['username'];
        $this->password = $getSet[$direct]['password'];
        $this->baseUrl = $getSet[$direct]['base_url'];

        $HttpSocket = new HttpSocket(array(
            'ssl_allow_self_signed' => true
        ));
        $HttpSocket->configAuth('Basic', $this->username, $this->password);
        $request = array(
            'header' => array('Content-Type' => 'application/json'),
            'raw' => null,
        );
        $results = $HttpSocket->post($this->baseUrl . 'logs', json_encode($log), $request);
        pr($log);
        pr($results);
    }

    public function deleteSerbiaAll($delete = null) {
        $HttpSocket = new HttpSocket(array(
            'ssl_allow_self_signed' => true
        ));
        $HttpSocket->configAuth('Basic', 'scraper', 'b8nf*(nf');
        $request = array(
            'header' => array('Content-Type' => 'application/json'),
            'raw' => null,
        );
//        $list = array(
//            'logs', 'people', 'posts', 'organizations', 'speeches', 'events', 'motions', 'votes', 'areas', 'memberships', 'vote-events'
//        );
        $list = array(
            'events', 'speeches',
        );
        if (is_null($delete)) {
            foreach ($list as $l) {
//                $results = $HttpSocket->delete('https://api.parldata.eu/rs/skupstina/' . $l, array(), $request);
//                $results = $HttpSocket->delete('https://api.parldata.eu/kv/kuvendi/' . $l, array(), $request);
//                $results = $HttpSocket->delete('https://api.parldata.eu/al/kuvendi/' . $l, array(), $request);
            }
        }
    }

}
