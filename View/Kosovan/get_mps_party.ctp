<div class="actions">
    <?php
    $menu[] = $this->Html->link('back', '/kosovan');
    foreach ($listMenu as $k => $m) {
        $menu[] = $this->Html->link($m, '/kosovan/listMpsIndex/' . $k);
    }
    $menu[] = $this->Html->link('logs', '/kosovan/listLogMpsIndex/');

    echo $this->Html->nestedList($menu);
    ?>
</div>
<div class="posts form">
    <h2>Plenary Speeches</h2>
    <?php
    if (!empty($pagin)) {
        pr($pagin);
    }
    if (!empty($content)) {
        pr($content);
    }
    ?>
</div>
