<div class="actions">
    <?php
    echo $this->element('m_albanian_speeches');
    ?>
</div>
<div class="posts form">
    <h2>List Log</h2>
    <?php //pr($content); ?>
    <?php if (isset($content) && !empty($content)): ?>
        <table cellpadding="0" cellspacing="0">
            <?php
            $tableHeaders = $this->Html->tableHeaders(array(
                $this->Paginator->sort('id'),
                $this->Paginator->sort('created'),
                $this->Paginator->sort('logcontent'),
            ));
            echo $tableHeaders;

            $rows = array();
            foreach ($content AS $record) {
                // $actions = $this->Html->link(__('zobacz', true), array('action' => 'view', $user['User']['id']));
                $text = $this->Text->truncate(
                        $record['AlbaniaLog']['logcontent'], 150, array(
                    'ellipsis' => '...',
                    'exact' => false
                        )
                );
                $rows[] = array(
                    $record['AlbaniaLog']['id'],
                    $this->Html->link($record['AlbaniaLog']['created'], '/albanian/viewLog/' . $record['AlbaniaLog']['id']),
                    $text
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