<?php

require_once 'config/config.php';

// ========================================

function DB_connexion() {
  global $bdd, $sqlConnexionState;

  //var_dump($sqlConnexionState);
  if ($sqlConnexionState == FALSE) {
    $bdd_co = 'mysql:host=' . DB_URL
        . ';dbname=' . DB_NAME
        . ';charset=utf8';

    $bdd = new PDO($bdd_co, DB_LOGIN, DB_PWD);

    $sqlConnexionState = TRUE;
  }

  return $bdd;
}
