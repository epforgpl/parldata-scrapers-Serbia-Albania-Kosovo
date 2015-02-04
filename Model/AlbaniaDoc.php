<?php

App::uses('HttpSocket', 'Network/Http');

class AlbaniaDoc extends AppModel {

// instal libre-office centos 6.5: https://drujoopress.wordpress.com/2014/07/10/how-to-install-unoconv-in-centos-6-5/
// unoconv fix find problem: yum install http://pkgs.repoforge.org/unoconv/unoconv-0.5-1.el6.rf.noarch.rpm

    public $belongsTo = 'AlbaniaSpeecheIndex';

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

}
