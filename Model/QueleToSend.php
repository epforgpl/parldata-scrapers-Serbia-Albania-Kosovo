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

    public function putDataDB($data, $direct) {
        $puts = true;
        foreach ($data as $d) {
            foreach ($d as $k => $v) {
                $type = $k;
                $uid = $v['id'];
                $serialize = serialize($v);
                $md5 = md5($serialize);
                $check = $this->find('first', array(
                    'fields' => array('id'),
                    'conditions' => array(
                        'direct' => $direct,
                        'type' => $type,
                        'uid' => $uid,
                        'md5' => $md5
                    )
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

        $HttpSocket = new HttpSocket(array(
            'ssl_allow_self_signed' => true
        ));
        $HttpSocket->configAuth('Basic', 'scraper', 'ngaA(f77');
        $request = array(
            'header' => array('Content-Type' => 'application/json'),
            'raw' => null,
        );

        $data = $this->findById($id);
        $postSend = unserialize($data['QueleToSend']['data']);
        $putSend = $postSend;
        unset($putSend['id']);

        $delete = false;
        $combine = array(
            'url_post' => 'https://api.parldata.eu/rs/parlament/' . $data['QueleToSend']['type'],
            'post_send' => json_encode($postSend),
            'url_put' => 'https://api.parldata.eu/rs/parlament/' . $data['QueleToSend']['type'] . '/' . $data['QueleToSend']['uid'],
            'put_send' => json_encode($putSend),
            'delete' => 'https://api.parldata.eu/rs/parlament/' . $data['QueleToSend']['type'],
        );
//        if ($delete) {
//            $results = $HttpSocket->delete($combine['delete'], array(), $request);
//            return json_decode($results->body);
//        }

        $results = $HttpSocket->post($combine['url_post'], $combine['post_send'], $request);
        if ($test) {
            pr($results);
        }
        $result = json_decode($results->body);
        $status = false;

        if ($result->_status == 'ERR') {
            $results = null;
            $results = $HttpSocket->put($combine['url_put'], $combine['put_send'], $request);
            if ($test) {
                pr($results);
            }
            $result = json_decode($results->body);
            if ($result->_status == 'OK') {
                $status = true;
            }
        } elseif ($result->_status == 'OK') {
            $status = true;
        }
        if ($test) {
            pr(array($status, $data, $results, $postSend));
        } else {
            return $status;
        }
        // return array($status, $data, $results, $postSend);
    }

}
