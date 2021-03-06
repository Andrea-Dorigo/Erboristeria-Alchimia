<?php
    require_once("DBAccess.php");
    require_once("validate_form.php");

    $pagina = file_get_contents('register.html');
    if($_POST['Registrati']){
      $nome = ucfirst(strtolower(mysql_real_escape_string(trim($_POST['nome']))));
      $cognome = ucfirst(strtolower(mysql_real_escape_string(trim($_POST['cognome']))));
      $email = mysql_real_escape_string(trim($_POST['email']));
      $password = mysql_real_escape_string(trim($_POST['password']));
      $password_conferma = mysql_real_escape_string(trim($_POST['password_conferma']));
      $data_nascita = mysql_real_escape_string(trim($_POST['data_nascita']));
      /* messaggi di errore */
      $errore_empty = '<span class="errore">Completa tutti i campi</span>';
      $errore_full = '<span class="errore">Questo utente sembra essere gi&agrave; registrato. Hai dimenticato la password?</span>';
      $errore_nome = '<span class="errore">Inserisci un nome di lunghezza tra 3 e 100 caratteri</span>'; // riferirsi alle regole di validate_form
      $errore_cognome = '<span class="errore">Inserisci un cognome di lunghezza tra 3 e 100 caratteri</span>'; // riferirsi alle regole di validate_form
      $errore_email = '<span class="errore">Inserisci una email valida</span>';
      $errore_password = '<span class="errore">Inserisci una password di lunghezza tra 8 e 100 caratteri, almeno 1 lettera ed 1 numero</span>'; // riferirsi alle regole di validate_form
      $errore_conferma = '<span class="errore">Le password inserite non corrispondono</span>';

      $fields = [$nome, $cognome, $email, $password, $data_nascita];
      if(Validate_form::is_empty($fields)){
        $errore = $errore_empty;
      }
      else if(!Validate_form::check_str($nome)){
        $errore = $errore_nome;
      }
      else if(!Validate_form::check_str($cognome)){
        $errore = $errore_cognome;
      }
      else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $errore = $errore_email;
      }
      else if(!Validate_form::check_pwd($password)){
        $errore = $errore_password;
      }
      else if($password != $password_conferma){
        $errore = $errore_conferma;
      }
      else{
        $con = new DBAccess();
        if(!$con->openConnection()){
         echo '<span class="errore">Impossibile connettersi al database riprovare pi&ugrave; tardi</span>';
         exit;
        }

        if($con->getUser($email)){
          $errore = $errore_full;
        }
        else{
          $con->insertUser($nome,
                           $cognome,
                           $email,
                           $password,
                           $data_nascita);
        }

        $con->closeConnection();
      }
    }

    if($_POST['Registrati']){
      $status = (empty($errore)
                ? "<span>Registrazione riuscita</span>"
                : "<span>Registrazione fallita</span>");
    }
    $pagina = str_replace("%REGISTER_STATUS%", $status, $pagina);
    $pagina = str_replace("%REGISTER_ERROR%", $errore, $pagina);
    echo $pagina;
?>
