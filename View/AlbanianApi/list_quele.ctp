<div class="actions">
    <?php
    echo $this->element('m_albania_api');
    ?>
</div>
<div class="posts form">
    <h2>List combined Quelle data</h2>
    <?php //pr($content); ?>
    <?php if (isset($content) && !empty($content)): ?>
        <table cellpadding="0" cellspacing="0">
            <?php
            $tableHeaders = $this->Html->tableHeaders(array(
                $this->Paginator->sort('id'),
                $this->Paginator->sort('uid'),
                $this->Paginator->sort('type'),
                $this->Paginator->sort('status'),
                $this->Paginator->sort('hints'),
                $this->Paginator->sort('code'),
                $this->Paginator->sort('created'),
            ));
            echo $tableHeaders;

            $rows = array();
            foreach ($content AS $record) {
                $rows[] = array(
                    $this->Html->link($record['QueleToSend']['id'], '/kosovanApi/viewData/' . $record['QueleToSend']['id']),
                    $record['QueleToSend']['uid'],
                    $record['QueleToSend']['type'],
                    $record['QueleToSend']['status'],
                    $record['QueleToSend']['hints'],
                    $record['QueleToSend']['code'],
                    $record['QueleToSend']['created'],
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