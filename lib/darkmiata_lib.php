<?php

/**
 *
 */

// ================================
function progessBat($index, $total) {


}
// ------------------------
function debugCLI ($string) {
  if (DEBUGCLI === TRUE) {
    echo ($string."\n");
  }
}
// ------------------------
function echln ($text) {
  echo ($text."\n");
}
// ------------------------
function cleanString ($string, $deep = 5) {
  $string = trim($string);
  $string = str_replace("\n", "", $string);
  $string = str_replace("\r", "", $string);
  $string = str_replace("\t", "", $string);
  for ($n = 1; $n < $deep; $n++) {
    $string = str_replace("  ", " ", $string);
  }

  return $string;
}
// ------------------------
/**
 * Fonction qui permet de charger une page HTML.
 * Si la page n'existe pas en local, elle est récupéré via internet et sauvegardé en local pour être réutilisé par la suite
 * @param type $url
 * @param type $directory
 * @return type
 */
function loadAndSaveHTML ($url, $directory, $return) {
  $fileName = explode("/", $url);
  $fileName = end($fileName);

  $fileExtension = explode(".", $fileName)[1];

  if ($fileExtension != "html") {
    throw new Exception("Le type de fichier attendu doit être en HTML - fichier: ".$fileName);
  }

  $localFile = $directory . $fileName;

  // Si le fichier existe en local, le charger.
  // Sinon le charger de l'url et le sauvegarder en local.
  if (file_exists($localFile) == FALSE) {
    $html = file_get_html($url);
    file_put_contents($localFile, $html);
  } else {
    if ($return == TRUE) {
      $html = file_get_html($localFile);
    }
  }

  // si $return est à TRUE, retourner l'html, sinon retourner NULL
  if ($return == FALSE) {
    $html = NULL;
  }

  return $html;
}


