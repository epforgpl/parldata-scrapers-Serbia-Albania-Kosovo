<div class="actions">
    <?php
    echo $this->element('m_serbia_peeches');
    ?>
</div>
<div class="posts form">
    <h2>List Index Pagin</h2>
    <?php //pr($content); ?>
    <?php if (isset($content) && !empty($content)): ?>
        <table cellpadding="0" cellspacing="0">
            <?php
            $tableHeaders = $this->Html->tableHeaders(array(
                $this->Paginator->sort('id'),
                $this->Paginator->sort('post_uid'),
                $this->Paginator->sort('lang'),
                $this->Paginator->sort('post_date'),
                $this->Paginator->sort('url'),
                'Content',
                'Pdfs',
                $this->Paginator->sort('status'),
                $this->Paginator->sort('active'),
                $this->Paginator->sort('created'),
                $this->Paginator->sort('modified'),
            ));
            echo $tableHeaders;

            $rows = array();
            foreach ($content AS $record) {
                // $actions = $this->Html->link(__('zobacz', true), array('action' => 'view', $user['User']['id']));
                if (!empty($record['SerbianSpeecheContent']['id'])) {
                    $linkContent = $this->Html->link('complete', '/serbian/viewContent/' . $record['SerbianSpeecheContent']['id'], array());
                }
                $rows[] = array(
                    $this->Html->link($record['SerbianSpeecheIndex']['id'], '/serbian/combineToApiSpeeches/' . $record['SerbianSpeecheIndex']['id']),
                    $record['SerbianSpeecheIndex']['post_uid'],
                    $record['SerbianSpeecheIndex']['lang'],
                    $record['SerbianSpeecheIndex']['post_date'],
                    $this->Html->link('link', $serbiaHost . $record['SerbianSpeecheIndex']['url'], array('target' => '_blanc')),
                    isset($linkContent) ? $linkContent : 'wait...',
                    count($record['pdfs']),
                    $record['SerbianSpeecheIndex']['status'],
                    $record['SerbianSpeecheIndex']['active'],
                    $record['SerbianSpeecheIndex']['created'],
                    $record['SerbianSpeecheIndex']['modified'],
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