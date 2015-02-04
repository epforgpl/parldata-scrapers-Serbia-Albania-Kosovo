<div class="actions">
    <?php
    echo $this->element('m_albanian_votings');
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
                $this->Paginator->sort('post_date'),
                $this->Paginator->sort('url'),
                $this->Paginator->sort('name'),
                'Content',
//                'Pdfs',
//                'Txts',
                $this->Paginator->sort('status'),
                $this->Paginator->sort('active'),
                $this->Paginator->sort('created'),
                $this->Paginator->sort('modified'),
            ));
            echo $tableHeaders;

            $rows = array();
            foreach ($content AS $record) {
                // $actions = $this->Html->link(__('zobacz', true), array('action' => 'view', $user['User']['id']));
                if (!empty($record['AlbaniaVoteContent']['id'])) {
                    $linkContent = $this->Html->link('gets', '/kosovan/viewContent/' . $record['AlbaniaVoteContent']['id'], array());
                    if (!empty($record['AlbaniaVoteContent']['KosovoPdf'])) {
                        $pdfs = count($record['AlbaniaVoteContent']['KosovoPdf']);
                    }
                    if (!empty($record['AlbaniaVoteContent']['KosovoTxt'])) {
                        $txts = count($record['AlbaniaVoteContent']['KosovoTxt']);
                    }
                }
                $rows[] = array(
                    $record['AlbaniaVoteIndex']['id'],
                    $record['AlbaniaVoteIndex']['post_date'],
                    $this->Html->link('link', $record['AlbaniaVoteIndex']['url'], array('target' => '_blanc')),
                    $record['AlbaniaVoteIndex']['name'],
                    isset($linkContent) ? $linkContent : 'wait...',
//                    isset($pdfs) ? $pdfs : '0',
//                    isset($txts) ? $txts : '0',
                    $record['AlbaniaVoteIndex']['status'],
                    $record['AlbaniaVoteIndex']['active'],
                    $record['AlbaniaVoteIndex']['created'],
                    $record['AlbaniaVoteIndex']['modified'],
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