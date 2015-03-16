<?php

App::uses('HttpSocket', 'Network/Http');
App::uses('CakeTime', 'Utility');

class SerbianParty extends AppModel {

    public $hasAndBelongsToMany = array(
        'SerbianMpsDetail'
    );
    public $partyLink;

    public function getIdFromUidAndName($uid, $name) {
        $conditions = array('SerbianParty.name' => $name);
        if (!empty($uid)) {
            $conditions = array('SerbianParty.uid' => $uid);
        }
        $result = $this->find('first', array(
            'fields' => array('SerbianParty.id'),
            'conditions' => $conditions,
            'recursive' => -1
        ));

        if (!$result) {
            $this->create();
            $this->set(array(
                'uid' => $uid,
                'name' => $name,
            ));
            $this->save();
            return $this->getLastInsertID();
        }
        return $result['SerbianParty']['id'];
    }

    public function getContactsInfoUids($uids) {
        $formulaPartyBlock = '/<div\sclass="block">.*(?=<div\sclass="actualities\sbordered">)/msxi';
        if (count($uids) && is_array($uids)) {
            $ndata = array();

            foreach ($uids as $id => $uid) {
                $this->partyLink = 'http://www.parlament.gov.rs/%D0%BD%D0%B0%D1%80%D0%BE%D0%B4%D0%BD%D0%B0-%D1%81%D0%BA%D1%83%D0%BF%D1%88%D1%82%D0%B8%D0%BD%D0%B0/%D1%81%D0%B0%D1%81%D1%82%D0%B0%D0%B2/%D0%BF%D0%BE%D0%BB%D0%B8%D1%82%D0%B8%D1%87%D0%BA%D0%B5-%D1%81%D1%82%D1%80%D0%B0%D0%BD%D0%BA%D0%B5/%D1%81%D0%B0%D0%B7%D0%B8%D0%B2-%D0%BE%D0%B4-11-%D1%98%D1%83%D0%BD%D0%B0-2008-%D0%B3%D0%BE%D0%B4%D0%B8%D0%BD%D0%B5.' . $uid . '.1562.html';
                $HttpSocket = new HttpSocket();
                $page = $HttpSocket->get($this->partyLink);
                $body = ($page->body);
                if (preg_match($formulaPartyBlock, $body, $matches)) {
                    $result = reset($matches);
                    $check = trim(strip_tags($result));
                    if (!empty($check)) {
                        $data[] = $this->getDataParty($result, $id);
                    }
                }
            }
            return $data;
        }
        return false;
    }

    public function getDataParty($result, $id) {
//        pr($result);
        $formulaEmail = "/[\w.-]+@[\w.-]+\.[A-Za-z]{2,6}/i";
        $formulaWww = '/http:\/\/.*?(">)/i';
        $formulaWwwReplace = '/http:|\/|"|>|target=|_blank/i';
        $formulaAddress = '/адреса:.*?(<br\/>)/i';
        $formulaAddressReplace = '/адреса:/i';
        $formulaImage = '/src=".*?(")/i';
        $formulaImageReplace = '/src=|"/';
        $formulaPhone = '/телефон:.*?(<b>)/i';
        $formulaPhoneReplace = '/телефон:|<br\/>/i';
        $formulaFax = '/<b>факс:.*?(<br\/>)/i';
        $formulaFaxReplace = '/факс:|\//i';
        $formulaPhoneFax = '/телефон.\/.факс:.*?(<br\/>)/i';
        $formulaPhoneFaxReplace = '/факс|телефон|:|\//i';

        $data = array(
            'id' => $id,
            'email' => $this->extractTrimStrip($result, $formulaEmail),
            'www' => trim($this->extractAndReplace($result, $formulaWww, $formulaWwwReplace)),
            'address' => trim($this->extractAndReplace($result, $formulaAddress, $formulaAddressReplace)),
            'image' => trim($this->extractAndReplace($result, $formulaImage, $formulaImageReplace)),
            'phone' => trim($this->extractAndAfterReplace($result, $formulaPhone, $formulaPhoneReplace, ' ')),
            'fax' => trim($this->extractAndReplace($result, $formulaFax, $formulaFaxReplace)),
            'phone_fax' => trim($this->extractAndReplace($result, $formulaPhoneFax, $formulaPhoneFaxReplace)),
            'sources' => $this->partyLink
        );
        return $data;
    }

    public function combineToApiArray($data) {
        $i = 0;
        $partyId = 'party_' . $data['SerbianParty']['uid'];
        $party[$i]['organizations']['id'] = $partyId;
        $party[$i]['organizations']['name'] = $data['SerbianParty']['name'];
        $party[$i]['organizations']['classification'] = 'party';
        if (!empty($data['SerbianParty']['shortcut'])) {
            $party[$i]['organizations']['other_names'] = array(
                array(
                    'name' => $data['SerbianParty']['shortcut'],
                    'note' => 'shortcut'
                )
            );
        }
        if (!empty($data['SerbianParty']['image'])) {
            $party[$i]['organizations']['image'] = $this->getSerbiaHost . $data['SerbianParty']['image'];
        }

        if (!empty($data['SerbianParty']['sources'])) {
            $party[$i]['organizations']['sources'] = array(
                array(
                    'url' => $data['SerbianParty']['sources'],
                )
            );
        }
        $contact_details = array();
        if (!empty($data['SerbianParty']['email'])) {
            $contact_details[] = array(
                'label' => 'Email',
                'type' => 'email',
                'value' => $data['SerbianParty']['email']
            );
        }
        if (!empty($data['SerbianParty']['www'])) {
            $contact_details[] = array(
                'label' => 'Website',
                'type' => 'url',
                'value' => 'http://' . $data['SerbianParty']['www']
            );
        }
        if (!empty($data['SerbianParty']['address'])) {
            $contact_details[] = array(
                'label' => 'Address',
                'type' => 'address',
                'value' => $data['SerbianParty']['address']
            );
        }
        if (!empty($data['SerbianParty']['phone'])) {
            $contact_details[] = array(
                'label' => 'Phone',
                'type' => 'tel',
                'value' => $data['SerbianParty']['phone']
            );
        }
        if (!empty($data['SerbianParty']['fax'])) {
            $contact_details[] = array(
                'label' => 'Fax',
                'type' => 'tel',
                'value' => $data['SerbianParty']['fax']
            );
        }
        if (!empty($data['SerbianParty']['phone_fax'])) {
            $contact_details[] = array(
                'label' => 'Phone / Fax',
                'type' => 'fax',
                'value' => $data['SerbianParty']['phone_fax']
            );
        }
        if (!empty($contact_details)) {
            $party[$i]['organizations']['contact_details'] = $contact_details;
        }
        $l = $i;
        if (isset($data['SerbianMpsDetail']) && !empty($data['SerbianMpsDetail'])) {
            foreach ($data['SerbianMpsDetail'] as $key => $mp) {
                $i++;
//                $person = $i;
                $person = $this->checkPeopleExist($mp['name']);
                $party[$i]['memberships']['id'] = $partyId . '-' . $person;
                $party[$i]['memberships']['label'] = 'MP';
                $party[$i]['memberships']['person_id'] = $person;
                $party[$i]['memberships']['organization_id'] = $partyId;
                $party[$i]['memberships']['sources'] = array(
                    array(
                        'url' => 'http://www.parlament.gov.rs/national-assembly/composition/members-of-parliament.' . $mp['id'] . '.245.html',
                    )
                );
            }
        }
        return $party;
    }

}
