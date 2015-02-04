<div class="actions">
    <?php
    $menu = array(
        $this->Html->link('back', $back),
    );
    echo $this->Html->nestedList($menu);
    ?>
</div>
<div class="posts form">
    <h2>view Data</h2>
    <?php
    echo '<pre>';
    print_r($content);
    echo '</pre>';
    ?>
    <?php
    if (isset($content) && !empty($content)) {
//        foreach ($content as $key => $record) {
//            echo $this->Html->tag('h3', $key);
//            if (count($record)) {
//                foreach ($record as $k => $v) {
//                    echo $this->Html->tag('h4', '&nbsp; ' . $k);
//                    if (is_array($v)) {
//                        foreach ($v as $k1 => $v1) {
//                            echo $this->Html->tag('h4', '&nbsp;&nbsp;&nbsp;&nbsp; ' . $k1);
//                            echo $this->Html->para(null, nl2br('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ' . $v1));
//                        }
//                    } else {
//                        if (!is_array($v)) {
//                            echo $this->Html->para(null, nl2br('&nbsp;&nbsp; ' . $v));
//                        }
//                    }
//                }
//            } else {
//                if (!empty($record)) {
//                    echo $this->Html->para(null, nl2br('&nbsp; ' . $record));
//                }
//            }
//        }
    } else {
        echo $this->Html->tag('h3', 'Empty data');
    }
    ?>
</div>