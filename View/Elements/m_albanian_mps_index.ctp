<?php

$menu = array(
    $this->Html->link('back', '/albanian'),
    $this->Html->link('list index Mp\'s', '/albanian/listMpsIndex/'),
    $this->Html->link('logs', '/albanian/listLogMps/'),
);
echo $this->Html->nestedList($menu);
?>