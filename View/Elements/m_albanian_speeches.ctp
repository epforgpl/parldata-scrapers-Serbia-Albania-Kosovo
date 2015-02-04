<?php

$menu = array(
    $this->Html->link('back', '/albanian'),
    $this->Html->link('list index gets Speches', '/albanian/listIndex/'),
    $this->Html->link('logs', '/albanian/listLogSpeches/'),
);
echo $this->Html->nestedList($menu);
?>