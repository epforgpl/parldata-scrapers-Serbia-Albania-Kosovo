<?php

$menu = array(
    $this->Html->link('back', '/albanian'),
    $this->Html->link('list Actual Deputed', '/albanian/listMpsActualDeputed/'),
    $this->Html->link('list Mp\'s details', '/albanian/listMpsDetails/'),
    $this->Html->link('logs', '/albanian/listLogMpsDetails/'),
);
echo $this->Html->nestedList($menu);
?>