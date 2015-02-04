<div class="actions">
    <?php
    $menu[] = $this->Html->link('back', '/serbian');
    foreach ($listMenu as $k => $m) {
        $menu[] = $this->Html->link($m, '/serbian/listTable/' . $k);
    }
    $menu[] = $this->Html->link('logs', '/serbian/listLogTable/');

    echo $this->Html->nestedList($menu);
    ?>
</div>
<div class="posts form">
    <h2>Mp's party table</h2>
    <!--<h2>Plenary Speeches</h2>-->
    <?php
    echo $this->Html->tag('h2', 'combined Api data');
    if (!empty($combine)) {
        echo '<pre>';
        print_r($combine);
        echo '</pre>';
    }
    echo $this->Html->tag('h2', 'scrapper data');
    if (!empty($content)) {
        echo '<pre>';
        print_r($content);
        echo '</pre>';
    }
    ?>

</div>