<?php

/**
 * Application model for CakePHP.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('Model', 'Model');
App::uses('CakeTime', 'Utility');
/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
set_time_limit(3600);

class AppModel extends Model {

    public $actsAs = array('Containable');
    public $getSerbiaHost = 'http://www.parlament.gov.rs';
    public $getKosovoHost = 'http://www.kuvendikosoves.org';
    public $enableProxy = 0;
    public $proxyServer = array(
        'ip' => 'w3cache.tpnet.pl',
        'port' => '8080'
    );
    public $pdfUrl;

    public function extractTrimStrip($data, $formula) {
        if (preg_match($formula, $data, $matches)) {
            return trim(strip_tags(reset($matches)));
        }
    }

    public function extractAndReplace($data, $formula, $replace) {
        if (preg_match($formula, $data, $matches)) {
            $result = trim(strip_tags(reset($matches)));
            return preg_replace($replace, '', $result);
        }
    }

    public function extractAndAfterReplace($data, $formula, $replace, $special = '') {
        if (preg_match($formula, $data, $matches)) {
            $result = trim(reset($matches));
            $result = strip_tags(preg_replace($replace, $special, $result));
            return preg_replace('/\s+/', ' ', $result);
        }
    }

    public function extractMoreAndReplace($data, $formula, $replace) {
        $d = array();
        if (preg_match_all($formula, $data, $matches)) {
            $result = reset($matches);
            foreach ($result as $r) {
                $d[] = preg_replace($replace, '', $r);
            }
        }
        return $d;
    }

    public function extractMoreAndJoin($data, $formula) {
        $d = null;
        if (preg_match_all($formula, $data, $matches)) {
            $result = reset($matches);
            foreach ($result as $r) {
                $d .= $r;
            }
        }
        return $d;
    }

    public function extractMoreAndStripTrim($data, $formula) {
        $d = null;
        if (preg_match_all($formula, $data, $matches)) {
            $result = reset($matches);
            foreach ($result as $r) {
                $d[] = trim(strip_tags($r));
            }
        }
        return $d;
    }

    public function hint($id) {
        if ((int) $id) {
            $this->id = $id;
            $hint = $this->field('hints');
            $hint++;
            if ($this->saveField('hints', $hint)) {
                return $hint;
            }
        }
        return false;
    }

    public function findAllAlbaniaChamberFromContent($content) {
        $formulaChamber = '/(X{0,3})(IX|IV|V?I{0,3})/';

        if (preg_match_all($formulaChamber, trim(strip_tags($content)), $matches)) {
            $results = reset($matches);
            $results = array_unique($results);
//            pr($results);
            if ($results) {
                foreach ($results as $r) {
                    if (!empty($r)) {
                        $ch[] = $this->findAlbaniaChamber($r);
                    }
                }
                return $ch;
            }
        }
    }

    public function getChamber($date) {
        App::import('Model', 'SerbianMenuData');
        $this->SerbianMenuData = new SerbianMenuData();
        $organization_id = $this->SerbianMenuData->field(
                'start_date', array(
            'start_date <' => $date,
                ), 'start_date DESC'
        );

        return $this->toChamber($organization_id);
    }

    public function toChamber($date) {
        return 'chamber_' . $date;
    }

    public function toCamelCase($result) {
        $result = preg_replace('/\s+|:|\.|\,|\-|\(|\)/i', " ", $result);
        $result = trim(mb_convert_case(mb_strtolower($result), MB_CASE_TITLE, "UTF-8"));
        $result = preg_replace('/\s/i', "", $result); //to CamelCase
        return $result;
    }

    public function toApiDate($date) {
        $date = CakeTime::toAtom($date);
        return preg_replace('/Z/', '', $date);
    }

    public function toSerbiaNameSplit($name = null) {

    }

    public function findAlbaniaChamber($name) {
        $name = trim($name);
        App::import('Model', 'AlbaniaChamber');
        $this->AlbaniaChamber = new AlbaniaChamber();
        $checkName = $this->AlbaniaChamber->find('first', array(
            'fields' => array('id', 'name', 'start_date', 'end_date'),
            'conditions' => array('AlbaniaChamber.name LIKE' => $name)
                )
        );
        if ($checkName) {
            return $checkName;
        } else {
            $newId = 'chamber_' . $name;
            $data[]['organizations']['id'] = $newId;
            $data[]['logs'] = array(
                'id' => $newId . '_' . time() . '_' . rand(0, 999),
                'label' => 'not found: ' . $newId,
                'status' => 'finished',
//                        'params' => $t
            );
            App::import('Model', 'QueleToSend');
            $this->QueleToSend = new QueleToSend();
            $this->QueleToSend->putDataDB($data, 'Albanian', false);
            return $newId;
        }
    }

    public function findAlbaniaChamberDate($date) {

        App::import('Model', 'AlbaniaChamber');
        $this->AlbaniaChamber = new AlbaniaChamber();
        $lastDate = $this->AlbaniaChamber->find('first', array(
            'fields' => array('id', 'name', 'start_date', 'end_date'),
            'order' => 'start_date DESC'
                )
        );
        if ($lastDate['AlbaniaChamber']['start_date'] >= $date) {
            $checkName = $this->AlbaniaChamber->find('first', array(
                'fields' => array('id', 'name', 'start_date', 'end_date'),
                'conditions' => array(
                    'AlbaniaChamber.start_date <=' => $date,
                    'AlbaniaChamber.end_date >=' => $date,
                ),
                'order' => 'start_date DESC'
                    )
            );
        } elseif (CakeTime::format($lastDate['AlbaniaChamber']['start_date'] . ' + 4 years', '%Y-%m-%d') < $date) {
            $data[]['logs'] = array(
                'id' => 'Chamber_' . $date . '_' . time() . '_' . rand(0, 999),
                'label' => 'AlbaniaChamber not found: ' . $date,
                'status' => 'finished',
//                        'params' => $t
            );
            App::import('Model', 'QueleToSend');
            $this->QueleToSend = new QueleToSend();
            $this->QueleToSend->putDataDB($data, 'Albanian', false);
        } else {
            $checkName = $lastDate;
        }

        if ($checkName) {
            return $checkName;
        } else {
            $newId = 'chamber_' . $date;
            $data[]['organizations']['id'] = $newId;
//            $data[]['logs'] = array(
//                'id' => 'people_' . $newId . '_voteId_' . $this->voteId . '_' . time() . '_' . rand(0, 999),
//                'label' => 'not found: ' . $newId,
//                'status' => 'finished',
////                        'params' => $t
//            );
//            App::import('Model', 'QueleToSend');
//            $this->QueleToSend = new QueleToSend();
//            $this->QueleToSend->putDataDB($data, 'Albanian', false);
            return $newId;
        }
    }

    public function checkAlbaniaPeopleExist($name) {
        $name = trim(preg_replace('/\:/', '', $name));
        $searchs = explode(' ', $name);
//        pr($searchs);
        foreach ($searchs as $na) {
            $cond[] = array('AlbaniaMpsDetail.name LIKE' => '%' . $na . '%');
        }
        $conditions[] = array('AND' => $cond);

        App::import('Model', 'AlbaniaMpsDetail');
        $this->AlbaniaMpsDetail = new AlbaniaMpsDetail();
        $checkName = $this->AlbaniaMpsDetail->field(
                'AlbaniaMpsDetail.name', $conditions
        );
        if ($checkName) {
            return 'mp_' . $this->toCamelCase($checkName);
        } else {
            $newId = 'mp_' . $this->toCamelCase($name);
            $data[]['people']['id'] = $newId;
//            $data[]['logs'] = array(
//                'id' => 'people_' . $newId . '_voteId_' . $this->voteId . '_' . time() . '_' . rand(0, 999),
//                'label' => 'not found: ' . $newId,
//                'status' => 'finished',
////                        'params' => $t
//            );
//            App::import('Model', 'QueleToSend');
//            $this->QueleToSend = new QueleToSend();
//            $this->QueleToSend->putDataDB($data, 'Serbian', false);
            return $newId;
        }
    }

    public function checkPeopleExist($name) {
        $name = trim(preg_replace('/\:/', '', $name));
        $searchs = explode(' ', $name);
//        pr($searchs);
        foreach ($searchs as $na) {
            $cond[] = array('SerbianMpsDetail.name LIKE' => '%' . $na . '%');
        }
        $conditions[] = array('AND' => $cond);

        App::import('Model', 'SerbianMpsDetail');
        $this->SerbianMpsDetail = new SerbianMpsDetail();
        $checkName = $this->SerbianMpsDetail->field(
                'SerbianMpsDetail.name', $conditions
        );
        if ($checkName) {
            return 'mp_' . $this->toCamelCase($checkName);
        } else {
            $newId = 'mp_' . $this->toCamelCase($name);
            $data[]['people']['id'] = $newId;
//            $data[]['logs'] = array(
//                'id' => 'people_' . $newId . '_voteId_' . $this->voteId . '_' . time() . '_' . rand(0, 999),
//                'label' => 'not found: ' . $newId,
//                'status' => 'finished',
////                        'params' => $t
//            );
            App::import('Model', 'QueleToSend');
            $this->QueleToSend = new QueleToSend();
            $this->QueleToSend->putDataDB($data, 'Serbian', false);
            return $newId;
        }
    }

    public function checkKosovoPeopleExist($name, $menuId) {
        $name = trim(preg_replace('/\:/', '', $name));
        $searchs = explode(' ', $name);
//        pr($name);
        foreach ($searchs as $na) {
            $cond[] = array('KosovoMpsIndex.name LIKE' => '%' . $na . '%');
        }
        $conditions[] = array('AND' => $cond);
        $conditions[] = array('KosovoMpsIndex.kosovo_mps_menu_id' => $menuId);

        App::import('Model', 'KosovoMpsIndex');
        $this->KosovoMpsIndex = new KosovoMpsIndex();
        $checkName = $this->KosovoMpsIndex->field(
                'KosovoMpsIndex.name', $conditions
        );
        if ($checkName) {
            return 'mp_' . $menuId . '_' . $this->toCamelCase($checkName);
        } else {
            $newId = 'mp_' . $menuId . '_' . $this->toCamelCase($name);
            $data[]['people']['id'] = $newId;
//            $data[]['logs'] = array(
//                'id' => 'people_' . $newId . '_voteId_' . $this->voteId . '_' . time() . '_' . rand(0, 999),
//                'label' => 'not found: ' . $newId,
//                'status' => 'finished',
////                        'params' => $t
//            );
            App::import('Model', 'QueleToSend');
            $this->QueleToSend = new QueleToSend();
            $this->QueleToSend->putDataDB($data, 'Kosovan', false);
            pr($data);
            return $newId;
        }
    }

//        public function checkKosovoPeopleExist($name, $menuId) {
//        $name = trim(preg_replace('/\:/', '', $name));
//        $searchs = explode(' ', $name);
////        pr($searchs);
//        foreach ($searchs as $na) {
//            $cond[] = array('KosovoMpsDetail.name LIKE' => '%' . $na . '%');
//        }
//        $conditions[] = array('AND' => $cond);
//        $conditions[] = array('KosovoMpsDetail.kosovo_mps_index_id' => $menuId);
//
//        App::import('Model', 'KosovoMpsDetail');
//        $this->KosovoMpsDetail = new KosovoMpsDetail();
//        $checkName = $this->KosovoMpsDetail->field(
//                'KosovoMpsDetail.name', $conditions
//        );
//        if ($checkName) {
//            return 'mp_' . $menuId . '_' . $this->toCamelCase($checkName);
//        } else {
//            $newId = 'mp_' . $menuId . '_' . $this->toCamelCase($name);
//            $data[]['people']['id'] = $newId;
////            $data[]['logs'] = array(
////                'id' => 'people_' . $newId . '_voteId_' . $this->voteId . '_' . time() . '_' . rand(0, 999),
////                'label' => 'not found: ' . $newId,
////                'status' => 'finished',
//////                        'params' => $t
////            );
//            App::import('Model', 'QueleToSend');
//            $this->QueleToSend = new QueleToSend();
//            $this->QueleToSend->putDataDB($data, 'Kosovan', false);
//            return $newId;
//        }
//    }

    public function checkPartyeExist($shortcut) {
        $shortcut = trim(preg_replace('/\s+|\(|\)/', '', $shortcut));
        $searchs = explode('-', $shortcut);

//        foreach ($searchs as $na) {
//            $cond[] = array('SerbianParty.shortcut LIKE' => '%' . $na . '%');
//        }
//        $conditions[] = array('AND' => $cond);
        $conditions = array('SerbianParty.shortcut LIKE' => '%' . $searchs[0] . '%');

        App::import('Model', 'SerbianParty');
        $this->SerbianParty = new SerbianParty();
        $checkUid = $this->SerbianParty->field(
                'SerbianParty.uid', $conditions
        );
        if ($checkUid) {
            return 'party_' . $checkUid;
        } else {
            $newId = 'party_' . $searchs[0];

            $data[]['organizations'] = array(
                'id' => $newId,
                'name' => $searchs[0],
                'classification' => 'party',
                'other_names' => array(
                    array(
                        'name' => $searchs[0],
                        'note' => 'shortcut'
                    )
                ),
                'sources' => array(
                    array(
                        'url' => $this->pdfUrl
                    )
                )
            );
//            $data[]['logs'] = array(
//                'id' => 'party_' . $newId . '_voteId_' . $this->voteId . '_' . time() . '_' . rand(0, 999),
//                'label' => 'not found: ' . $newId,
//                'status' => 'finished',
////                        'params' => $t
//            );

            App::import('Model', 'QueleToSend');
            $this->QueleToSend = new QueleToSend();
            $this->QueleToSend->putDataDB($data, 'Serbian');
            return $newId;
        }
        return $shortcut;
    }

    public function extractDateAndTrim($result) {
        $formulaDate = '/\d{2}\.\d{2}\.\d{4}/i';
        $formulaDate1 = '/\d{1}\.\d{2}\.\d{4}/i';
        $formulaDate2 = '/\d{2}\.\d{2}\d{4}/i';
        $date = null;
        if (preg_match($formulaDate, $result, $matches)) {
            $result = trim(reset($matches));
            $date = CakeTime::format($result, '%Y-%m-%d');
        }
        if (is_null($date)) {
            if (preg_match($formulaDate1, $result, $matches)) {
                $result = trim(reset($matches));
                $date = CakeTime::format($result, '%Y-%m-%d');
            }
        }
        if (is_null($date)) {
            if (preg_match($formulaDate2, $result, $matches)) {
                $result = trim(reset($matches));
                $result = preg_replace("/(\d{2})\.(\d{2})(\d{4})/", "$1.$2.$3", $result);
                $date = CakeTime::format($result, '%Y-%m-%d');
            }
        }
        return $date;
    }

    public function kosovoTextRepir($data) {
        $find_table = array(
            '/\&\#263\;/',
            '/\&\#158\;/',
            '/\&\#154\;/',
            '/\&\#305\;/',
            '/\&\#287\;/',
            '/\&\#350\;/',
            '/\&\#273\;/',
            '/\s+/'
        );

        $replace_table = array(
            'ć',
            'ž',
            'š',
            'ı',
            'ğ',
            "Ş",
            'đ',
            ' '
        );
        return preg_replace($find_table, $replace_table, $data);
    }

}
