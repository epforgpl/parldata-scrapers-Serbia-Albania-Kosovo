<?php

App::uses('HttpSocket', 'Network/Http');
App::import('Vendor', 'PdfToHtml', array('file' => 'Gufy' . DS . 'PdfToHtml.php'));

class AlbaniaPdf extends AppModel {

    public function getPdfPage($link) {
        return $link;
    }

    public function getContentPdfFromId($id) {
        $url = ('http://www.parlament.al/previewdoc.php?file_id=' . $id);
        $fileFolder = WWW_ROOT . 'files' . DS . 'albania' . DS;
        $path = $fileFolder . $id . '.pdf';

        $headers = $this->getHeaders($url);
        pr($headers);
        pr(get_headers($url, true));
        pr($this->remotefileSize($url));
        if ($headers['http_code'] === 200 and $headers['download_content_length'] < 1024 * 1024) {
            if ($this->download($url, $path)) {

                echo 'Download complete!';
                return true;
                // $content = $this->combinePdfToHtml($path, $id);
            }
        }
        //  return false;
    }

    private function getHeaders($url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
        curl_exec($ch);
        $headers = curl_getinfo($ch);
        curl_close($ch);

        return $headers;
    }

    private function download($url, $path) {
        $fp = fopen($path, 'w+');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);

        if (filesize($path) > 0)
            return true;
    }

    function remotefileSize($url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)');
        curl_setopt($ch, CURLOPT_NOBODY, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
        curl_exec($ch);
        $filesize = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
        curl_close($ch);
        if ($filesize)
            return $filesize;
    }

    public function combinePdfToHtml($filePath, $id) {

        $fileFolder = WWW_ROOT . 'files' . DS . 'albania' . DS;
        if (file_exists($filePath)) {
            $pdf = new \Gufy\PdfToHtml;
            $pdf->open($filePath);
            $pdf->generateOptions('singlePage');
            $pdf->setOutputDirectory($fileFolder);
            if ($pdf->generate()) {
                $content = (file_get_contents($fileFolder . $id . '.html'));
                pr($content);
            }
        }
    }

    public function getContentPdfFromIda($id) {
        $fileFolder = WWW_ROOT . 'files' . DS . 'albania' . DS;
//        $this->id = $id;
//        $this->recursive = -1;
//        $pdf = $this->read();
//        // pr($pdf);
//        if ($pdf) {
//        $url = ($this->getKosovoHost . $pdf['KosovoPdf']['pdf_url']);
        $url = ('http://www.parlament.al/previewdoc.php?file_id=' . $id);
        //  pr($url);
        $httpSocket = new HttpSocket();
        $filePdfName = $fileFolder . $id . '.pdf';
        $f = fopen($filePdfName, 'w');
        chmod($filePdfName, 0666);
//        $httpSocket->get($url);
//        if ($httpSocket->response->code == 200) {
        $httpSocket->setContentResource($f);
        $httpSocket->get($url);
//        }
        fclose($f);
        sleep(5);
        if (file_exists($filePdfName) && $httpSocket->response->code == 200 && filesize($filePdfName) > 0) {
//            $content = $this->combinePdfToHtml($filePdfName, $id);
//            if ($content) {
//                $pdf_md5 = md5($content);
//                if ($pdf['KosovoPdf']['pdf_md5'] != $pdf_md5) {
//                    $this->set(array(
//                        'pdf_md5' => $pdf_md5,
//                        'content_sr' => $content,
//                        'status' => 1
//                    ));
//                    if ($this->save()) {
//                        return true;
//                    }
//                }
//            }
//            } else {
//                $this->set(array(
//                    'status' => 1
//                ));
//                if ($this->save()) {
//                    return false;
//                }
//            }
        }
        return false;
    }

}
