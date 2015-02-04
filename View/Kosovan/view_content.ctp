<div class="actions">
    <?php
    echo $this->element('m_kosovo_voting');
    ?>
</div>
<div class="posts form">
    <?php //pr($content); ?>
    <?php
    if (isset($content) && !empty($content)) {

        echo $this->Html->tag('h2', 'KosovoSpeecheContent');
        foreach ($content['KosovoSpeecheContent'] as $key => $record) {
            echo $this->Html->tag('h4', $key);
            echo $this->Html->para(null, ($record != '' ? nl2br($record) : 'waiting for process...'));
        }

        echo $this->Html->tag('h2', 'KosovoPdf');
        foreach ($content['KosovoPdf'] as $kosovoPdf) {
            foreach ($kosovoPdf as $key => $record) {
                echo $this->Html->tag('h4', $key);
                echo $this->Html->para(null, ($record != '' ? nl2br($record) : 'waiting for process...'));
            }
        }
        ?>

        <h2>Voting txts</h2>
        <?php if (isset($content['KosovoTxt']) && !empty($content['KosovoTxt'])): ?>
            <table cellpadding="0" cellspacing="0">
                <?php
                $tableHeaders = $this->Html->tableHeaders(array(
                    __('id'),
                    __('kosovo_speeche_content_id'),
                    __('txt_url'),
                    __('content_sr'),
                    // $this->Paginator->sort('content_en'),
                    __('status'),
                    __('created'),
                    __('modified'),
                ));
                echo $tableHeaders;

                $rows = array();
                foreach ($content['KosovoTxt'] AS $record) {
                    $name_sr = $name_en = $content_sr = $content_en = null;

                    if (!empty($record['content_sr'])) {
                        $content_sr = $this->Text->truncate(
                                strip_tags($record['content_sr']), 150, array(
                            'ellipsis' => '...',
                            'exact' => false
                                )
                        );
                    }
                    if (!empty($record['content_en'])) {
                        $content_en = $this->Text->truncate(
                                strip_tags($record['content_en']), 150, array(
                            'ellipsis' => '...',
                            'exact' => false
                                )
                        );
                    }

                    $rows[] = array(
                        $record['id'],
                        $this->Html->link('view', '/kosovan/viewTxt/' . $record['id']),
                        $this->Html->link('link', $kosovoHost . $record['txt_url'], array('target' => '_blanc')),
                        !empty($content_sr) ? $content_sr : 'wait..',
                        //  !empty($content_en) ? $content_en : 'wait..',
                        $record['status'],
                        $record['created'],
                        $record['modified'],
                    );
                }

                echo $this->Html->tableCells($rows);
                //echo $tableHeaders;
                ?>
            </table>
        <?php else: ?>
            <h3>Empty data</h3>
        <?php endif; ?>
        <?php
    } else {
        echo $this->Html->tag('h3', 'Empty data');
    }
    ?>
</div>