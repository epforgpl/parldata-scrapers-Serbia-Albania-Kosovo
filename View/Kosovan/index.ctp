<div class="actions">
    <?php
    echo $this->element('m_kosovan');
    ?>
</div>
<div class="posts form">
    <?php echo $this->Session->flash(); ?>
    <h2>Kosovo – Assembly</h2>

    <h4>MP's party - description: HTML, since 2001, 2001-2014 in archive (root link 1, search Arkivi), since 2014 root link 2, part of MPs' profiles</h4>
    <p>
        MP's party - root link:
        <br />
        <?php echo $this->Html->link('http://www.kuvendikosoves.org/?cid=1,158', 'http://www.kuvendikosoves.org/?cid=1,158', array('target' => '_blanc')); ?>
        <br />
        <?php echo $this->Html->link('http://www.kuvendikosoves.org/?cid=1,192', 'http://www.kuvendikosoves.org/?cid=1,192', array('target' => '_blanc')); ?>
        <br />
        MP's party - example link:
        <br />
        <?php echo $this->Html->link('http://www.kuvendikosoves.org/?cid=1,192,880', 'http://www.kuvendikosoves.org/?cid=1,192,880', array('target' => '_blanc')); ?>
    </p>

    <h4>MP's contact info - HTML, since 2001, 2001-2014 in archive (root link 1, search Arkivi), since 2014 root link 2, part of MPs' profiles</h4>
    <p>
        MP's contact info - root link:
        <br />
        <?php echo $this->Html->link('http://www.kuvendikosoves.org/?cid=1,192', 'http://www.kuvendikosoves.org/?cid=1,192', array('target' => '_blanc')); ?>
        <br /><br />
        MP's contact info - example link:
        <br />
        <?php echo $this->Html->link('http://www.kuvendikosoves.org/?cid=1,192,880', 'http://www.kuvendikosoves.org/?cid=1,192,880', array('target' => '_blanc')); ?>
    </p>

    <h4>Plenary voting - TXT, since 2010, in info on sessions (must select date, session and search Elektronsko glasanje)</h4>
    <p>
        Plenary voting - root link:
        <br />
        <?php echo $this->Html->link('http://www.kuvendikosoves.org/?cid=3,177,5596', 'http://www.kuvendikosoves.org/?cid=3,177,5596', array('target' => '_blanc')); ?>
        <br /><br />
        Plenary voting - example link:
        <br />
        <?php echo $this->Html->link('http://www.kuvendikosoves.org/common/docs/voting/vot_4_2014_04_17_10_1.txt', 'http://www.kuvendikosoves.org/common/docs/voting/vot_4_2014_04_17_10_1.txt', array('target' => '_blanc')); ?>
    </p>

    <h4>Plenary speeches - Native PDF, since 2007, must select date and search Transkript</h4>
    <p>
        Plenary speeches - root link:
        <br />
        <?php echo $this->Html->link('http://www.kuvendikosoves.org/?cid=3,177,5596', 'http://www.kuvendikosoves.org/?cid=3,177,5596', array('target' => '_blanc')); ?>
        <br /><br />
        Plenary speeches - example link:
        <br />
        <?php echo $this->Html->link('http://www.kuvendikosoves.org/common/docs/proc/trans_s_2014_04_23_16_5609_sr.pdf', 'http://www.kuvendikosoves.org/common/docs/proc/trans_s_2014_04_23_16_5609_sr.pdf', array('target' => '_blanc')); ?>
    </p>

    <?php ?>
    <hr>
    <h2>Notatki:</h2>
    <p>
        https://docs.google.com/document/d/1H9ZmR01UQkwc_H16Yc7_UMs1Rp2SZEYoSifuOocxuxE/edit#
    </p>
    <?php ?>

</div>