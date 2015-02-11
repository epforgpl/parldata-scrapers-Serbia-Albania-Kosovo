<?php

$menu = array(
    $this->Html->link('back', '/kosovan'),
);
echo $this->Html->nestedList($menu);
echo $this->Html->tag('h4', 'quelle list');
$menu = array(
    $this->Html->link('people', '/kosovanApi/listQuele/people'),
    $this->Html->link('organizations', '/kosovanApi/listQuele/organizations'),
    $this->Html->link('events', '/kosovanApi/listQuele/events'),
    $this->Html->link('speeches', '/kosovanApi/listQuele/speeches'),
    $this->Html->link('motions', '/kosovanApi/listQuele/motions'),
    $this->Html->link('vote-events', '/kosovanApi/listQuele/vote-events'),
    $this->Html->link('votes', '/kosovanApi/listQuele/votes'),
    $this->Html->link('memberships', '/kosovanApi/listQuele/memberships'),
//    $this->Html->link('logs', '/serbian/listLogSpeches/'),
);
echo $this->Html->nestedList($menu);
echo $this->Html->tag('h4', 'manual execute');
$menu = array(
    $this->Html->link('send to api', '/kosovanApi/sendToApi'),
    $this->Html->link('people', '/kosovanApi/people'),
    $this->Html->link('organization Convocations', '/kosovanApi/organizationConvocation'),
    $this->Html->link('organization Party', '/kosovanApi/organizationParty'),
    $this->Html->link('organization Parliamentary groups', '/kosovanApi/organizationParliamentaryGroups'),
    $this->Html->link('organization Committe', '/kosovanApi/organizationCommitte'),
    $this->Html->link('speeche events', '/kosovanApi/speecheEvents'),
    $this->Html->link('votes', '/kosovanApi/votes'),
    $this->Html->link('speeches', '/kosovanApi/speeches'),
);
echo $this->Html->nestedList($menu);
?>