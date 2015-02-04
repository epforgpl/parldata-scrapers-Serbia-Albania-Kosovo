<div class="actions">
    <?php
    echo $this->element('m_albanian_mps_details');
    ?>
</div>
<div class="posts form">
    <h2>list Mp's contact details</h2>
    <?php // pr($content); ?>
    <?php if (isset($content) && !empty($content)): ?>
        <table cellpadding="0" cellspacing="0">
            <?php
            $tableHeaders = $this->Html->tableHeaders(array(
                $this->Paginator->sort('id'),
                $this->Paginator->sort('name') .
                $this->Paginator->sort('post_data') .
                $this->Paginator->sort('image') .
                $this->Paginator->sort('url'),
                $this->Paginator->sort('address') .
                $this->Paginator->sort('email') .
                $this->Paginator->sort('year_of_birth') .
                $this->Paginator->sort('home_town') .
                $this->Paginator->sort('marital_status'),
                $this->Paginator->sort('education'),
                $this->Paginator->sort('professional_activity'),
                $this->Paginator->sort('political_activity'),
                $this->Paginator->sort('opting_in'),
                $this->Paginator->sort('group_parliamentary_committees'),
                $this->Paginator->sort('publishing'),
                $this->Paginator->sort('status') .
                $this->Paginator->sort('created') .
                $this->Paginator->sort('modified'),
            ));
            echo $tableHeaders;

            $rows = array();
            foreach ($content AS $record) {

                $education = $this->Text->truncate(
                        $record['AlbaniaMpsDetail']['education'], 100, array(
                    'ellipsis' => '...',
                    'exact' => false
                        )
                );
                $professional_activity = $this->Text->truncate(
                        $record['AlbaniaMpsDetail']['professional_activity'], 100, array(
                    'ellipsis' => '...',
                    'exact' => false
                        )
                );
                $political_activity = $this->Text->truncate(
                        $record['AlbaniaMpsDetail']['political_activity'], 100, array(
                    'ellipsis' => '...',
                    'exact' => false
                        )
                );
                $opting_in = $this->Text->truncate(
                        $record['AlbaniaMpsDetail']['opting_in'], 100, array(
                    'ellipsis' => '...',
                    'exact' => false
                        )
                );
                $group_parliamentary_committees = $this->Text->truncate(
                        $record['AlbaniaMpsDetail']['group_parliamentary_committees'], 100, array(
                    'ellipsis' => '...',
                    'exact' => false
                        )
                );
                $publishing = $this->Text->truncate(
                        $record['AlbaniaMpsDetail']['publishing'], 100, array(
                    'ellipsis' => '...',
                    'exact' => false
                        )
                );

                $rows[] = array(
                    $record['AlbaniaMpsDetail']['id'],
                    $this->Html->link($record['AlbaniaMpsDetail']['name'], '/albanian/viewDelegate/' . $record['AlbaniaMpsDetail']['id']) . '<br>' .
                    $record['AlbaniaMpsDetail']['post_data'] . '<br>' .
                    $this->Html->link('image', $record['AlbaniaMpsDetail']['image'], array('target' => '_blanc')) . '<br>' .
                    $this->Html->link('link', $record['AlbaniaMpsDetail']['url'], array('target' => '_blanc')),
                    $record['AlbaniaMpsDetail']['address'] . '<br>' .
                    $record['AlbaniaMpsDetail']['email'] . '<br>' .
                    $record['AlbaniaMpsDetail']['year_of_birth'] . '<br>' .
                    $record['AlbaniaMpsDetail']['home_town'] . '<br>' .
                    $record['AlbaniaMpsDetail']['marital_status'],
                    $education,
                    $professional_activity,
                    $political_activity,
                    $opting_in,
                    $group_parliamentary_committees,
                    $publishing,
                    $record['AlbaniaMpsDetail']['status'] . '<br>' .
                    $record['AlbaniaMpsDetail']['created'] . '<br>' .
                    $record['AlbaniaMpsDetail']['modified'],
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