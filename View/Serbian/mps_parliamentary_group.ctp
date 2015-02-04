<div class="actions">
    <?php
    echo $this->element('m_serbia_mps_contact');
    ?>
</div>
<div class="posts form">
    <h2>List Parliamentary Group</h2>
    <?php
    // pr($content);
    ?>
    <?php if (isset($content) && !empty($content)): ?>
        <table cellpadding="0" cellspacing="0">
            <?php
            $tableHeaders = $this->Html->tableHeaders(array(
                $this->Paginator->sort('id'),
                $this->Paginator->sort('uid'),
                $this->Paginator->sort('url'),
                __('Mps'),
                $this->Paginator->sort('name'),
                $this->Paginator->sort('created'),
                $this->Paginator->sort('modified'),
            ));
            echo $tableHeaders;

            $rows = array();
            foreach ($content AS $record) {
                $url = null;
                if (!empty($record['SerbianParliamentaryGroup']['url'])) {
                    $url = $this->Html->link('link', $serbiaHost . $record['SerbianParliamentaryGroup']['url'], array('target' => '_blanc'));
                }
                $rows[] = array(
                    $record['SerbianParliamentaryGroup']['id'],
                    $record['SerbianParliamentaryGroup']['uid'],
                    $url,
                    count($record['SerbianMpsDetail']),
                    $record['SerbianParliamentaryGroup']['name'],
                    $record['SerbianParliamentaryGroup']['created'],
                    $record['SerbianParliamentaryGroup']['modified'],
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