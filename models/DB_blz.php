<?php

require_once 'config/config.php';

// ========================================

function DB_ref_exit($ref) {
  $bdd = DB_connexion();

  $reqSql = $bdd->query(
          " SELECT ref"
          . " FROM blz"
          . " WHERE ref='$ref';"
      )->fetch();

  return $reqSql;
}
// ------------------------
function DB_get_BlzAll($ref) {
  $bdd = DB_connexion();

  $reqSql = $bdd->query(
          " SELECT nom, marque, ref, prix, img_fichier, img_url"
          . " FROM blz"
          . " WHERE ref='$ref';"
      )->fetch();

  return $reqSql;
}
// ------------------------
function DB_add_article($nom, $marque, $ref, $prix, $img_fichier, $img_url) {

  $bdd = DB_connexion();

  if (DB_ref_exit($ref) === FALSE) {

    $reqSql = $bdd->query(
            " INSERT INTO blz"
            . " (nom, marque, ref, prix, img_fichier, img_url)"
            . " VALUES ('$nom', '$marque', '$ref', '$prix', '$img_fichier', '$img_url');"
        )->fetch();
  }
  else {
    //echo "la ref $ref existe déjà dans la DB<br>";
    $reqSql = FALSE;
  }

  return $reqSql;
}
// ------------------------
function DB_getBlzLinkUrl($ref) {
  $bdd = DB_connexion();

  $reqSql = $bdd->query(
          "SELECT url_page_article"
          . " FROM blz"
          . " WHERE ref='$ref';"
      )->fetch();

  var_dump($reqSql);

  return $reqSql;
}
// ------------------------
function DB_setBlzHrefByRef($ref, $href) {
  $bdd = DB_connexion();

  echo ("setblzhrefburef: ref:" . $ref . " - href:" . $href . "<br>");

  $reqSql = $bdd->query(
          "UPDATE blz"
          . " SET url_page_article='$href'"
          . " WHERE ref='$ref'"
      )->fetch();
}
// ------------------------
function DB_BLZ_add_photo($id, $ref_article, $file_name) {
  global $bdd;
  $bdd = DB_connexion();

  //var_dump($bdd);

  $reqSql = $bdd->query(
      "INSERT INTO blz_photos"
      . " (id, ref_article, file_name)"
      . " VALUES ('$id', '$ref_article', '$file_name');");
}
// ------------------------
function DB_BLZ_listOfImages($ref_article) {
  global $bdd;
  $bdd = DB_connexion();

  $reqSql = $bdd->query(
      "SELECT file_name"
      . " FROM blz_photos"
      . " WHERE ref_article='$ref_article'"
      . " ORDER BY id ASC;")->fetchAll();

  return $reqSql;


}

