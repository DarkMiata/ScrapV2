<?php
/*
 * Autheur: Samuel Vermeulen
 * Date: 01/08/2017
 */

require_once ("config/config.php");

$bdd               = null;
$sqlConnexionState = FALSE;
define('DEBUGCLI', false);
set_time_limit(36000);

// retire le buffer de la sortie console ?
ob_start(null, 2);

echln("");
echln("** lancement du script **");

argumentsController($argv);

exit();
// ========================================
// ========================================

function argumentsController(array $argv) {

  switch ($argv[1]) {

    case 'script_scrap.php':
      break;

    case 'help':
      helpAction();
      break;

    case 'scanlistcat':
      scanSaveCatsAction();
      break;

    case 'scanall':
      scanAllAction();
      break;

    case 'scanimages':
      scanImagesAction();
      break;

    case 'onlysavehtml':
      onlySaveHtmlAction();
      break;

    case 'saveimgs':
      saveImgsAction();
      break;

    case 'scancat':
      if (isset($argv[2]) == FALSE) {
        echln('argument manquant');
      }
      else {
        getCatsByIDAction($argv[2]);
      }
      break;

    default:
      break;
  }
}
// ------------------------
function helpAction() {
  echln("help doc\nscanlistcat\nscanall\nonlysavehtml\n");
}
// ------------------------
// Récupère les images du site en local par rapport aux données DB
function saveImgsAction() {
  $namePhotos = DB_Photos_getAll();

  $countPhotos = count($namePhotos);
  $index       = 1;

  foreach ($namePhotos as $namePhoto) {
    $fileName = $namePhoto['file_name'];
    $refArt   = $namePhoto['ref_article'];
    $refImg   = $namePhoto['id'];

    $localFile = PATH_LOCAL_IMG . $fileName;

    echo($index . "/" . $countPhotos);

    if (file_exists($localFile) == FALSE) {
      $urlImgLarge = "http://blzjeans.com/" . $refArt . "-" . $refImg . "-thickbox/" . $fileName;

      // Charge l'image JPG du site et sauvegarde en local.
      $fileContent = file_get_contents($urlImgLarge);
      file_put_contents(PATH_LOCAL_IMG . $fileName, $fileContent);
      echo(" (WEB) - ");
    }
    else {
      echo(" (LOC) - ");
    }

    // pourcentage d'avancée de la sauvegarde
    $pourcentage = round(floatval(100 * $index) / floatval($countPhotos), 1);

    echln($pourcentage . "% - " . $fileName);
    $index++; // numéro d'index de l'image pour calcul pourcentage
  }
}
// ------------------------
function scanImagesAction() {
  // récupère les urls des pages de tout les articles
  $urls = DB_Articles_getAll_urls();

  $countArticles = count($urls);
  $indexArticle  = 1;

  foreach ($urls as $url) {
    // Charge les pages en local, sinon récup web et sauvegarde en local
    $html = loadAndSaveHTML($url['url'], LOCAL_SAVE_HTML, true);

    // récupération des images de la page articles
    scrap_imgsArticle($html, $indexArticle++, $countArticles);
  }
}
// ------------------------
function getCatsByIDAction($catId) {
  $ArticlesCat = getCatsByID($catId);
}
// ------------------------
function scrap_imgsArticle($htmlFile, $indexArticle, $countArticles) {

  $blockTinyImgs = $htmlFile->find('ul[id=thumbs_list_frame]')[0];

  $listImgsThumbs = $blockTinyImgs->find('li');

  foreach ($listImgsThumbs as $blockLiImg) {
    $urlImg = $blockLiImg->find('img')[0]->attr['src'];

    // explode de l'url et récupération du nom du répertoire
    $urlImgExpl = explode("/", $urlImg);

    $directoryImg = $urlImgExpl[1];
    $fileImg      = $urlImgExpl[2];

    // explode du nom du répertoire racine et récupération du numéro image
    $directoryExpl = explode("-", $directoryImg);

    $refArticle = $directoryExpl[0];
    $refImage   = $directoryExpl[1];

    if (($refImage !== null) && ($refArticle !== null)) {
      // nouveau format du nom de l'image: ref article + ref img
      $imgLocalName = $refArticle . "-" . $refImage . "-large.jpg";

      echln($indexArticle . "/" . $countArticles . " - " . $imgLocalName);

      DB_BLZ_add_photo($refImage, $refArticle, $imgLocalName);
    }
  }
}
// ------------------------
// Scanne les catégories et récupère les pages html des articles en les sauvegardant en local.
function onlySaveHtmlAction() {
  $cats = scanSaveCats();

  foreach ($cats as $cat) {
    $catUrl = $cat->get_url();

    $nbrArticles = $cat->get_countArticles();
    $nbrPages    = floor($nbrArticles / 30) + 1;

    // scan toutes les pages de la liste articles de la catégorie
    for ($page = 1; $page < ($nbrPages + 1); $page++) {
      $listeUrlArticlesPageCat = scanPageCat($page, $cat);

      foreach ($listeUrlArticlesPageCat as $articleUrl) {
        // injecte dans la liste des articles de la catégories, les éléments
        // trouvé dans la nouvelle page.
        $listeUrlArticlesCat[] = $articleUrl;
        $articleCount++;

        echo ($articleCount . " - ");
        echln($articleUrl);

        $article = new Article();
        $article->onlySaveHtml($articleUrl);
      }
      echln("================================================");
    }
  }
}
// ------------------------
function scanAllAction() {
  $cats = scanSaveCats();

  var_dump($cats);

  foreach ($cats as $cat) {
    $catUrl = $cat->get_url();

    $nbrArticles = $cat->get_countArticles();
    $nbrPages    = floor($nbrArticles / 30) + 1;

    // scan toutes les pages de la liste articles de la catégorie
    for ($page = 1; $page < ($nbrPages + 1); $page++) {
      $listeUrlArticlesPageCat = scanPageCat($page, $cat);

      foreach ($listeUrlArticlesPageCat as $articleUrl) {
        // injecte dans la liste des articles de la catégories, les éléments
        // trouvé dans la nouvelle page.
        $listeUrlArticlesCat[] = $articleUrl;
        $articleCount++;

        echo ($articleCount . " - ");
        echln($articleUrl);

        $article = new Article();
        $article->scrap_pageArticle($articleUrl);

        //var_dump($article);
      }
      echln("================================================");
    }
  }
}
// ========================================
// ========================================

function scrapCategories() {
  $htmlMainPage = file_get_html(URL_SITE);

  // récup des deux menus: "catégories" et "marque"
  $block_menus = $htmlMainPage->find('ul[class=advcSearchList]');

  // dans le menu "catégories" on recherche les "li"
  $block_menuCat = $block_menus[0]->find('li');

  $countCats = count($block_menuCat);

  echo ('Nombre de catégories trouvés: ' . $countCats . "\n");

  // dans chaque catégorie, rechercher le lien
  foreach ($block_menuCat as $htmlCat) {
    $categorie = new Categorie();

    //récup du lien
    $categorie->set_url($htmlCat->find('[href]')[0]->attr['href']);

    // récup du nom de la catégorie
    $nomCatHref = $htmlCat->find('[href]')[0]->plaintext;
    $nomCatExpl = explode("(", $nomCatHref);

    $categorie->set_countArticles(explode(")", $nomCatExpl[1])[0]);
    $categorie->set_name(trim($nomCatExpl[0]));

    $categories[] = $categorie;

    debugCLI('nom: ' . trim($nomCatExpl[0]));
    debugCLI('url: ' . $categorie->get_url());
    debugCLI('nbr articles: ' . $categorie->get_countArticles());
    debugCLI('---------------------------------');
  }
  return $categories;
}
// ------------------------
function scanSaveCatsAction() {
  $cats = scrapCategories();  // Récup des Cats en array
  catsArrayToDB($cats);       // Sauvegarde en DB

  return $cats;
}
// ------------------------
function scanPageCat($page, Categorie $cat) {

  $urlPage = $cat->get_url() . "?p=$page";

  $html = file_get_html($urlPage);

  $blockListe    = $html->find('div[id=products_list]')[0];
  $blockArticles = $blockListe->find('li[class=product_list_block]');

  foreach ($blockArticles as $blockArt) {

    $urlArticle = $blockArt->find('a')[0]->attr['href'];

    // l'article existe dans la DB ?
    if (DB_is_articleByUrl($urlArticle) == FALSE) {

      $article = new Article;
      $article->scrap_pageArticle($urlArticle);
      $article->to_DB();
    }

    $listeUrlArticlePageCat[] = $urlArticle;
  }

  return $listeUrlArticlePageCat;
}
// ------------------------
function getCatsByID($catId) {
  $articleCount = 0;

  $cat = DB_get_catById($catId);

  $categorie = new Categorie();
  $categorie->hydrateFromArray($cat[0]);

  $nbrArticles = $categorie->get_countArticles();
  $nbrPages    = floor($nbrArticles / 30) + 1;

  // scan toutes les pages de la liste articles de la catégorie
  for ($page = 1; $page < ($nbrPages + 1); $page++) {
    $listeUrlArticlesPageCat = scanPageCat($page, $categorie);

    foreach ($listeUrlArticlesPageCat as $articleUrl) {
      // injecte dans la liste des articles de la catégories, les éléments
      // trouvé dans la nouvelle page.
      $listeUrlArticlesCat[] = $articleUrl;
      $articleCount++;

      echo ($articleCount . " - ");
      echln($articleUrl);

      $article = new Article();
      $article->scrap_pageArticle($articleUrl);

      //var_dump($article);
    }
    echln("================================================");
  }

  return $listeUrlArticlesCat;
}
