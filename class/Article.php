<?php

/**
 *
 */

class Article
{

  /**
   * @var string
   */
  public $name;

  /**
   * @var string
   */
  public $url;

  /**
   * @var string
   */
  public $description;

  /**
   * @var integer
   */
  public $ref;

  /**
   * @var string
   */
  public $refSite;

  /**
   * @var string
   */
  public $marque;

  /**
   * @var float
   */
  public $prix;

  /**
   * @var string
   */
  public $categorie;

  /**
   * @var array
   */
  public $tailles;

  /**
   * @var array
   */
  public $images;

  /**
   * @var array
   */
  public $stocks;


  // ------------------------

  /**
   * @var Categorie[]
   */
  public $articles;

  // ========================================

  public function get_name() {
    return $this->name;
  }
  public function get_url() {
    return $this->url;
  }
  public function get_description() {
    return $this->description;
  }
  public function get_ref() {
    return $this->ref;
  }
  public function get_refSite() {
    return $this->refSite;
  }
  public function get_marque() {
    return $this->marque;
  }
  public function get_prix() {
    return $this->prix;
  }
  public function get_tailles() {
    return $this->tailles;
  }
  public function get_images() {
    return $this->images;
  }
  public function get_stocks() {
    return $this->stocks;
  }
  public function get_articles() {
    return $this->articles;
  }
  public function get_categorie() {
    return $this->categorie;
  }
  // ========================================

  public function set_name($name) {
    $this->name = $name;
  }
  public function set_url($url) {
    $this->url = $url;
  }
  public function set_description($description) {
    $this->description = $description;
  }
  public function set_ref($ref) {
    $this->ref = $ref;
  }
  public function set_refSite($refSite) {
    $this->refSite = $refSite;
  }
  public function set_marque($marque) {
    $this->marque = $marque;
  }
  public function set_prix($prix) {
    $this->prix = $prix;
  }
  public function set_tailles($tailles) {
    $this->tailles = $tailles;
  }
  public function set_images($images) {
    $this->images = $images;
  }
  public function set_stocks($stocks) {
    $this->stocks = $stocks;
  }
  public function set_articles(array $articles) {
    $this->articles = $articles;
  }
  public function set_categorie($categorie) {
    $this->categorie = $categorie;
  }
  // ========================================
  // ========================================

  public function to_DB() {

    $reqSql = DB_hydr_article(
          $this->get_name()
        , $this->get_url()
        , $this->get_description()
        , $this->get_ref()
        , $this->get_refSite()
        , $this->get_marque()
        , $this->get_prix()
        , "0"
        , $this->get_categorie()
        );

    return $reqSql;
  }
  // ------------------------

  public function onlySaveHtml($url) {
    $this->set_url($url);

    // Récupération du numéro ref (dans url lien de la page)
    $urlExpl = explode("/", $url)[4];
    $ref     = explode('-', $urlExpl)[0];
    $ref     = intval($ref);
    $this->set_ref($ref);

    debugCLI($ref);

    loadAndSaveHTML($url, LOCAL_SAVE_HTML, false);
  }

  // ------------------------
  public function scrap_pageArticle($url) {

    $this->set_url($url);

    // Récupération du numéro ref (dans url lien de la page)
    $urlExpl = explode("/", $url)[4];
    $ref     = explode('-', $urlExpl)[0];
    $ref     = intval($ref);
    $this->set_ref($ref);

    debugCLI($ref);

    $htmlPage = loadAndSaveHTML($url, LOCAL_SAVE_HTML, true);
    //$htmlPage = file_get_html($url);

    // Récupération du bloc principal de la page contenant les infos
    $urlBlock = $htmlPage->find('div[id=product_block]')[0];

    // Récupération du nom (div h1)
    $nom = $urlBlock->find('h1')[0]->plaintext;
    $nom = html_entity_decode($nom);
    $this->set_name($nom);

    debugCLI($nom);

    // Récupération du descriptif de l'article
    $descript = $htmlPage->find('div[id=desc_long]')[0]->plaintext;
    $descript = cleanString($descript);
    $this->set_description($descript);
    // Espaces en trop dans le corps du texte à supprimer par la suite

    debugCLI($descript);

    $refSite = $urlBlock->find('span[class=editable]')[0]->plaintext;
    $refSite = trim($refSite);
    $this->set_refSite($refSite);

    // Récupération du prix
    $prix = $urlBlock->find('span[id=our_price_display]')[0]->plaintext;
    $prix = trim($prix);
    $prix = str_replace("&#8364;", ".", $prix);
    $prix = floatval($prix);
    $this->set_prix($prix);

    // Récupération de la marque (dans le titre de la page)
    $marque = $htmlPage->find('head')[0]->children[0]->plaintext;
    $marque = explode("-", $marque)[0];
    $marque = trim($marque);
    $this->set_marque($marque);

    // Récupération de la catégorie (dans: "vous êtes ici:")
    $cat = $urlBlock->find('div[class=breadcrumb]')[0]->plaintext;
    $cat = explode(">", $cat)[1];
    $cat = trim($cat);
    $this->set_categorie($cat);

    // Récupération des tailles disponibles et stockage en array
    $taille     = $urlBlock->find('select[id=group_4]')[0]->plaintext;
    $taille     = trim($taille);
    $taille_tab = explode(" ", $taille);

    foreach ($taille_tab as $key => $value) {
      $taille_tab[$key] = trim($value);
    }
    $this->set_tailles($taille_tab);

    // Récupération des URLs des images en array
    $img_block = $urlBlock->find('ul[id=thumbs_list_frame]')[0]->children;

    foreach ($img_block as $key => $block) {
      $img[] = $block->children[0]->attr['href'];
    }
    $this->set_images($img);
  }
  // ------------------------
  public function scrap_imgsArticle($htmlFile) {
    //var_dump($htmlFile);

    $blockTinyImgs = $htmlFile->find('ul[id=thumbs_list_frame]')[0];

    //var_dump($blockTinyImgs);

    $listImgsThumbs = $blockTinyImgs->find('li');

    //var_dump($listImgsThumbs);

    foreach ($listImgsThumbs as $blockLiImg) {
      $urlImg = $blockLiImg->find('img')[0]->attr['src'];
      var_dump($urlImg);

      // explode de l'url et récupération du nom du répertoire
      $urlImgExpl = explode("/", $urlImg);

      $directoryImg = $urlImgExpl[1];
      $fileImg      = $urlImgExpl[2];

      // explode du nom du répertoire racine et récupération du numéro image
      $directoryExpl = explode("-", $directoryImg);

      $refArticle   = $directoryExpl[0];
      $refImage     = $directoryExpl[1];

      if ( ($refImage !== null) && ($refArticle !== null)) {
        // nouveau format du nom de l'image: ref article + ref img
        $imgLocalName = $refArticle . "-" . $refImage . "-large.jpg";

        echln($imgLocalName);

        DB_BLZ_add_photo($refImage, $refArticle, $imgLocalName);

//        $urlImgLarge = "http://blzjeans.com/".$nbrRef."-".$nbrImg."-thickbox/".$fileImg;

//        // sauvegarde en local du JPG sous le nouveau nom
//        // si le fichier n'existe pas
//        $file_local = PATH_LOCAL_IMG . $imgLocalName;
//
//        if (file_exists($file_local) == FALSE) {
//          file_put_contents(
//              $file_local,
//              file_get_contents($urlImgLarge)
//              );
//        }
      }
    }

}
  // ========================================
  }
