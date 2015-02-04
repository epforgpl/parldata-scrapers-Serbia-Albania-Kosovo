<div class="actions">
    <?php
    echo $this->element('m_albanian_speeches');
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
                $this->Paginator->sort('post_date'),
                $this->Paginator->sort('url'),
                'Content',
                $this->Paginator->sort('status'),
                $this->Paginator->sort('active'),
                $this->Paginator->sort('created'),
                $this->Paginator->sort('modified'),
            ));
            echo $tableHeaders;

            $rows = array();
            foreach ($content AS $record) {
                // $actions = $this->Html->link(__('zobacz', true), array('action' => 'view', $user['User']['id']));
                $linkContent = null;
                if (!is_null($record['AlbaniaDoc']['id'])) {
                    $linkContent = $this->Html->link('downloading', '/albanian/viewContent/' . $record['AlbaniaDoc']['id'], array());
                }
                $rows[] = array(
                    $record['AlbaniaSpeecheIndex']['id'],
                    $record['AlbaniaSpeecheIndex']['post_date'],
                    $this->Html->link('link', $record['AlbaniaSpeecheIndex']['url'], array('target' => '_blanc')),
                    isset($linkContent) ? $linkContent : 'wait...',
                    $record['AlbaniaSpeecheIndex']['status'],
                    $record['AlbaniaSpeecheIndex']['active'],
                    $record['AlbaniaSpeecheIndex']['created'],
                    $record['AlbaniaSpeecheIndex']['modified'],
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