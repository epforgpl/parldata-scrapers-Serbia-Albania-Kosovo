<?php

App::uses('HttpSocket', 'Network/Http');
App::uses('CakeTime', 'Utility');

class SerbianMps extends AppModel {

    //en  private $menuLink = '/national-assembly/composition/members-of-parliament/current-convocation.487.html';
    private $menuLink = '/народна-скупштина/састав/народни-посланици/актуелни-сазив.11.html';
    private $link = 'http://www.parlament.gov.rs/national-assembly/composition/members-of-parliament.';

    public function getTableFromLink($menuData) {
        if (!is_array($menuData) && empty($menuData['url'])) {
            return;
        }
        $HttpSocket = new HttpSocket();
        $page = $HttpSocket->get($this->getSerbiaHost . $menuData['url']);
        $body = ($page->body);

        $formulaPrint = '/<!--\sprint_start\s-->.*?(<!--\sprint_end\s-->)/msxi';
        $formulaResidence = '/<select\sid="locSelect.*?(<\/select>)/msxi';
        $formulaParty = '/<select\sname="party.*?(<\/select>)/msxi';
        $formulaParliamentaryGroups = '/<select\sname="group.*?(<\/select>)/msxi';
        $formulaCommittees = '/<select\sname="board.*?(<\/select>)/msxi';
        $formulaDelegateList = '/<div\sclass="delegate_list">.*?(<!--\sdelegate_list\s-->)/msxi';
        $formulaDelegateOld = '/<table.*?(<\/table>)/msxi';
        $newData['SerbianMenuData'] = $menuData;
        $data = array();

        if (preg_match($formulaPrint, $body, $matches)) {
            $data = reset($matches);
            $table_md5 = md5($data);
            if (!isset($menuData['table_md5']) || $menuData['table_md5'] != $table_md5) {
                $newData['SerbianMenuData']['table_md5'] = $table_md5;
                if (preg_match($formulaResidence, $data, $matches)) {
                    $result = reset($matches);
                    $newData['SerbianResidence'] = $this->extractSelectOptions($result);
                }
                if (preg_match($formulaParty, $data, $matches)) {
                    $result = reset($matches);
                    $newData['SerbianParty'] = $this->extractSelectOptions($result);
                }
                if (preg_match($formulaParliamentaryGroups, $data, $matches)) {
                    $result = reset($matches);
                    $newData['SerbianParliamentaryGroup'] = $this->extractSelectOptions($result);
                }
                if (preg_match($formulaCommittees, $data, $matches)) {
                    $result = reset($matches);
                    $newData['SerbianCommitte'] = $this->extractSelectOptions($result);
                }
                $delegates = array();
                if (preg_match_all($formulaDelegateList, $data, $matches)) {
                    $results = reset($matches);

                    $delegates = array();
                    foreach ($results as $r) {
                        $delegates[] = $this->extractDelegateList($r, $menuData['id']);
                    }
                    if (count($delegates) == 2) {
                        $delegates = array_merge($delegates[0], $delegates[1]);
                    } else {
                        $delegates = $delegates[0];
                    }
                } elseif (preg_match($formulaDelegateOld, $data, $matches)) {
                    $result = reset($matches);
                    $delegates = $this->extractDelegateList($result, $menuData['id']);
                    // $delegates[0]['type'] = 'th_name';
                }
                $newData['SerbianDelegate'] = $delegates;
            }
        }
        return $newData;
    }

    public function extractDelegateList($data, $menu_id) {
        echo $menu_id;

        $formulaTh = '/<th.*?(<\/th>)/msxi';
        $formulaTrs = '/<tr.*?(<\/tr>)/msxi';
        $formulaTds = '/<td.*?(<\/td>)/msxi';
        $formulaHref = '/href=\".*?(\")/i';
        $formulaHrefReplace = '/href=|"/';
        $formulaUrlUids = '/\.\d+\.(?=\d)/i';
        $formulaUrlUidsReplace = '/html|\./i';
        $formulaTerminated = '/Посланици/msxi';
        if (preg_match($formulaTerminated, $data, $matches)) {
            $terminated = 1;
            //  echo '<br />$terminated1 ' . $terminated . '<br />';
        } else {
            $terminated = 0;
            // echo '<br />$terminated2 ' . $terminated . '<br />';
        }

        $tableItem = array();

        if (preg_match_all($formulaTh, $data, $matches)) {
            $results = reset($matches);

            $col1 = preg_replace('/\s+/', '', trim(strip_tags($results[1])));
            $col2 = preg_replace('/\s+/', '', trim(strip_tags($results[2])));
            $col3 = preg_replace('/\s+/', '', trim(strip_tags($results[3])));
        }

        $formulaDate = '/\d{2}\.\d{2}\.\d{4}/i';
        if (preg_match_all($formulaTrs, $data, $matches)) {
            $results = reset($matches);

            if (!isset($col1) && !isset($col2) && !isset($col3)) {
                if (preg_match_all($formulaTds, $results[0], $matches)) {
                    $cols = reset($matches);
                    $col1 = preg_replace('/\s+/', '', trim(strip_tags($cols[1])));
                    $col2 = preg_replace('/\s+/', '', trim(strip_tags($cols[2])));
                    $col3 = preg_replace('/\s+/', '', trim(strip_tags($cols[3])));
                    unset($results[0]);
                }
            }

            foreach ($results as $r) {
                if (preg_match_all($formulaTds, $r, $matches)) {
                    $result = reset($matches);

                    $checkTerm = 'мандата'; //Term of office // $col2
                    $checkTown = 'есто'; //town // $col2 $col3
                    $checkAge = 'одиште'; //age // $col3
                    $checkPaGr = 'група'; //PalamentaryGroup // $col1
                    $checkParty = 'транка'; //Party // $col1 $col2


                    $term = $hometown = $age = $paGr = $party = $start_date = $end_date = null;
                    if (strpos($col2, $checkTerm) !== false) {
                        $term = trim(strip_tags($result[2]));

                        if (preg_match_all($formulaDate, $term, $matches)) {
                            $term = reset($matches);
                            // pr($term);
                            $start_date = CakeTime::format($term[0], '%Y-%m-%d');
                            $end_date = CakeTime::format($term[1], '%Y-%m-%d');
                        }
                    }

                    if (strpos($col2, $checkTown) !== false) {
                        $hometown = trim(strip_tags($result[2]));
                    } elseif (strpos($col3, $checkTown) !== false) {
                        $hometown = trim(strip_tags($result[3]));
                    }

                    if (strpos($col3, $checkAge) !== false) {
                        $age = trim(strip_tags($result[3]));
                        $age = trim(strip_tags(preg_replace('/\./', '', $age)));
                    }

                    if (strpos($col1, $checkPaGr) !== false) {
                        $paGr = trim(strip_tags($result[1]));
                    }

                    if (strpos($col1, $checkParty) !== false) {
                        $party = trim(strip_tags($result[1]));
                    } elseif (strpos($col2, $checkParty) !== false) {
                        $party = trim(strip_tags($result[2]));
                    }
                    $name = trim(strip_tags($result[0]));

                    $tableItem[] = array(
                        'serbian_menu_data_id' => $menu_id,
                        'api_uid' => $this->toCamelCase($name),
                        'terminated' => $terminated,
                        'url_uid' => $this->extractAndReplace($result[0], $formulaUrlUids, $formulaUrlUidsReplace),
                        'url' => $this->extractAndReplace($result[0], $formulaHref, $formulaHrefReplace),
                        'name' => $name,
                        'parlamentary_group' => $paGr,
                        'party' => $party,
                        'start_date' => $start_date,
                        'end_date' => $end_date,
                        'hometown' => $hometown,
                        'age' => $age,
                    );
                }
            }
        }
        return $tableItem;
    }

    public function extractSelectOptions($data) {
        $formulaOptions = '/<option.*?(<\/option>)/msxi';
        $tmpData = $results = array();
        if (preg_match_all($formulaOptions, $data, $matches)) {
            $tmpData = reset($matches);
            foreach ($tmpData as $td) {
                // pr($td);
                if (preg_match('/\d+(?=")/i', $td, $matches)) {
                    $results[] = array(
                        'uid' => reset($matches),
                        'name' => trim(strip_tags($td))
                    );
                }
            }
            if (isset($results[0])) {
                unset($results[0]);
            }
        }
        return $results;
    }

    public function getMenuFromLink() {
        $HttpSocket = new HttpSocket();
        $page = $HttpSocket->get($this->getSerbiaHost . $this->menuLink);
        $data = $this->extractMembersParliamentMenu($page->body);
        if (count($data) > 1) {
            $page = $HttpSocket->get($this->getSerbiaHost . $data[1]['url']);
            $data = $this->extractMembersParliamentMenu($page->body);
        }
        $newData['SerbianMenuData'] = $data;
        return $newData;
    }

    public function getContactsInfo($last, $limit = 100) {
        $HttpSocket = new HttpSocket();
        $data = array();
        $i = $last;
        $limit = $i + $limit;
        do {
            $page = $HttpSocket->get($this->link . $i . '.488.html');
            $personData = $this->getPersonData($page->body, $i);
            $data[$i] = $personData;
            $i++;
            usleep(50000);
        } while ($i < $limit);
        return $data;
    }

    public function getContactsInfoFromDelegates($uids) {
        if (!empty($uids) && is_array($uids)) {
            $HttpSocket = new HttpSocket();
            foreach ($uids as $uid) {
                $page = $HttpSocket->get($this->link . $uid . '.245.html');
                $personData = $this->getPersonData($page->body, $uid);
                $data[$uid] = $personData;
                usleep(50000);
            }
            return $data;
        }
    }

    public function extractMembersParliamentMenu($data) {
        $formulaMenu = '/<div\sclass="side_menu">.*?(<\/div>)/msxi';
        $formulaMembersMenu = '/(?=Народни\sпосланици<\/a>).*?(Радна\sтела)/msxi';
        $folmulaLink = '/<a.*?(<\/a>)/msxi';
        if (preg_match($formulaMenu, $data, $matches)) {
            $result = trim(reset($matches));
            // pr($result);
            if (preg_match($formulaMembersMenu, $result, $matches)) {
                $result = trim(reset($matches));
                if (preg_match_all($folmulaLink, $result, $matches)) {
                    $result = reset($matches);
                    $result = $this->parseMenuLink($result);
                }
                return $result;
            }
        }
    }

    public function parseMenuLink($results) {
        if ($results) {

            $formulaHref = '/href=\'.*?(\')/i';
            $formulaHrefReplace = '/href=|\'/';
            $formulaUrlUids = '/\.\d+\.html/i';
            $formulaUrlUidsReplace = '/html|\./i';
            $formulaDate = '/\d{2}.*?(\d{4})/i';
            $formulaDateReplace = '/\./i';
            $data = array();
            foreach ($results as $result) {
                $date = $this->extractAndReplace($result, $formulaDate, $formulaDateReplace);
                if ($date) {
                    $date = CakeTime::format($date, '%Y-%m-%d');
                }
                $data[] = array(
                    'id' => $this->extractAndReplace($result, $formulaUrlUids, $formulaUrlUidsReplace),
                    'name' => strip_tags($result),
                    'start_date' => $date,
                    'url' => $this->extractAndReplace($result, $formulaHref, $formulaHrefReplace),
                    'menu_md5' => md5($result)
                );
            }
            //  return $data;

            $i = 0;
            while (($nu = array_shift($data)) !== NULL) {
                $tmpArray[$i] = $nu;
//                pr('$num');
//                pr($num);
                foreach ($data as $k => $p) {
                    if ($nu['url'] == $p['url']) {
                        $tmpArray[$i]['name'] = $p['name'];
                        unset($data[$k]);
//                            pr('$p');
//                            pr($p);
                    }
                }
                $i++;
            }
            return $tmpArray;
        }
    }

    public function getPersonData($body, $i) {

        $formulaPrint = '/<!--\sprint_start\s-->.*?(<!--\sprint_end\s-->)/msxi';
        $formulaName = '/<h2>.*?(<\/h2>)/msxi';
        $formulaImage = '/src=".*?(")/i';
        $formulaImageReplace = '/src=|"/';
        $formulaYear = '/<h4>Година.*?(<\/p>)/i';
        $formulaYearReplace = '/<h4.*?(h4>)|\./';
        $formulaOccupation = '/<h4>Занимање.*?(<\/p>)/i';
        $formulaElectoral = '/<h4>Изборна.*?(<\/p>)/i';

        $formulaH4Replace = '/<h4.*?(h4>)/';
        $formulaDateVerification = '/мандата<\/h4>.*?(<\/p>)/i';
        $formulaDateVerificationReplace = '/мандата/';

        $formulaBiography = '/<h3>Биографија.*?(<\/p>|<\/div>)/msxi';
        $formulaH3Replace = '/<h3.*?(h3>)/';
        $formulaWww = '/Адреса\sличног\sсајта.*?(<\/a>)/i';
        $formulaFacebook = '/Facebook\sналога.*?(<\/a>)/i';
        $formulaTwitter = '/Twitter\sналога.*?(<\/a>)/i';


        $data = array();
        if (preg_match($formulaPrint, $body, $matches)) {
            $result = reset($matches);
            App::import('Model', 'SerbianMpsDetail');
            $this->SerbianMpsDetail = new SerbianMpsDetail();
            $md5 = $this->SerbianMpsDetail->field('md5', array('SerbianMpsDetail.id' => $i), false);
            $newMd5 = md5($result);
            if ($md5) {
                $this->SerbianMpsDetail->hint($i);
            }
            if ($md5 == $newMd5) {
                return false;
            }
            $data['SerbianMpsDetail']['id'] = $i;
            $data['SerbianMpsDetail']['md5'] = $newMd5;
            $data['SerbianMpsDetail']['en_name'] = $this->getEnglishName($i);
            $data['SerbianMpsDetail']['name'] = $this->extractTrimStrip($result, $formulaName);
            $data['SerbianMpsDetail']['image'] = $this->extractAndReplace($result, $formulaImage, $formulaImageReplace);
            $data['SerbianMpsDetail']['year_of_birth'] = $this->extractAndAfterReplace($result, $formulaYear, $formulaYearReplace);
            $data['SerbianMpsDetail']['occupation'] = $this->extractAndAfterReplace($result, $formulaOccupation, $formulaH4Replace);
            $data['SerbianMpsDetail']['electoral_list'] = $this->extractAndAfterReplace($result, $formulaElectoral, $formulaH4Replace);
            $data['SerbianMpsDetail']['verification_mandate'] = CakeTime::format($this->extractAndAfterReplace($result, $formulaDateVerification, $formulaDateVerificationReplace), '%Y-%m-%d');
            $data['SerbianMpsDetail']['biography'] = $this->extractAndAfterReplace($result, $formulaBiography, $formulaH3Replace);
            $data['SerbianMpsDetail']['www'] = $this->extractLink($result, $formulaWww);
            $data['SerbianMpsDetail']['facebook'] = $this->extractLink($result, $formulaFacebook);
            $data['SerbianMpsDetail']['twitter'] = $this->extractLink($result, $formulaTwitter);
            $data['SerbianMpsDetail']['status'] = 0;

//SerbianResidence
            $formulaResidence = '/<h4>Место.*?(<\/p>)/i';
            $getResidence = $this->extractListsAndReplace($result, $formulaResidence, $formulaH4Replace, 'SerbianResidence');
            // $data['residence'] = $getResidence;
            $data['SerbianResidence'] = Set::extract('/id', $getResidence);

//SerbianParliamentaryGroup
            $formulaParliamentaryGroup = '/Посланичка\sгрупа.*?(<\/p>)/msxi';
            $formulaParliamentaryGroupReplace = '/Посланичка\sгрупа|<\/h4>/';
            $getGroup = $this->extractListsAndReplace($result, $formulaParliamentaryGroup, $formulaParliamentaryGroupReplace, 'SerbianParliamentaryGroup');

            $d = array();
            if ($getGroup) {
                App::import('Model', 'SerbianParliamentaryGroupFunc');
                $this->SerbianParliamentaryGroupFunc = new SerbianParliamentaryGroupFunc();
                $this->SerbianParliamentaryGroupFunc->deleteAll(array('serbian_mps_detail_id' => $i), false);
                foreach ($getGroup as $k => $v) {
                    if (!empty($v['shortcut'])) {
                        $d[] = array(
                            'serbian_mps_detail_id' => $i,
                            'serbian_parliamentary_group_id' => $v['id'],
                            'name' => $v['shortcut']
                        );
                        unset($getGroup[$k]['shortcut']);
                    }
                }
            }
            $data['SerbianParliamentaryGroupFunc'] = $d;
            //  $data['parliamentary_group'] = $getGroup;
            $data['SerbianParliamentaryGroup'] = Set::extract('/id', $getGroup);

//SerbianParty
            $formulaParliamentaryPoliticalParty = '/Политичка\sстранка.*?(<\/p>)/msxi';
            $formulaParliamentaryPoliticalPartyReplace = '/Политичка\sстранка|<\/h4>|\(.*\)/';
            $getParty = $this->extractListsAndReplace($result, $formulaParliamentaryPoliticalParty, $formulaParliamentaryPoliticalPartyReplace, 'SerbianParty');
            //  $data['party'] = $getParty;
            $data['SerbianParty'] = Set::extract('/id', $getParty);

//SerbianCommitte
            $formulaCommittee = '/Чланство\sу\sодборима.*?(<\/ul>)/msxi';
            $formulaCommitteeReplace = '/Чланство\sу\sодборима|<\/h4>/';
            $getCommitte = $this->extractListsAndReplace($result, $formulaCommittee, $formulaCommitteeReplace, 'SerbianCommitte');
            $d = array();
            if ($getCommitte) {
                App::import('Model', 'SerbianCommitteFunc');
                $this->SerbianCommitteFunc = new SerbianCommitteFunc();
                $this->SerbianCommitteFunc->deleteAll(array('serbian_mps_detail_id' => $i), false);
                foreach ($getCommitte as $k => $v) {
                    if (!empty($v['shortcut'])) {
                        $d[] = array(
                            'serbian_mps_detail_id' => $i,
                            'serbian_committe_id' => $v['id'],
                            'name' => $v['shortcut']
                        );
                        unset($getCommitte[$k]['shortcut']);
                    }
                }
            }
            $data['SerbianCommitteFunc'] = $d;
            //  $data['committe'] = $getCommitte;
            $data['SerbianCommitte'] = Set::extract('/id', $getCommitte);

//SerbianDelegationMembership
            $formulaDelegation = '/Чланство\sу\sделегацијама.*?(<\/ul>)/msxi';
            $formulaDelegationReplace = '/Чланство\sу\sделегацијама|<\/h4>/';
            $getDelegation = $this->extractListsAndReplace($result, $formulaDelegation, $formulaDelegationReplace, 'SerbianDelegationMembership');
            $d = array();
            if ($getDelegation) {
                App::import('Model', 'SerbianDelegationMembershipFunc');
                $this->SerbianDelegationMembershipFunc = new SerbianDelegationMembershipFunc();
                $this->SerbianDelegationMembershipFunc->deleteAll(array('serbian_mps_detail_id' => $i), false);
                foreach ($getDelegation as $k => $v) {
                    if (!empty($v['shortcut'])) {
                        $d[] = array(
                            'serbian_mps_detail_id' => $i,
                            'serbian_delegation_membership_id' => $v['id'],
                            'name' => $v['shortcut']
                        );
                        unset($getDelegation[$k]['shortcut']);
                    }
                }
            }
            $data['SerbianDelegationMembershipFunc'] = $d;
            //  $data['delegation'] = $getDelegation;
            $data['SerbianDelegationMembership'] = Set::extract('/id', $getDelegation);

//SerbianFriendship
            $formulaFriendship = '/Чланство\sу\sгрупама\sпријатељства.*?(<\/ul>)/msxi';
            $formulaFriendshipReplace = '/Чланство\sу\sгрупама\sпријатељства|<\/h4>/';
            $getFriendship = $this->extractListsAndReplace($result, $formulaFriendship, $formulaFriendshipReplace, 'SerbianFriendship');
            $d = array();
            if ($getFriendship) {
                App::import('Model', 'SerbianFriendshipFunc');
                $this->SerbianFriendshipFunc = new SerbianFriendshipFunc();
                $this->SerbianFriendshipFunc->deleteAll(array('serbian_mps_detail_id' => $i), false);
                foreach ($getFriendship as $k => $v) {
                    if (!empty($v['shortcut'])) {
                        $d[] = array(
                            'serbian_mps_detail_id' => $i,
                            'serbian_friendship_id' => $v['id'],
                            'name' => $v['shortcut']
                        );
                        unset($getFriendship[$k]['shortcut']);
                    }
                }
            }
            $data['SerbianFriendshipFunc'] = $d;
            //  $data['friendship'] = $getFriendship;
            $data['SerbianFriendship'] = Set::extract('/id', $getFriendship);

//SerbianFunction
            $formulaFunction = '/Функција\sу\sНародној\sскупштини\sРепублике\sСрбије.*?(<\/ul>)/msxi';
            $formulaFunctionReplace = '/Функција\sу\sНародној\sскупштини\sРепублике\sСрбије|<\/h4>/';
            $getFunction = $this->extractListsAndReplace($result, $formulaFunction, $formulaFunctionReplace, 'SerbianFunction');
            //  $data['function'] = $getFunction;
            $data['SerbianFunction'] = Set::extract('/id', $getFunction);
        }
        return $data;
    }

    public function getEnglishName($uid) {
        $formulaName = '/<div\sclass="optimize".*?(<\/h2>)/msxi';
        $HttpSocket = new HttpSocket();
        $page = $HttpSocket->get($this->link . $uid . '.488.html');
        if (preg_match($formulaName, $page->body, $matches)) {
            $result = trim(strip_tags(reset($matches)));
            $result = preg_replace('/\s+/', ' ', $result);
            // pr($result);
            return $result;
        }
        // $personData = $this->getPersonData($page->body, $uid);
        return null;
    }

    public function extractLink($data, $formula) {
        $formulaHref = '/href=\".*?(\")/i';
        $formulaHrefReplace = '/href=|"/';
        if (preg_match($formula, $data, $matches)) {
            $result = trim(reset($matches));
            return $this->extractAndReplace($result, $formulaHref, $formulaHrefReplace);
        }
    }

    public function extractListsAndReplace($data, $formula, $replace, $model) {

        $formulaLi = '/<li>.*?(<\/li>)/msxi';
        $newData = array();
        if (preg_match($formula, $data, $matches)) {
            $result = trim(reset($matches));
            if (preg_match_all($formulaLi, $result, $matches)) {
                $results = reset($matches);
                foreach ($results as $r) {
                    $newData[] = $this->combineLists($r, $replace, $model);
                }
            } else {
                $newData[] = $this->combineLists($result, $replace, $model);
            }



            return $newData;
        }
    }

    public function combineLists($result, $replace, $model) {

        $formulaHref = '/href=\".*?(\")/i';
        $formulaHrefReplace = '/href=|"/';
        $formulaUrlUids = '/\.\d+\.(?=\d)/i';
        $formulaUrlUidsReplace = '/html|\./i';
        $formulaShortcut = '/\(.*?(\))/i';
        $formulaShortcutReplace = '/\(|\)/i';
        $formulaShortcutRemove = '/\(.*\)/i';

        $uid = $this->extractAndReplace($result, $formulaUrlUids, $formulaUrlUidsReplace);
        $name = trim(strip_tags(preg_replace($replace, '', $result)));
        $name = trim(preg_replace($formulaShortcutRemove, '', $name));

        App::import('Model', $model);
        $this->$model = new $model();
        $getId = $this->$model->getIdFromUidAndName($uid, $name);
        if ($getId) {
            $this->$model->id = $getId;
        }
        $url = $this->extractAndReplace($result, $formulaHref, $formulaHrefReplace);
        $shortcut = $this->extractAndReplace($result, $formulaShortcut, $formulaShortcutReplace);

        $newData['id'] = $getId;
        $newData['uid'] = $uid;
        $newData['name'] = $name;
        if ($url) {
            $newData['url'] = $url;
            if ($getId) {
                $this->$model->saveField('url', $url);
            }
        }
        if ($shortcut) {
            $newData['shortcut'] = $shortcut;
            if ($model == 'SerbianParty' && $getId) {
                $this->$model->saveField('shortcut', $shortcut);
            }
        }
        return $newData;
    }

}
