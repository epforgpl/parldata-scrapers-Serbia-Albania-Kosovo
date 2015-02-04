<?php

$menu = array(
    $this->Html->link('back', '/albanian'),
    $this->Html->link('list index gets Votings', '/albanian/listVoteIndex/'),
    $this->Html->link('logs', '/albanian/listLogVoteSpeches/'),
);
echo $this->Html->nestedList($menu);
?>