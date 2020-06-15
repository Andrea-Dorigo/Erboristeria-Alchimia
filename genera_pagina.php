<?php
  require_once("session.php");
  require_once("menu_pagina.php");

  class Genera_pagina {

    public function genera($base, $target, $contenuto="") {
      $pagina = file_get_contents($base);
      // impostazione icona del carrello
      if($_SESSION['auth'] && $_SESSION['tipo_utente']=="User") {
        $icona_carrello = '<span id="cart_icon" class="material-icons-outlined top_icon">shopping_cart</span>';
      }
      // pagine generabili
      switch($target) {
        case "carrello" :
          $icona_carello = "";
        break;

        case "eventi" :
          $titolo = "Eventi";
          $titolo_pagina = "Eventi di Erboristeria Alchimia";
          $descrizione_pagina = "Qui troverai i prossimi eventi in programma e quelli passati organizzati";
          $keywords_pagina = "eventi, evento, relatori, relatore, organizzazione, organizza, erboristeria, alchimia";
          $container_pagina = "container_te_e_infusi"; // TODO: controllare  effetti su CSS usando container_eventi
          $lista_menu = menu_pagina::menu("eventi.php");
        break;

        case "form_eventi" :
        break;

        case "index" :
        break;

        case "informazioni" :
          $titolo = "Informazioni";
          $titolo_pagina = "Informazioni di Erboristeria Alchimia";
          $descrizione_pagina = "Informazioni utili per contattarci: qui trovi i nostri contatti, gli orari di apertura e il nostro indirizzo";
          $keywords_pagina = "informazioni, orari, apertura, chiusura, email, mail, telefono, cellulare, posizione, mappa, erboristeria, alchimia";
          $container_pagina = "container_informazioni";
          $lista_menu = menu_pagina::menu("informazioni.php");
        break;

        case "la_mia_storia" :
          $titolo = "La mia storia";
          $titolo_pagina = "La mia storia di Erboristeria Alchimia";
          $descrizione_pagina = "Dove e come siamo nati, tutti i dettagli e le curiosità di Erboristeria Alchimia";
          $keywords_pagina = "storia, Marika, erboristeria, alchimia";
          $container_pagina = "container_la_mia_storia";
          $lista_menu = menu_pagina::menu("la_mia_storia.php");
        break;

        case "prodotti" :
          $titolo = "Prodotti";
          $titolo_pagina = "Prodotti di Erboristeria Alchimia";
          $descrizione_pagina = "I prodotti online di Erboristeria Alchimia. Qualità, sicurezza e convenienza garantiti";
          $keywords_pagina = "prodotto, prodotti, cosmetici, alimentari, erboristeria, alchimia";
          $container_pagina = "container_prodotti";
          $lista_menu = menu_pagina::menu("prodotti.php");
        break;

        case "profilo" :
        break;

        case "registrazione" :
        break;

        case "teinfusi" :
          $titolo = "T&egrave; &amp; Infusi";
          $titolo_pagina = "T&egrave; e infusi di Erboristeria Alchimia";
          $descrizione_pagina = "Visualizza i nostri te e infusi";
          $keywords_pagina = "te, infusi, te e infusi, erboristeria, alchimia";
          $container_pagina = "container_te_e_infusi";
          $lista_menu = menu_pagina::menu("teinfusi.php");
        break;
      }
      $pagina = str_replace("%TITOLO%", $titolo, $pagina);
      $pagina = str_replace("%TITOLO_PAGINA%", $titolo_pagina, $pagina);
      $pagina = str_replace("%DESCRIZIONE_PAGINA%", $descrizione_pagina, $pagina);
      $pagina = str_replace("%KEYWORDS_PAGINA%", $keywords_pagina, $pagina);
      $pagina = str_replace("%CONTAINER_PAGINA%", $container_pagina, $pagina);
      $pagina = str_replace("%LISTA_MENU%", $lista_menu, $pagina);
      $pagina = str_replace("%ICONA_CARRELLO%", $icona_carrello, $pagina);
      $pagina = str_replace("%CONTENUTO_PAGINA%", $contenuto, $pagina);
      return $pagina;
    }

  }
?>
