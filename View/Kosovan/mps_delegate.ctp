<div class="actions">
    <?php
    echo $this->element('m_kosovo_mps_contact');
    ?>
</div>
<div class="posts form">
    <h2>List MP's Contact</h2>
    <?php
    //  pr($content);
    ?>
    <?php if (isset($content) && !empty($content)): ?>
        <table cellpadding="0" cellspacing="0">
            <?php
            $tableHeaders = $this->Html->tableHeaders(array(
                $this->Paginator->sort('id'),
                $this->Paginator->sort('kosovo_mps_index_id'),
                $this->Paginator->sort('name'),
                'url',
                $this->Paginator->sort('image'),
                $this->Paginator->sort('phone'),
                $this->Paginator->sort('status'),
                $this->Paginator->sort('hints'),
                $this->Paginator->sort('created'),
                $this->Paginator->sort('modified'),
            ));
            echo $tableHeaders;

            $rows = array();
            foreach ($content AS $record) {
                $image = null;
                if (!empty($record['KosovoMpsDetail']['image'])) {
                    $image = $this->Html->link('image', $kosovoHost . $record['KosovoMpsDetail']['image'], array('target' => '_blanc'));
                }

                $url = $this->Html->link('link', $kosovoHost . $record['KosovoMpsIndex']['url'], array('target' => '_blanc'));

                $rows[] = array(
                    $record['KosovoMpsDetail']['id'],
                    $record['KosovoMpsIndex']['id'],
                    $this->Html->link($record['KosovoMpsDetail']['name'], '/kosovan/viewDelegate/' . $record['KosovoMpsDetail']['id']),
                    $url,
                    $image,
                    $record['KosovoMpsDetail']['phone'],
                    $record['KosovoMpsDetail']['status'],
                    $record['KosovoMpsDetail']['hints'],
                    $record['KosovoMpsDetail']['created'],
                    $record['KosovoMpsDetail']['modified'],
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