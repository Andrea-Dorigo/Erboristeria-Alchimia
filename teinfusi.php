<?php
  header('Content-Type: text/html; charset=UTF-8');

  require_once("DBAccess.php");
  require_once("Image.php");
  require_once('menu_pagina.php');

  $con = new DBAccess();
  if($con->openConnection()){
    $pagina = file_get_contents('base.html');
    $pagina = str_replace("%TITOLO_PAGINA%", "T&egrave; &amp; Infusi", $pagina);
    $pagina = str_replace("%DESCRIZIONE_PAGINA%", "Visualizza i nostri te e infusi", $pagina);
    $pagina = str_replace("%KEYWORDS_PAGINA%", "te, infusi, te e infusi, erboristeria, alchimia", $pagina);
    $pagina = str_replace("%CONTAINER_PAGINA%", "container_te_e_infusi", $pagina);
    $pagina = str_replace("%LISTA_MENU%", menu_pagina::menu("teinfusi.php"), $pagina);

    $lista_te_e_infusi = $con->getTeInfusi();

    foreach ($lista_te_e_infusi as $row){
      $immagine = Image::getImage("./img/te_e_infusi/", $row["id_te_e_infusi"]);
      $descrizione_immagine = htmlentities($row["descrizione_immagine_te_e_infusi"]);
      $nome = htmlentities($row["nome_te_e_infusi"]);
      $ingredienti = DBAccess::nl2p(htmlentities($row["ingredienti_te_e_infusi"]));
      $descrizione = DBAccess::nl2p(htmlentities($row["descrizione_te_e_infusi"]));
      $preparazione = DBAccess::nl2p(htmlentities($row["preparazione_te_e_infusi"]));

      $lista .=
        '<div class="card collapsed" title="'.$nome.'. Premi per espandere la descrizione">
          <h3>'.$nome.'</h3>
          <a class="accessibility_hidden">Salta la descrizione di questo t&egrave; o infuso</a>
          <img src="'.$immagine.'" alt="'.$descrizione_immagine.'"/>
          <h4>Ingredienti</h4>
          <p>'.$ingredienti.'</p>
          <h4>Descrizione</h4>
          <p>'.$descrizione.'</p>
          <h4>Preparazione</h4>
          <p>'.$preparazione.'</p>
          <span class="expand_btn material-icons-round">expand_more</span>
        </div>

        ';
    }

    $container = file_get_contents('teinfusi.html');
    $container = str_replace("%LISTA_TE_E_INFUSI%", $lista, $container);
    $pagina = str_replace("%CONTENUTO_PAGINA%", $container, $pagina);
    echo $pagina;
  }
  else{
    echo "<h1>Impossibile connettersi al database riprovare pi&ugrave; tardi<h1>";
    exit;
  }
?>
