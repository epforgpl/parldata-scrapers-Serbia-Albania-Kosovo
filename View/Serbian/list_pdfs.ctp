<div class="actions">
    <?php
    echo $this->element('m_serbia_voting');
    ?>
</div>
<div class="posts form">
    <h2>Voting pdfs</h2>
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
                $name_sr = $name_en = $content_sr = $content_en = null;
                // $actions = $this->Html->link(__('zobacz', true), array('action' => 'view', $user['User']['id']));
                if (!empty($record['SerbianPdf']['name_sr'])) {
                    $name_sr = $this->Text->truncate(
                            strip_tags($record['SerbianPdf']['name_sr']), 150, array(
                        'ellipsis' => '...',
                        'exact' => false
                            )
                    );
                }
                if (!empty($record['SerbianPdf']['name_en'])) {
                    $name_en = $this->Text->truncate(
                            strip_tags($record['SerbianPdf']['name_en']), 150, array(
                        'ellipsis' => '...',
                        'exact' => false
                            )
                    );
                }
                if (!empty($record['SerbianPdf']['content_sr'])) {
                    $content_sr = $this->Text->truncate(
                            strip_tags($record['SerbianPdf']['content_sr']), 150, array(
                        'ellipsis' => '...',
                        'exact' => false
                            )
                    );
                }
                if (!empty($record['SerbianPdf']['content_en'])) {
                    $content_en = $this->Text->truncate(
                            strip_tags($record['SerbianPdf']['content_en']), 150, array(
                        'ellipsis' => '...',
                        'exact' => false
                            )
                    );
                }

                $rows[] = array(
                    $record['SerbianPdf']['id'],
                    $this->Html->link($record['SerbianPdf']['post_date'], '/serbian/viewPdf/' . $record['SerbianPdf']['id']),
                    $record['SerbianPdf']['stamp_in_text'],
                    $this->Html->link('link', $serbiaHost . $record['SerbianPdf']['pdf_url'], array('target' => '_blanc')),
                    $name_sr,
                    !empty($name_en) ? $name_en : 'wait..',
                    !empty($content_sr) ? $content_sr : 'wait..',
                    !empty($content_en) ? $content_en : 'wait..',
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