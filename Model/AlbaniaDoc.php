<?php

App::uses('HttpSocket', 'Network/Http');

class AlbaniaDoc extends AppModel {

// instal libre-office centos 6.5: https://drujoopress.wordpress.com/2014/07/10/how-to-install-unoconv-in-centos-6-5/
// unoconv fix find problem: yum install http://pkgs.repoforge.org/unoconv/unoconv-0.5-1.el6.rf.noarch.rpm

    public $belongsTo = 'AlbaniaSpeecheIndex';
    public $eventId;

//    public $formulaSpeaker = '/<B>.*?(<\/B>)(\–|\-|\s\–|\s\-)/msxi';

    public function getDocFromLink($link, $id) {

        $fileFolder = WWW_ROOT . 'files' . DS . 'albania' . DS;
//        $this->id = $l['SerbianPdf']['id'];
//
        $HttpSocket = new HttpSocket();
        if ($this->enableProxy) {
            $HttpSocket->configProxy($this->proxyServer['ip'], $this->proxyServer['port']);
        }
        $fileDocName = $fileFolder . $id . '.doc';
        $f = fopen($fileDocName, 'w');
        chmod($fileDocName, 0666);
        $HttpSocket->setContentResource($f);
        $HttpSocket->get($link);
        if ($HttpSocket->response->code != 200) {
            $HttpSocket->get($link);
        }
        fclose($f);
        sleep(15);
//  return $link;
        if (file_exists($fileDocName)) {
            $command = '/usr/bin/unoconv -f html ' . $fileDocName . ' 2>&1';
            pr($command);
            $output = shell_exec($command);
            pr($output);
            if ($output) {
                $fileHtml = $fileFolder . $id . '.html';
                if (file_exists($fileHtml) && filesize($fileHtml) > 0) {
                    chmod($fileHtml, 0666);
                    $docToHtml = file_get_contents($fileHtml);
                    $data = array(
                        'id' => $id,
                        'albania_speeche_index_id' => $id,
                        'doc_md5' => md5($docToHtml),
                        'doc_url' => $link,
                        'content_al' => $docToHtml,
                    );
                    $this->save($data);
                    return true;
                    ;
                }
            }
            return false;
// return $httpSocket->response->code;
        } else {
            return false;
        }
    }

    public function strip_word_html($text, $allowed_tags = '<b><i><sup><sub><em><strong><u><br>') {
        mb_regex_encoding('UTF-8');
//replace MS special characters first
        $search = array('/&lsquo;/u', '/&rsquo;/u', '/&ldquo;/u', '/&rdquo;/u', '/&mdash;/u');
        $replace = array('\'', '\'', '"', '"', '-');
        $text = preg_replace($search, $replace, $text);
//make sure _all_ html entities are converted to the plain ascii equivalents - it appears
//in some MS headers, some html entities are encoded and some aren't
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
//try to strip out any C style comments first, since these, embedded in html comments, seem to
//prevent strip_tags from removing html comments (MS Word introduced combination)
        if (mb_stripos($text, '/*') !== FALSE) {
            $text = mb_eregi_replace('#/\*.*?\*/#s', '', $text, 'm');
        }
//introduce a space into any arithmetic expressions that could be caught by strip_tags so that they won't be
//'<1' becomes '< 1'(note: somewhat application specific)
        $text = preg_replace(array('/<([0-9]+)/'), array('< $1'), $text);
        $text = strip_tags($text, $allowed_tags);
//eliminate extraneous whitespace from start and end of line, or anywhere there are two or more spaces, convert it to one
        $text = preg_replace(array('/^\s\s+/', '/\s\s+$/', '/\s\s+/u'), array('', '', ' '), $text);
//strip out inline css and simplify style tags
        $search = array('#<(strong|b)[^>]*>(.*?)</(strong|b)>#isu', '#<(em|i)[^>]*>(.*?)</(em|i)>#isu', '#<u[^>]*>(.*?)</u>#isu');
        $replace = array('<b>$2</b>', '<i>$2</i>', '<u>$1</u>');
        $text = preg_replace($search, $replace, $text);
//on some of the ?newer MS Word exports, where you get conditionals of the form 'if gte mso 9', etc., it appears
//that whatever is in one of the html comments prevents strip_tags from eradicating the html comment that contains
//some MS Style Definitions - this last bit gets rid of any leftover comments */
        $num_matches = preg_match_all("/\<!--/u", $text, $matches);
        if ($num_matches) {
            $text = preg_replace('/\<!--(.)*--\>/isu', '', $text);
        }
        return $text;
    }

//    public $formulaSpeaker = '/<B>.*?(<\/B>\s\–|<\/B>\s\-|\-<\/B>|\-\s<\/B>)/';
    public $formulaSpeaker = '/<B>([A-ZÇË]{1}[a-zëç]{1,}).([A-ZÇË]{1}[a-zëç]{1,}).*?(\-|\–|\–)/';

    public function combineToApiArray($content) {

        $this->eventId = $this->getEventIdFromUrl($content['AlbaniaDoc']['doc_url']);

        $organization_id = $this->findAlbaniaChamberFromContent($content['AlbaniaDoc']['content_al']);
//        pr($organization_id);
//        return;

        $combine = ':split: ' . $content['AlbaniaDoc']['content_al'] . ' :split: a';
        $combine = preg_replace('/LANG=".*?(")|STYLE=".*?(")|CLASS=".*?(")|ALIGN=JUSTIFY|<!DOCTYPE.*?(<\/HEAD>)|<BODY.*?(">)|<!DOCTYPE\shtml>|<\/HTML>|<\/BODY>/msxi', '', $combine);
        $combine = preg_replace($this->formulaSpeaker, " :split::split: $0", $combine);

        $formulaSplit = '/:split:.*?(:split:)/msxi';

        $results = $data = array();
        if (preg_match_all($formulaSplit, $combine, $matches)) {
            $results = reset($matches);
        }
        $date = $this->toApiDate($content['AlbaniaSpeecheIndex']['post_date']);

        if (count($results) > 0) {
            foreach ($results as $key => $result) {
                $result = preg_replace('/:split:/i', "", $result);
                $result = preg_replace('/\s+/i', " ", $result);

                $text = $this->combineSpeche($result);

                $speaker = $this->getSpeaker($result);
                if ($speaker) {
                    $data[$key]['speeches']['id'] = $this->eventId . '-' . $key;
                    $data[$key]['speeches']['type'] = 'speech';
                    $data[$key]['speeches']['text'] = trim(($text['speche']));
                    $data[$key]['speeches']['date'] = $date;
                    $data[$key]['speeches']['position'] = $key;
                    $data[$key]['speeches']['event_id'] = 'event_' . $this->eventId;
                    $data[$key]['speeches']['attribution_text'] = $speaker['attribution_text'];
                    $data[$key]['speeches']['creator_id'] = $this->checkAlbaniaPeopleExist($speaker['attribution_text']);
                    $data[$key]['speeches']['sources'] = array(
                        array(
                            'url' => $content['AlbaniaSpeecheIndex']['url'],
                        )
                    );
                } else {
                    $data[$key]['speeches']['id'] = $this->eventId . '-' . $key;
                    $data[$key]['speeches']['type'] = 'narrative';
                    $data[$key]['speeches']['text'] = trim(($text['speche']));
                    $data[$key]['speeches']['date'] = $date;
                    $data[$key]['speeches']['position'] = $key;
                    $data[$key]['speeches']['event_id'] = 'event_' . $this->eventId;
                    $data[$key]['speeches']['sources'] = array(
                        array(
                            'url' => $content['AlbaniaSpeecheIndex']['url'],
                        )
                    );
                }
            }
        }


        $events['events']['id'] = 'event_' . $this->eventId;
        $events['events']['organization_id'] = $organization_id;
        $events['events']['start_date'] = $date;
        $events['events']['sources'][] = array(
            'url' => $content['AlbaniaSpeecheIndex']['url']
        );
        if (isset($content['AlbaniaSpeecheIndex']['AlbaniaSpecheSession']['id']) && !empty($content['AlbaniaSpeecheIndex']['AlbaniaSpecheSession']['id'])) {
            $session_id = $organization_id . '-' . $content['AlbaniaSpeecheIndex']['AlbaniaSpecheSession']['id'];
            $events['events']['type'] = 'sitting';
            $events['events']['parent_id'] = $session_id;

            $sessions['events']['id'] = $session_id;
            $sessions['events']['organization_id'] = $organization_id;
            $sessions['events']['name'] = $content['AlbaniaSpeecheIndex']['AlbaniaSpecheSession']['name'];
            $sessions['events']['type'] = 'session';
            $sessions['events']['sources'][] = array(
                'url' => $content['AlbaniaSpeecheIndex']['AlbaniaSpecheSession']['url']
            );
            $data[] = $sessions;
        }

        $data[] = $events;
//        if (isset($tlogs) && count($tlogs)) {
//            foreach ($tlogs as $tl) {
//                foreach ($tl as $t) {
////                    $data[]['logs'] = array(
////                        'id' => 'events_' . $content['SerbianSpeecheIndex']['post_uid'] . '_' . time() . '_' . rand(0, 999),
////                        'label' => 'remove: ' . trim(strip_tags($t)),
////                        'status' => 'finished',
//////                        'params' => $t
////                    );
//                }
//            }
//        }
        return $data;
    }

    public function findAlbaniaChamberFromContent($content) {
        $formulaTop = '/LEGJISLATURA.*?(\–|\-)/msx';

        if (preg_match($formulaTop, trim(strip_tags($content)), $matches)) {
            $result = reset($matches);
            $result = preg_replace('/LEGJISLATURA|\–|\-|\n|\r|\t|\s+/', '', $result);
            $result = $this->findAlbaniaChamber($result);
            if (is_array($result)) {
//                pr($result);
                return('chamber_' . $result['AlbaniaChamber']['name']);
            } else {
                return($result);
            }
        }
    }

    public function getSpeaker($combine) {
        if (preg_match($this->formulaSpeaker, $combine, $matches)) {
            $result = reset($matches);
            if ($result) {
                $result = preg_replace('/\–|\-/', '', $result);
                $result = trim(strip_tags($result));
//                pr($result);
                $data['attribution_text'] = $result;
                return($data);
            }
        }
        return false;
    }

    public function combineSpeche($combine) {
        $combine = preg_replace('/<P.*?(>)/', "<p>", $combine);
        $combine = preg_replace('/<p>(\n+|\r+|\t+|\s+)<BR>(\n+|\r+|\t+|\s+)<\/P>/', "", $combine);
        $combine = preg_replace('/<p>\s+<\/P>/', "", $combine);
        $combine = preg_replace('/\n+|\t+|\r+|\s\s+/', "\n", $combine);
        $combine = preg_replace('/\n+/', "\n", $combine);
        $combine = preg_replace('/<p><BR>\n<\/P>/', "\n", $combine);
        $combine = preg_replace('/<SPAN.*?(>)|<\/SPAN>|<FONT.*?(>)|<\/FONT>/', "", $combine);
        $combine = preg_replace('/\s+/', " ", $combine);
        return array('speche' => $combine);
    }

    public function getEventIdFromUrl($url) {
        $formulaFile = '/pub\/.*?(\.doc)/i';
        $formulaFileReplace = '/pub\/|\.doc/';
        return $this->extractAndReplace($url, $formulaFile, $formulaFileReplace);
    }

}
