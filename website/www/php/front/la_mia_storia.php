<?php
  require_once("../back/genera_pagina.php");

  $contenuto = file_get_contents("../../html/la_mia_storia.html");
  $pagina = Genera_pagina::genera("../../html/base.html", "la_mia_storia", $contenuto);
  echo $pagina;
?>
