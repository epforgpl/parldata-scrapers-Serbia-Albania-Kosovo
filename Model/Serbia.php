<?php

App::uses('HttpSocket', 'Network/Http');
App::uses('CakeTime', 'Utility');

class Serbia extends AppModel {

    public function getFirst() {
        return 'nawet OK';
    }

    public function getPlenarySpeechesScrap($link) {

        $formulaPagin = '/offset=.*?(")/i';
        $HttpSocket = new HttpSocket();

        $data = array();
        $i = 0;
        do {
            $page = $HttpSocket->get($link . '?offset=' . $i);
            $result = $this->getPlenarySpeechesIndex($page->body);
            $i++;
            if (empty($result)) {
                break;
            } else {
                $data[] = $result;
            }
        } while ($result);

        $ndata = array();
        foreach ($data as $d) {
            $ndata = array_merge($ndata, $d);
        }
        return $ndata;
    }

    public function getPlenarySpeechesIndex($page) {
        $formulaPrint = '/<!--\sprint_start\s-->.*?(<!--\sprint_end\s-->)/msxi';
        $formulaTopLeft = '/<div\sclass="main_left">.*?(<!--\s\/main_left\s-->)/msxi';
        $formulaTopRight = '/<div\sclass="main_right">.*?(<!--\s\/main_right\s-->)/msxi';

        $data = array();
        if (preg_match($formulaPrint, $page, $matches)) {
            $data = reset($matches);

            $topLeft = array();
            if (preg_match($formulaTopLeft, $data, $matches)) {
                $topLeft = reset($matches);
                $topLeft = $this->getPlenarySpeechesTopLeft($topLeft);
            }
            $topRight = $this->getPlenarySpeechesList($data);
        }
        $result = array_merge($topLeft, $topRight);
        return $result;
    }

    public function getPlenarySpeechesTopLeft($topLeft) {
        $data = array();
        $formulaGroup = '/<div\sclass="sitting_main">.*?(<\/div>)/i';
        $formulaUrlTitle = '/<h3>.*?(<\/h3>)/msxi';

        $urlData = $this->extractUrlFromTitle($topLeft, $formulaUrlTitle);
        $post_date = $this->extractDateAndReplace($topLeft);
        $post_date = CakeTime::format($this->replaceMonth($post_date), '%Y-%m-%d');
        // $content = $this->extraktContent($urlData['url']);
        $group = $this->extractTrimStrip($topLeft, $formulaGroup);
        if ($urlData['url']) {
            $data[] = array(
                'post_uid' => $urlData['post_uid'],
                'lay_uid' => $urlData['section_uid'],
                'post_group_title' => $group,
                'post_date' => $post_date,
                //  'title' => $content['title'],
                //  'image' => $content['image'],
                //  'caption' => $content['caption'],
                // 'post_date' => $content['post_date'],
                //  'convert_date' => CakeTime::format($content['post_date'], '%Y-%m-%d'),
                //  'intro' => $content['intro'],
                'url' => $urlData['url'],
                'index_md5' => md5(serialize($urlData)),
                    //  'content' => $content['content'],
            );
        }
        return !empty($data) ? $data : null;
    }

    public function getPlenarySpeechesList($topRight) {

        $formulaBlock = '/<li.*?(<\/li>)/msxi';
        $formulaGroup = '/<li\sclass="sitting_list_item">.*?(<\/li>)/i';
        $formulaUrlTitle = '/<h4>.*?(<\/h4>)/msxi';
        $formulaList = '/<li>.*?(<\/li>)/msxi';

        $data = array();
        if (preg_match_all($formulaBlock, $topRight, $matches)) {
            $results = reset($matches);
            foreach ($results as $result) {
                $group = !empty($group) ? $group : null;
                if (preg_match($formulaGroup, $result, $matches)) {
                    $resGroup = reset($matches);
                    $g = $this->extractTrimStrip($resGroup, $formulaGroup);
                    $group = $g == $group ? null : $g;
                }
                if (preg_match($formulaList, $result, $matches)) {
                    $resLi = reset($matches);
                    $urlData = $this->extractUrlFromTitle($resLi, $formulaUrlTitle);
                    $post_date = $this->extractDateAndReplace($result);
                    $post_date = CakeTime::format($this->replaceMonth($post_date), '%Y-%m-%d');
                    //  $content = $this->extraktContent($urlData['url']);
                    if ($urlData['url']) {
                        $data[] = array(
                            'post_uid' => $urlData['post_uid'],
                            'lay_uid' => $urlData['section_uid'],
                            'post_group_title' => $group,
                            'post_date' => $post_date,
                            // 'title' => $content['title'],
                            // 'image' => $content['image'],
                            // 'caption' => $content['caption'],
                            // 'post_date' => $content['post_date'],
                            // 'convert_date' => CakeTime::format($content['post_date'], '%Y-%m-%d'),
                            //  'intro' => $content['intro'],
                            'url' => $urlData['url'],
                            'index_md5' => md5(serialize($urlData)),
                                //  'content' => $content['content'],
                        );
                    }
                }
            }
        }
        return $data;
    }

    public function extraktContent($link) {
        // pr($this->getSerbiaHost . $link);
        $HttpSocket = new HttpSocket();
        $page = $HttpSocket->get($this->getSerbiaHost . $link);

        $formulaContent = '/"delimiter.*?("delimiter)/msxi';
        $formulaImage = '/src=".*?(")/i';
        $formulaImageReplace = '/src=|"/';
        $formulaPara = '/<p>.*?(<\/p>)/msxi';
        $formulaMainDiv = '/<div\sid="main">.*?(<hr\sclass="delimiter"\/>)/msxi';
        $formulaTitle = '/<h2>.*?(<\/h2>)/msxi';
        $formulaPreamble = '/<p\sclass="preamble">.*?(<\/p>)/msxi';

        $contentArray = array();
        if (preg_match($formulaMainDiv, $page->body, $matches)) {
            $result = trim(reset($matches));
            $contentArray['title'] = $this->extractTrimStrip($result, $formulaTitle);
            $contentArray['post_date'] = $this->extractDateAndReplace($result);
            $contentArray['convert_date'] = CakeTime::format($this->replaceMonth($contentArray['post_date']), '%Y-%m-%d');
            $contentArray['intro'] = $this->extractTrimStrip($result, $formulaPreamble);
            $contentArray['image'] = $this->extractAndReplace($result, $formulaImage, $formulaImageReplace);
            $contentArray['caption'] = $this->extractTrimStrip($result, $formulaPara);
        }
        // pr($this->replaceMonth($contentArray['post_date']));

        if (preg_match($formulaContent, $page->body, $matches)) {
            $result = trim(reset($matches));
            $result = trim(preg_replace('/<hr\sclass="delimiter|"delimiter"\/>/', '', $result));
            // $contentArray['content'] = base64_decode(trim(preg_replace('/<div\s.*?(>)|<div>|<\/div>/', '', $result)));
            $content = trim(preg_replace('/<div\s.*?(>)|<div>|<\/div>/', '', $result));
            $contentArray['content_md5'] = md5($content);
            $pdfs = $this->extractPdfs($content, $contentArray['convert_date']);
            $contentArray = array_merge($contentArray, $pdfs);
        }
        // pr($contentArray['content']);
        return $contentArray;
    }

    private function extractPdfs($data, $date) {
        if (!empty($date)) {
            $folmulaLink = '/<a.*?(<\/a>)/msxi';
            //$folmulaLink = '/<a(.*)href="\/upload.*?(<\/a>)/msxi';
            $formulaPdf = '/href="\/upload.*?(\.pdf")/msxi';

            $results = array();
            $i = 0;
            $stamp = CakeTime::toUnix($date);
            do {
                $result = $alink = $name = $next = null;
                $tstamp = $stamp . '_' . $i;
                if (preg_match($folmulaLink, $data, $matches)) {
                    $alink = reset($matches);
                    $checkEmpty = preg_replace('/\s+/msxi', '', strip_tags($alink));

                    if (!empty($checkEmpty)) {
                        if (preg_match($formulaPdf, $alink, $matches)) {
                            $result = reset($matches);
                        }
                        $name = preg_replace('/<a.*?(">)|<\/a>/msxi', '', $alink);
                    } else {
                        $data = str_replace($alink, '', $data);
                        $next = 1;
                    }
                }
                if (empty($result) && empty($next)) {
                    break;
                } else {
                    if (!$next) {
                        $result = preg_replace('/href=|"/i', '', $result);
                        $result = htmlspecialchars_decode($result);
                        $data = str_replace($alink, '<!-- ###' . $tstamp . '### -->' . $name, $data);

                        $results['pdfs'][] = array(
                            'post_date' => $date,
                            'stamp_in_text' => $tstamp,
                            'pdf_url' => $result,
                            'name_sr' => $name,
                            'pdf_md5' => md5($alink)
                        );
                    }
                }
                $i++;
            } while ($result || $next);
            //check duplicate
            if ($results) {
                $i = 0;
                while (($nu = array_shift($results['pdfs'])) !== NULL) {
                    $tmpArray[$i] = $nu;
//                pr('$num');
//                pr($num);
                    foreach ($results['pdfs'] as $k => $p) {
                        if ($nu['pdf_url'] == $p['pdf_url']) {
                            $tmpArray[$i]['name_sr'] .= $p['name_sr'];
                            unset($results['pdfs'][$k]);
//                            pr('$p');
//                            pr($p);
                        }
                    }
                    $i++;
                }
                $results['pdfs'] = $tmpArray;
            }
            $content['content'] = $data;
            if ($results) {
                $content = array_merge($content, $results);
            }
            return $content;
        }
        return;
    }

    public function extractUrlFromTitle($data, $formula) {
        $formulaUrl = '/href=".*?(")/i';
        $formulaUrlUids = '/\.\d+\.\d+\./i';
        // $formulaUrlUids = '/\d+/i';
        $dataArray = array();
        if (preg_match($formula, $data, $matches)) {
            $result = trim(reset($matches));
            if (preg_match($formulaUrl, $result, $matches)) {
                $result = trim(reset($matches));
                $dataArray['url'] = preg_replace('/href=|"/', '', $result);
                if (preg_match($formulaUrlUids, $dataArray['url'], $matches)) {
                    $result = trim(reset($matches));
                    $result = explode('.', substr($result, 1, -1));
                    $dataArray['post_uid'] = $result[0];
                    $dataArray['section_uid'] = $result[1];
                }
            }
        }
        return $dataArray;
    }

    public function extractDateAndReplace($result) {
        $formulaDate = '/<p\sclass="posted">.*?(<\/p>)/msxi';
        $formulaDateReplace = '/\|/';
        $getToYear = '/.*?(\d{4})/i';
        if (preg_match($formulaDate, $result, $matches)) {
            $result = trim(strip_tags(reset($matches)));
            $result = preg_replace($formulaDateReplace, '', $result);
            if (preg_match($getToYear, $result, $matches)) {
                return reset($matches);
            }
        }
    }

    public function replaceMonth($text) {
        // $toChange = chop($text);
        $aFind = array(
            'јануар', 'фебруар', 'март', 'април', 'мај', 'јун', 'јул', 'август', 'септембар', 'октобар', 'новембар', 'децембар',
            'Понедељак', 'Уторак', 'Среда', 'Четвртак', 'Петак', 'Субота', 'Недеља'
        );
        $aChange = array(
            'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December',
            'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'
        );
        return str_replace($aFind, $aChange, $text);
    }

}
