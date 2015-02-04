<div class="actions">
    <?php
    $menu = array(
        $this->Html->link('back', $back),
    );
    echo $this->Html->nestedList($menu);
    ?>
</div>
<div class="posts form">
    <h2>view Data</h2>
    <?php
    echo '<pre>';
    print_r($content);
    echo '</pre>';
    ?>
    <?php
    if (isset($content) && !empty($content)) {

    } else {
        echo $this->Html->tag('h3', 'Empty data');
    }
    ?>
</div>