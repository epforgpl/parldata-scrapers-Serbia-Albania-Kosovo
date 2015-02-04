<div class="actions">
    <?php
    echo $this->element('m_serbia_peeches');
    ?>
</div>
<div class="posts form">
    <h2>Plenary Speeches</h2>
    <?php
    if (!empty($combine)) {
        echo '<pre>';
        print_r($combine);
        echo '</pre>';
    }

    if (!empty($content)) {
        echo '<pre>';
        print_r($content);
        echo '</pre>';
    }
    ?>
</div>