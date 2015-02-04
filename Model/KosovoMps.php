<?php

App::uses('HttpSocket', 'Network/Http');
App::uses('CakeTime', 'Utility');

class KosovoMps extends AppModel {

    public $useTable = false;

    public function getMpsContact($data) {
//        return $data;
        $formulaMain = '/(<h1>Arkivi|<h1>Lista).*(?=related-links)/msxi';

        $HttpSocket = new HttpSocket();
        $page = $HttpSocket->get($this->getKosovoHost . $data['KosovoMpsIndex']['url']);
        $ndata = null;
//        pr($data['KosovoMpsIndex']['url']);
        if (preg_match($formulaMain, utf8_encode($page->body), $matches)) {
            $result = reset($matches);
//            pr($result);
            if ($result) {
                $ndata = $this->getPersonData($result, $data['KosovoMpsIndex']['id']);
            }
        }
        return $ndata;
    }

    public function getPersonData($result, $id) {
        $formulaName = '/<h2>.*?(<\/h2>)/msxi';
        $formulaImage = '/src=".*?(")/i';
        $formulaImageReplace = '/src=|"/';
        $formulaPhone = '/<h3>Telefoni.*?(<\/ul>)/i';
        $formulaPhoneReplace = '/Telefoni/';

        $data['KosovoMpsDetail']['id'] = $data['KosovoMpsDetail']['kosovo_mps_index_id'] = $id;
        $data['KosovoMpsDetail']['md5'] = md5(trim(strip_tags($result)));
        $data['KosovoMpsDetail']['name'] = $this->extractTrimStrip($result, $formulaName);
        $data['KosovoMpsDetail']['image'] = $this->extractAndReplace($result, $formulaImage, $formulaImageReplace);
        $data['KosovoMpsDetail']['phone'] = $this->extractAndReplace($result, $formulaPhone, $formulaPhoneReplace);


//KosovoParliamentaryGroup
        $formulaParliamentaryGroup = '/Grupi\sparlamentar<\/h3>.*?(<\/ul>)/msxi';
        $formulaParliamentaryGroupReplace = '/Grupi\sparlamentar|<\/h3>/';
        $getGroup = $this->extractListsAndReplace($result, $formulaParliamentaryGroup, $formulaParliamentaryGroupReplace, 'KosovoParliamentaryGroup');
        $data['KosovoParliamentaryGroup'] = Set::extract('/id', $getGroup);
//KosovoParty
        $formulaParty = '/<h3>Partia.*?(<\/ul>)/msxi';
        $formulaPartyReplace = '/Partia|<\/h3>|\(.*\)/';
        $getParty = $this->extractListsAndReplace($result, $formulaParty, $formulaPartyReplace, 'KosovoParty');
        //  $data['party'] = $getParty;
        $data['KosovoParty'] = Set::extract('/id', $getParty);
//KosovoMpsPersonalData
        $formulaPersonalData = '/<h3>Të\sdhëna\spersonale.*?(<\/ul>)/msxi';
        $formulaPersonalDataReplace = '/Të\sdhëna\spersonale|<\/h3>|\(.*\)/';
        $getPersonalData = $this->extractListsAndReplace($result, $formulaPersonalData, $formulaPersonalDataReplace, 'KosovoMpsPersonalData', $id);
        $data['KosovoMpsPersonalData'] = $getPersonalData;
//KosovoMpsEducation
        $formulaEducation = '/<h3>Arsimimi.*?(<\/ul>)/msxi';
        $formulaEducationReplace = '/Arsimimi|<\/h3>|\(.*\)/';
        $getEducation = $this->extractListsAndReplace($result, $formulaEducation, $formulaEducationReplace, 'KosovoMpsEducation', $id);
        $data['KosovoMpsEducation'] = $getEducation;
//KosovoMpsActivity
        $formulaActivity = '/<h3>Aktivitete.*?(<\/ul>)/msxi';
        $formulaActivityReplace = '/Aktivitete|<\/h3>|\(.*\)/';
        $getActivity = $this->extractListsAndReplace($result, $formulaActivity, $formulaActivityReplace, 'KosovoMpsActivity', $id);
        $data['KosovoMpsActivity'] = $getActivity;
//KosovoMpsLanguage
        $formulaLanguage = '/<h3>Gjuhë\stjetër.*?(<\/ul>)/msxi';
        $formulaLanguageReplace = '/Gjuhë\stjetër|<\/h3>|\(.*\)/';
        $getLanguage = $this->extractListsAndReplace($result, $formulaLanguage, $formulaLanguageReplace, 'KosovoMpsLanguage', $id);
        $data['KosovoMpsLanguage'] = $getLanguage;
//KosovoMpsAddress
        $formulaAddress = '/<h3>Adresa.*?(<\/ul>)/msxi';
        $formulaAddressReplace = '/Adresa|<\/h3>|\(.*\)/';
        $getAddress = $this->extractListsAndReplace($result, $formulaAddress, $formulaAddressReplace, 'KosovoMpsAddress', $id);
        $data['KosovoMpsAddress'] = $getAddress;

        return $data;
    }

    public function extractListsAndReplace($data, $formula, $replace, $model, $id = null) {
        // pr($data);
        $formulaLi = '/<li.*?(<\/li>)/msxi';
        $newData = array();
        if (preg_match($formula, $data, $matches)) {
            $result = trim(reset($matches));
            //   pr($result);
            if (preg_match_all($formulaLi, $result, $matches)) {
                $results = reset($matches);
                foreach ($results as $r) {
                    $newData[] = $this->combineLists($r, $replace, $model, $id);
                }
            } else {
                $newData[] = $this->combineLists($result, $replace, $model, $id);
            }

            return $newData;
        }
    }

    public function combineLists($result, $replace, $model, $id) {

        $formulaHref = '/href=\".*?(\")/i';
        $formulaHrefReplace = '/href=|"/';
        $formulaUrlUids = '/\,\d+(?=")/i';
        $formulaUrlUidsReplace = '/html|\,/i';
        $formulaShortcut = '/\(.*?(\))/i';
        $formulaShortcutReplace = '/\(|\)/i';
        $formulaShortcutRemove = '/\(.*\)/i';

        //  pr($result);
        $uid = $this->extractAndReplace($result, $formulaUrlUids, $formulaUrlUidsReplace);
//        pr($uid);
        $name = trim(strip_tags(preg_replace($replace, '', $result)));
        $name = trim(preg_replace($formulaShortcutRemove, '', $name));

        App::import('Model', $model);
        $this->$model = new $model();
        if (!is_null($id)) {
            $getId = $this->$model->getIdFromUidAndName($id, $name);
        } else {
            $getId = $this->$model->getIdFromUidAndName($uid, $name);
        }
        if ($getId) {
            $this->$model->id = $getId;
        }
        $url = $this->extractAndReplace($result, $formulaHref, $formulaHrefReplace);
        $shortcut = $this->extractAndReplace($result, $formulaShortcut, $formulaShortcutReplace);

        $newData['id'] = $getId;
        if (!is_null($id)) {
            $newData['kosovo_mps_detail_id'] = $id;
        }
        if ($uid) {
            $newData['uid'] = $uid;
        }
        $newData['name'] = $name;
        if ($url) {
            $newData['url'] = $url;
            if ($getId) {
                $this->$model->saveField('url', $url);
            }
        }
        if ($shortcut) {
            $newData['shortcut'] = $shortcut;
            if ($model == 'KosovoParty' && $getId) {
                $this->$model->saveField('shortcut', $shortcut);
            }
        }

        return $newData;
    }

}
