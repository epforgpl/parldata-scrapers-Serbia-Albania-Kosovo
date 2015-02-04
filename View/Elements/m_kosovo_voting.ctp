<?php

$menu = array(
    $this->Html->link('back', '/kosovan'),
    $this->Html->link('list Txts', '/kosovan/listTxts/'),
    $this->Html->link('logs', '/kosovan/listLogTxts/'),
);
echo $this->Html->nestedList($menu);
?>