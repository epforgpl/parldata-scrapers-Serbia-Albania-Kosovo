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
    <?php
    echo '<pre>';
    print_r($content);
    echo '</pre>';
    echo $this->Html->tag('h3', 'unserialize');
    echo '<pre>';
    print_r(unserialize($content['QueleToSend']['data']));
    echo '</pre>';
    echo $this->Html->tag('h3', 'json');
    echo '<pre>';
    print_r(json_encode(unserialize($content['QueleToSend']['data'])));
    echo '</pre>';
    ?>
</div>