<div class="actions">
    <?php
    echo $this->element('m_kosovan_api');
    ?>
</div>
<div class="posts form">
    <?php echo $this->Session->flash(); ?>
    <h2>membership Convocation: Kosovo – Api</h2>
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