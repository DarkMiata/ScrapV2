<?php

//function test_scanUneCategorie($catId) {
//
//  $cat = DB_get_catById($catId);
//
//  $categorie = new Categorie();
//  $categorie->hydrateFromArray($cat[0]);
//
//  $nbrArticles  = $categorie->get_countArticles();
//  $nbrPages     = floor($nbrArticles / 30)+1;
//
//  for ($n=1; $n < ($nbrPages+1); $n++) {
//    $categorie->scrap_PageCategorie($n);
//  }
//}
// ------------------------
function scanRootPhotoAndSaveToDB() {
  $listLocalImgs = scandir(PATH_LOCAL_IMG, SCANDIR_SORT_ASCENDING);

  //var_dump($listLocalImgs);

  foreach ($listLocalImgs as $localImgFileName) {
    $lastChar = substr($localImgFileName, -9);

    // le fichier fini bien par "large.jpg" ?
    if ($lastChar == "large.jpg") {
      $explFile     = explode("-", $localImgFileName);
      $uniqueId     = $explFile[1];
      $refArticle   = $explFile[0];

      DB_BLZ_add_photo($uniqueId, $refArticle, $localImgFileName);
    }
  }
}
