<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2011                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;


//
// Fonctions graphiques
//
// La matrice permet au compilateur de reconnaitre un filtre
// et de faire la bonne inclusion au moment de son appel dans un squelette
// Filtres image -> image
$GLOBALS['spip_matrice']['image_ragged'] = 'filtres/image_ragged.php';

?>
