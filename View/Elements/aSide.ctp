<?php

$menu = array(
    $this->Html->link('Serbia', '/serbian'),
    $this->Html->link('Albania', '/albanian'),
    $this->Html->link('Kosowo', '/kosovan'),
);
echo $this->Html->nestedList($menu);
?>