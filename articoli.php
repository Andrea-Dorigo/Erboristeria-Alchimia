<?php

  require_once("DBAccess.php");
  $pagina = file_get_contents('articoli.html');
  $conn = new DBAccess();
  if(!$conn->openConnection()) {
   echo '<p class= "errori">' . "Impossibile connettersi al database: riprovare pi&ugrave; tardi" . '</p>';
   exit(1);
  }

  //Stabilita connessione al db
  /* visualizza il set di caratteri utilizzato dal client */
  //printf("Initial character set: %s\n", $con->character_set_name());
  /* cambia il set di caratteri in utf8 */

  if (!$conn->connection->set_charset("utf8")) {
    //printf("Error loading character set utf8: %s\n", $con->error);
    exit(1);
  }

  /*-------------menu a tendina sesso---------------*/
  $opt_sesso = '<label for="sesso">Sesso</label>' . "\n" .
                '<select name="sesso" id="sesso">' . "\n" .
                '<option selected value="none">Seleziona filtro</option>' . "\n" .
                '<option value="Unisex">Unisex</option>' . "\n" .
                '<option value="Per lei">Per lei</option>' . "\n" .
                '<option value="Per lui">Per lui</option>' . "\n" .'</select>' . "\n";

  /*-------------menu a tendina categoria---------------*/
  $opt_categoria = '<label for="categoria">Categoria</label>' . "\n" .
                   '<select name="categoria" id="categoria">' . "\n" .
                   '<option selected value="none">Seleziona filtro</option>' . "\n";
  $result = $conn->connection->query('SELECT distinct nome_categoria from categorie');
  while($row = $result->fetch_assoc()) {
    $opt_categoria .= '<option value="' . $row['nome_categoria'] . '">' .$row['nome_categoria'] . '</option>' . "\n";
  }
  $opt_categoria .= '</select>' . "\n";

  /*-------------menu a tendina casa produttrice---------------*/
  $opt_casa_prod = '<label for="casa_prod">Casa produttrice</label>' . "\n" .
            '<select name="casa_prod" id="casa_prod">' . "\n" .
            '<option selected value="none">Seleziona filtro</option>' . "\n";
  $result = $conn->connection->query('SELECT distinct nome_ditta from ditte');
  while($row = $result->fetch_assoc()) {
    $opt_casa_prod .= '<option value="' . $row['nome_ditta'] . '">' . $row['nome_ditta'] . '</option>' . "\n";
  }
  $opt_casa_prod .= '</select>' . "\n";

  if(isset($_POST['search'])) {
    $noOption = "none"; //costante: valore opzione "Seleziona filtro"
    $search_value = trim($_POST['search']);
    $sex_filter = mysql_real_escape_string(trim($_POST['sesso'])); //forse superfluo il trim qua!
    $categ_filter = mysql_real_escape_string(trim($_POST['categoria']));
    $casa_prod_filter =  mysql_real_escape_string(trim($_POST['casa_prod']));
    $query_ricerca = "SELECT nome_articolo, quantita_magazzino_articolo
                      FROM articoli %categorie% %ditte% %produzioni%
                      WHERE nome_articolo LIKE ? %sex% %categ% %casa_prod%
                      ORDER BY id_articolo DESC";

    if($sex_filter != $noOption) { //restituisco i dati usati da utente per compilare il form + aggiorno la query di ricerca
      $opt_sesso = str_replace('selected', '' , $opt_sesso);
      $opt_sesso = str_replace('<option value="' . $sex_filter . '">' . $sex_filter . '</option>',
       '<option selected value="' . $sex_filter . '">' . $sex_filter . '</option>' , $opt_sesso);
      $query_ricerca = str_replace("%sex%", "AND sesso_target = '$sex_filter'", $query_ricerca);
    }
    else {
      $query_ricerca = str_replace("%sex%", "" , $query_ricerca);
    }

    if($categ_filter != $noOption) {
      $opt_categoria = str_replace('selected', '' , $opt_categoria);
      $opt_categoria = str_replace('<option value="' . $categ_filter . '">' . $categ_filter . '</option>',
       '<option selected value="' . $categ_filter . '">' . $categ_filter . '</option>' , $opt_categoria);
      $query_ricerca = str_replace("%categorie%", ",categorie" , $query_ricerca);
      $query_ricerca = str_replace("%categ%", "AND categoria_articolo = id_categoria
          AND nome_categoria = '$categ_filter'", $query_ricerca);
    }
    else {
      $query_ricerca = str_replace("%categorie%", "" , $query_ricerca);
      $query_ricerca = str_replace("%categ%", "" , $query_ricerca);
    }

    if($casa_prod_filter != $noOption) {
      $opt_casa_prod = str_replace('selected', '' , $opt_casa_prod);
      $opt_casa_prod = str_replace('<option value="' . $casa_prod_filter . '">' . $casa_prod_filter . '</option>',
       '<option selected value="' . $casa_prod_filter . '">' . $casa_prod_filter . '</option>' , $opt_casa_prod);
      $query_ricerca = str_replace("%ditte%", ",ditte" , $query_ricerca);
      $query_ricerca = str_replace("%produzioni%", ",produzioni" , $query_ricerca);
      $query_ricerca = str_replace("%casa_prod%", "AND articolo_produzione = id_articolo
        AND ditta_produzione = id_ditta AND nome_ditta = '$casa_prod_filter' ", $query_ricerca);
    }
    else {
       $query_ricerca = str_replace("%ditte%", "" , $query_ricerca);
       $query_ricerca = str_replace("%produzioni%", "" , $query_ricerca);
       $query_ricerca = str_replace("%casa_prod%", "" , $query_ricerca);
    }

    if(!$stmt = mysqli_prepare($conn->connection, $query_ricerca)) {
      die('prepare() failed: ' . htmlspecialchars(mysqli_error($conn->connection)));
    }

    $search_value = preg_replace("#[^0-9a-z]#", "", $search_value); //capire cosa fa!!!
    $search_value = '%'.$search_value.'%'; //non ricordo a cosa serve!!!
    $stmt->bind_param("s", $search_value);
    $stmt->execute();
    $result = $stmt->get_result();

    $productToPrint = "";
    if($result->num_rows === 0) {
      $productToPrint = '<p>La ricerca non ha prodotto alcun risultato</p>';
    }
    else while($row = $result->fetch_assoc()) {
      $productToPrint .= '<div class = "col-sn-4 col-md-3">' . "\n" .
      '<form method="post" action="carrello.php?action=add&id_articolo='. $row["id_articolo"] .  '">'. "\n" .
      '<div class="products">' . "\n" .
      '<img src="img/articoli/'.(file_exists("
      img/articoli/".$row["id_articolo"].".jpg") ? $row["id_articolo"].'.jpg' : '0.jpg').'" class="img-responsive"/>'."\n" .
      '<h4 class="text-info">' . $row["nome_articolo"] . '</h4>' . "\n" .
      '<h4>' . $row["prezzo_articolo"] . ' €' . '</h4>' ."\n" .
      '<input type="text" name="quantita" class="form-control" value="1" />' ."\n" .
      '<input type="hidden" name="nome_articolo" value="' . $row["nome_articolo"] . '"/>' . "\n" .
      '<input type="hidden" name="prezzo_articolo" value="' . $row["prezzo_articolo"] . '"/>' . "\n" .
       '<input type="submit" name="add_to_cart" class="btn btn-info CUSTOM_MARGIN" value="Add to Cart" />' . "\n" .
      '</div>' . "\n" . '</form>' . "\n" . '</div>' ."\n";
    }

    $stmt->close();
    $pagina = str_replace("%PRODUCTS%", $productToPrint , $pagina);
  }

  $conn->closeConnection();
  $pagina = str_replace("%PRODUCTS%", '' , $pagina);
  $pagina = str_replace("%SEX_FILTER%", $opt_sesso , $pagina);
  $pagina = str_replace("%CATEGORY_FILTER%", $opt_categoria , $pagina);
  $pagina = str_replace("%COMPANY_FILTER%", $opt_casa_prod , $pagina);
  echo $pagina;
?>
