<div class="actions">
    <?php
    echo $this->element('m_albania');
    ?>
</div>
<div class="posts form">
    <?php echo $this->Session->flash(); ?>
    <h2>Albania – Parliament</h2>

    <h4>MP's party - description: HTML, since 2013, part of MPs' profiles</h4>
    <p>
        MP's party - root link:
        <br />
        <?php echo $this->Html->link('http://www.parlament.al/web/Jeteshkrime_7033_1.php', 'http://www.parlament.al/web/Jeteshkrime_7033_1.php', array('target' => '_blanc')); ?>
        <br />
        MP's party - example link:
        <br />
        <?php echo $this->Html->link('http://www.parlament.al/web/BOCI_Lu_c_iano_10834_1.php', 'http://www.parlament.al/web/BOCI_Lu_c_iano_10834_1.php', array('target' => '_blanc')); ?>
    </p>

    <h4>MP's contact info - HTML, since 2013, part of MPs' profiles</h4>
    <p>
        MP's contact info - root link:
        <br />
        <?php echo $this->Html->link('http://www.parlament.al/web/Jeteshkrime_7033_1.php', 'http://www.parlament.al/web/Jeteshkrime_7033_1.php', array('target' => '_blanc')); ?>
        <br /><br />
        MP's contact info - example link:
        <br />
        <?php echo $this->Html->link('http://www.parlament.al/web/BOCI_Lu_c_iano_10834_1.php', 'http://www.parlament.al/web/BOCI_Lu_c_iano_10834_1.php', array('target' => '_blanc')); ?>
    </p>

    <h4>Plenary voting - Native or scanned PDFs, since 2009, listed by bills</h4>
    <p>
        Plenary voting - root link:
        <br />
        <?php echo $this->Html->link('http://www.parlament.al/web/Si_kane_votuar_deputetet_71_1.php', 'http://www.parlament.al/web/Si_kane_votuar_deputetet_71_1.php', array('target' => '_blanc')); ?>
        <br /><br />
        Plenary voting - example link:
        <br />
        <?php echo $this->Html->link('http://www.parlament.al/web/P_L_Per_shoqerite_e_bashkepunimit_bujqesor_14071_1.php', 'http://www.parlament.al/web/P_L_Per_shoqerite_e_bashkepunimit_bujqesor_14071_1.php', array('target' => '_blanc')); ?>
    </p>

    <h4>Plenary speeches - DOC, since 2009</h4>
    <p>
        Plenary speeches - root link:
        <br />
        <?php echo $this->Html->link('http://www.parlament.al/web/Sesioni_i_tete_15657_1.php', 'http://www.parlament.al/web/Sesioni_i_tete_15657_1.php', array('target' => '_blanc')); ?>
        <br /><br />
        Plenary speeches - example link:
        <br />
        <?php //echo $this->Html->link('http://www.parlament.gov.rs/Tre%C4%87a_posebna_sednica_Narodne_skup%C5%A1tine_Republike_Srbije_u_2014._godini_.21373.941.html', 'http://www.parlament.gov.rs/Tre%C4%87a_posebna_sednica_Narodne_skup%C5%A1tine_Republike_Srbije_u_2014._godini_.21373.941.html', array('target' => '_blanc')); ?>
    </p>

    <?php /* ?>
      <hr>
      <h2>Notatki:</h2>
      <p>
      http://www.parlament.gov.rs/national-assembly/composition/members-of-parliament.800.488.html
      <br>
      http://www.parlament.gov.rs/national-assembly.469.html
      </p>
      <?php */ ?>

</div>