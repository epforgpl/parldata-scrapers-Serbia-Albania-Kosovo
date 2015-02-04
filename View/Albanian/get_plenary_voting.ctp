<div class="actions">
    <?php
    echo $this->element('m_albanian_votings');
    ?>
</div>
<div class="posts form">
    <h2>Plenary Votings</h2>
    <?php
    if (!empty($pagin)) {
        pr($list);
    }
    if (!empty($content)) {
        pr($content);
    }
    ?>
</div>