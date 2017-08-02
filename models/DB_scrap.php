<?php

require_once 'config/config.php';

// ========================================
/**
 * DB_CAT
 *  id
 *  name
 *  url
 *  countArticles

  DB_ART
 *  id
 *  name
 *  url
 *  description
 *  ref
 *  refsite
 *  marque
 *  prix
 *  categorie_id
 *  tailles_id
 *  images_id
 *  stocks_id
 */
// ========================================

function DB_get_catIdByName($name) {

  $bdd = DB_connexion();

  $reqSql = $bdd->query(
          " SELECT id"
          . " FROM " . DB_CAT
          . " WHERE name='$name';"
      )->fetch();

  return $reqSql;
}
// ------------------------
function DB_hydr_categorie($name, $url, $cntArt) {

  $bdd = DB_connexion();

  $reqSqlTxt = " INSERT INTO " . DB_CAT
      . " (name, url, countArticles)"
      . " VALUES ('$name', '$url', '$cntArt');"
  ;

  $reqSql = $bdd->query($reqSqlTxt);

  if ($reqSql == FALSE) {
    if (DEBUG_DB)
    {
      echo("erreur DB_hydr_categorie<br>");
      echo("requete: " . $reqSqlTxt);
    }
    $result = FALSE;
  }
  else {
    $result = TRUE;
  }

  return $result;
}
// ------------------------
function DB_hydr_article($name, $url, $description
, $ref, $refsite, $marque, $prix, $cat_id, $cat_name) {

  $bdd = DB_connexion();

  $description = addslashes($description);

  $reqSqlTxt = " INSERT INTO " . DB_ART
      . " (name, url, description, ref, "
      . "refsite, marque, prix, categorie_id, cat_name)"
      . " VALUES ("
      . "'$name', '$url', '$description', '$ref'"
      . ", '$refsite', '$marque', '$prix', '$cat_id', '$cat_name'"
      . ");";

  $reqSql = $bdd->query($reqSqlTxt);

  if ($reqSql == FALSE) {
    if (DEBUG_DB) {
      echo("erreur DB_hydr_article<br>");
      echo("requete: " . $reqSqlTxt);
    }
    $result = FALSE;
  }
  else {
    $result = TRUE;
  }

  return $result;
}
// ------------------------
function DB_get_catById($id) {

  $bdd = DB_connexion();

  $reqSqlTxt = "SELECT *"
      . " FROM " . DB_CAT
      . " WHERE id='$id';";

  $reqSql = $bdd->query($reqSqlTxt)->fetchall();

  if ($reqSql == FALSE) {
    echo("erreur DB_get_catById<br>");
    echo("requete: " . $reqSqlTxt);
    return FALSE;
  }
  else {
    return $reqSql;
  }
}
// ------------------------
function DB_is_articleByUrl($url) {

  $bdd = DB_connexion();

  $reqSqlTxt = "SELECT 1"
      . " FROM " . DB_ART
      . " WHERE url='$url';";

  $reqSql = $bdd->query($reqSqlTxt)->fetch();

  if ($reqSql == FALSE) {
    return FALSE;
  }
  else {
    return TRUE;
  }
}
// ------------------------
function DB_up_scanById($id, $scan) {

  $bdd = DB_connexion();

  $reqSqlTxt =
      "UPDATE " . DB_CAT
      . " SET scan='$scan'"
      . " WHERE id='$id';"
  ;

  $reqSql = $bdd->query($reqSqlTxt);
}
// ------------------------
function DBScrap_getAllUrls() {

  $bdd = DB_connexion();

  $reqSqlTxt = "SELECT url"
      . " FROM " . DB_ART;

  $reqSql = $bdd->query($reqSqlTxt)->fetchall();

  return $reqSql;
}
// ------------------------
function DB_Articles_getAll_urls() {
  $bdd = DB_connexion();

  $reqSqlTxt = "SELECT url"
      . " FROM " . DB_ART;

  $reqSql = $bdd->query($reqSqlTxt)->fetchAll();

  return $reqSql;
}
// ------------------------
function DB_Photos_getAll() {
  $bdd = DB_connexion();

  $reqSqlTxt = "SELECT *"
      . " FROM " . DB_PHOTOS;

  $reqSql = $bdd->query($reqSqlTxt)->fetchAll();

  return $reqSql;
}
// ========================================

