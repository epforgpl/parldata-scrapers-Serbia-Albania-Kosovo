<div class="actions">
    <?php
    echo $this->element('m_kosovo_voting');
    ?>
</div>
<div class="posts form">
    <h2>Voting txts</h2>
    <?php //pr($txts); ?>
    <?php if (isset($txts) && !empty($txts)): ?>
        <table cellpadding="0" cellspacing="0">
            <?php
            $tableHeaders = $this->Html->tableHeaders(array(
                $this->Paginator->sort('id'),
                $this->Paginator->sort('kosovo_speeche_content_id'),
                $this->Paginator->sort('txt_url'),
                $this->Paginator->sort('content_sr'),
                // $this->Paginator->sort('content_en'),
                $this->Paginator->sort('status'),
                $this->Paginator->sort('created'),
                $this->Paginator->sort('modified'),
            ));
            echo $tableHeaders;

            $rows = array();
            foreach ($txts AS $record) {
                $name_sr = $name_en = $content_sr = $content_en = null;

                if (!empty($record['KosovoTxt']['content_sr'])) {
                    $content_sr = $this->Text->truncate(
                            strip_tags($record['KosovoTxt']['content_sr']), 150, array(
                        'ellipsis' => '...',
                        'exact' => false
                            )
                    );
                }
                if (!empty($record['KosovoTxt']['content_en'])) {
                    $content_en = $this->Text->truncate(
                            strip_tags($record['KosovoTxt']['content_en']), 150, array(
                        'ellipsis' => '...',
                        'exact' => false
                            )
                    );
                }

                $rows[] = array(
                    $record['KosovoTxt']['id'],
                    $this->Html->link($record['KosovoSpeecheContent']['title'] . ' - ' . $record['KosovoSpeecheContent']['id'], '/kosovan/viewTxt/' . $record['KosovoTxt']['id']),
                    $this->Html->link('link', $kosovoHost . $record['KosovoTxt']['txt_url'], array('target' => '_blanc')),
                    !empty($content_sr) ? $content_sr : 'wait..',
                    //  !empty($content_en) ? $content_en : 'wait..',
                    $record['KosovoTxt']['status'],
                    $record['KosovoTxt']['created'],
                    $record['KosovoTxt']['modified'],
                );
            }

            echo $this->Html->tableCells($rows);
            //echo $tableHeaders;
            ?>
        </table>
        <div class="paging"><?php echo $this->Paginator->numbers(); ?></div>
        <div class="counter">
            <?php
            echo $this->Paginator->counter(
                    'Strona {:page} z {:pages}, wyświetlono {:current} artykuły z wszystkich
     {:count}, początek {:start}, koniec {:end} artykuł'
            );
            ?>
        </div>
    <?php else: ?>
        <h3>Empty data</h3>
    <?php endif; ?>
</div>