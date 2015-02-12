<?php

App::uses('CakeTime', 'Utility');

class ScheduleShell extends Shell {

    public $tasks = array('Serbian', 'Kosovan', 'Albanian');
    public $uses = array(
        'Schedule',
    );

    function main() {
//        $now = CakeTime::toServer(time(), "- 84 minute");
//        $now = CakeTime::format('2011-08-22 11:53:00', '%B %e, %Y %H:%M %p');
//        $now = CakeTime::format('-84 minutes', '%Y-%m-%d %H:%M:%S');
//        $now = CakeTime::format('-' . 10 . ' minutes', '%Y-%m-%d %H:%M:%S');
//        return;
        //     echo $this->Albanian->check_mps_contacts($now);
//        echo $this->Kosovan->combine_pdfs(3);
//        echo $this->Albanian->get_doc_from_link(1);
//////        echo $this->Serbian->set_hint(9);
////        //   $this->out('lipa');
//        echo $this->Kosovan->check_mps_contacts($now, 5);
//        echo $this->Kosovan->get_mps_contacts(100);
//        return;
//        $fileFolder = WWW_ROOT . 'files' . DS . 'albania' . DS;
//        $fileName = $fileFolder . '123.doc';
//        $fileName2 = $fileFolder . '123.html';
//        $command = '/usr/bin/unoconv -c html ' . $fileName . ' 2>&1';
//        pr($command);
//        $output = shell_exec($command);
//        echo $output;
        #############################
//        echo $this->Kosovan->kosovo_combine_to_quelle(600);
//        echo $this->Serbian->combine_pdfs(30);
//        return;
//        echo $this->Albanian->albania_combine_to_quelle();
//        echo $this->Albanian->albania_send_to_quelle();
//        echo $this->Kosovan->kosovo_send_to_quelle();
//        echo $this->Serbian->serbia_send_to_quelle();
//////////////
////        echo $this->Serbian->serbia_combine_to_quelle();
//        return;

        $schedules = $this->Serbian->get_schedules();
        if ($schedules) {
            foreach ($schedules as $schedule) {
                $this->out('#### TASK ' . $schedule['Schedule']['task'] . ' ###');
                $now = CakeTime::format('-' . $schedule['Schedule']['interval'] . ' minutes', '%Y-%m-%d %H:%M:%S');
                switch ($schedule['Schedule']['task']) {
                    //Kosovan
                    case'get_index_list_page':
                        $this->out('#now ' . $now);
                        $this->out('#modified ' . $schedule['Schedule']['modified']);
                        if ($schedule['Schedule']['modified'] < $now) {
                            $this->out('var ' . $now);
                            echo $this->Kosovan->get_index_list_page();
                            echo $this->Schedule->hint($schedule['Schedule']['id']);
                        }
                        break;
                    case'get_mps_index':
                        $this->out('#now ' . $now);
                        $this->out('#modified ' . $schedule['Schedule']['modified']);
                        if ($schedule['Schedule']['modified'] < $now) {
                            $this->out('var ' . $now);
                            echo $this->Kosovan->get_mps_index();
                            echo $this->Schedule->hint($schedule['Schedule']['id']);
                        }
                        break;
                    case'get_kosovo_mps_contacts':
                        $this->out('#now ' . $now);
                        $this->out('#modified ' . $schedule['Schedule']['modified']);
                        if ($schedule['Schedule']['modified'] < $now) {
                            $this->out('var ' . $now);
                            echo $this->Kosovan->get_mps_contacts(10);
                            echo $this->Schedule->hint($schedule['Schedule']['id']);
                        }
                        break;
                    case'check_kosovo_mps_contacts':
                        $this->out('#now ' . $now);
                        $this->out('#modified ' . $schedule['Schedule']['modified']);
                        if ($schedule['Schedule']['modified'] < $now) {
                            $this->out('var ' . $now);
                            echo $this->Kosovan->check_mps_contacts($now, 5);
                            echo $this->Schedule->hint($schedule['Schedule']['id']);
                        }
                        break;
                    case'index_kosovo_plenary_speeches':
                        $this->out('#now ' . $now);
                        $this->out('#modified ' . $schedule['Schedule']['modified']);
                        if ($schedule['Schedule']['modified'] < $now) {
                            $this->out('var ' . $now);
                            echo $this->Kosovan->get_index();
                            echo $this->Schedule->hint($schedule['Schedule']['id']);
                        }
                        break;
                    case'get_content_kosovo_plenary_speeches':
                        $this->out('#now ' . $now);
                        $this->out('#modified ' . $schedule['Schedule']['modified']);
                        if ($schedule['Schedule']['modified'] < $now) {
                            $this->out('var ' . $now);
                            echo $this->Kosovan->get_content(30);
                            echo $this->Schedule->hint($schedule['Schedule']['id']);
                        }
                        break;
                    case'download_kosovo_txts':
                        $this->out('#$now ' . $now);
                        $this->out('#modified ' . $schedule['Schedule']['modified']);
                        if ($schedule['Schedule']['modified'] < $now) {
                            $this->out('var ' . $now);
                            echo $this->Kosovan->combine_txts(30);
                            echo $this->Schedule->hint($schedule['Schedule']['id']);
                        }
                        break;
                    case'download_kosovo_pdfs':
                        $this->out('#$now ' . $now);
                        $this->out('#modified ' . $schedule['Schedule']['modified']);
                        if ($schedule['Schedule']['modified'] < $now) {
                            $this->out('var ' . $now);
                            echo $this->Kosovan->combine_pdfs(3);
                            echo $this->Schedule->hint($schedule['Schedule']['id']);
                        }
                        break;
                    case'kosovo_combine_to_quelle':
                        $this->out('#$now ' . $now);
                        $this->out('#modified ' . $schedule['Schedule']['modified']);
                        if ($schedule['Schedule']['modified'] < $now) {
                            $this->out('var ' . $now);
                            echo $this->Kosovan->kosovo_combine_to_quelle();
                            echo $this->Schedule->hint($schedule['Schedule']['id']);
                        }
                        break;
                    case'kosovo_send_to_quelle':
                        $this->out('#$now ' . $now);
                        $this->out('#modified ' . $schedule['Schedule']['modified']);
                        if ($schedule['Schedule']['modified'] < $now) {
                            $this->out('var ' . $now);
                            echo $this->Kosovan->kosovo_send_to_quelle();
                            echo $this->Schedule->hint($schedule['Schedule']['id']);
                        }
                        break;
                    //Serbian
                    case'combine_mps_list_from_menu':
                        $this->out('#now ' . $now);
                        $this->out('#modified ' . $schedule['Schedule']['modified']);
                        if ($schedule['Schedule']['modified'] < $now) {
                            $this->out('var ' . $now);
                            echo $this->Serbian->get_mps_menu();
                            echo $this->Schedule->hint($schedule['Schedule']['id']);
                        }
                        break;
                    case'get_mps_tables':
                        $this->out('#now ' . $now);
                        $this->out('#modified ' . $schedule['Schedule']['modified']);
                        if ($schedule['Schedule']['modified'] < $now) {
                            $this->out('var ' . $now);
                            echo $this->Serbian->get_mps_tables();
                            echo $this->Schedule->hint($schedule['Schedule']['id']);
                        }
                        break;
                    case'get_mps_contact_from_table_delegates':
                        $this->out('#now ' . $now);
                        $this->out('#modified ' . $schedule['Schedule']['modified']);
                        if ($schedule['Schedule']['modified'] < $now) {
                            $this->out('var ' . $now);
                            echo $this->Serbian->get_mps_table_delegates(20);
                            echo $this->Schedule->hint($schedule['Schedule']['id']);
                        }
                        break;
                    case'check_mps_contacts':
                        $this->out('#now ' . $now);
                        $this->out('#modified ' . $schedule['Schedule']['modified']);
                        if ($schedule['Schedule']['modified'] < $now) {
                            $this->out('var ' . $now);
                            echo $this->Serbian->check_mps_contacts($now);
                            echo $this->Schedule->hint($schedule['Schedule']['id']);
                        }
                        break;
                    case'index_plenary_speeches':
                        $this->out('#now ' . $now);
                        $this->out('#modified ' . $schedule['Schedule']['modified']);
                        if ($schedule['Schedule']['modified'] < $now) {
                            $this->out('var ' . $now);
                            echo $this->Serbian->get_index();
                            echo $this->Schedule->hint($schedule['Schedule']['id']);
                        }
                        break;
                    case'get_content_plenary_speeches':
                        $this->out('#now ' . $now);
                        $this->out('#modified ' . $schedule['Schedule']['modified']);
                        if ($schedule['Schedule']['modified'] < $now) {
                            $this->out('var ' . $now);
                            echo $this->Serbian->get_content(50);
                            echo $this->Schedule->hint($schedule['Schedule']['id']);
                        }
                        break;
                    case'download_and_transform_pdfs':
                        $this->out('#$now ' . $now);
                        $this->out('#modified ' . $schedule['Schedule']['modified']);
                        if ($schedule['Schedule']['modified'] < $now) {
                            $this->out('var ' . $now);
                            echo $this->Serbian->combine_pdfs(30);
                            echo $this->Schedule->hint($schedule['Schedule']['id']);
                        }
                        break;
                    case'get_party_data':
                        $this->out('#$now ' . $now);
                        $this->out('#modified ' . $schedule['Schedule']['modified']);
                        if ($schedule['Schedule']['modified'] < $now) {
                            $this->out('var ' . $now);
                            echo $this->Serbian->get_party_data();
                            echo $this->Schedule->hint($schedule['Schedule']['id']);
                        }
                        break;
                    case'serbia_combine_to_quelle':
                        $this->out('#$now ' . $now);
                        $this->out('#modified ' . $schedule['Schedule']['modified']);
                        if ($schedule['Schedule']['modified'] < $now) {
                            $this->out('var ' . $now);
                            echo $this->Serbian->serbia_combine_to_quelle();
                            echo $this->Schedule->hint($schedule['Schedule']['id']);
                        }
                        break;
                    case'serbia_send_to_quelle':
                        $this->out('#$now ' . $now);
                        $this->out('#modified ' . $schedule['Schedule']['modified']);
                        if ($schedule['Schedule']['modified'] < $now) {
                            $this->out('var ' . $now);
                            echo $this->Serbian->serbia_send_to_quelle();
                            echo $this->Schedule->hint($schedule['Schedule']['id']);
                        }
                        break;

//                    Albanian
                    case'index_albania_plenary_speeches':
                        $this->out('#now ' . $now);
                        $this->out('#modified ' . $schedule['Schedule']['modified']);
                        if ($schedule['Schedule']['modified'] < $now) {
                            $this->out('var ' . $now);
                            echo $this->Albanian->get_index();
                            echo $this->Schedule->hint($schedule['Schedule']['id']);
                        }
                        break;
                    case'albania_get_doc_from_link':
                        $this->out('#now ' . $now);
                        $this->out('#modified ' . $schedule['Schedule']['modified']);
                        if ($schedule['Schedule']['modified'] < $now) {
                            $this->out('var ' . $now);
                            echo $this->Albanian->get_doc_from_link(3);
                            echo $this->Schedule->hint($schedule['Schedule']['id']);
                        }
                        break;
                    case'index_albania_vote':
                        $this->out('#now ' . $now);
                        $this->out('#modified ' . $schedule['Schedule']['modified']);
                        if ($schedule['Schedule']['modified'] < $now) {
                            $this->out('var ' . $now);
                            echo $this->Albanian->get_vote_index();
                            echo $this->Schedule->hint($schedule['Schedule']['id']);
                        }
                        break;
                    case'index_albania_mps':
                        $this->out('#now ' . $now);
                        $this->out('#modified ' . $schedule['Schedule']['modified']);
                        if ($schedule['Schedule']['modified'] < $now) {
                            $this->out('var ' . $now);
                            echo $this->Albanian->get_mps_index();
                            echo $this->Schedule->hint($schedule['Schedule']['id']);
                        }
                        break;
                    case'get_mps_details_from_index':
                        $this->out('#now ' . $now);
                        $this->out('#modified ' . $schedule['Schedule']['modified']);
                        if ($schedule['Schedule']['modified'] < $now) {
                            $this->out('var ' . $now);
                            echo $this->Albanian->get_mps_details_from_index(2);
                            echo $this->Schedule->hint($schedule['Schedule']['id']);
                        }
                        break;
                    case'check_mps_contacts':
                        $this->out('#now ' . $now);
                        $this->out('#modified ' . $schedule['Schedule']['modified']);
                        if ($schedule['Schedule']['modified'] < $now) {
                            $this->out('var ' . $now);
                            echo $this->Albanian->check_mps_contacts($now);
                            echo $this->Schedule->hint($schedule['Schedule']['id']);
                        }
                        break;
                    case'albania_combine_to_quelle':
                        $this->out('#$now ' . $now);
                        $this->out('#modified ' . $schedule['Schedule']['modified']);
                        if ($schedule['Schedule']['modified'] < $now) {
                            $this->out('var ' . $now);
                            echo $this->Albanian->albania_combine_to_quelle();
                            echo $this->Schedule->hint($schedule['Schedule']['id']);
                        }
                        break;
                    case'albania_send_to_quelle':
                        $this->out('#$now ' . $now);
                        $this->out('#modified ' . $schedule['Schedule']['modified']);
                        if ($schedule['Schedule']['modified'] < $now) {
                            $this->out('var ' . $now);
                            echo $this->Albanian->albania_send_to_quelle();
                            echo $this->Schedule->hint($schedule['Schedule']['id']);
                        }
                        break;
                }

                pr($schedules);
            }
        }
        // echo $this->Serbian->set_hint(1);
        // echo $this->Serbian->get_index();
        // echo $this->Serbian->get_content(20);
        //echo $this->Serbian->combine_pdfs();
    }

}
