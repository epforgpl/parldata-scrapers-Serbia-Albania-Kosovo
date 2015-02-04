<div class="actions">
    <?php
    echo $this->element('m_serbia_peeches');
    ?>
</div>
<div class="posts form">
    <h2>get data Pdf</h2>
    <?php //pr($content); ?>
    <?php
    if (isset($content) && !empty($content)) {
        foreach ($content['SerbianPdf'] as $key => $record) {
            echo $this->Html->tag('h4', $key);
            echo $this->Html->para(null, ($record != '' ? nl2br($record) : 'waiting for process...'));
        }
    } else {
        echo $this->Html->tag('h3', 'Empty data');
    }
    ?>
</div>