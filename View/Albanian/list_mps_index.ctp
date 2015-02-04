<div class="actions">
    <?php
    echo $this->element('m_albanian_mps_index');
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
                $this->Paginator->sort('uid'),
                $this->Paginator->sort('url'),
                $this->Paginator->sort('name'),
                $this->Paginator->sort('status'),
                $this->Paginator->sort('active'),
                $this->Paginator->sort('created'),
                $this->Paginator->sort('modified'),
            ));
            echo $tableHeaders;

            $rows = array();
            foreach ($content AS $record) {

                $rows[] = array(
                    $record['AlbaniaMpsIndex']['id'],
                    $record['AlbaniaMpsIndex']['uid'],
                    $this->Html->link('link', $record['AlbaniaMpsIndex']['url'], array('target' => '_blanc')),
                    $record['AlbaniaMpsIndex']['name'],
                    $record['AlbaniaMpsIndex']['status'],
                    $record['AlbaniaMpsIndex']['active'],
                    $record['AlbaniaMpsIndex']['created'],
                    $record['AlbaniaMpsIndex']['modified'],
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