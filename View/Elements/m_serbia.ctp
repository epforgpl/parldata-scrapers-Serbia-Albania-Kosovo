<?php

$menu = array(
    $this->Html->link('Home', '/'),
    $this->Html->link('MP\'s party', '/serbian/getMpsParty'),
    $this->Html->link('MP\'s contact info', '/serbian/getMpsContact'),
    $this->Html->link('Plenary voting', '/serbian/getPlenaryVoting'),
    $this->Html->link('Plenary speeches', '/serbian/getPlenarySpeeches'),
    $this->Html->link('Schedules', '/serbian/getSchedules'),
    $this->Html->link('Serbia – Api', '/serbianApi'),
//    $this->Html->link('Kosowo', '/kosovan'),
);
echo $this->Html->nestedList($menu);
?>