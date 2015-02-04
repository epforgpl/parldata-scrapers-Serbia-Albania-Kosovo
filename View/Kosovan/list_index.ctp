<div class="actions">
    <?php
    echo $this->element('m_kosovan_speeches');
    ?>
</div>
<div class="posts form">
    <h2>List Index Pagin</h2>
    <?php // pr($content); ?>
    <?php if (isset($content) && !empty($content)): ?>
        <table cellpadding="0" cellspacing="0">
            <?php
            $tableHeaders = $this->Html->tableHeaders(array(
                $this->Paginator->sort('id'),
                $this->Paginator->sort('post_uid'),
//                $this->Paginator->sort('lang'),
                $this->Paginator->sort('post_date'),
                $this->Paginator->sort('url'),
                'Content',
                'Pdfs',
                'Txts',
                $this->Paginator->sort('status'),
                $this->Paginator->sort('active'),
                $this->Paginator->sort('created'),
                $this->Paginator->sort('modified'),
            ));
            echo $tableHeaders;

            $rows = array();
            foreach ($content AS $record) {
                // $actions = $this->Html->link(__('zobacz', true), array('action' => 'view', $user['User']['id']));
                if (!empty($record['KosovoSpeecheContent']['id'])) {
                    $linkContent = $this->Html->link('gets', '/kosovan/viewContent/' . $record['KosovoSpeecheContent']['id'], array());
                    if (!empty($record['KosovoSpeecheContent']['KosovoPdf'])) {
                        $pdfs = count($record['KosovoSpeecheContent']['KosovoPdf']);
                    }
                    if (!empty($record['KosovoSpeecheContent']['KosovoTxt'])) {
                        $txts = count($record['KosovoSpeecheContent']['KosovoTxt']);
                    }
                }
                $rows[] = array(
                    $record['KosovoSpeecheIndex']['id'],
                    $record['KosovoSpeecheIndex']['post_uid'],
//                    $record['KosovoSpeecheIndex']['lang'],
                    $record['KosovoSpeecheIndex']['post_date'],
                    $this->Html->link('link', $kosovoHost . $record['KosovoSpeecheIndex']['url'], array('target' => '_blanc')),
                    isset($linkContent) ? $linkContent : 'wait...',
                    isset($pdfs) ? $pdfs : '0',
                    isset($txts) ? $txts : '0',
                    $record['KosovoSpeecheIndex']['status'],
                    $record['KosovoSpeecheIndex']['active'],
                    $record['KosovoSpeecheIndex']['created'],
                    $record['KosovoSpeecheIndex']['modified'],
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