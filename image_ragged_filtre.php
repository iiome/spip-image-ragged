<?php
/**
 * Copyright Arnaud Martin
 * Filtre trouve sur paris-beyrouth:
 * http://www.paris-beyrouth.org/Un-habillage-irregulier-2
 *
 * integre en plugin par Pierre Andrews
 */

function image_ragged ($img, $align, $margin=10, $coul=-1) {

  include_spip("inc/filtres_images");
  if (strlen($coul) == 6) {
	$couleurs = couleur_hex_to_dec($coul);
	$dr= $couleurs["red"];
	$dg= $couleurs["green"];
	$db= $couleurs["blue"];
	$placer_fond = true;
  }
  else $placer_fond = false;

  $image = image_valeurs_trans($img, "float-$align$coul$margin", "php");
  if (!$image) return("");

  $w = $image["largeur"];
  $h = $image["hauteur"];
  $precision = round($h / 5);
	
  $im = $image["fichier"];
  $dest = $image["fichier_dest"];
  $creer = $image["creer"];

	$placer_fond = true;

  if (!$placer_fond) $ret = "<div style='position: relative; float: $align; width: 0px; height: 0px;'><img src='$im' class='format_png spip_ragged' alt='' style='position: absolute; $align: 0px;' /></div>";

  if ($creer) {
	include_spip('inc/logos'); // bicoz presence reduire_image
	$im_n = extraire_attribut(image_reduire($im, 0, $precision), "src");
	$nouveau = image_valeurs_trans($im_n, "reduction-$precision");
	$im_n = $nouveau["fichier"];
		
	$x_i = $nouveau["largeur"];
	$y_i = $nouveau["hauteur"];
	$rapport = ($w / $x_i);
	
		
	$im_n = $image["fonction_imagecreatefrom"]($im_n);



	// une premiere passe
	// pour recuperer les valeurs
	for ($j = 0; $j < $y_i; $j++) {
	  $transp = true;
		
	  for ($i = 0; $i < $x_i && $transp; $i++) {

		if ($align == "right") $rgb = ImageColorAt($im_n, $i+1, $j);
		else $rgb = ImageColorAt($im_n, ($x_i - $i)-1, $j);
		$a = ($rgb >> 24) & 0xFF;
		$r = ($rgb >> 16) & 0xFF;
		$g = ($rgb >> 8) & 0xFF;
		$b = $rgb & 0xFF;

		if ($a > 125) $larg[$j] ++;
		else if ($placer_fond && abs($r-$dr)+abs($g-$dg)+abs($b-$db) < 40) $larg[$j] ++;
		else $transp = false;
	  }			
	}
		
	$larg[-1] = $x_i;
	$larg[$y_i] = $x_i;
	

	// On reprend chaque larg pour appliquer le margin verticalement	
	// La valeur "larg" de chaque ligne est inchangee,
	// mais on calcule une valeur a ajouter si les lignes avant et apres sont plus longues
	// le $add sera ensuite ajouté à la marge en "margin", sinon on a des caracteres masques sous IE.
	// La valeur de l'espacement vertical est au moins 25px, parce que les lignes de texte se calculent non sur la ligne de base, mais sur leur premier pixel superieur
	// il faut donc y aller large pour eviter les superpositions
	$vertical = ceil(max($margin, 25) / 5);
	for ($i = -1; $i <= $y_i; $i ++) {
		
		$add[$i] = 0;
		
		for ($j = max($i - $vertical, -1); $j <= min($i + $vertical, $y_i); $j++) {
			$add[$i] = max($add[$i], ($larg[$i] - $larg[$j]));
			
		}
	}

	if ($align == "left") $mrg = "margin-right";
	else $mrg = "margin-left";

	// On ajoute un div pour forcer le margin vertical au dessus - ca evite notamment que la premiere ligne de texte passe sous l'image
    $forme .= "\n<div class='ragged_ligne_vide' style='float: $align; clear: $align; $mrg: ".round($add[-1]*$rapport + $margin)."px; width:".round($w - ($larg[-1] * $rapport))."px ; height: ".max($margin,25)."px; overflow: hidden;'></div>";

	// une deuxieme passe
	// pour appliquer les valeurs
	// en utilisant les valeurs precedente et suivante
	for ($j = 0; $j < $y_i; $j++) {
	  $reste = ($precision - $j);
	  $haut_rest = $h - $haut_tot;
	  $hauteur = round(($haut_rest) / $reste);
	  $haut_tot = $haut_tot + $hauteur;
	  $resultat = $larg[$j];

	  // Placer l'image en fond de differentes tranches
	  // uniquement si detourage par la couleur de fond
	  if ($placer_fond && $haut_tot <= $h) $backg = " background: url($im) $align -".($haut_tot-$hauteur)."px no-repeat;";
	  else $backg = "";

	  $forme .= "\n<div class='ragged_ligne_image' style='float: $align; clear: $align; $mrg: ".round($add[$j] * $rapport + $margin)."px; width: ".round(($w - ($resultat)*$rapport))."px ; height: ".round($hauteur)."px; overflow: hidden;$backg'></div>";
	}
	// Ajouter un div de plus en dessous
	$forme .= "\n<div class='ragged_ligne_vide' style='float: $align; clear: $align; width: ".($margin+round(($w - ($resultat)*$rapport)))."px ; height: ".round($hauteur)."px; overflow: hidden;'></div>";

	// Sauvegarder le fichier		
	$handle = fopen($dest, 'w');
	fwrite($handle, $forme);
	fclose($handle);

	$ret .= $forme;
  }
  else {
	$ret .= join(file($dest),"");
  }

  return $ret;
}
?>
