<div class="actions">
    <?php
    $menu = array(
        $this->Html->link('back', $back),
    );
    echo $this->Html->nestedList($menu);
    ?>
</div>
<div class="posts form">
    <h2>Log Data</h2>
    <?php //pr($content); ?>
    <?php
    if (isset($content) && !empty($content)) {
        foreach ($content['SerbianLog'] as $key => $record) {
            echo $this->Html->tag('h4', $key);
            echo $this->Html->para(null, nl2br($record));
        }
    } else {
        echo $this->Html->tag('h3', 'Empty data');
    }
    ?>
</div>