<?php

$menu = array(
    $this->Html->link('back', '/albanian'),
);
echo $this->Html->nestedList($menu);
echo $this->Html->tag('h4', 'quelle list');
$menu = array(
    $this->Html->link('people', '/albanianApi/listQuele/people'),
    $this->Html->link('organizations', '/albanianApi/listQuele/organizations'),
    $this->Html->link('events', '/albanianApi/listQuele/events'),
    $this->Html->link('speeches', '/albanianApi/listQuele/speeches'),
    $this->Html->link('motions', '/albanianApi/listQuele/motions'),
    $this->Html->link('vote-events', '/albanianApi/listQuele/vote-events'),
    $this->Html->link('votes', '/albanianApi/listQuele/votes'),
    $this->Html->link('memberships', '/albanianApi/listQuele/memberships'),
);
echo $this->Html->nestedList($menu);
echo $this->Html->tag('h4', 'manual execute');
$menu = array(
    $this->Html->link('send to api', '/albanianApi/sendToApi'),
    $this->Html->link('organization Convocations', '/albanianApi/organizationConvocation'),
    $this->Html->link('people, party, commitete..', '/albanianApi/people'),
    $this->Html->link('speeches', '/albanianApi/speeches'),
//    $this->Html->link('organization Party', '/kosovanApi/organizationParty'),
//    $this->Html->link('organization Parliamentary groups', '/kosovanApi/organizationParliamentaryGroups'),
//    $this->Html->link('organization Committe', '/kosovanApi/organizationCommitte'),
//    $this->Html->link('votes', '/kosovanApi/votes'),
);
echo $this->Html->nestedList($menu);
?>