<?php

$menu = array(
    $this->Html->link('back', '/serbian'),
);
echo $this->Html->nestedList($menu);
echo $this->Html->tag('h4', 'quelle list');
$menu = array(
    $this->Html->link('back', '/serbian'),
    $this->Html->link('people', '/serbianApi/listQuele/people'),
    $this->Html->link('organizations', '/serbianApi/listQuele/organizations'),
    $this->Html->link('events', '/serbianApi/listQuele/events'),
    $this->Html->link('speeches', '/serbianApi/listQuele/speeches'),
    $this->Html->link('motions', '/serbianApi/listQuele/motions'),
    $this->Html->link('vote-events', '/serbianApi/listQuele/vote-events'),
    $this->Html->link('votes', '/serbianApi/listQuele/votes'),
    $this->Html->link('memberships', '/serbianApi/listQuele/memberships'),
//    $this->Html->link('logs', '/serbian/listLogSpeches/'),
);
echo $this->Html->nestedList($menu);
echo $this->Html->tag('h4', 'manual execute');
$menu = array(
    $this->Html->link('send to api', '/serbianApi/sendToApi'),
    $this->Html->link('people', '/serbianApi/people'),
    $this->Html->link('organization Convocations', '/serbianApi/organizationConvocation'),
    $this->Html->link('organization Party', '/serbianApi/organizationParty'),
    $this->Html->link('organization Parliamentary groups', '/serbianApi/organizationParliamentaryGroups'),
    $this->Html->link('organization Committe', '/serbianApi/organizationCommitte'),
    $this->Html->link('organization Delegation', '/serbianApi/organizationDelegation'),
    $this->Html->link('organization Friendship', '/serbianApi/organizationFriendship'),
    $this->Html->link('organization Deputy $ Speaker', '/serbianApi/organizationSpeaker'),
    $this->Html->link('membership Convocations', '/serbianApi/membershipConvocation'),
    $this->Html->link('speeches', '/serbianApi/speeches'),
    $this->Html->link('votes', '/serbianApi/votes'),
);
echo $this->Html->nestedList($menu);
?>