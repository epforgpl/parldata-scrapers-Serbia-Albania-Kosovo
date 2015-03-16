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
        'ip' => '117.170.40.247',
        'port' => '8123'
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
        $result = preg_replace('/\s+|:|\.|\,|\-|\(|\)|\"/i', " ", $result);
        $result = trim(mb_convert_case(mb_strtolower($result), MB_CASE_TITLE, "UTF-8"));
        $result = preg_replace('/\s/i', "", $result); //to CamelCase
        return $result;
    }

    public function toNameCamelCase($result) {
        $result = preg_replace('/\s+|:|\.|\,|\-|\(|\)|\"/i', " ", $result);
        $result = trim(mb_convert_case(mb_strtolower($result), MB_CASE_TITLE, "UTF-8"));
        return $result;
    }

    public function toLatinCamelCase($result) {
        $result = preg_replace('/\s+|:|\.|\,|\-|\(|\)|\"/i', " ", $result);
        $result = trim(mb_convert_case(mb_strtolower($result), MB_CASE_TITLE, "UTF-8"));
        $result = preg_replace('/\s/i', "", $result); //to CamelCase
        return $this->convertCyrToLat($result);
    }

    function convertCyrToLat($textcyr) {
        $cyr = array('а', 'б', 'в', 'г', 'д', 'e', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у',
            'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ь', 'ю', 'я', 'ђ', 'ћ', 'љ', 'њ', 'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У',
            'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ь', 'Ю', 'Я', 'Ђ', 'Ћ', 'Љ', 'Њ');
        $lat = array('a', 'b', 'v', 'g', 'd', 'e', 'zh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u',
            'f', 'h', 'ts', 'ch', 'sh', 'sht', 'a', 'y', 'yu', 'ya', 'gj', 'c', 'lj', 'nj', 'A', 'B', 'V', 'G', 'D', 'E', 'Zh',
            'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U',
            'F', 'H', 'Ts', 'Ch', 'Sh', 'Sht', 'A', 'Y', 'Yu', 'Ya', 'Dj', 'C', 'Lj', 'Nj');
        return str_replace($cyr, $lat, $textcyr);
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
            $data[0]['organizations']['id'] = $newId;
            $data[0]['organizations']['classification'] = 'chamber';
            $data[0]['organizations']['name'] = 'Kuvendi i Shqipërisë: Legjislatura ' . $name;
//            $data[1]['logs'] = array(
//                'id' => $newId . '_' . time() . '_' . rand(0, 999),
//                'label' => 'not found: ' . $newId,
//                'status' => 'finished',
////                        'params' => $t
//            );
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

            $data['people'] = $this->combineAlbaniaPeopleName($name);
//            $data[]['logs'] = array(
//                'id' => 'people_' . $newId . '_people_' . $newId . '_' . time() . '_' . rand(0, 999),
//                'label' => 'not found: ' . $newId,
//                'status' => 'finished',
////                        'params' => $t
//            );
            App::import('Model', 'QueleToSend');
            $this->QueleToSend = new QueleToSend();
            $this->QueleToSend->putDataDB(array($data), 'Albanian', false);
            return $data['people']['id'];
        }
    }

    public function combineAlbaniaPeopleName($name) {
        $nname['id'] = 'mp_' . $this->toCamelCase($name);
        $name = preg_replace('/\s\-\s/', '-', $name);
        $name = preg_replace('/\,/', '', $name);
        $name = preg_replace('/\s+/', ' ', $name);
        $tmpName = explode(' ', $name);
        $familyName = $this->toCamelCase(array_shift($tmpName));
        $givenName = null;
        if (count($tmpName) && is_array($tmpName)) {
            foreach ($tmpName as $tn) {
                $givenName .= ' ' . $tn;
            }
        }
        $givenName = trim($givenName);
        $nname['name'] = $givenName . ' ' . $familyName;
        $nname['given_name'] = $givenName;
        $nname['family_name'] = trim($familyName);
        $nname['sort_name'] = $nname['family_name'] . ' ' . $nname['given_name'];
        return $nname;
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
            return 'mp_' . $this->toLatinCamelCase($checkName);
        } else {
            $data['people'] = $this->combineSerbianPeopleName($name);
//            pr($data);
//            $data[]['logs'] = array(
//                'id' => 'people_' . $newId . '_voteId_' . $this->voteId . '_' . time() . '_' . rand(0, 999),
//                'label' => 'not found: ' . $newId,
//                'status' => 'finished',
////                        'params' => $t
//            );
            App::import('Model', 'QueleToSend');
            $this->QueleToSend = new QueleToSend();
            $this->QueleToSend->putDataDB(array($data), 'Serbian', false);
            return $data['people']['id'];
        }
    }

    public function combineSerbianPeopleName($name) {
        $nname['id'] = 'mp_' . $this->toLatinCamelCase($name);
        $nname['name'] = $this->toNameCamelCase($name);
        $name = preg_replace('/\s\-\s/', '-', $name);
        $find_table = array(
            '/ ДОЦ. ДР /',
            '/ Проф. Др /',
            '/ проф. др /',
            '/ ПРОФ. ДР /',
            '/ ДР /',
            '/ Др /',
            '/ др /',
            '/ МР /',
            '/ Мр /',
            '/ мр /'
        );

        $replace_table = array(
            ' dd ',
            ' pd ',
            ' pd ',
            ' pd ',
            ' dr ',
            ' dr ',
            ' dr ',
            ' mgr ',
            ' mgr ',
            ' mgr ',
        );

        $honorific_prefix = array(
            'dd' => 'Доц. Др',
            'pd' => 'Проф. Др',
            'dr' => 'Др',
            'dr' => 'Мр',
        );
        $name = preg_replace($find_table, $replace_table, $name);
        $name = explode(' ', $name);
        foreach ($name as $key => $nn) {
            if (array_key_exists($nn, $honorific_prefix)) {
                $nname['honorific_prefix'] = $honorific_prefix[$nn];
                unset($name[$key]);
            }
        }
        $name = array_values($name);
//        if (count($name) == 2) {
//            $nname['given_name'] = $name[0];
//            //  $nname['last_name'] = $name[1];
//        } else {
        $nname['given_name'] = array_shift($name);
        $nname['given_name'] = trim($this->toNameCamelCase($nname['given_name']));
//        $nname['last_name'] = array_pop($name);
        $formulaCombineName = '/\(.*?(\))/msxi';
        if (count($name) > 0) {
            $nname['family_name'] = null;
            foreach ($name as $nn) {
                $nname['family_name'] .= ' ' . $nn;
            }
            if (preg_match($formulaCombineName, $nname['family_name'], $matches)) {
                $result = reset($matches);
                $newname = $result;
            }
            $tmp_family_name = $nname['family_name'];
            $nname['family_name'] = trim($this->toNameCamelCase($nname['family_name']));
        }
//        }
        if (isset($nname['family_name']) && (is_null($nname['family_name']) || $nname['family_name'] == '' || empty($nname['family_name']))) {
            unset($nname['family_name']);
        }

        if (isset($newname) && !empty($newname)) {
            $tmp_family_name = isset($tmp_family_name) ? preg_replace($formulaCombineName, '', trim($tmp_family_name)) : '';
            $nname['identifiers'][] = array(
                'scheme' => 'latin_name',
                'identifier' => $nname['given_name'] . (isset($tmp_family_name) ? ' ' . trim($this->toNameCamelCase($tmp_family_name)) : '')
            );
            $tempname = preg_replace('/\(|\)|\s\s+/', '', trim($newname));
            $tempname = explode(' ', $tempname);
            $nname['given_name'] = array_shift($tempname);
            $nname['given_name'] = trim($this->toNameCamelCase($nname['given_name']));
            if (count($tempname) > 0) {
                $nname['family_name'] = null;
                foreach ($tempname as $nn) {
                    $nname['family_name'] .= ' ' . $nn;
                }
                $nname['family_name'] = trim($this->toNameCamelCase($nname['family_name']));
            }
        }
        $nname['sort_name'] = (isset($nname['family_name']) ? $nname['family_name'] : '') . ' ' . $nname['given_name'];
        return $nname;
    }

    public function checkKosovoPeopleExist($name, $menuId) {
        $name = trim(preg_replace('/\:/', '', $name));
        $searchs = explode(' ', $name);
//        pr($name);
        foreach ($searchs as $na) {
            $cond[] = array('KosovoMpsIndex.name LIKE' => '%' . $na . '%');
        }
        $conditions[] = array('AND' => $cond);
//        $conditions[] = array('KosovoMpsIndex.kosovo_mps_menu_id' => $menuId);

        App::import('Model', 'KosovoMpsIndex');
        $this->KosovoMpsIndex = new KosovoMpsIndex();
        $checkName = $this->KosovoMpsIndex->field(
                'KosovoMpsIndex.name', $conditions
        );
        if ($checkName) {
            return 'mp_' . $this->toCamelCase($checkName);
        } else {
            $data['people'] = $this->combineKosovoPeopleName($name);
//            $data[]['logs'] = array(
//                'id' => 'people_' . $newId . '_voteId_' . $this->voteId . '_' . time() . '_' . rand(0, 999),
//                'label' => 'not found: ' . $newId,
//                'status' => 'finished',
////                        'params' => $t
//            );
            App::import('Model', 'QueleToSend');
            $this->QueleToSend = new QueleToSend();
            $this->QueleToSend->putDataDB(array($data), 'Kosovan', false);
            return $data['people']['id'];
        }
    }

    public function combineKosovoPeopleName($name) {
        $nname['id'] = 'mp_' . $this->toCamelCase($name);
        $nname['name'] = $name;
        $name = preg_replace('/\s\-\s/', '-', $name);
        $name = explode(' ', $name);
        $name = array_values($name);
        $nname['given_name'] = array_shift($name);
        if (count($name) > 0) {
            $nname['family_name'] = null;
            foreach ($name as $nn) {
                $nname['family_name'] .= ' ' . $nn;
            }
            $nname['family_name'] = trim($nname['family_name']);
        }
        if (isset($nname['family_name']) && (is_null($nname['family_name']) || $nname['family_name'] == '' || empty($nname['family_name']))) {
            unset($nname['family_name']);
        }
        $nname['sort_name'] = (isset($nname['family_name']) ? $nname['family_name'] : '') . ' ' . $nname['given_name'];
        return $nname;
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
            '/\&\#262\;/',
            '/\&\#263\;/',
            '/\&\#158\;/',
            '/\&\#154\;/',
            '/\&\#305\;/',
            '/\&\#287\;/',
            '/\&\#350\;/',
            '/\&\#351\;/',
            '/\&\#273\;/',
            '/\s+/'
        );

        $replace_table = array(
            'Ć',
            'ć',
            'ž',
            'š',
            'ı',
            'ğ',
            "Ş",
            "ş",
            'đ',
            ' '
        );
        return preg_replace($find_table, $replace_table, $data);
    }

}
