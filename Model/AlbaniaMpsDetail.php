<?php

class AlbaniaMpsDetail extends AppModel {

    public function combineToApiArray($data) {

        $name = trim($data['AlbaniaMpsDetail']['name']);
        $nname['people']['id'] = 'mp_' . $this->toCamelCase($name);


        $name = preg_replace('/\s\-\s/', '-', $name);
        $name = preg_replace('/\,/', '', $name);
        $name = preg_replace('/\s+/', ' ', $name);
        $tmpName = explode(' ', $name);
        $familyName = $this->toCamelCase(array_shift($tmpName));
        $givenName = null;
        if (count($tmpName) && is_array($tmpName)) {
            foreach ($tmpName as $tn) {
                $givenName .= ' ' . $tn;
            }
        }
        $givenName = trim($givenName);
        $nname['people']['name'] = $givenName . ' ' . $familyName;
        $nname['people']['given_name'] = $givenName;
        $nname['people']['family_name'] = trim($familyName);
        $nname['people']['image'] = $data['AlbaniaMpsDetail']['image'];
        if (!empty($data['AlbaniaMpsDetail']['year_of_birth'])) {
            $nname['people']['birth_date'] = $data['AlbaniaMpsDetail']['year_of_birth'];
        }
        if (!empty($data['AlbaniaMpsDetail']['url'])) {
            $nname['people']['sources'] = array(
                array(
                    'url' => $data['AlbaniaMpsDetail']['url'],
                )
            );
        }
        $contact_details = array();
        if (!empty($data['AlbaniaMpsDetail']['email'])) {
            $contact_details[] = array(
                'label' => 'Email',
                'type' => 'email',
                'value' => $data['AlbaniaMpsDetail']['email']
            );
        }
        if (!empty($data['AlbaniaMpsDetail']['address'])) {
            $address = strip_tags($data['AlbaniaMpsDetail']['address']);
            $address = preg_replace('/\s+/', ' ', $address);
            $contact_details[] = array(
                'label' => 'Address',
                'type' => 'address',
                'value' => $address
            );
        }
        if (!empty($contact_details)) {
            $nname['people']['contact_details'] = $contact_details;
        }
        $biography = null;
        if (!empty($data['AlbaniaMpsDetail']['education'])) {
            $biography .= '<section class=”education”><h1>Edukimi</h1><p>' . trim($data['AlbaniaMpsDetail']['education']) . '</p></section>';
        }
        if (!empty($data['AlbaniaMpsDetail']['professional_activity'])) {
            $biography .= '<section class=”professional_activity”><h1>Veprimtaria profesionale</h1>' . trim($data['AlbaniaMpsDetail']['professional_activity']) . '</p></section>';
        }
        if (!empty($data['AlbaniaMpsDetail']['political_activity'])) {
            $biography .= '<section class=”political_activity”><h1>Veprimtaria politike</h1>' . trim($data['AlbaniaMpsDetail']['political_activity']) . '</p></section>';
        }
        if (!empty($data['AlbaniaMpsDetail']['opting_in'])) {
            $biography .= '<section class=”electoral_circuit”><h1>Zgjedhur në:</h1>' . trim($data['AlbaniaMpsDetail']['opting_in']) . '</p></section>';
        }
        $nname['people']['biography'] = $biography;
        $toSend['people'][] = $nname;

        ///
        if (!empty($data['AlbaniaMpsDetail']['group_parliamentary_committees'])) {
            $group = $this->getParlamentaryGroup($data['AlbaniaMpsDetail']['group_parliamentary_committees']);
            if ($group) {
                $groupId = 'parliamentary_group_' . $group['id'];
                $g['organizations']['id'] = $groupId;
                $g['organizations']['name'] = $group['name'];
                $g['organizations']['classification'] = 'parliamentary_group';

                $m['memberships']['id'] = $groupId . '-' . $nname['people']['id'];
                $m['memberships']['label'] = 'MP';
                $m['memberships']['person_id'] = $nname['people']['id'];
                $m['memberships']['organization_id'] = $groupId;
                $toSend['organizations'][] = $g;
                $toSend['memberships'][] = $m;
            }
            $committee = $this->getCommittee($data['AlbaniaMpsDetail']['group_parliamentary_committees']);
            if ($committee) {
                $g = $m = null;
                foreach ($committee as $com) {
                    $comId = 'committee_' . $com['id'];
                    $g['organizations']['id'] = $comId;
                    $g['organizations']['name'] = $com['name'];
                    $g['organizations']['classification'] = 'committee';

                    $m['memberships']['id'] = $comId . '-' . $nname['people']['id'];
                    $m['memberships']['label'] = 'MP';
                    $m['memberships']['person_id'] = $nname['people']['id'];
                    $m['memberships']['organization_id'] = $comId;
                    $toSend['organizations'][] = $g;
                    $toSend['memberships'][] = $m;
                }
            }
            if (!empty($data['AlbaniaMpsDetail']['opting_in'])) {
                $chamber = $this->findAllAlbaniaChamberFromContent($data['AlbaniaMpsDetail']['opting_in']);

                if ($chamber) {
                    $g = $m = null;
                    foreach ($chamber as $ch) {
                        if (is_array($ch)) {
//                            pr($ch);
                            $m['memberships']['id'] = 'chamber_' . $ch['AlbaniaChamber']['name'] . '-' . $nname['people']['id'];
                            $m['memberships']['label'] = 'Deputet';
                            $m['memberships']['person_id'] = $nname['people']['id'];
                            $m['memberships']['organization_id'] = 'chamber_' . $ch['AlbaniaChamber']['name'];
                            if (!empty($ch['AlbaniaChamber']['start_date'])) {
                                $m['memberships']['start_date'] = $ch['AlbaniaChamber']['start_date'];
                            }
                            if (!empty($ch['AlbaniaChamber']['end_date'])) {
                                $m['memberships']['end_date'] = $ch['AlbaniaChamber']['end_date'];
                            }
                        } else {
                            $m['memberships']['id'] = $ch . '-' . $nname['people']['id'];
                            $m['memberships']['label'] = 'Deputet';
                            $m['memberships']['person_id'] = $nname['people']['id'];
                            $m['memberships']['organization_id'] = $ch;
                        }
                        $toSend['memberships'][] = $m;
                    }
                }
            }
            if (!empty($data['AlbaniaMpsDetail']['political_activity'])) {
                $party = $this->getParty($data['AlbaniaMpsDetail']['political_activity']);
//                pr($party);
                if ($party) {
                    $g = $m = null;
                    foreach ($party as $pr) {
                        $prId = 'party_' . $pr['id'];
                        $g['organizations']['id'] = $prId;
                        $g['organizations']['name'] = $pr['name'];
                        $g['organizations']['classification'] = 'party';

                        $m['memberships']['id'] = $prId . '-' . $nname['people']['id'];
                        $m['memberships']['label'] = $pr['label'];
                        $m['memberships']['person_id'] = $nname['people']['id'];
                        $m['memberships']['organization_id'] = $prId;
                        if (isset($pr['start_date']) && !empty($pr['start_date'])) {
                            $m['memberships']['start_date'] = $pr['start_date'];
                        }
                        if (isset($pr['end_date']) && !empty($pr['end_date'])) {
                            $m['memberships']['end_date'] = $pr['end_date'];
                        }
                        $toSend['organizations'][] = $g;
                        $toSend['memberships'][] = $m;
                    }
                }
            }
        }





        return $toSend;
    }

    public function getParty($content) {
        $formulaParty = '/Partia.*?(\,|$)/';
        $formulaDate = '/\d{4}/';
        $results = explode("<br />", $content);
        if ($results) {
            $p = array();
            foreach ($results as $k => $r) {
                $pt = $this->extractTrimStrip($r, $formulaParty);
                if ($pt) {
                    $p[$k]['id'] = $this->toCamelCase($pt);
                    $p[$k]['name'] = $pt;
                    $dates = $this->extractMoreAndStripTrim($r, $formulaDate);
                    if (is_array($dates) && !empty($dates)) {
                        if (isset($dates[0]) && !empty($dates[0])) {
                            $p[$k]['start_date'] = $dates[0];
                        }
                        if (isset($dates[1]) && !empty($dates[1])) {
                            $p[$k]['end_date'] = $dates[1];
                        }
                    }
                    if (strpos($r, 'Kryetar') !== false) {
                        $p[$k]['label'] = 'Kryetar';
                    } else {
                        $p[$k]['label'] = 'Anëtar';
                    }
                }
            }
            return($p);
        }
    }

    public function getParlamentaryGroup($content) {
        $formulaParlamentaryGroup = '/parlamentar\:.*?(<br)/';
        $formulaParlamentaryGroupReplace = '/parlamentar\:|<br|\./';
        $group['name'] = trim($this->extractAndReplace($content, $formulaParlamentaryGroup, $formulaParlamentaryGroupReplace));
        $group['id'] = trim($this->toCamelCase($group['name']));
        return $group;
    }

    public function getCommittee($content) {
        $formulaCommittee = '/(?=\/>).*$/msx';
        $formulaCommitteeReplace = '/\/>|<br/';
        $formulaRole = '/anëtar/';

        $data = trim($this->extractAndReplace($content, $formulaCommittee, $formulaCommitteeReplace));
        $data = explode('.', $data);
        $ndata = array();
        foreach ($data as $d) {
            if (!empty($d)) {
                $name = trim(preg_replace($formulaRole, '', $d));
                $name = trim(preg_replace('/\,$/', '', $name));
                $ndata[] = array(
                    'role' => trim($this->extractTrimStrip($d, $formulaRole)),
                    'name' => $name,
                    'id' => trim($this->toCamelCase($name)),
                );
            }
        }
        return $ndata;
    }

}
