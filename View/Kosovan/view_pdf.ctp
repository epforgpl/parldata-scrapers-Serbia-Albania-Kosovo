<div class="actions">
    <?php
    echo $this->element('m_kosovan_speeches');
    ?>
</div>
<div class="posts form">
    <?php //pr($content); ?>
    <?php
    if (isset($content) && !empty($content)) {
        echo $this->Html->tag('h2', 'KosovoPdf');
        foreach ($content['KosovoPdf'] as $key => $record) {
            echo $this->Html->tag('h4', $key);
            echo $this->Html->para(null, ($record != '' ? nl2br($record) : 'waiting for process...'));
        }
        if (isset($content) && !empty($content)) {
            echo $this->Html->tag('h2', 'KosovoSpeecheContent');
            foreach ($content['KosovoSpeecheContent'] as $key => $record) {
                echo $this->Html->tag('h4', $key);
                echo $this->Html->para(null, ($record != '' ? nl2br($record) : 'waiting for process...'));
            }
        }
    } else {
        echo $this->Html->tag('h3', 'Empty data');
    }
    ?>
</div>