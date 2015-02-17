<?php

App::uses('HttpSocket', 'Network/Http');
App::import('Vendor', 'PdfToHtml', array('file' => 'Gufy' . DS . 'PdfToHtml.php'));

class AlbaniaPdf extends AppModel {

    public function getPdfPage($link) {
        return $link;
    }

    public function getPdfFromIs($id) {
        $link = ('http://www.parlament.al/previewdoc.php?file_id=' . $id);
        $fileFolder = WWW_ROOT . 'files' . DS . 'albania' . DS;
//        $this->id = $l['SerbianPdf']['id'];
//
        $HttpSocket = new HttpSocket();
        if ($this->enableProxy) {
            $HttpSocket->configProxy($this->proxyServer['ip'], $this->proxyServer['port']);
        }
        $filePdfName = $fileFolder . $id . '.pdf';
        $f = fopen($filePdfName, 'w');
        chmod($filePdfName, 0666);
        $HttpSocket->setContentResource($f);
        $HttpSocket->get($link);
        if ($HttpSocket->response->code != 200) {
            $HttpSocket->get($link);
        }
        fclose($f);
        sleep(15);
//  return $link;
        if (file_exists($filePdfName)) {
            return $filePdfName;
        } else {
            return false;
        }
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

    public function combinePdfToHtml($filePath, $uid, $id) {
        $fileFolder = WWW_ROOT . 'files' . DS . 'albania' . DS;
        if (file_exists($filePath)) {
            $pdf = new \Gufy\PdfToHtml;
            $pdf->open($filePath);
            $pdf->generateOptions('singlePage');
            $pdf->setOutputDirectory($fileFolder);
            if ($pdf->generate()) {
                $content = (file_get_contents($fileFolder . $uid . '.html'));
                $content = preg_replace('/&nbsp;/i', ' ', $content);
                $content = preg_replace('/|<hr>|<\!DOCTYPE.*?(>)|<BODY.*?(>)|<\/BODY>|<\/HTML>/i', '', $content);
                $content = preg_replace('/<HTML>.*?(<\/HEAD>)/ism', '', $content);
                $content = preg_replace('/\s<br>\\n(?=[Ј,с,\(])/ism', ' ', $content);
                $content = preg_replace('/\\n<A.*?(<\/a>)/ism', '', $content);
                $content = preg_replace('/\s<br><b>\s<\/b><br>|<br>/i', '<br />', $content);
                $content = trim($content);
                //  pr($content);
                if ($content) {
                    $formulaSplit = '/:split:.*?(:split:)/msxi';

                    $combine = ':split: ' . $content . ' :split: a';
                    $combine = preg_replace('/\d+\./', " :split::split: $0", $combine);
                    if (preg_match_all($formulaSplit, $combine, $matches)) {
                        $results = reset($matches);
                    }

                    if (count($results) > 0) {
                        foreach ($results as $key => $result) {
                            $result = preg_replace('/:split:/i', "", $result);
                            $toMd5 = $result = preg_replace('/\s+/i', " ", $result);
                            $result = explode('<br />', $result);
                            $name = $paternity = $surname = $party = null;
                            if (preg_match('/\d+\./', $result[0])) {
                                $i = 0;
                                $data['albania_chamber_id'] = $id;
                                $data['md5'] = md5($toMd5);
                                $data['status'] = 0;
                                $name = trim(strip_tags(preg_replace('/\d+\./', "", $result[$i])));
                                if (empty($name)) {
                                    $i++;
                                    $name = trim(strip_tags(preg_replace('/\d+\./', "", $result[$i])));
                                }

                                if (preg_match('/\s/', $name)) {
                                    $tmps = explode(' ', $name);
//                                    pr($tmps);
                                    $name = trim(strip_tags($tmps[0]));
                                    $paternity = trim(strip_tags($tmps[1]));
                                }

                                $data['name'] = $name;
                                if (is_null($paternity)) {
                                    $i++;
                                    $paternity = trim(strip_tags($result[$i]));
                                }

                                $data['paternity'] = $paternity;
                                $i++;
                                $surname = trim(strip_tags(preg_replace('/\d+|\(.*?(\))/', "", $result[$i])));
                                if (preg_match('/\s/', $surname)) {
                                    $surname = preg_replace('/\s+/', " ", $surname);
                                    $tmps = explode(' ', $surname);
//                                    pr($tmps);
                                    $surname = trim(strip_tags($tmps[0]));
                                    $party = trim(strip_tags($tmps[1])) . ' ';
                                }

                                if (!is_null($surname)) {
                                    $data['surname'] = $surname;
                                } else {
                                    $data['surname'] = trim(strip_tags(preg_replace('/\d+/', "", $result[$i])));
                                }
                                $i++;

                                $tmp = trim(strip_tags($result[$i]));
                                if (!empty($tmp)) {
                                    $party .= $tmp;
                                    $i++;
                                    $tmp = trim(strip_tags($result[$i]));
                                    if (!empty($tmp)) {
                                        $party .= ' ' . $tmp;
                                        $i++;
                                        if (isset($result[$i])) {
                                            $tmp = trim(strip_tags($result[$i]));
                                            if (!empty($tmp) && strpos($tmp, 'Qarku') === false) {
                                                $party .= ' ' . $tmp;
                                                $i++;
                                                if (isset($result[$i])) {
                                                    $tmp = trim(strip_tags($result[$i]));
                                                    if (!empty($tmp)) {
                                                        $party .= ' ' . $tmp;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                                if ($party == 'Partia') {
                                    $tmp = trim(strip_tags($result[10]));
                                    $party .= ' ' . $tmp;
                                }
                                $data['party'] = $party;
                                $all[] = $data;
                            }
                        }
                    }

                    return $all;
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

}
