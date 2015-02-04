<?php

$menu = array(
    $this->Html->link('back', '/serbian'),
    $this->Html->link('list index gets Serbia', '/serbian/listIndexPagin/sr'),
    $this->Html->link('list index gets English', '/serbian/listIndexPagin/en'),
    $this->Html->link('logs', '/serbian/listLogSpeches/'),
);
echo $this->Html->nestedList($menu);
?>