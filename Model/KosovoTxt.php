<?php

App::uses('HttpSocket', 'Network/Http');

class KosovoTxt extends AppModel {

    public $belongsTo = 'KosovoSpeecheContent';

    public function getContentTxtFromId($id) {
        $this->recursive = -1;
        $txt = $this->findById($id);

        $url = $this->getKosovoHost . $txt['KosovoTxt']['txt_url'];

        if (@ $content = file_get_contents($url)) {
            $txt_md5 = md5($content);
            if ($txt['KosovoTxt']['txt_md5'] != $txt_md5) {
                $this->id = $id;
                $this->set(array(
                    'txt_md5' => $txt_md5,
                    'status' => 1,
                    'content_sr' => utf8_encode($content)
                ));
                if ($this->save()) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
    }

}
