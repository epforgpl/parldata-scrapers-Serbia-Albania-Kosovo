<div class="actions">
    <?php
    $menu[] = $this->Html->link('back', '/kosovan');
    foreach ($listMenu as $k => $m) {
        $menu[] = $this->Html->link($m, '/kosovan/listMpsIndex/' . $k);
    }
    $menu[] = $this->Html->link('logs', '/kosovan/listLogMpsIndex/');

    echo $this->Html->nestedList($menu);
    ?>
</div>
<div class="posts form">
    <h2>List Index Mps</h2>
    <?php //pr($content); ?>
    <?php if (isset($content) && !empty($content)): ?>
        <table cellpadding="0" cellspacing="0">
            <?php
            $tableHeaders = $this->Html->tableHeaders(array(
                $this->Paginator->sort('id'),
                $this->Paginator->sort('start_date'),
                $this->Paginator->sort('end_date'),
                $this->Paginator->sort('url'),
                $this->Paginator->sort('name'),
                $this->Paginator->sort('status'),
                $this->Paginator->sort('created'),
                $this->Paginator->sort('modified'),
            ));
            echo $tableHeaders;

            $rows = array();
            foreach ($content AS $record) {

                $rows[] = array(
                    $record['KosovoMpsIndex']['id'],
                    $record['KosovoMpsIndex']['start_date'],
                    $record['KosovoMpsIndex']['end_date'],
                    $this->Html->link('link', $kosovoHost . $record['KosovoMpsIndex']['url'], array('target' => '_blanc')),
                    $record['KosovoMpsIndex']['name'],
                    $record['KosovoMpsIndex']['status'],
                    $record['KosovoMpsIndex']['created'],
                    $record['KosovoMpsIndex']['modified'],
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
