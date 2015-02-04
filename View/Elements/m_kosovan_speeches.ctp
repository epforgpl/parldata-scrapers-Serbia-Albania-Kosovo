<?php

$menu = array(
    $this->Html->link('back', '/kosovan'),
    $this->Html->link('list index gets Kosovo', '/kosovan/listIndex/'),
    $this->Html->link('list Pdfs', '/kosovan/listPdf/'),
    $this->Html->link('logs', '/kosovan/listLogSpeches/'),
);
echo $this->Html->nestedList($menu);
?>