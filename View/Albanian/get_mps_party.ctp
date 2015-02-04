<div class="actions">
    <?php
    echo $this->element('m_albanian_mps_index');
    ?>
</div>
<div class="posts form">
    <h2>Plenary Speeches</h2>
    <?php
    if (!empty($pagin)) {
        pr($list);
    }
    if (!empty($content)) {
        pr($content);
    }
    ?>
</div>