<?php

App::uses('HttpSocket', 'Network/Http');
App::import('Vendor', 'PdfToHtml', array('file' => 'Gufy' . DS . 'PdfToHtml.php'));

class KosovoPdf extends AppModel {

    public $belongsTo = 'KosovoSpeecheContent';
    public $menuId;

    public function getContentPdfFromId($id) {
        $fileFolder = WWW_ROOT . 'files' . DS . 'kosovo' . DS;
        $this->id = $id;
        $this->recursive = -1;
        $pdf = $this->read();
        // pr($pdf);
        if ($pdf) {
            $url = ($this->getKosovoHost . $pdf['KosovoPdf']['pdf_url']);
            //  pr($url);
            $httpSocket = new HttpSocket();
            $filePdfName = $fileFolder . $pdf['KosovoPdf']['id'] . '.pdf';
            $f = fopen($filePdfName, 'w');
            chmod($filePdfName, 0666);
            $httpSocket->get($url);
            if ($httpSocket->response->code == 200) {
                $httpSocket->setContentResource($f);
                $httpSocket->get($url);
            }
            fclose($f);
            sleep(5);
            if (file_exists($filePdfName) && $httpSocket->response->code == 200 && filesize($filePdfName) > 0) {
                $content = $this->combinePdfToHtml($filePdfName, $id);
                if ($content) {
                    $pdf_md5 = md5($content);
                    if ($pdf['KosovoPdf']['pdf_md5'] != $pdf_md5) {
                        $this->set(array(
                            'pdf_md5' => $pdf_md5,
                            'content_sr' => $content,
                            'status' => 1
                        ));
                        if ($this->save()) {
                            return true;
                        }
                    }
                }
            } else {
                $this->set(array(
                    'status' => 1
                ));
                if ($this->save()) {
                    return false;
                }
            }
        }
        return false;
    }

    public function combinePdfToHtml($filePath, $id) {
        $fileFolder = WWW_ROOT . 'files' . DS . 'kosovo' . DS;
        if (file_exists($filePath)) {
            $pdf = new \Gufy\PdfToHtml;
            $pdf->open($filePath);
            $pdf->generateOptions('singlePage');
            $pdf->setOutputDirectory($fileFolder);
            if ($pdf->generate()) {
                $content = (file_get_contents($fileFolder . $id . '.html'));
                $content = preg_replace('/&nbsp;/i', ' ', $content);
                $content = preg_replace('/|<hr>|<\!DOCTYPE.*?(>)|<BODY.*?(>)|<\/BODY>|<\/HTML>/i', '', $content);
                $content = preg_replace('/<HTML>.*?(<\/HEAD>)/ism', '', $content);
                $content = preg_replace('/\s<br>\\n(?=[Ј,с,\(])/ism', ' ', $content);
                $content = preg_replace('/\\n<A.*?(<\/a>)/ism', '', $content);
                $content = preg_replace('/(\xC2\xA0|&nbsp;)/', ' ', $content);
//        $content = preg_replace('/<br>\s<br>|<b>\s<\/b>/ism', '', $content);
                $content = preg_replace('/<br><br><br><br>|<br><br><br>|<br><br>/ism', '<br>', $content);
                $content = preg_replace('/<br><b>\s\s+<\/b><br>/i', '', $content);
                $content = preg_replace('/\s\s+/i', ' ', $content);
                $content = trim($content);
                return($content);
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function combineToApiArray($content) {
        $this->formulaSpeaker = '/[A-ZÇË][^a-zëç0-9]{3,}\s[A-ZÇË]{3,}[^a-zëç0-9](\–|\-|\:)|(RAMË\sBUJA|KRYETARI|KRYESUESI)(\–|\-|\:)/';
        $formulaSplit = '/:split:.*?(:split:)/msxi';

        $txt = trim($content['KosovoPdf']['content_sr']);
        $combine = ':split: ' . $txt . ' :split: a';
        $combine = preg_replace($this->formulaSpeaker, " :split::split: $0", $combine);
        $date = $this->toApiDate($content['KosovoSpeecheContent']['post_date']);

        App::import('Model', 'KosovoMpsMenu');
        $this->KosovoMpsMenu = new KosovoMpsMenu();
        $organization = $this->KosovoMpsMenu->find('first', array(
            'fields' => array('id', 'start_date'),
            'conditions' => array(
                'start_date <=' => $content['KosovoSpeecheContent']['post_date'],
            ),
            'order' => 'start_date DESC'
        ));
        $this->menuId = $organization['KosovoMpsMenu']['id'];
        $organization_id = $organization['KosovoMpsMenu']['start_date'];

        $results = $data = array();
        if (preg_match_all($formulaSplit, $combine, $matches)) {
            $results = reset($matches);
        }

        if (count($results) > 0) {
            foreach ($results as $key => $result) {
                $result = preg_replace('/:split:/i', "", $result);
                $result = preg_replace('/\s+/i', " ", $result);

                $text = $this->combineSpeche($result);

                $speaker = $this->getSpeaker($result);
                if ($speaker) {

                    $data[$key]['speeches']['type'] = 'speech';
                    $data[$key]['speeches']['text'] = trim(($text['speche']));
                    $data[$key]['speeches']['date'] = $date;
                    $data[$key]['speeches']['position'] = $key;
                    $data[$key]['speeches']['event_id'] = 'event_' . $content['KosovoSpeecheContent']['kosovo_speeche_index_id'];
                    $data[$key]['speeches']['id'] = $content['KosovoSpeecheContent']['kosovo_speeche_index_id'] . '-' . $key;
                    $data[$key]['speeches']['attribution_text'] = $speaker['attribution_text'];
                    $data[$key]['speeches']['creator_id'] = $speaker['attribution_text'];
                    $data[$key]['speeches']['role'] = $speaker['role'];
                    $data[$key]['speeches']['title'] = $content['KosovoSpeecheContent']['title'];
                    if (!empty($content['KosovoPdf']['pdf_url'])) {
                        $data[$key]['speeches']['sources'][] = array(
                            'url' => $this->getKosovoHost . trim($content['KosovoPdf']['pdf_url']),
                        );
                    }
                } else {
                    $data[$key]['speeches']['id'] = $content['KosovoSpeecheContent']['kosovo_speeche_index_id'] . '-' . $key;
                    $data[$key]['speeches']['type'] = 'narrative';
                    $data[$key]['speeches']['text'] = trim(($text['speche']));
                    $data[$key]['speeches']['date'] = $date;
                    $data[$key]['speeches']['position'] = $key;
                    $data[$key]['speeches']['event_id'] = 'event_' . $content['KosovoSpeecheContent']['kosovo_speeche_index_id'];
                    if (!empty($content['KosovoPdf']['pdf_url'])) {
                        $data[$key]['speeches']['sources'][] = array(
                            'url' => $this->getKosovoHost . trim($content['KosovoPdf']['pdf_url']),
                        );
                    }
                }
            }
        }

        if (preg_match_all($this->formulaSpeaker, $txt, $matches)) {
            $result = (reset($matches));
        }
        return $data;
    }

    public function combineSpeche($data) {
        $find_table = array(
            $this->formulaSpeaker,
            '/<span([^>]*)>/',
            "/<\/span>/",
            "/<p([^>]*)>/",
            "/<\/p>/",
        );

        $replace_table = array(
            '',
            '',
            '',
            '',
            "<br/>",
        );
        $data = preg_replace($find_table, $replace_table, $data);
        return array('speche' => $data);
    }

    public function getSpeaker($data) {

        if (preg_match($this->formulaSpeaker, $data, $matches)) {
            $result = reset($matches);
            if ($result) {
                $name = $this->clearPdfMps($result);

                $data = array();
                $data['role'] = 'speaker';
                if (!empty($name)) {
                    $data['attribution_text'] = $this->checkKosovoPeopleExist($name, $this->menuId);
                } else {
                    if ($result == 'KRYESUESI:') {
                        $data['role'] = 'chair';
                    }
                    $data['attribution_text'] = $this->checkKosovoPeopleExist($result, $this->menuId);
                }
                return($data);
            }
        }
        return false;
    }

    public function clearPdfMps($result) {
        $clearArray = array(
            '/MINISTRI/',
            '/MINSITRI/',
            '/MIISTRI/',
            '/MINISTRJA/',
            '/MINISTËR/',
            '/KRYEMINISTRI/',
            '/KRYEMNISTRI/',
            '/KRYEMINSTRI/',
            '/DEPUTETJA/',
            '/DEPUTETI/',
            '/KUVENDIT/',
            '/KRYESUESI/',
            '/ËNKRYETARI/',
            '/ËVENDSKRYE/',
            '/\sI\s/',
            '/ËS|\,|\–|\-|\:/'
        );
        $result = preg_replace($clearArray, '', $result);
        $result = preg_replace('/\s\s+/i', ' ', $result);
        $result = trim($result);
        return $result;
    }

}
