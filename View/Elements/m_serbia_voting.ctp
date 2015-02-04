<?php

$menu = array(
    $this->Html->link('back', '/serbian'),
    $this->Html->link('list Pdfs', '/serbian/listPdfs/'),
    $this->Html->link('logs', '/serbian/listLogPdfs/'),
);
echo $this->Html->nestedList($menu);
?>