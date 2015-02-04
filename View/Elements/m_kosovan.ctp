<?php

$menu = array(
    $this->Html->link('Home', '/'),
    $this->Html->link('MP\'s party', '/kosovan/getMpsParty'),
    $this->Html->link('MP\'s contact info', '/kosovan/getMpsContact'),
    $this->Html->link('Plenary voting', '/kosovan/getPlenaryVoting'),
    $this->Html->link('Plenary speeches', '/kosovan/getPlenarySpeeches'),
    $this->Html->link('Schedules', '/kosovan/getSchedules'),
    $this->Html->link('Kosovo - Api', '/kosovanApi'),
//    $this->Html->link('Kosowo', '/kosovan'),
);
echo $this->Html->nestedList($menu);
?>