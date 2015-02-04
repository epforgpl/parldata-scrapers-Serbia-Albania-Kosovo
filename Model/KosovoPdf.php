<?php

App::uses('HttpSocket', 'Network/Http');
App::import('Vendor', 'PdfToHtml', array('file' => 'Gufy' . DS . 'PdfToHtml.php'));

class KosovoPdf extends AppModel {

    public $belongsTo = 'KosovoSpeecheContent';

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
                $content = preg_replace('/<br>\s<br>|<b>\s<\/b>|\s\s+/ism', '', $content);
                $content = preg_replace('/<br><br><br><br>|<br><br><br>|<br><br>/ism', '<br>', $content);
                $content = preg_replace('/<br><b>\s\s+<\/b><br>/i', '', $content);
                $content = trim($content);
                return($content);
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

}
