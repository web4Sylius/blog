<?php
/*
 * Plugin Name: MCAV Connexion
 * Plugin URI: 
 * Description: Récupère les infos du site pour les intégrer à wordpress
 * Version: 1
 * Author: CC
 * Author URI: 
 * License: GPLv2+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

if(!defined("DONT_USE_MCAV")){

    include_once("class/mysql.php");
    include_once("class/categorie.class.php");
    include_once($_SERVER["DOCUMENT_ROOT"]."/configurations/www.ma-cave-a-vin.fr/config_mysql.php");

    add_action ('init', 'mcav_init',0); //run before widget init
}

$mcavDB=null;

function mcav_init(){
    global $mcavDB;
    global $DBname,$db_server,$DBusername,$DBpassword;

    $mcavDB=new DB_Sql;
    $mcavDB->appname='boutique';
    $mcavDB->appshortname='boutique';
    $mcavDB->database=$DBname;
    $mcavDB->server=$db_server;
    $mcavDB->user=$DBusername;
    $mcavDB->password=$DBpassword;
    $mcavDB->connect();
    $DBpassword="";

    add_filter( 'mcav_marques', "mcav_footer_marques", 10, 2 );
    add_filter('mcav_categories','mcav_categories');

    wp_register_style('mcav_css', plugins_url('css/mcav.css',__FILE__ ));
    wp_enqueue_style('mcav_css');
}

function mcav_footer_marques($col){
    global $mcavDB;
    $cols=4;
   
    $list=array();
    $rq_menu=$mcavDB->query("SELECT institutionnelid,libelleFR AS libelle,m.marqueid,m.libelle AS marque,m.typeFR AS type,m.urlFR AS url FROM institutionnel i INNER JOIN marque m ON (instr(i.libelleFR,m.libelle)) WHERE i.menu = '2' and i.active = '1' ORDER BY i.position");
    while ($rs_menu=$mcavDB->fetch_array($rq_menu)) {
	$url_menu = "/".url_rewrite($rs_menu['url'])."-".$rs_menu[marqueid].".htm";
	$list[$rs_menu['libelle']]=$url_menu;
    }
    $nblist=count($list);
    $nbmcol=ceil($nblist/$cols);
    $part=array_slice($list,$col*$nbmcol,$nbmcol);
    
    $html="";
    foreach($part as $marque=>$url){
	$html.="<li><a href=\"$url\">".utf8_encode($marque)."</a></li>";
    }
    return $html;
}

function mcav_categories($catids){
    global $mcavDB;
    if(!is_array($catids)) return false;

    $mid=round(count($catids)/2);
    $i=0;

    $html="<div class=\"site-categories\">";
    $html.="<ul>";
    foreach($catids as $catid){
	$categorie=new categorie($mcavDB,$catid);
	$html.="<li style=\"border-color:".$categorie->color	."\"><a href=\"".$categorie->url()['url']."\">".utf8_encode($categorie->libelle)."</a></li>";
	$i++;
	if($i==$mid) $html.="</ul><ul>";
    }
    $html.="</ul>
    </div>";
  
    return $html;
}

if(!function_exists("url_rewrite")){
    function url_rewrite($string) {
	$string=html_entity_decode($string , ENT_NOQUOTES , "ISO-8859-1" );
	$caracteres = array("¥" => "Y", "µ" => "u", "À" => "A", "Á" => "A", "Â" => "A", "Ã" => "A", "Ä" => "A", "Å" => "A",
	"Æ" => "A", "Ç" => "C", "È" => "E", "É" => "E", "Ê"=> "E", "Ë" => "E", "Ì" => "I", "Í" => "I",
	"Î" => "I", "Ï" => "I", "Ð" => "D", "Ñ" => "N", "Ò" => "O", "Ó" => "O", "Ô" => "O", "Õ" => "O",
	"Ö" => "O", "Ø" => "O", "Ù" => "U", "Ú" => "U", "Û" => "U", "Ü" => "U", "Ý" => "Y", "ß" => "s",
	"à" => "a", "á" => "a", "â" => "a", "ã" => "a", "ä" => "a", "å"=> "a", "æ" => "a", "ç"=> "c",
	"è" => "e", "é" => "e", "ê" => "e", "ë" => "e", "ì" => "i", "í" => "i", "î" => "i", "ï"=> "i",
	"ð"=> "o", "ñ"=> "n", "ò" => "o", "ó" => "o", "ô" => "o", "õ" => "o", "ö" => "o", "ø" => "o",
	"ù" => "u", "ú" => "u", "û" => "u", "ü" => "u", "ý" => "y", "ÿ" => "y");
	$string = strtr($string , $caracteres);
	$string=strtolower($string);
	$len = strlen($string);
	for($a=0; $a<$len; $a++){
	    $p = ord($string[$a]);
	    if ( ($p > 96 && $p < 123) or ($p > 47 && $p < 58) ) {
		$return .= $string[$a];
	    } else {
		$return .= "-";
	    }
	}
	while(strpos($return,"--")!==FALSE) {
	    $return=str_replace("--","-",$return);
	}
	$return=trim($return,"-");
	return $return;
    }
}

?>