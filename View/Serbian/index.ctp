<div class="actions">
    <?php
    echo $this->element('m_serbia');
    ?>
</div>
<div class="posts form">
    <?php echo $this->Session->flash(); ?>
    <h2>Parliament: Serbia – National Assembly</h2>

    <h4>MP's party - description: HTML, since 2004, 2004-2012 root link 1, since 2012 root link 2</h4>
    <p>
        MP's party - root link:
        <br />
        <?php echo $this->Html->link('http://www.parlament.gov.rs/narodna-skupstina-/sastav/narodni-poslanici/arhiva-saziva/saziv-od-27-januara-2004.893.html', 'http://www.parlament.gov.rs/narodna-skupstina-/sastav/narodni-poslanici/arhiva-saziva/saziv-od-27-januara-2004.893.html', array('target' => '_blanc')); ?>
        <br />
        <?php echo $this->Html->link('http://www.parlament.gov.rs/narodna-skupstina-/sastav/narodni-poslanici/aktuelni-saziv.890.html', 'http://www.parlament.gov.rs/narodna-skupstina-/sastav/narodni-poslanici/aktuelni-saziv.890.html', array('target' => '_blanc')); ?>
        <br /><br />
        MP's party - example link:
        <br />
        <?php echo $this->Html->link('http://www.parlament.gov.rs/MARKO_ATLAGI%C4%86+.603.891.html', 'http://www.parlament.gov.rs/MARKO_ATLAGI%C4%86+.603.891.html', array('target' => '_blanc')); ?>
    </p>

    <h4>MP's contact info - description: Contact form, since 2012</h4>
    <p>
        MP's contact info - root link:
        <br />
        <?php echo $this->Html->link('http://www.parlament.gov.rs/narodna-skupstina-/sastav/narodni-poslanici/aktuelni-saziv.890.html', 'http://www.parlament.gov.rs/narodna-skupstina-/sastav/narodni-poslanici/aktuelni-saziv.890.html', array('target' => '_blanc')); ?>
        <br /><br />
        MP's contact info - example link:
        <br />
        <?php echo $this->Html->link('http://www.parlament.gov.rs/MARKO_ATLAGI%C4%86+.603.891.html', 'http://www.parlament.gov.rs/MARKO_ATLAGI%C4%86+.603.891.html', array('target' => '_blanc')); ?>
    </p>

    <h4>Plenary voting - description: Native PDF, since 2012, in transctipts of sessions</h4>
    <p>
        Plenary voting - root link:
        <br />
        <?php echo $this->Html->link('http://www.parlament.gov.rs/%D0%9E%D1%81%D0%BC%D0%BE_%D0%B2%D0%B0%D0%BD%D1%80%D0%B5%D0%B4%D0%BD%D0%BE_%D0%B7%D0%B0%D1%81%D0%B5%D0%B4%D0%B0%D1%9A%D0%B5_%D0%9D%D0%B0%D1%80%D0%BE%D0%B4%D0%BD%D0%B5.19061.43.html', 'http://www.parlament.gov.rs/%D0%9E%D1%81%D0%BC%D0%BE_%D0%B2%D0%B0%D0%BD%D1%80%D0%B5%D0%B4%D0%BD%D0%BE_%D0%B7%D0%B0%D1%81%D0%B5%D0%B4%D0%B0%D1%9A%D0%B5_%D0%9D%D0%B0%D1%80%D0%BE%D0%B4%D0%BD%D0%B5.19061.43.html', array('target' => '_blanc')); ?>
        <br /><br />
        Plenary voting - example link:
        <br />
        <?php echo $this->Html->link('http://www.parlament.gov.rs/upload/archive/files/cir/doc/Listinzi/2013.07.05%201.%20Predlog%20zakona%20o%20izmenama%20i%20dopunama%20Zakona%20o%20budzetu%20Republike%20Srbije%20za%202013.%20godinu,%20u%20celini.pdf', 'http://www.parlament.gov.rs/upload/archive/files/cir/doc/Listinzi/2013.07.05%201.%20Predlog%20zakona%20o%20izmenama%20i%20dopunama%20Zakona%20o%20budzetu%20Republike%20Srbije%20za%202013.%20godinu,%20u%20celini.pdf', array('target' => '_blanc')); ?>
    </p>

    <h4>Plenary speeches - description: HTML, since 2012</h4>
    <p>
        Plenary speeches - root link:
        <br />
        <?php echo $this->Html->link('http://www.parlament.gov.rs/aktivnosti/narodna-skup%C5%A1tina/zasedanja/redovna.949.html', 'http://www.parlament.gov.rs/aktivnosti/narodna-skup%C5%A1tina/zasedanja/redovna.949.html', array('target' => '_blanc')); ?>
        <br /><br />
        Plenary speeches - example link:
        <br />
        <?php echo $this->Html->link('http://www.parlament.gov.rs/Tre%C4%87a_posebna_sednica_Narodne_skup%C5%A1tine_Republike_Srbije_u_2014._godini_.21373.941.html', 'http://www.parlament.gov.rs/Tre%C4%87a_posebna_sednica_Narodne_skup%C5%A1tine_Republike_Srbije_u_2014._godini_.21373.941.html', array('target' => '_blanc')); ?>
    </p>


    <hr>
    <h2>Notatki:</h2>
    <p>
        http://api.parldata.eu/rs/parlament
        Domyślnie ma prawa odczytu, ale można do niej zapisywać podając użytkownika "scraper" i hasło "ngaA(f77"

    </p>
    <p>
        https://docs.google.com/spreadsheets/d/1rtV61VL3MdbgzqWh4MbCGukTdU6xPWpYc7SsNApSLOE/edit#gid=0
    </p>
    <p>
        https://docs.google.com/document/d/1N9thSTGnRuy9GlK81QVqSB1T42K2CHlITTv6OrostS0/edit
    </p>


</div>