<?php

$menu = array(
    $this->Html->link('Home', '/'),
    $this->Html->link('MP\'s party', '/albanian/getMpsParty'),
    $this->Html->link('MP\'s contact info', '/albanian/getMpsContact'),
    $this->Html->link('Plenary voting', '/albanian/getPlenaryVoting'),
    $this->Html->link('Plenary speeches', '/albanian/getPlenarySpeeches'),
    $this->Html->link('Schedules', '/albanian/getSchedules'),
//    $this->Html->link('Albania', '/albanian'),
//    $this->Html->link('Kosowo', '/kosovan'),
);
echo $this->Html->nestedList($menu);
?>