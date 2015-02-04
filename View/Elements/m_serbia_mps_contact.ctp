<?php

$menu = array(
    $this->Html->link('back', '/serbian'),
    $this->Html->link('Mps', '/serbian/mpsDelegate/'),
    $this->Html->link('Parliamentary Group', '/serbian/mpsParliamentaryGroup/'),
    $this->Html->link('Party', '/serbian/mpsParty/'),
    $this->Html->link('Committe', '/serbian/mpsCommitte/'),
    $this->Html->link('Delegation Membership', '/serbian/mpsDelegationMembership/'),
    $this->Html->link('Friendship', '/serbian/mpsFriendship/'),
    $this->Html->link('Function', '/serbian/mpsFunction/'),
    $this->Html->link('Residence', '/serbian/mpsResidence/'),
    $this->Html->link('logs', '/serbian/listLogDelegate/'),
);
echo $this->Html->nestedList($menu);
?>