<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function catsArrayToDB (array $cats) {

  $cat = new Categorie();

  foreach ($cats as $cat) {
    $result = $cat->to_DB();

    if ($result == false) { echo "(ER) - "; }
    else { echo "(OK) - "; }

    echo "Ecriture categorie ".$cat->get_name(). " en DB\n";
  }
}

 // ------------------------