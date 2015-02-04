<?php

App::uses('HttpSocket', 'Network/Http');
App::uses('CakeTime', 'Utility');

class Kosovo extends AppModel {

    public $useTable = false;

    public function getFirst() {
        return 'nawet OK';
    }

    public function getMpsIndex($link) {
        $formulaMain = '/<ul\sclass="character-details">.*?(<\/ul>)/msxi';
        $formulaDate = '/\d{2}\.\d{2}\.\d{4}\s\-\s\d{2}\.\d{2}\.\d{4}/i';

        $HttpSocket = new HttpSocket();
        $page = $HttpSocket->get($this->getKosovoHost . $link);
        $data = $ndata = null;

        $date = $this->getConvovationInfo($link);

        if (preg_match_all($formulaMain, utf8_encode($page->body), $matches)) {
            $data = reset($matches);

            if ($data) {
                foreach ($data as $d) {
                    $ndata[] = $this->getAllLink($d, 'mps');
                }
                $data = array();
                if ($ndata) {
                    foreach ($ndata as $nds) {
                        foreach ($nds as $nd) {
                            $data[] = $nd;
                        }
                    }
                }
            }
        }
        return array('date' => $date, 'data' => $data);
    }

    public function getIndexListPage() {
        $link = '/?cid=1,158';
        $formulaMain = '/<h1>Arkivi.*?(<\/div>)/msxi';

        $HttpSocket = new HttpSocket();
        $page = $HttpSocket->get($this->getKosovoHost . $link);
        $data = null;
        if (preg_match($formulaMain, utf8_encode($page->body), $matches)) {
            $data = reset($matches);
            if ($data) {
                $data = $this->getAllLinkConv($data);
            }
        }
        return $data;
    }

    public function getUrl($link) {
        $formulaUrl = '/href=".*?(")/i';
        $formulaUrlReplace = '/href=|"/i';
        return $this->extractAndReplace($link, $formulaUrl, $formulaUrlReplace);
    }

    public function getUid($link) {
        $formulaUid = '/legid=\d+/i';
        $formulaUidReplace = '/legid=/';
        return $this->extractAndReplace($link, $formulaUid, $formulaUidReplace);
    }

    public function getUidMps($link) {
        $formulaUid = '/\,\d+(?=\&|")/i';
        $formulaUidReplace = '/\,|\&/';
        $id = $this->extractAndReplace($link, $formulaUid, $formulaUidReplace);
        return $id;
    }

    public function getAllLinkConv($data, $type = null) {
        $formulaAllLink = '/<a\s.*?(<\/a>)/msxi';
        $ndata = array();
        if (preg_match_all($formulaAllLink, $data, $matches)) {
            $results = reset($matches);

            $i = 1;
            foreach ($results as $result) {
                $result = preg_replace('/\&amp\;/', '&', $result);
                $result = preg_replace('/\&euml\;/', 'ë', $result);
                $url = $this->getUrl($result);

                $ndata[] = $this->getConvovationInfo($url, $i);
                $i++;
            }
            $urlActual = '?cid=1,102';
            $ndata[] = $this->getConvovationInfo($urlActual, $i);
        }
        return $ndata;
    }

    public function getConvovationInfo($link, $i = null) {
        $formulaMain = '/<div\sid="details">.*?(<div\sid="by\-character">)/msxi';
        $formulaDate = '/\d{2}\.\d{2}\.\d{4}/msxi';
        $formulaH1 = '/<h1>.*?(<\/h1>)/msxi';
        $formulaName = '/<h1>.*(?=\()/i';


        $HttpSocket = new HttpSocket();
        $page = $HttpSocket->get($this->getKosovoHost . $link);

        $data = $date = $ndata = null;
        if (preg_match($formulaMain, utf8_encode($page->body), $matches)) {
            $result = reset($matches);

            if (!is_null($i)) {
                if (preg_match_all($formulaH1, $result, $matches)) {
                    $results = reset($matches);

                    $name = preg_replace('/\&amp\;/', '&', $results[1]);
                    $name = preg_replace('/\&euml\;/', 'ë', $name);

                    $data['id'] = $i;
//                $data['uid'] = $this->getUid($link);
                    $data['url'] = $link;
                    $data['name'] = $this->extractTrimStrip($name, $formulaName);
                }
            }

            if (preg_match_all($formulaDate, $result, $matches)) {
                $date = reset($matches);
                pr($date);
                if ($date) {
                    if (isset($date[0]) && !empty($date[0])) {
                        $data['start_date'] = CakeTime::format($date[0], '%Y-%m-%d');
                    }
                    if (isset($date[0]) && !empty($date[1])) {
                        $data['end_date'] = CakeTime::format($date[1], '%Y-%m-%d');
                    }
                }
            }
        }
        return $data;
    }

    public function getAllLink($data, $type = null) {
        $formulaAllLink = '/<a\s.*?(<\/a>)/msxi';
        $ndata = array();
        if (preg_match_all($formulaAllLink, $data, $matches)) {
            $results = reset($matches);
            foreach ($results as $result) {
                $result = preg_replace('/\&amp\;/', '&', $result);
                $result = preg_replace('/\&euml\;/', 'ë', $result);
                $url = $this->getUrl($result);

                $ndata[] = array(
                    'id' => is_null($type) ? $this->getUid($result) : $this->getUidMps($result),
                    'url' => $url,
                    'name' => trim(strip_tags($result)),
                );
            }
        }
        return $ndata;
    }

    public function getPlenarySpeechesScrap($link) {

// $date = CakeTime::format('2007-01', '%Y-%m'); //start
        $end = CakeTime::format(time(), '%Y-%m');
        $date = CakeTime::format($end . " -3 month", '%Y-%m'); //last 3 month

        $l[] = $date;
        $data = array();
        $HttpSocket = new HttpSocket();
        $page = $HttpSocket->get($link . '&date=' . $date);
        $data[] = $this->getPlenarySpeechesIndex(utf8_encode($page->body));
        do {
            $date = CakeTime::format($date . " +1 month", '%Y-%m');
            $page = $HttpSocket->get($link . '&date=' . $date);
            $data[] = $this->getPlenarySpeechesIndex(utf8_encode($page->body));
            $l[] = $date;
        } while ($date < $end);

        $ndata = array();
        foreach ($data as $d) {
            $ndata = array_merge($ndata, $d);
        }
        return $ndata;
    }

    public function getPlenarySpeechesIndex($page) {
        $formulaLiSpan = '/<li><span>.*?(<\/li>)/msxi';
        $formulaMore = '/<a\sclass="more".*?(<\/a>)/i';
        $formulaUrl = '/href=".*?(")/i';
        $formulaUrlReplace = '/href=|"/i';
        $formulaUrlUids = '/\,\d+"/i';
        $formulaUrlUidsReplace = '/\,|"/i';
        $formulaH4 = '/<h4>.*?(<\/h4>)/i';
        $data = $more = array();
        if (preg_match_all($formulaLiSpan, $page, $matches)) {
            $data = reset($matches);
        }
        if (count($data) > 0) {
            foreach ($data as $d) {
                if (preg_match($formulaMore, $d, $matches)) {
                    $result = reset($matches);
                    $id = $this->extractAndReplace($result, $formulaUrlUids, $formulaUrlUidsReplace);
                    $more[] = array(
                        'id' => $id,
                        'post_uid' => $id,
                        'post_date' => $this->extractDateAndTrim($d),
                        'post_group_title' => $this->extractTrimStrip($d, $formulaH4),
                        'url' => $this->extractAndReplace($result, $formulaUrl, $formulaUrlReplace),
                        'index_md5' => md5($d)
                    );
                }
            }
        }
        return $more;
    }

    public function extractContent($id) {
// pr($this->getSerbiaHost . $link);
        $linkBse = '/?cid=3,177,';
        $HttpSocket = new HttpSocket();
        $page = $HttpSocket->get($this->getKosovoHost . $linkBse . $id);

        $formulaMain = '/<h1>Trans.*?(links">)/msxi';
        $formulaTitle = '/<h2>.*?(<\/h2>)/msxi';
        $formulaPara = '/<p>.*?(<\/p>)/msxi';

        $contentArray = array();
        if (preg_match($formulaMain, utf8_encode($page->body), $matches)) {
            $result = trim(reset($matches));
            $contentArray['KosovoSpeecheContent']['id'] = $id;
            $contentArray['KosovoSpeecheContent']['kosovo_speeche_index_id'] = $id;
            $contentArray['KosovoSpeecheContent']['title'] = $this->extractTrimStrip($result, $formulaTitle);
            $contentArray['KosovoSpeecheContent']['post_date'] = $this->extractDateAndTrim($result);
            $contentArray['KosovoSpeecheContent']['intro'] = $this->extractMoreWithOutDate($result, $formulaPara);
            $contentArray['KosovoSpeecheContent']['content_md5'] = md5($result);

            $contentArray['KosovoPdf'] = $this->extractPdfs($result, $id);
            $contentArray['KosovoTxt'] = $this->extractTxt($result, $id);
        }
        return $contentArray;
    }

    private function extractPdfs($data, $id) {
        $formulaPdf = '/href="common\/docs\/proc\/trans.*?(\.pdf")/msxi';
        $ndata = array();
        if (preg_match($formulaPdf, $data, $matches)) {
            $result = reset($matches);
            $ndata[] = array(
                'kosovo_speeche_content_id' => $id,
                'pdf_url' => '/' . preg_replace('/href=|"/', '', $result)
            );
        }
        return $ndata;
    }

    private function extractTxt($data, $id) {
        $formulaTxt = '/href="common\/docs\/voting\/vot.*?(\.txt")/msxi';
        $results = array();
        if (preg_match_all($formulaTxt, $data, $matches)) {
            $results = reset($matches);
            foreach ($results as $k => $result) {
                $results[$k] = array(
                    'kosovo_speeche_content_id' => $id,
                    'txt_url' => '/' . preg_replace('/href=|"/', '', $result)
                );
            }
        }
        return $results;
    }

    public function extractDateAndTrim($result) {
        $formulaDate = '/\d{2}\.\d{2}\.\d{4}\s\d{2}\:\d{2}/i';
        if (preg_match($formulaDate, $result, $matches)) {
            $result = trim(reset($matches));
            $date = CakeTime::format($result, '%Y-%m-%d %H:%M:%S');
            return $date;
        }
    }

    public function extractMoreWithOutDate($data, $formula) {
        $d = null;
        $formulaDate = '/\d{2}\.\d{2}\.\d{4}\s\d{2}\:\d{2}/i';
        $formulaTitle = '/<p><strong>.*?(<\/strong>)/i';
        $formulaListNum = '/\d+\.\s.*?(<strong>|">)/msxi';
        if (preg_match($formulaListNum, $data, $matches)) {
            $result = reset($matches);
            $result = preg_replace('/<br\s\/><br\s\/>|<br\s\/>|<br>|<BR>/', "\n", $result);
            $result = trim(strip_tags($result));
            $result = preg_replace('/\n(?=[a-zA-Z])/', " ", $result);
            $result = preg_replace('/\r\n|\r|\n/', "\n", $result);
            $result = trim(preg_replace('/(\n\xC2\xA0|\n&nbsp;)/', '', $result));
        }
        return $result;
    }

}
