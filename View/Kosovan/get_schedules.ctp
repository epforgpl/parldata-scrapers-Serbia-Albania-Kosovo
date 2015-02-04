<div class="actions">
    <?php
    echo $this->element('m_kosovan');
    ?>
</div>
<div class="posts form">
    <h2>List Schedules</h2>
    <h4>in crontab: */1 * * * * cd <?php echo ROOT; ?>/app && Console/cake schedule</h4>
    <?php //pr($content); ?>
    <?php if (isset($content) && !empty($content)): ?>
        <table cellpadding="0" cellspacing="0">
            <?php
            $tableHeaders = $this->Html->tableHeaders(array(
                $this->Paginator->sort('id'),
                $this->Paginator->sort('created'),
                //  $this->Paginator->sort('name'),
                $this->Paginator->sort('task', 'Task Serbian Action'),
                $this->Paginator->sort('interval'),
                $this->Paginator->sort('hints'),
                $this->Paginator->sort('modified', 'Last Run'),
                $this->Paginator->sort('modified', 'Next Run'),
            ));
            echo $tableHeaders;

            $rows = array();
            foreach ($content AS $record) {
                $next = CakeTime::format($record['Schedule']['modified'] . ' +' . $record['Schedule']['interval'] . ' minutes', '%Y-%m-%d %H:%M:%S');
                $rows[] = array(
                    $record['Schedule']['id'],
                    $record['Schedule']['created'],
                    //  $record['Schedule']['name'],
                    Inflector::humanize($record['Schedule']['task']),
                    $record['Schedule']['interval'] . ' min',
                    $record['Schedule']['hints'],
                    $record['Schedule']['modified'],
                    $next,
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