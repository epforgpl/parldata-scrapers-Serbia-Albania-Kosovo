<?php

$menu = array(
    $this->Html->link('back', '/kosovan'),
    $this->Html->link('Mps', '/kosovan/mpsDelegate/'),
    $this->Html->link('Parliamentary Group', '/kosovan/mpsParliamentaryGroup/'),
    $this->Html->link('Party', '/kosovan/mpsParty/'),
    $this->Html->link('Committee', '/kosovan/mpsCommittee/'),
    $this->Html->link('logs', '/kosovan/listLogDelegate/'),
);
echo $this->Html->nestedList($menu);
?>