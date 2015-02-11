<?php

App::uses('HttpSocket', 'Network/Http');

class QueleToSend extends AppModel {

//    public $request = array(
//        'method' => 'GET',
//        'uri' => array(
//            'scheme' => 'http',
//            'host' => null,
//            'port' => 80,
//            'user' => null,
//            'pass' => null,
//            'path' => null,
//            'query' => null,
//            'fragment' => null
//        ),
//        'auth' => array(
//            'method' => 'Basic',
//            'user' => 'scraper',
//            'pass' => 'ngaA(f77'
//        ),
//        'version' => '1.1',
//        'body' => '',
//        'line' => null,
//        'header' => array(
//            'Connection' => 'close',
//            'User-Agent' => 'CakePHP'
//        ),
//        'raw' => null,
//        'redirect' => false,
//        'cookies' => array()
//    );

    public function beforeFilter() {
        parent::beforeFilter();
    }

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

        $username = $getSet[$direct]['username'];
        $password = $getSet[$direct]['password'];
        $baseUrl = $getSet[$direct]['base_url'];

        $HttpSocket = new HttpSocket(array(
            'ssl_allow_self_signed' => true
        ));



        //  $HttpSocket->configAuth('Basic', 'scraper', 'ngaA(f77');
        $HttpSocket->configAuth('Basic', $username, $password);
        $request = array(
            'header' => array('Content-Type' => 'application/json'),
            'raw' => null,
        );


        $postSend = unserialize($data['QueleToSend']['data']);
        $putSend = $postSend;
        pr($putSend);
        pr(json_encode($postSend));
        unset($putSend['id']);

        $delete = false;
        $combine = array(
            'url_post' => $baseUrl . $data['QueleToSend']['type'],
            'post_send' => json_encode($postSend),
            'url_put' => $baseUrl . $data['QueleToSend']['type'] . '/' . $data['QueleToSend']['uid'],
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
            return $status;
        }

        if ($result->_status == 'ERR') {
            $results = null;
            $results = $HttpSocket->put($combine['url_put'], $combine['put_send'], $request);
            $status['code'] = $results->code;
            if ($status['code'] == 500) {
                sleep(5);
                return $status;
            }
            if ($test) {
                pr($results);
            }
            $result = json_decode($results->body);
            if ($result->_status == 'OK') {
                $status['status'] = true;
            }
        } elseif ($result->_status == 'OK') {
            $status['status'] = true;
        }
        if ($test) {
//            pr(array($status, $data, $results, $postSend));
        } else {
            return $status;
        }
        // return array($status, $data, $results, $postSend);
    }

    public function deleteSerbiaAll($delete = null) {
        $HttpSocket = new HttpSocket(array(
            'ssl_allow_self_signed' => true
        ));
        $HttpSocket->configAuth('Basic', 'scraper', 'ngaA(f77');
        $request = array(
            'header' => array('Content-Type' => 'application/json'),
            'raw' => null,
        );
        $list = array(
            'logs', 'people', 'posts', 'organizations', 'speeches', 'events', 'motions', 'votes', 'areas', 'memberships', 'vote-events'
        );
        if (is_null($delete)) {
            foreach ($list as $l) {
                $results = $HttpSocket->delete('https://api.parldata.eu/al/kuvendi/' . $l, array(), $request);
            }
        }
    }

}
