<?php

App::uses('HttpSocket', 'Network/Http');
App::uses('CakeTime', 'Utility');

class Albania extends AppModel {

    public $useTable = false;

    public function getMpsContent($link, $id) {
        $formulaDivClass = '/<div\sclass="si_content\slist-center-component.*?(<div\sclass="print_email">)/msxi';
        $formulaName = '/<h1>.*?(<\/h1><\/div>)/i';
        $formulaPostDate = '/\(\'\d+\.\d+.\d+\'\)/i';
        $formulaPostDateReplace = '/\(|\)|\'/i';
        $formulaImage = '/src=".*?(")/i';
        $formulaImageReplace = '/src=|"/';

        $formulaEmail = "/[\w.-]+@[\w.-]+\.[A-Za-z]{2,6}/i";
        $formulaEmailReplace = '/info@parlament.al/i';
        $formulaBirthDate = '/Datëlindja:.*\d+\.\d+.\d+/i';
        $formulaBirthDateReplace = '/Datëlindja:|&nbsp;/i';
        $formulaHomeTown = '/Vendlindja:.*?(<\/)/msxi';
        $formulaHomeTownReplace = '/Vendlindja:|<\//i';
        $formulaMarital = '/civil:.*?(<\/p>)/i';
        $formulaMaritalReplace = '/civil:/i';
        $formulaEducation = '/Edukimi:.*?(<\/p><\/td>)/msxi';
        $formulaEducationReplace = '/Edukimi:/';

        $formulaProfessionalActivity = '/Veprimtaria\sprofesionale:.*?(<\/p><\/td>)/msxi';
        $formulaProfessionalActivityReplace = '/Veprimtaria\sprofesionale:/';
        $formulaPoliticalActivity = '/Veprimtaria\spolitike:.*?(<\/p><\/td>)/msxi';
        $formulaPoliticalActivityReplace = '/Veprimtaria\spolitike:/';
        $formulaOptingIn = '/Zgjedhur\snë:.*?(<\/p><\/td>)/msxi';
        $formulaOptingInReplace = '/Zgjedhur\snë:/';
        $formulaGroupParliamentary = '/Komisionet\sparlamentare:.*?(<\/p><\/td>)/msxi';
        $formulaParliamentaryReplace = '/Komisionet\sparlamentare:/';
        $formulaPublishing = '/Botime.*?(<\/p><\/td>)/msxi';
        $formulaPublishingReplace = '/Botime:|Botimet:/';


        $HttpSocket = new HttpSocket();
        if ($this->enableProxy) {
            $HttpSocket->configProxy($this->proxyServer['ip'], $this->proxyServer['port']);
        }
        $page = $HttpSocket->get($link);
        //pr($page->body);
        $data = array();
        if (preg_match($formulaDivClass, $page->body, $matches)) {
            $result = reset($matches);
            $result = preg_replace('/&nbsp;/', ' ', $result);
            $data = array(
                'id' => $id,
                'url' => $link,
                'name' => $this->extractTrimStrip($result, $formulaName),
                'md5' => md5($result),
                'post_data' => CakeTime::format($this->extractAndReplace($result, $formulaPostDate, $formulaPostDateReplace), '%Y-%m-%d'),
                'image' => $this->extractAndReplace($result, $formulaImage, $formulaImageReplace),
                'address' => $this->extractAddress($result),
                'email' => $this->extractAndReplace($result, $formulaEmail, $formulaEmailReplace),
                'year_of_birth' => CakeTime::format($this->extractAndReplace($result, $formulaBirthDate, $formulaBirthDateReplace), '%Y-%m-%d'),
                'home_town' => $this->extractAndReplace($result, $formulaHomeTown, $formulaHomeTownReplace),
                'marital_status' => $this->extractAndReplace($result, $formulaMarital, $formulaMaritalReplace),
                'education' => $this->extractAndReplaceBlock($result, $formulaEducation, $formulaEducationReplace),
                'professional_activity' => $this->extractAndReplaceBlock($result, $formulaProfessionalActivity, $formulaProfessionalActivityReplace),
                'political_activity' => $this->extractAndReplaceBlock($result, $formulaPoliticalActivity, $formulaPoliticalActivityReplace),
                'opting_in' => $this->extractAndReplaceBlock($result, $formulaOptingIn, $formulaOptingInReplace),
                'group_parliamentary_committees' => $this->extractAndReplaceBlock($result, $formulaGroupParliamentary, $formulaParliamentaryReplace),
                'publishing' => $this->extractAndReplaceBlock($result, $formulaPublishing, $formulaPublishingReplace),
                'all' => $result
            );
        }
//        pr($data);
//        pr($result);
        return$data;
    }

    public function getMenuListFromLink($linkMenu) {
        $formulaDivClass = '/<div\sclass="li_item_inner">.*?(<\/div>)/msxi';

        $ndata = $pagin = $list = array();
        $HttpSocket = new HttpSocket();
        if ($this->enableProxy) {
            $HttpSocket->configProxy($this->proxyServer['ip'], $this->proxyServer['port']);
        }
        $page = $HttpSocket->get($linkMenu);
        if (preg_match_all($formulaDivClass, $page->body, $matches)) {
            $results = reset($matches);
            foreach ($results as $result) {
                $ndata[] = $this->getUrl($result);
            }
        }

        if (count($ndata)) {
            // pr($ndata);
            foreach ($ndata as $nd) {
                $p = $this->checkPagin($nd);
                if (count($p) > 1) {
                    foreach ($p as $l) {
                        $pagin[] = $l;
                    }
                } else {
                    $pagin[] = $nd;
                }
            }
        }

        if (count($pagin)) {
            foreach ($pagin as $d) {
                $pd = $this->getPaginPage($d);
                if (!empty($pd) && count($pd)) {
                    foreach ($pd as $pl) {
                        $list[] = $pl;
                    }
                }
            }
        }
        return $list;
    }

    public function getPaginPage($link) {
        $formulaDivClass = '/<div\sclass="li_item_inner">.*?(<\/div>)/msxi';
        $HttpSocket = new HttpSocket();
        if ($this->enableProxy) {
            $HttpSocket->configProxy($this->proxyServer['ip'], $this->proxyServer['port']);
        }
        $page = $HttpSocket->get($link);
        // pr($page);
        $ndata = array();
        if (preg_match_all($formulaDivClass, $page->body, $matches)) {
            $results = reset($matches);
            foreach ($results as $result) {
                $ndata[] = array(
                    'uid' => $this->getUid($result),
                    'post_date' => $this->extractDateAndTrim($result),
                    'name' => trim(strip_tags($result)),
                    'url' => $this->getUrl($result)
                );
            }
        }
        return $ndata;
    }

    public function checkPagin($link) {
        $formulaPagin = '/<div\sclass="block_navigation\stop">.*?(<\/div>)/msxi';
        $HttpSocket = new HttpSocket();
        if ($this->enableProxy) {
            $HttpSocket->configProxy($this->proxyServer['ip'], $this->proxyServer['port']);
        }
        $page = $HttpSocket->get($link);
        $links = array();
        if (preg_match($formulaPagin, $page->body, $matches)) {
            $result = reset($matches);
            if ($result) {
                $links = $this->getAllPaginLink($result);
            }
        }
        return $links;
    }

    public function getUrl($link) {
        $formulaUrl = '/href=".*?(")/i';
        $formulaUrlReplace = '/href=|"/i';
        return $this->extractAndReplace($link, $formulaUrl, $formulaUrlReplace);
    }

    public function getUid($link) {
        $formulaUid = '/\d+(?=\_\d+\.)/i';
        return $this->extractTrimStrip($link, $formulaUid);
    }

    public function getAllLink($data) {
        $formulaAllLink = '/<a\s.*?(<\/a>)/msxi';
        $ndata = array();
        if (preg_match_all($formulaAllLink, $data, $matches)) {
            $results = reset($matches);
            foreach ($results as $result) {
                $result = preg_replace('/\&amp\;/', '&', $result);
                $ndata[] = $this->getUrl($result);
            }
        }
        return $ndata;
    }

    public function getAllPaginLink($data) {
        $formulaAllLink = '/<a\s.*?(<\/a>)/msxi';
        $ndata = array();
        if (preg_match_all($formulaAllLink, $data, $matches)) {
            $results = reset($matches);
            foreach ($results as $result) {
                $result = preg_replace('/\&amp\;/', '&', $result);
                $ndata[] = $this->getUrl($result);
            }
        }
        if (count($ndata) > 1) {
            $last = end($ndata);
            $formulaLast = '/\d+(?=\&rp=)/i';
            if (preg_match($formulaLast, $result, $matches)) {
                $lastNo = trim(reset($matches));
                $ndata = array();
                if (!empty($lastNo) && (int) $lastNo) {
                    for ($i = 1; $i <= $lastNo; $i++) {
                        $ndata[] = preg_replace($formulaLast, $i, $last);
                    }
                }
            }
        }
        return $ndata;
    }

    public function extractAddress($result) {
        $formulaAddress = '/Adresa:.*?(<\/td>)/msxi';
        $formulaAddressReplace = '/Adresa:|<a|E-mail:.*$/';
        $result = preg_replace('/<a.*?(<\/a>)/msxi', '', $result);
        $result = preg_replace('/<br\s\/>|<\/p>|\r|\n|<\/div>/', "###", $result);
        $result = $this->extractAndReplace($result, $formulaAddress, $formulaAddressReplace);
        $result = preg_replace('/[\w.-]+@[\w.-]+\.[A-Za-z]{2,6}/msxi', '', $result);
        $result = preg_replace('/^#*#/i', '', $result);
        $result = preg_replace('/#*#$/i', '', $result);
        $result = preg_replace('/#\s#/i', '##', $result);
        $result = preg_replace('/#*#/i', '<br />', $result);
        return trim(preg_replace('/\s+/', ' ', $result));
    }

    public function extractAndReplaceBlock($data, $formula, $replace) {
        if (preg_match($formula, $data, $matches)) {
            $result = trim(reset($matches));
            $result = preg_replace('/<br\s\/>|<\/p>|\r|\n|<\/div>/', "###", $result);
            $result = strip_tags(preg_replace($replace, '', $result));
            $result = preg_replace('/&nbsp;/', ' ', $result);
            //   $result = preg_replace('/###/', '<br />', $result);
            $result = preg_replace('/^#*#/i', '', $result);
            $result = preg_replace('/#*#$/i', '', $result);
            $result = preg_replace('/#\s#/i', '##', $result);
            $result = preg_replace('/#*#/i', '<br />', $result);
            return trim(preg_replace('/\s+/', ' ', $result));
        }
    }

}
