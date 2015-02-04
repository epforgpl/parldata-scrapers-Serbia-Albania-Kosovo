<div class="actions">
    <?php
    echo $this->element('m_serbia_voting');
    ?>
</div>
<div class="posts form">
    <h2>Voting pdfs</h2>
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