<?php


class categorie{
  private $DB=null;
  private $langue="FR";
  public $loaded;
  public $imghost="http://www.ma-cave-a-vin.fr";
  public $hauteurs=array(0,700,900,1300,1500,1700,3000);
  public $largeurs=array(0,300,500,550,600,650,700,1000,1600,2000,3000);
  public $profondeurs=array(0,500,550,600,650,700,800,10000);
  public $listeprix=array(0,100,250,400,800,1200,1600,2000,3000,4000,6000,10000,25000,100000);
  public $capacites=array(0,10,20,50,100,150,200,250,350,500,750,1000,1500,2000,4000,6000,10000);
  public $origines=array(0=>"France",1=>"Europe",2=>"Asie",3=>"USA");

  public $catid=null;
  public $parentid;
  public $libelle;
  public $description;
  public $description2;
  public $ref_title;
  public $image;
  public $image_small;
  public $image_cat;
  public $position;
  public $artcount=0;
  public $rechid;
  public $onglet;
  public $color;
  public $url;

  public $subcatids=array();
  public $marqueids=array();
  public $usageids=array();
  public $capaciteids=array();
  public $emplacementids=array();
  public $typeporteids=array();
  public $colorisids=array();
  public $moderangementids=array();
  public $finitionids=array();
  public $largeurids=array();
  public $hauteurids=array();
  public $profondeurids=array();
  public $prixids=array();
  public $prix=array();
  public $origineids=array();
  public $bonsplans=array();
  public $largeurmin=null;
  public $largeurmam=null;
  public $hauteurmin=null;
  public $hauteurmax=null;
  public $profondeurmin=null;
  public $profondeurmax=null;

  private $parents=array();

  function __construct($DB,$catid=0,$langue="FR"){
      $this->DB=$DB;
      $this->langue=$langue;
      $this->loaded=true;
      if(intval($catid)>0) 
	  $this->load($catid);
      else
	  $this->catid=0;

//       $this->parents();
  }

  function load($catid){
      if(is_null($this->DB)) return false;
      if($catid==0) return true;

      if(is_array($catid)){
	  $c=$catid;
      }else{
	  $c=$this->DB->query_first("SELECT * FROM categorie WHERE catid=".intval($catid));
	  if($c['catid']!=$catid) return false;
      }

      $this->internal_load($c);
      return true;
  }

  function load_from_url($url){
      if($url=="") return false;
      $c=$this->DB->query_first("SELECT * FROM categorie WHERE url".$this->langue."='".$url."'");
      if($c['catid']=="") return false;

      $this->internal_load($c);
      return true;
  }

  function internal_load($c){
      $this->catid=$c['catid'];
      $this->parentid=$c['parentid'];
      $this->libelle=$c['libelle'.$this->langue];
      $this->description=$c['description'.$this->langue];
      $this->description2=$c['description2'.$this->langue];
      $this->image=$this->imghost."/configurations/www.ma-cave-a-vin.fr/images/categories/FR/".$c['catid'].".".$c['imageFR'];
      $this->image_small=$this->imghost."/configurations/www.ma-cave-a-vin.fr/images/categories/FR/br/".$c['catid'].".".$c['imageFR'];
      $this->image_cat=$this->imghost."/configurations/www.ma-cave-a-vin.fr/images/categories/FR/description/".$c['catid'].".".$c['imageFR'];
      $this->position=$c['position'];
      $this->rechid=$c['rechid'];
      $this->onglet=$c['onglet'];
      $this->color=$c['color'];
      if($c['url'.$this->langue]!="")
	      $this->url=(substr($c['url'.$this->langue],0,1)!="/"?"/":"").$c['url'.$this->langue];
      else
	      $this->url="/cat,".$this->catid.",".url_rewrite($c['libelle'.$this->langue]).".htm";
	
      $this->ref_title=$this->libelle;
      if($c['ref_title'.$this->langue]!="") $this->ref_title=$c['ref_title'.$this->langue];
      
      $this->loaded=true;
  }

  function verif_url($url){
      if($url=="") return false;
      $c=$this->DB->query_first("SELECT * FROM categorie WHERE url".$this->langue."='".$url."'");
      if($c==false || $c['catid']==""){
	  $c=$this->DB->query_first("SELECT * FROM categorie_url WHERE langue='".$this->langue."' AND url='".$url."'");
	  if($c==false || $c['catid']=="")
	    return false;
	  else{
	    $cat=$this->DB->query_first("SELECT url".$this->langue." FROM categorie WHERE catid=".$c['catid']);
	    return $cat[0];
	  }
      }else
	  return $url;
  }

  function url(){
      $catid=$this->catid;
      $parentid=$this->parentid;
      $menu_appartenance=$this->menu_appartenance;
      while($parentid!=0){
        $c=$this->DB->query_first("SELECT catid,parentid,menu_appartenance FROM categorie WHERE catid=".$parentid); 
        $parentid=$c['parentid'];
        $catid=$c['catid'];
        $menu_appartenance=$c['menu_appartenance'];
        $subcatids=$this->catid;
      }

      return $this->mkurl(array("catid"=>$catid,"subcatids"=>$subcatids));
  }

  function mkurl($params,$newparam=array(),$multi=false){

    if(!isset($params)) return false;
    $simpleparams=array("catid","subcatids","marqueids","start","page");

    foreach($newparam as $name=>$value){
      if(isset($params[$name]) && $multi && $name!="start"){
        $arr=explode(",",$params[$name]);
        if(in_array($value,$arr)){
            $arr=array_diff($arr,array($value));
            $params[$name]=implode(",",$arr);
            if($params[$name]=="") unset($params[$name]);
        }else{
            $params[$name]=$params[$name].",".$newparam[$name];
        }
      }else{
        if(is_null($newparam[$name]))
            unset($params[$name]);
        else
            $params[$name]=$newparam[$name];
      }
    }

    foreach($params as $name=>$value){
      if(!in_array($name,$simpleparams) && !is_null($value)){
          $paramlink=true;
          break;
      }
    }
//       $paramlink=count(array_diff(array_keys($params),$simpleparams))>0;
//       $params=array_merge($params,$newparam);
    $subcatids=explode(",",$params['subcatids']);
    $marqueids=explode(",",$params['marqueids']);
    if($paramlink || count($subcatids)>1 || count($marqueids)>1)
       return array("url"=>"/categorie.php?".http_build_query($params),"rel"=>"nofollow");
    else{
       $cat=$this->DB->query_first("SELECT url".$this->langue." AS url,parentid,menu_appartenance FROM categorie WHERE catid=".intval($params['catid']));

    while(intval($cat['parentid'])!=0){
       if(!isset($params['subcatids'])) $params['subcatids']=$params['catid'];
         $subcatids=explode(",",$params['subcatids']);
         $cat=$this->DB->query_first("SELECT catid,url".$this->langue." AS url,parentid,menu_appartenance FROM categorie WHERE catid=".intval($cat['parentid']));
         $params['catid']=$cat['catid'];
    }
    if($cat['url']=="") return array("url"=>"/categorie.php?".http_build_query($params),"rel"=>"nofollow");
      $url="/".str_replace(".htm","",$cat['url']);
    if(isset($params['subcatids']) && $params['subcatids']!=""){
      $sscat=$this->DB->query_first("SELECT url".$this->langue." AS url FROM categorie WHERE catid=".intval($subcatids[0]));
      if($sscat['url']=="") return array("url"=>"/categorie.php?".http_build_query($params),"rel"=>"nofollow");
        $url.="/".str_replace(".htm","",$sscat['url']);
    }
    if(isset($params['marqueids']) && $params['marqueids']!=""){
      $marque=$this->DB->query_first("SELECT libelle FROM marque WHERE marqueid=".intval($marqueids[0]));
      $url.="/".url_rewrite($marque['libelle'])."-m";
    }
    if(isset($params['page']) && $params['page']!=1) $url.="/".$params['page'];
      $url.=".htm";
      return array("url"=>$url);
    }
}

  function subcategories($order="position",$onglet=false,$childs=null){
      if(is_null($this->DB)) return false;
      if(is_null($this->catid)) return false;
      if(!in_array($order,array("position","libelle".$this->langue))) $order="position";

      $this->subcatids=array($this->catid);
      $return=array();
      if($maincatid==0){
	if(is_null($childs))
	  $this->parents();
	else
	  $this->parents=$childs;

	if(!is_null($this->parents[$this->catid])){
	$position=1;
	foreach($this->parents[$this->catid] as $ssc){
	    if($ssc->onglet || $onglet==false){
		if(count($this->marqueids)>0) $ssc->marqueids=$this->marqueids;
		$return[$position][0]=$ssc;
		$this->subcatids[]=$ssc->catid;
		
		$ssc2list=$ssc->subcategories($order,false,$this->parents);
		$this->artcount+=$ssc->artcount;
		if(is_array($ssc->subcatids)) $this->subcatids=array_merge($this->subcatids,$ssc->subcatids);
		foreach($ssc2list as $p=>$ssc2){
		    $return[$position][$p]=$ssc2;
		}
		$position++;
	    }
	}
	}
	$this->subcatids=array_unique($this->subcatids);

	ksort($return);
	return $return;
      }
  }

  function parents(){
      $this->parents=array();
      $req=$this->DB->query("SELECT * FROM categorie ORDER BY position");
      while($cat=$this->DB->fetch_array($req)){
	  $this->parents[$cat['parentid']][]=new categorie($this->DB,$cat['catid'],$this->langue);
      }
  }
  
  function setsubcatids($catids){
      $this->subcatids=$this->getsubcatids($catids);
  }

  function getsubcatids($catids){

      if(is_array($catids)){
	$strcatids=implode("','",$catids);
	$return=$catids;
      }else{
	$return[]=intval($catids);
	$strcatids=intval($catids);
      }

      $r=$this->DB->query("SELECT catid FROM categorie WHERE parentid IN ('".$strcatids."')");
      while($sscat=$this->DB->fetch_array($r)){
	  $return[]=$sscat[0];
	  $sscatids=$this->getsubcatids($sscat[0]);
	  $return=array_merge($return,$sscatids);
      }

      return $return;
  }

  function setparam($param,$value){
      if(is_null($param) || $value=="") return false;
//       if(!isset($this->$param)) return false;
      if(is_array($this->$param))
	  if(is_array($value))
	    $this->$param=$value;
	  else
	    $this->$param=explode(",",$value);
      else
	  $this->$param=intval($value);
  }

  function getsqlwhere($params=null,$virtuel=true){
      $subcatids=$this->subcatids;
      $marqueids=$this->marqueids;
      $usageids=$this->usageids;
      $capaciteids=$this->capaciteids;
      $emplacementids=$this->emplacementids;
      $typeporteids=$this->typeporteids;
      $colorisids=$this->colorisids;
      $moderangementids=$this->moderangementids;
      $finitionids=$this->finitionids;
      $largeurids=$this->largeurids;
      $hauteurids=$this->hauteurids;
      $profondeurids=$this->profondeurids;
      $prixids=$this->prixids;
      $prix=$this->prix;
      $origineids=$this->origineids;
      $bonsplans=$this->bonsplans;

      if(!is_null($params)){
	  foreach($params as $name=>$value){
	      if(isset($$name)) $$name=$value;
	  }
      }
      $sql=" WHERE a.active='1' AND (a.catid IN ('".implode("','",$subcatids)."')";
      if($virtuel) $sql.=" OR (SELECT COUNT(artid) FROM vcategorie WHERE catid IN ('".implode("','",$subcatids)."') AND artid=a.artid)>0";
      $sql.=")";
      if(count($marqueids)>0) $sql.=" AND am.marqueid IN ('".implode("','",$marqueids)."')";
      if(count($usageids)>0) $sql.=" AND (SELECT COUNT(artid) FROM articlerecherche_usage WHERE rechercheusageid IN ('".implode("','",$usageids)."') AND artid=a.artid)>0";
      if(count($capaciteids)>0){
	  foreach($capaciteids as $capaciteid) $sqlc[]="capacite_bouteilles BETWEEN ".intval($this->capacites[$capaciteid])." AND ".intval($this->capacites[$capaciteid+1]);
	  $sql.=" AND (".implode(" OR ",$sqlc).")";
      }
//       if(count($capaciteids)>0) $sql.=" AND (SELECT COUNT(artid) FROM articlerecherche_capacitebouteilles WHERE recherchecapacitebouteillesid IN ('".implode("','",$capaciteids)."') AND artid=a.artid)>0";
      if(count($emplacementids)>0) $sql.=" AND (SELECT COUNT(artid) FROM articlerecherche_emplacement WHERE rechercheemplacementid IN ('".implode("','",$emplacementids)."') AND artid=a.artid)>0";
      if(count($typeporteids)>0) $sql.=" AND (SELECT COUNT(artid) FROM articlerecherche_typeporte WHERE recherchetypeporteid IN ('".implode("','",$typeporteids)."') AND artid=a.artid)>0";
      if(count($colorisids)>0) $sql.=" AND (SELECT COUNT(artid) FROM articlerecherche_coloris WHERE recherchecolorisid IN ('".implode("','",$colorisids)."') AND artid=a.artid)>0";
      if(count($moderangementids)>0) $sql.=" AND (SELECT COUNT(artid) FROM articlerecherche_moderangement WHERE recherchemoderangementid IN ('".implode("','",$moderangementids)."') AND artid=a.artid)>0";
      if(count($finitionids)>0) $sql.=" AND (SELECT COUNT(artid) FROM articlerecherche_finition WHERE finitionid IN ('".implode("','",$finitionids)."') AND artid=a.artid)>0";
      if(count($largeurids)>0){
	  foreach($largeurids as $largeurid) $sqll[]="ROUND(a.largeur) BETWEEN ".intval($this->largeurs[$largeurid])." AND ".intval($this->largeurs[$largeurid+1]);
	  $sql.=" AND (".implode(" OR ",$sqll).")";
      }
      if(count($hauteurids)>0){
	  foreach($hauteurids as $hauteurid) $sqlh[]="ROUND(a.hauteur) BETWEEN ".intval($this->hauteurs[$hauteurid])." AND ".intval($this->hauteurs[$hauteurid+1]);
	  $sql.=" AND (".implode(" OR ",$sqlh).")";
      }
      if(count($profondeurids)>0){
	  foreach($profondeurids as $profondeurid) $sqlp[]="ROUND(a.profondeur) BETWEEN ".intval($this->profondeurs[$profondeurid])." AND ".intval($this->profondeurs[$profondeurid+1]);
	  $sql.=" AND (".implode(" OR ",$sqlp).")";
      }
      if(!is_null($this->largeurmin)) $sql.=" AND ROUND(a.largeur)>=".intval($this->largeurmin);
      if(!is_null($this->largeurmax)) $sql.=" AND ROUND(a.largeur)<=".intval($this->largeurmax);
      if(!is_null($this->hauteurmin)) $sql.=" AND ROUND(a.hauteur)>=".intval($this->hauteurmin);
      if(!is_null($this->hauteurmax)) $sql.=" AND ROUND(a.hauteur)<=".intval($this->hauteurmax);
      if(!is_null($this->profondeurmin)) $sql.=" AND ROUND(a.profondeur)>=".intval($this->profondeurmin);
      if(!is_null($this->profondeurmax)) $sql.=" AND ROUND(a.profondeur)<=".intval($this->profondeurmax);
      if(count($prix)>0) $sql.=" AND a.prix BETWEEN ".floatval($this->prix[0])." AND ".floatval($this->prix[1]);
      if(count($prixids)>0){
	  foreach($prixids as $prixid) $sqlp[]="a.prix BETWEEN ".intval($this->listeprix[$prixid])." AND ".intval($this->listeprix[$prixid+1]);
	  $sql.=" AND (".implode(" OR ",$sqlp).")";
      }
      if(count($origineids)>0) $sql.=" AND paysfabrication IN ('".implode("','",$origineids)."')";
      if(count($bonsplans)>0){
	$sqlbp=array();
	if(in_array("cdc",$bonsplans)) $sqlbp[]="(SELECT COUNT(artid) FROM coupdecoeur WHERE artid=a.artid)>0";
	if(in_array("promo",$bonsplans)) $sqlbp[]="(SELECT COUNT(artid) FROM promo WHERE artid=a.artid)>0";
	if(in_array("idkdos",$bonsplans)) $sqlbp[]="(SELECT COUNT(artid) FROM idkdos WHERE artid=a.artid)>0";
	if(count($sqlbp)>0) $sql.=" AND (".implode(" OR ",$sqlbp).")";
      }
      if(!in_array("selection",$bonsplans)) $sql.=" AND (SELECT COUNT(artid) FROM article_deselection WHERE artid=a.artid)=0";

      return $sql;
  }

  function getcdc($order="position",$limit=null){
      return $this->getfromlist("coupdecoeur",$order,$limit);
  }
  function getpromo($order="prix",$limit=null){
      return $this->getfromlist("promo",$order,$limit);
  }
  function getidkdos($order="prix",$limit=null){
      return $this->getfromlist("idkdos",$order,$limit);
  }
  function getfromlist($list,$order="prix",$limit=null){
      if(is_null($this->DB)) return false;

      $return=array();
      if(!is_null($limit)) $limit=" LIMIT ".$limit;
      if(!is_null($order)) $order=" ORDER BY ".$order;
      if(count($this->subcatids)>0){
	  $sql="SELECT p.artid FROM $list p INNER JOIN article a USING(artid) INNER JOIN article_marque am USING(artid)";
	  $sql.=$this->getsqlwhere(array(),false);
	  $sql.=$order.$limit;
	  $cdclist=$this->DB->query($sql);

	  if($this->DB->num_rows($cdclist)==0) return false;
	  while($cdc=$this->DB->fetch_array($cdclist)){
	    $return[]=$cdc['artid'];
	  }
	  ksort($return);
      }
      return $return;
  }

  function articles_vus($s,$limit=2){
      $return=array();
      $r=$this->DB->query("SELECT artid FROM article_vu WHERE s='$s' ORDER BY date DESC LIMIT ".$limit);
      while($art=$this->DB->fetch_array($r)){
	  $return[]=$art[0];
      }
      return $return;
  }

  function articlecount($catids){
      $sql="SELECT COUNT(a.artid) FROM article a INNER JOIN article_marque am USING(artid)";
      $sql.=$this->getsqlwhere(array("subcatids"=>$catids));
      $r=$this->DB->query_first($sql);
      return $r[0];
  }

  function autoremoveparam(){
      $removeparam=array("hauteurids","largeurids","marqueids","emplacementids","colorisids","moderangementids","finitionids","capaciteids");
      foreach($removeparam as $p){
	if($this->$p!=array()){
	    $this->$p=array();
	    $c=$this->articlecount($this->subcatids);
	    if($c>0) return $this->articles();
	}
      }
  }

  function getmarques($artids=null){
      $return=array();
      $strsubcatids=implode("','",$this->subcatids);
      if(is_null($artids)){
	  $sql="SELECT am.marqueid,COUNT(am.marqueid) FROM article_marque am INNER JOIN article a USING(artid) INNER JOIN marque m USING(marqueid)";
	  $sql.=$this->getsqlwhere(array("marqueids"=>array()));
	  $sql.=" GROUP BY am.marqueid ORDER BY m.libelle";
      }else
	  $sql="SELECT am.marqueid,COUNT(am.marqueid) FROM article a INNER JOIN article_marque am USING(artid) INNER JOIN marque m USING(marqueid) WHERE a.active='1' AND artid IN ('".implode("','",$artids)."') GROUP BY am.marqueid ORDER BY m.libelle";
      $r=$this->DB->query($sql);
      while($ru=$this->DB->fetch_array($r)){
	$param=new param($ru[0],"",false,$ru[1]);
	$param->setchecked($this->marqueids);
	$return[$ru[0]]=$param;
      }
      return $return;
  }

  function getusages($artids=null){
     
      $return=array();
      if($artids==false) return $return;
      $strsubcatids=implode("','",$this->subcatids);
      if(is_null($artids)){
	  $sql="SELECT rechercheusageid,nom".$this->langue.",COUNT(rechercheusageid) FROM articlerecherche_usage INNER JOIN article a USING(artid) INNER JOIN recherche_usage USING(rechercheusageid) WHERE a.active='1' AND (a.catid IN ('".$strsubcatids."')  OR a.artid IN (SELECT artid FROM vcategorie WHERE catid IN ('".$strsubcatids."'))) GROUP BY rechercheusageid";
      }else
	  $sql="SELECT rechercheusageid,nom".$this->langue.",COUNT(rechercheusageid) FROM articlerecherche_usage INNER JOIN recherche_usage USING(rechercheusageid) INNER JOIN article a USING(artid) WHERE a.active='1' AND artid IN ('".implode("','",$artids)."') GROUP BY rechercheusageid";
      $r=$this->DB->query($sql);
      while($ru=$this->DB->fetch_array($r)){
	$param=new param($ru[0],$ru[1],false,$ru[2]);
	$param->setchecked($this->usageids);
	$return[$ru[0]]=$param;
      }
      return $return;
  }

  function getcapacite($artids=null){
/*
      $return=array();
      $strsubcatids=implode("','",$this->subcatids);
      if(is_null($artids)){
	  $sql="SELECT recherchecapacitebouteillesid,nom,COUNT(recherchecapacitebouteillesid) FROM articlerecherche_capacitebouteilles INNER JOIN article a USING(artid) INNER JOIN article_marque am USING(artid) INNER JOIN recherche_capacitebouteilles USING(recherchecapacitebouteillesid)";
	  $sql.=$this->getsqlwhere(array("capaciteids"=>array()));
	  $sql.=" GROUP BY recherchecapacitebouteillesid ORDER BY position";
      }else
	  $sql="SELECT recherchecapacitebouteillesid,nom,COUNT(recherchecapacitebouteillesid) FROM articlerecherche_capacitebouteilles INNER JOIN recherche_capacitebouteilles USING(recherchecapacitebouteillesid) INNER JOIN article a USING(artid) WHERE a.active='1' AND artid IN ('".implode("','",$artids)."') GROUP BY recherchecapacitebouteillesid ORDER BY position";
      $r=$this->DB->query($sql);
      while($ru=$this->DB->fetch_array($r)){
	$param=new param($ru[0],$ru[1],false,$ru[2]);
	$param->setchecked($this->capaciteids);
	$return[$ru[0]]=$param;
      }
      return $return;
*/
      $return=array();
      $sql="SELECT a.capacite_bouteilles FROM article a INNER JOIN article_marque am USING(artid)";
      $sql.=$this->getsqlwhere(array("capaciteids"=>array()));
      $r=$this->DB->query($sql);
      while($article=$this->DB->fetch_array($r)){
	  for($i=0;$i<count($this->capacites);$i++) if($article['capacite_bouteilles']>=$this->capacites[$i] && $article['capacite_bouteilles']<=$this->capacites[$i+1]) $return[$i]++;
      }
      foreach($return as $id=>$nb){
	  $param=new param($id,$this->capacites[$id],false,$nb);
	  $param->from=$this->capacites[$id];
	  $param->to=$this->capacites[$id+1];
	  $param->setchecked($this->capaciteids);
	  $return[$id]=$param;
      }
      ksort($return);
      return $return;
  }

  function getemplacement($artids=null){
      
      $return=array();
      if($artids==false) return $return;
      $strsubcatids=implode("','",$this->subcatids);
      if(is_null($artids)){
	  $sql="SELECT rechercheemplacementid,nom".$this->langue.",COUNT(rechercheemplacementid) FROM articlerecherche_emplacement INNER JOIN article a USING(artid) INNER JOIN recherche_emplacement USING(rechercheemplacementid) WHERE (a.catid IN ('".$strsubcatids."')  OR a.artid IN (SELECT artid FROM vcategorie WHERE catid IN ('".$strsubcatids."'))) GROUP BY rechercheemplacementid";
      }else
	  $sql="SELECT rechercheemplacementid,nom".$this->langue.",COUNT(rechercheemplacementid) FROM articlerecherche_emplacement INNER JOIN recherche_emplacement USING(rechercheemplacementid) INNER JOIN article a USING(artid) WHERE artid IN ('".implode("','",$artids)."') GROUP BY rechercheemplacementid";

      $r=$this->DB->query($sql);
      while($ru=$this->DB->fetch_array($r)){
	  $param=new param($ru[0],$ru[1],false,$ru[2]);
	  $param->setchecked($this->emplacementids);
	  $return[$ru[0]]=$param;
      }
      return $return;
  }

  function gettypeporte($artids=null){
      $return=array();
      if($artids==false) return $return;
      $strsubcatids=implode("','",$this->subcatids);
      if(is_null($artids)){
	  $sql="SELECT recherchetypeporteid,nom".$this->langue.",COUNT(recherchetypeporteid) FROM articlerecherche_typeporte INNER JOIN article a USING(artid) INNER JOIN recherche_typeporte USING(recherchetypeporteid) WHERE a.active='1' AND (a.catid IN ('".$strsubcatids."')  OR a.artid IN (SELECT artid FROM vcategorie WHERE catid IN ('".$strsubcatids."'))) GROUP BY recherchetypeporteid";
      }else
	  $sql="SELECT recherchetypeporteid,nom".$this->langue.",COUNT(recherchetypeporteid) FROM articlerecherche_typeporte INNER JOIN recherche_typeporte USING(recherchetypeporteid) INNER JOIN article a USING(artid) WHERE a.active='1' AND artid IN ('".implode("','",$artids)."') GROUP BY recherchetypeporteid";
      
      $r=$this->DB->query($sql);
      while($ru=$this->DB->fetch_array($r)){
	  $param=new param($ru[0],$ru[1],false,$ru[2]);
	  $param->setchecked($this->typeporteids);
	  $return[$ru[0]]=$param;
      }
      return $return;
  }

  function getmoderangement($artids=null){
      $return=array();
      if($artids==false) return $return;
      $strsubcatids=implode("','",$this->subcatids);
      if(is_null($artids)){
	  $sql="SELECT recherchemoderangementid,nom".$this->langue.",COUNT(recherchemoderangementid) FROM articlerecherche_moderangement INNER JOIN article a USING(artid) INNER JOIN recherche_moderangement USING(recherchemoderangementid) WHERE a.active='1' AND (a.catid IN ('".$strsubcatids."')  OR a.artid IN (SELECT artid FROM vcategorie WHERE catid IN ('".$strsubcatids."'))) GROUP BY recherchemoderangementid";
      }else
	  $sql="SELECT recherchemoderangementid,nom".$this->langue.",COUNT(recherchemoderangementid) FROM articlerecherche_moderangement INNER JOIN recherche_moderangement USING(recherchemoderangementid) INNER JOIN article a USING(artid) WHERE a.active='1' AND artid IN ('".implode("','",$artids)."') GROUP BY recherchemoderangementid";

      $r=$this->DB->query($sql);
      while($ru=$this->DB->fetch_array($r)){
	  $param=new param($ru[0],$ru[1],false,$ru[2]);
	  $param->setchecked($this->moderangementids);
	  $return[$ru[0]]=$param;
      }
      return $return;
  }

  function getfinition($artids=null){
      $return=array();
      if(is_null($artids)){
	  $sql="SELECT finitionid,nom".$this->langue.",COUNT(finitionid) FROM articlerecherche_finition INNER JOIN article a USING(artid) INNER JOIN article_marque am USING(artid) INNER JOIN recherche_finition USING(finitionid)";
	  $sql.=$this->getsqlwhere(array("finitionids"=>array()));
	  $sql.=" GROUP BY finitionid ORDER BY finitionid";
      }else
	  $sql="SELECT finitionid,nom".$this->langue.",COUNT(finitionid) FROM articlerecherche_finition INNER JOIN recherche_finition USING(finitionid) INNER JOIN article a USING(artid) WHERE a.active='1' AND artid IN ('".implode("','",$artids)."') GROUP BY finitionid";

      $r=$this->DB->query($sql);
      while($ru=$this->DB->fetch_array($r)){
	  $param=new param($ru[0],$ru[1],false,$ru[2]);
	  $param->setchecked($this->finitionids);
	  $return[$ru[0]]=$param;
      }
      return $return;
  }

  function getprix(){
      $return=array();
      $sql="SELECT a.prix AS prix FROM article a INNER JOIN article_marque am USING(artid)";
      $sql.=$this->getsqlwhere(array("prixids"=>array()));
      $r=$this->DB->query($sql);
      while($article=$this->DB->fetch_array($r)){
	  for($i=0;$i<count($this->listeprix);$i++) if($article['prix']>=$this->listeprix[$i] && $article['prix']<=$this->listeprix[$i+1]) $return[$i]++;
      }
      foreach($return as $id=>$nb){
	  $param=new param($id,$this->listeprix[$id],false,$nb);
	  $param->from=$this->listeprix[$id];
	  $param->to=$this->listeprix[$id+1];
	  $param->setchecked($this->prixids);
	  $return[$id]=$param;
      }
      ksort($return);
      return $return;
  }

  function getcoloris($artids=null){
      $return=array();
      if($artids==false) return $return;
      $strsubcatids=implode("','",$this->subcatids);
      if(is_null($artids)){
	  $sql="SELECT recherchecolorisid,nom".$this->langue.",COUNT(recherchecolorisid) FROM articlerecherche_coloris INNER JOIN article a USING(artid) INNER JOIN recherche_coloris USING(recherchecolorisid) WHERE a.active='1' AND (a.catid IN ('".$strsubcatids."')  OR a.artid IN (SELECT artid FROM vcategorie WHERE catid IN ('".$strsubcatids."'))) GROUP BY recherchecolorisid";
      }else
	  $sql="SELECT recherchecolorisid,nom".$this->langue.",COUNT(recherchecolorisid) FROM articlerecherche_coloris INNER JOIN recherche_coloris USING(recherchecolorisid) INNER JOIN article a USING(artid) WHERE a.active='1' AND artid IN ('".implode("','",$artids)."') GROUP BY recherchecolorisid ORDER BY position";

      $r=$this->DB->query($sql);      
      while($ru=$this->DB->fetch_array($r)){
	  $param=new param($ru[0],$ru[1],false,$ru[2]);
	  $param->setchecked($this->colorisids);
	  $return[$ru[0]]=$param;
      }
      return $return;
  }

  function getdimids(){
      $return=array("largeur"=>array(),"hauteur"=>array(),"profondeur"=>array());

      $sql="SELECT a.largeur,a.hauteur,a.profondeur FROM article a INNER JOIN article_marque am USING(artid)";
      $sql.=$this->getsqlwhere(array("largeurids"=>array(),"hauteurids"=>array(),"profondeurids"=>array()));
      $r=$this->DB->query($sql);
      while($article=$this->DB->fetch_array($r)){
	  for($i=0;$i<count($this->largeurs);$i++) if($article['largeur']>=$this->largeurs[$i] && $article['largeur']<=$this->largeurs[$i+1]) $return['largeur'][$i]++;
	  for($i=0;$i<count($this->hauteurs);$i++) if($article['hauteur']>=$this->hauteurs[$i] && $article['hauteur']<=$this->hauteurs[$i+1]) $return['hauteur'][$i]++;
	  for($i=0;$i<count($this->profondeurs);$i++) if(intval($article['profondeur'])>=$this->profondeurs[$i] && intval($article['profondeur'])<=$this->profondeurs[$i+1]) $return['profondeur'][$i]++;
      }
   
      foreach($return['largeur'] as $largeurid=>$nb){
	  $param=new param($largeurid,$this->largeurs[$largeurid],false,$nb);
	  $param->from=$this->largeurs[$largeurid];
	  $param->to=$this->largeurs[$largeurid+1];
	  $param->setchecked($this->largeurids);
	  $return['largeur'][$largeurid]=$param;
      }
      ksort($return['largeur']);
      foreach($return['hauteur'] as $hauteurid=>$nb){
	  $param=new param($hauteurid,$this->hauteurs[$hauteurid],false,$nb);
	  $param->from=$this->hauteurs[$hauteurid];
	  $param->to=$this->hauteurs[$hauteurid+1];
	  $param->setchecked($this->hauteurids);
	  $return['hauteur'][$hauteurid]=$param;
      }
      ksort($return['hauteur']);
      foreach($return['profondeur'] as $profondeurid=>$nb){
	  $param=new param($profondeurid,$this->profondeurs[$profondeurid],false,$nb);
	  $param->from=$this->profondeurs[$profondeurid];
	  $param->to=$this->profondeurs[$profondeurid+1];
	  $param->setchecked($this->profondeurids);
	  $return['profondeur'][$profondeurid]=$param;
      }
      ksort($return['profondeur']);
      return $return;
  }

  function getdimensions($artids=null){
      $return=array();
      $strsubcatids=implode("','",$this->subcatids);
      if(is_null($artids)){
	  $sql="SELECT MIN(ROUND(a.largeur)),MIN(ROUND(a.hauteur)),MIN(ROUND(a.profondeur)),MAX(ROUND(a.largeur)),MAX(ROUND(a.hauteur)),MAX(ROUND(a.profondeur)) FROM article a WHERE a.active='1' AND (a.catid IN ('".$strsubcatids."')  OR a.artid IN (SELECT artid FROM vcategorie WHERE catid IN ('".$strsubcatids."')))";
      }else
	  $sql="SELECT MIN(ROUND(a.largeur)),MIN(ROUND(a.hauteur)),MIN(ROUND(a.profondeur)),MAX(ROUND(a.largeur)),MAX(ROUND(a.hauteur)),MAX(ROUND(a.profondeur)) FROM article a WHERE a.active='1' AND artid IN ('".implode("','",$artids)."')";

      $return=$this->DB->query_first($sql);
      return $return;
  }

  function getorigine($artids=null){
      $return=array();
      $strsubcatids=implode("','",$this->subcatids);
      
      if(is_null($artids)){
	  $sql="SELECT paysfabrication,COUNT(paysfabrication) FROM article a WHERE a.active='1' AND (a.catid IN ('".$strsubcatids."')  OR a.artid IN (SELECT artid FROM vcategorie WHERE catid IN ('".$strsubcatids."'))) GROUP BY paysfabrication";
      }else
	  $sql="SELECT paysfabrication,COUNT(paysfabrication) WHERE a.active='1' AND artid IN ('".implode("','",$artids)."') GROUP BY paysfabrication ORDER BY paysfabrication";

      $r=$this->DB->query($sql);      
      while($ru=$this->DB->fetch_array($r)){
	  $param=new param($ru[0],$this->origines[$ru[0]],false,$ru[1]);
	  $param->setchecked($this->origineids);
	  $return[$ru[0]]=$param;
      }
      return $return;
  }

  function getbonsplans(){
      $return=array();
      $strsubcatids=implode("','",$this->subcatids);
      $sql="SELECT SUM((SELECT COUNT(artid) FROM coupdecoeur WHERE artid=a.artid)) AS cdc,SUM((SELECT COUNT(artid) FROM promo WHERE artid=a.artid)) AS promo,SUM((SELECT COUNT(artid) FROM idkdos WHERE artid=a.artid)) AS idkdos,SUM((SELECT COUNT(artid) FROM article_deselection WHERE artid=a.artid)) AS deselection, COUNT(a.artid) AS tout FROM article a INNER JOIN article_marque am USING(artid)";
      $sql.=$this->getsqlwhere(array("bonsplans"=>array("selection")));
      $bp=$this->DB->query_first($sql);
      $bp['selection']=$bp['tout']-$bp['deselection'];
      return $bp;
  }


  function articles($order="prix",$virtuel=true){
      if(count($this->subcatids)==0) return false;

      $return=array();

      $sql="SELECT a.artid FROM article a INNER JOIN article_marque am USING(artid)";
      $sql.=$this->getsqlwhere(null,$virtuel);
      $sql.=" ORDER BY ".$order;
      $req=$this->DB->query($sql);
      while($a=$this->DB->fetch_array($req))
	   $return[]=$a[0];

      return $return;
  }

}

class param {
   public $id;
   public $libelle;
   public $qte;
   public $checked;
   public $from;
   public $to;

   function param($id,$libelle,$checked=false,$qte=0){
	$this->id=$id;
	$this->libelle=$libelle;
	$this->checked=$checked;
	$this->qte=$qte;
   }

   function setchecked($ids){
	$this->checked=(in_array($this->id,$ids));
	return $this->checked;
   }
}

?>