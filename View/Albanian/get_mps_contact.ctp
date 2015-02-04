<div class="actions">
    <?php
    echo $this->element('m_albanian_mps_details');
    ?>
</div>
<div class="posts form">
    <h2>Mp's contact details</h2>
    <?php
    if (!empty($pagin)) {
        pr($list);
    }
    if (!empty($content)) {
        pr($content);
    }
    ?>
</div>