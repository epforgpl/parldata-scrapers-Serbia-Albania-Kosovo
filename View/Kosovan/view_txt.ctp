<div class="actions">
    <?php
    echo $this->element('m_kosovo_voting');
    ?>
</div>
<div class="posts form">
    <h2>get data Txt</h2>
    <?php //pr($content); ?>
    <?php
    if (isset($content) && !empty($content)) {
        echo $this->Html->tag('h2', 'KosovoTxt');
        foreach ($content['KosovoTxt'] as $key => $record) {
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