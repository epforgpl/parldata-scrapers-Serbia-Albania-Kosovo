<div class="actions">
    <?php
    echo $this->element('m_serbia_mps_contact');
    ?>
</div>
<div class="posts form">
    <h2>List MP's Contact</h2>
    <?php
    //pr($content);
    ?>
    <?php if (isset($content) && !empty($content)): ?>
        <table cellpadding="0" cellspacing="0">
            <?php
            $tableHeaders = $this->Html->tableHeaders(array(
                $this->Paginator->sort('id'),
                $this->Paginator->sort('name'),
                $this->Paginator->sort('image') . $this->Paginator->sort('id', 'url'),
                $this->Paginator->sort('year_of_birth'),
                $this->Paginator->sort('occupation'),
                $this->Paginator->sort('electoral_list'),
                $this->Paginator->sort('verification_mandate', 'Mandate'),
                $this->Paginator->sort('biography'),
                $this->Paginator->sort('www') .
                $this->Paginator->sort('facebook') .
                $this->Paginator->sort('twitter'),
                $this->Paginator->sort('status'),
                $this->Paginator->sort('hints'),
                $this->Paginator->sort('created'),
                $this->Paginator->sort('modified'),
            ));
            echo $tableHeaders;

            $rows = array();
            foreach ($content AS $record) {
                $image = null;
                if (!empty($record['SerbianMpsDetail']['image'])) {
                    $image = $this->Html->link('image', $serbiaHost . $record['SerbianMpsDetail']['image'], array('target' => '_blanc')) . '<br />';
                }

                $url = $this->Html->link('link', 'http://www.parlament.gov.rs/aaa.' . $record['SerbianMpsDetail']['id'] . '.488.html', array('target' => '_blanc'));

                $text = null;
                if (!empty($record['SerbianMpsDetail']['biography'])) {
                    $text = $this->Text->truncate(
                            $record['SerbianMpsDetail']['biography'], 150, array(
                        'ellipsis' => '...',
                        'exact' => false
                            )
                    );
                }
                $rows[] = array(
                    $record['SerbianMpsDetail']['id'],
                    $record['SerbianMpsDetail']['name'],
                    $image .
                    $url,
                    $record['SerbianMpsDetail']['year_of_birth'],
                    $record['SerbianMpsDetail']['occupation'],
                    $record['SerbianMpsDetail']['electoral_list'],
                    $record['SerbianMpsDetail']['verification_mandate'],
                    $text,
                    $record['SerbianMpsDetail']['www'] . '<br />' .
                    $record['SerbianMpsDetail']['facebook'] . '<br />' .
                    $record['SerbianMpsDetail']['twitter'],
                    $record['SerbianMpsDetail']['status'],
                    $record['SerbianMpsDetail']['hints'],
                    $record['SerbianMpsDetail']['created'],
                    $record['SerbianMpsDetail']['modified'],
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