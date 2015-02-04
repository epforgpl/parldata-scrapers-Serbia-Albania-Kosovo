<div class="actions">
    <?php
    $menu[] = $this->Html->link('back', '/serbian');
    foreach ($listMenu as $k => $m) {
        $menu[] = $this->Html->link($m, '/serbian/listTable/' . $k);
    }
    $menu[] = $this->Html->link('logs', '/serbian/listLogTable/');

    echo $this->Html->nestedList($menu);
    ?>
</div>
<div class="posts form">
    <h2>Mp's party table</h2>
    <?php //pr($content); ?>
    <?php if (isset($content) && !empty($content)): ?>
        <table cellpadding="0" cellspacing="0">
            <?php
            $tableHeaders = $this->Html->tableHeaders(array(
                $this->Paginator->sort('id'),
                $this->Paginator->sort('terminated'),
                $this->Paginator->sort('url_uid'),
                $this->Paginator->sort('name'),
                $this->Paginator->sort('parlamentary_group'),
                $this->Paginator->sort('party'),
                $this->Paginator->sort('start_date'),
                $this->Paginator->sort('end_date'),
                $this->Paginator->sort('hometown'),
                $this->Paginator->sort('age'),
                $this->Paginator->sort('status'),
                $this->Paginator->sort('modified'),
            ));
            echo $tableHeaders;

            $rows = array();
            foreach ($content AS $record) {

                $rows[] = array(
                    $record['SerbianDelegate']['id'],
                    $record['SerbianDelegate']['terminated'],
                    $record['SerbianDelegate']['url_uid'],
                    $record['SerbianDelegate']['name'],
                    $record['SerbianDelegate']['parlamentary_group'],
                    $record['SerbianDelegate']['party'],
                    $record['SerbianDelegate']['start_date'],
                    $record['SerbianDelegate']['end_date'],
                    $record['SerbianDelegate']['hometown'],
                    $record['SerbianDelegate']['age'],
                    $record['SerbianDelegate']['status'],
                    $record['SerbianDelegate']['modified'],
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