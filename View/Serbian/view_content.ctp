<div class="actions">
    <?php
    echo $this->element('m_serbia_peeches');
    ?>
</div>
<div class="posts form">
    <h2>Content Data</h2>
    <?php //pr($content); ?>
    <?php
    if (isset($content) && !empty($content)) {
        foreach ($content['SerbianSpeecheContent'] as $key => $record) {
            echo $this->Html->tag('h4', $key);
            echo $this->Html->para(null, $record);
        }
    } else {
        echo $this->Html->tag('h3', 'Empty data');
    }
    ?>
    <h2>List Pdfs</h2>
    <?php //pr($pdfs); ?>
    <?php if (isset($pdfs) && !empty($pdfs)): ?>
        <table cellpadding="0" cellspacing="0">
            <?php
            $tableHeaders = $this->Html->tableHeaders(array(
                $this->Paginator->sort('id'),
                $this->Paginator->sort('post_date'),
                $this->Paginator->sort('stamp_in_text'),
                $this->Paginator->sort('pdf_url'),
                $this->Paginator->sort('name_sr'),
                $this->Paginator->sort('name_en'),
                $this->Paginator->sort('content_sr'),
                $this->Paginator->sort('content_en'),
                $this->Paginator->sort('status'),
                $this->Paginator->sort('created'),
                $this->Paginator->sort('modified'),
            ));
            echo $tableHeaders;

            $rows = array();
            foreach ($pdfs AS $record) {
                // $actions = $this->Html->link(__('zobacz', true), array('action' => 'view', $user['User']['id']));

                $rows[] = array(
                    $record['SerbianPdf']['id'],
                    $record['SerbianPdf']['post_date'],
                    $record['SerbianPdf']['stamp_in_text'],
                    $this->Html->link('link', $serbiaHost . $record['SerbianPdf']['pdf_url'], array('target' => '_blanc')),
                    $record['SerbianPdf']['name_sr'],
                    !empty($record['SerbianPdf']['name_en']) ? $record['SerbianPdf']['name_en'] : 'wait..',
                    !empty($record['SerbianPdf']['content_sr']) ? $record['SerbianPdf']['content_sr'] : 'wait..',
                    !empty($record['SerbianPdf']['content_en']) ? $record['SerbianPdf']['content_en'] : 'wait..',
                    $record['SerbianPdf']['status'],
                    $record['SerbianPdf']['created'],
                    $record['SerbianPdf']['modified'],
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