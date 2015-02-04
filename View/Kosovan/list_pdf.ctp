<div class="actions">
    <?php
    echo $this->element('m_kosovan_speeches');
    ?>
</div>
<div class="posts form">
    <h2>Speeche pdfs</h2>
    <?php // pr($pdfs); ?>
    <?php if (isset($pdfs) && !empty($pdfs)): ?>
        <table cellpadding="0" cellspacing="0">
            <?php
            $tableHeaders = $this->Html->tableHeaders(array(
                $this->Paginator->sort('id'),
                $this->Paginator->sort('kosovo_speeche_content_id'),
                $this->Paginator->sort('pdf_url'),
                $this->Paginator->sort('content_sr'),
                // $this->Paginator->sort('content_en'),
                $this->Paginator->sort('status'),
                $this->Paginator->sort('created'),
                $this->Paginator->sort('modified'),
            ));
            echo $tableHeaders;

            $rows = array();
            foreach ($pdfs AS $record) {
                $name_sr = $name_en = $content_sr = $content_en = null;

                if (!empty($record['KosovoPdf']['content_sr'])) {
                    $content_sr = $this->Text->truncate(
                            strip_tags($record['KosovoPdf']['content_sr']), 150, array(
                        'ellipsis' => '...',
                        'exact' => false
                            )
                    );
                }
                if (!empty($record['KosovoPdf']['content_en'])) {
                    $content_en = $this->Text->truncate(
                            strip_tags($record['KosovoPdf']['content_en']), 150, array(
                        'ellipsis' => '...',
                        'exact' => false
                            )
                    );
                }

                $rows[] = array(
                    $record['KosovoPdf']['id'],
                    $this->Html->link($record['KosovoSpeecheContent']['title'] . ' - ' . $record['KosovoSpeecheContent']['id'], '/kosovan/viewPdf/' . $record['KosovoPdf']['id']),
                    $this->Html->link('link', $kosovoHost . $record['KosovoPdf']['pdf_url'], array('target' => '_blanc')),
                    !empty($content_sr) ? $content_sr : 'wait..',
                    //  !empty($content_en) ? $content_en : 'wait..',
                    $record['KosovoPdf']['status'],
                    $record['KosovoPdf']['created'],
                    $record['KosovoPdf']['modified'],
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