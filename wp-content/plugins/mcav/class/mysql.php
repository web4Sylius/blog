<?php
error_reporting(0);

$nb_essais_connect = 0 ;

class DB_Sql {
  var $database = "";

  var $link_id  = 0;
  var $query_id = 0;
  var $record   = array();

  var $errdesc    = "";
  var $errno   = 0;
  var $reporterror = 1;

  var $compteur_requettes =0;

  var $server   = "localhost";
  var $user     = "root";
  var $password = "";
  
  var $logpath  = '.';

  var $last_query="";


  function __construct(){
    $this->link_id = 0;
  }
  /*function connect() {
    global $usepconnect, $nb_essais_connect;
    // connect to db server
    if ( 0 == $this->link_id ) {
      if ($this->password=="") {
        if ($usepconnect==1) {
          $this->link_id=@mysqli_connect($this->server,$this->user);
        } else {
          $this->link_id=@mysqli_connect($this->server,$this->user);
        }
      } else {
        if ($usepconnect==1) {
          $this->link_id=@mysqli_connect($this->server,$this->user,$this->password);
        } else {
          $this->link_id=@mysqli_connect($this->server,$this->user,$this->password);
        }
      }
      if (!$this->link_id) {
        if ($nb_essais_connect < 3)
            {
            //echo "NB essais : $nb_essais_connect<br>" ;
            $nb_essais_connect++ ;
            $this->connect() ;
            }
        else
            {
            $nb_essais_connect = 0 ;
            $this->halt("Link-ID == false, connect failed");
            }
      }
      if ($this->database!="") {
        if(!mysqli_select_db($this->link_id,$this->database)) {
          $this->halt("cannot use database ".$this->database);
        } else {
          mysqli_set_charset( $this->link_id,'utf8' );
        }
      }
    }
    var_dump($this->link_id);
  }*/
  function connect(){
    global $nb_essais_connect;
    
    if(!is_object($con)){
      //$this->con = new mysqli($this->server,$this->user,$this->password,$this->database);
      $this->link_id = mysqli_connect($this->server,$this->user,$this->password,$this->database);
    }

    if(!$this->link_id){
      if ($nb_essais_connect < 3){
        //echo "NB essais : $nb_essais_connect<br>" ;
        $nb_essais_connect++ ;
        $this->connectB() ;
      }else{
        $nb_essais_connect = 0 ;
        $this->halt("Link-ID == false, connect failed");
      }  
    }

  } 
  function geterrdesc() {
    $this->error=mysqli_error($this->link_id);
    return $this->error;
  }

  function geterrno() {
    $this->errno=mysqli_errno($this->link_id);
    return $this->errno;
  }

  function select_db($database="") {
    // select database
    if ($database!="") {
      $this->database=$database;
    }

    if(!mysqli_select_db($this->link_id,$this->database)) {
      $this->halt("NE PEUX PAS UTILISER LA BASE DE DONNEE ".$this->database);
    }

  }

  function query($query_string) {
    if(isset($_GET['debug_sql'])) print_r('>>>'.$query_string);
    global $HTTP_SERVER_VARS, $host;
    
    /*$f = fopen("dbhystory.txt","a");
    fputs($f, "[ ".date("j-m-Y H:i"). microtime()." ]   [ $query_string ] [ $host ] [ Script: $GLOBALS[$bburl]" . $HTTP_SERVER_VARS['REQUEST_URI'] . " ] [ Referer: " . $HTTP_SERVER_VARS['HTTP_REFERER'] . " ]");
    fclose($f);*/
    $last_query=$query_string;
    
    list($usec, $sec) = explode(" ", microtime());
    $tempavantrequette=((float)$usec + (float)$sec);
    
    $this->query_id = mysqli_query($this->link_id,$query_string,MYSQLI_STORE_RESULT);
    $this->compteur_requettes++;

    list($usec, $sec) = explode(" ", microtime());
    $tempapresrequette=((float)$usec + (float)$sec);

    $dureerequette=$tempapresrequette-$tempavantrequette;

    //Si la requête a un temps d'exécution supérieure à 1 seconde
    //On stock dans le fichier dbslow en vue de l'améliorer.
    if($dureerequette > 1)
    {
        $retour = exec("uptime"); //Ex. retour uptime : 15:56:15 up 360 days,  3:38,  0 users,  load average: 0.48, 0.49, 0.59
        $pos    = strpos($retour, "load average: "); //Récupération de la position de "load average dans la chaine"
        $pos2   = @strpos($retour, ",", $pos+14); //Récupération de la position de la 1ère virgule après "load average:"
        $load   = substr($retour, $pos+14, $pos2-$pos-14); //Récupération de la 1ère valeur du load average (après "load average" et avant la 1ère virgule)
        //$load=intval($load);
    
    /*
        //Récupération des params GET
        $params_get = '';
        foreach($_GET as $param => $value)
        {
            $params_get .= ($params_get != '' ? ' / ' : '') . $param . ' => ' . $value;
        }
        if($params_get == '') $params_get = 'N/A';

        //Récupération des params POST
        $params_post = '';
        foreach($_POST as $param => $value)
        {
            $params_post .= ($params_post != '' ? ' / ' : '') . $param . ' => ' . (strlen($value) > 40 ? substr($value,0,40) : $value);
        }
        if($params_post == '') $params_post = 'N/A';

        //Récupération des params COOKIES
        $params_cookies = '';
        foreach($_COOKIE as $param => $value)
        {
            $params_cookies .= ($params_cookies != '' ? ' / ' : '') . $param . ' => ' . (strlen($value) > 40 ? substr($value,0,40) : $value);
        }
        if($params_cookies == '') $params_cookies = 'N/A';
    */
    
        $params_get=preg_replace("/[\r\n\t]/","",var_export($_GET,true));
        $params_post=preg_replace("/[\r\n\t]/","",var_export($_POST,true));
        $params_cookies=preg_replace("/[\r\n\t]/","",var_export($_COOKIE,true));

        //Enregistrement dans le fichier de log du jour
        $log_texte = date("d/m/Y") . ' ||| ' . date("H:i:s") . ' ||| Script : ' . $_SERVER['REQUEST_URI'] . ' ||| Params GET : ' . $params_get . ' ||| Params POST : ' . $params_post . ' ||| ' . $params_cookies . ' ||| Requête : ' . $query_string . ' ||| Durée requête : ' . $dureerequette; 
        
        $f = fopen($this->logpath . "/dbslow_" . date("Y-m-d") . ".txt","a");
        fputs($f, $log_texte . "\r\n\r\n");
        fclose($f);
    }


    if (!$this->query_id) {
      $this->halt("Invalid SQL: ".$query_string);
    }
    return $this->query_id;
  }

  function fetch_array($query_id=-1,$query_string="") {
    // retrieve row
    if ($query_id!=-1) {
      $this->query_id=$query_id;
    }
    if ( isset($this->query_id) ) {
      $this->record = mysqli_fetch_array($this->query_id);
    } else {
      if ( !empty($query_string) ) {
        $this->halt("Invalid query id (".$this->query_id.") on this query: $query_string");
      } else {
        $this->halt("Invalid query id ".$this->query_id." specified");
      }
    }

    return $this->record;
  }

  function fetch_assoc($query_id=-1){
    if ($query_id!=-1) {
      $this->query_id=$query_id;
    }
    if ( isset($this->query_id) ) {
      $this->record = mysqli_fetch_assoc($this->query_id);
    } else {
      if ( !empty($query_string) ) {
        $this->halt("Invalid query id (".$this->query_id.") on this query: $query_string");
      } else {
        $this->halt("Invalid query id ".$this->query_id." specified");
      }
    }

    return $this->record;
  }

  function fetch_row($query_id=-1){
    if ($query_id!=-1) {
      $this->query_id=$query_id;
    }
    if ( isset($this->query_id) ) {
      $this->record = mysqli_fetch_row($this->query_id);
    } else {
      if ( !empty($query_string) ) {
        $this->halt("Invalid query id (".$this->query_id.") on this query: $query_string");
      } else {
        $this->halt("Invalid query id ".$this->query_id." specified");
      }
    }

    return $this->record;
  }
  
  function free_result($query_id=-1) {
    // retrieve row
    if ($query_id!=-1) {
      $this->query_id=$query_id;
    }
    return @mysqli_free_result($this->query_id);
  }

  function query_first($query_string) {
  global $host ;
    // does a query and returns first row
    /*$f = fopen("dbhystory.txt","a");
    fputs($f, "[ ".date("j-m-Y H:i"). microtime()." ]   [ $query_string ] [ $host ] [ Script: $GLOBALS[$bburl]" . $HTTP_SERVER_VARS['REQUEST_URI'] . " ] [ Referer: " . $HTTP_SERVER_VARS['HTTP_REFERER'] . " ]");
    fclose($f);*/
    $last_query=$query_string;
    $query_id = $this->query($query_string);
    $returnarray=$this->fetch_array($query_id, $query_string);
    $this->free_result($query_id);
    return $returnarray;
  }

  function data_seek($pos,$query_id=-1) {
    // goes to row $pos
    if ($query_id!=-1) {
      $this->query_id=$query_id;
    }
    return mysqli_data_seek($this->query_id, $pos);
  }

  function num_rows($query_id=-1) {
    // returns number of rows in query
    if ($query_id!=-1) {
      $this->query_id=$query_id;
    }
    return mysqli_num_rows($this->query_id);
  }

  function num_fields($query_id=-1) {
    // returns number of fields in query
    if ($query_id!=-1) {
      $this->query_id=$query_id;
    }
    return mysqli_num_fields($this->query_id);
  }

  function insert_id() {
    // returns last auto_increment field number assigned
    return mysqli_insert_id($this->link_id);
  }

  function close() {
    // closes connection to the database
    return mysqli_close($this->link_id);
  }

  function halt($msg) {
      
      $this->errdesc = mysqli_connect_error($this->link_id);
      $this->errno   = mysqli_connect_errno($this->link_id);
      global $HTTP_SERVER_VARS, $host;

      //Récupération des params GET
      /*$params_get = '';
      foreach($_GET as $param => $value)
      {
          $params_get .= ($params_get != '' ? ' / ' : '') . $param . ' => ' . $value;
      }
      if($params_get == '') $params_get = 'N/A';*/
      $params_get=preg_replace("/[\r\n\t]/","",var_export($_GET,true));

      //Récupération des params POST
      /*$params_post = '';
      foreach($_POST as $param => $value)
      {
          $params_post .= ($params_post != '' ? ' / ' : '') . $param . ' => ' . (strlen($value) > 40 ? substr($value,0,40) : $value);
      }
      if($params_post == '') $params_post = 'N/A';*/
      $params_post=preg_replace("/[\r\n\t]/","",var_export($_POST,true));

      //Récupération des params COOKIES
      /*$params_cookies = '';
      foreach($_COOKIE as $param => $value)
      {
          $params_cookies .= ($params_cookies != '' ? ' / ' : '') . $param . ' => ' . (strlen($value) > 40 ? substr($value,0,40) : $value);
      }
      if($params_cookies == '') $params_cookies = 'N/A';*/
      $params_cookies=preg_replace("/[\r\n\t]/","",var_export($_COOKIE,true));

      //Enregistrement dans le fichier de log du jour
      $log_texte = date("d/m/Y") . ' ||| ' . date("H:i:s") . ' ||| Script : ' . $_SERVER['REQUEST_URI'] . ' ||| GET : ' . $params_get . ' ||| POST : ' . $params_post . ' ||| COOKIE ' . $params_cookies . ' ||| Erreur : ' . $this->errdesc . ' ||| Message : ' . $msg;

      $f = fopen($this->logpath . "/dberror_" . date("Y-m-d") . ".txt","a");
      fputs($f, $log_texte . "\r\n\r\n");
      fclose($f);

      //if ($this->reporterror==1) {
      $message="Erreur de requetes SQL :\n\n$msg\n";
      $message.="détail : " . $this->errdesc . "\n\n";
      $message.="numéro : " . $this->errno . "\n\n";
      //$message.="Date: ".date("l dS of F Y h:i:s A")."\n";
      //$message.="Script: $GLOBALS[$bburl]" . $HTTP_SERVER_VARS['REQUEST_URI'] . "\n";
      //$message.="Referer: ".$HTTP_SERVER_VARS['HTTP_REFERER']."\n";
      //if ($adminmail) {
      //@mail ($adminmail,"erreur BDD de $GLOBALS[bbtitle]",$message,"From: $adminmail");
      //}

    $phpvars=get_defined_vars();
    $txtvars="Variables :\n";
    foreach($phpvars as $var=>$value) $txtvars.="\$$var = ".var_export($value,true)."\n";
    //@mail ("ph.rodriguez@acilyon.com","Erreur BDD $host","[ ".date("j-m-Y H:i")." ] [ ".$this->errdesc." ]    [ $host ] [ Script: $GLOBALS[$bburl]" . $HTTP_SERVER_VARS['REQUEST_URI'] . " ]  [ Referer: " . $HTTP_SERVER_VARS['HTTP_REFERER'] . " ]","From: webmaster@ma-cave-a-vin.fr");
    //on vide les deux variable username et pass pour ne pas les afficher dans le mail d'erreur
    $this->password="";
    $this->user="";
    $backtrace=var_export(debug_backtrace(),true);
    @mail ("webmaster@ma-cave-a-vin.fr,webmaster1@ma-cave-a-vin.fr,webmaster3@ma-cave-a-vin.fr","Erreur BDD $host","[ ".date("j-m-Y H:i")." ] [ ".$this->errdesc." ]    [ $host ] [ Script: $GLOBALS[$bburl]" . $_SERVER['REQUEST_URI'] . " ] [ Referer: " . $_SERVER['HTTP_REFERER'] . " ]\n".$this->last_query."\n $txtvars\n$backtrace","From: webmaster@ma-cave-a-vin.fr");


    if ($this->errdesc=="Too many connections") {
    echo "<html><head><title>$GLOBALS[bbtitle] ERREUR BASE DE DONNEE</title></head>
        <body><table width=100%><tr><td align=center width=100%>Le serveur est actuellement surchargé , veuillez réessayer ulterieurement.<br>nous sommes désolé du dérangement.
        </td></tr></table></body></html>";
    exit;
    } else {

        //Définition du message d'erreur en fonction de la langue
        if(isset($_SERVER['SERVER_NAME']) && strpos($_SERVER['SERVER_NAME'], 'my-wine-cabinet') !== false){

            $this->errdesc = mysqli_error($this->link_id);

            $txt_erreur = ""
            . "<a href=\"/\"><img src=\"/images/templates/".(strpos($_SERVER['SERVER_NAME'], '.co.uk') !== false ? 'uk' : 'base')."/EN/logo.jpg\"></a>"
            . "<p style=\"font-size: 35px; font-weight: bold; color: #98003A;\">Oops...</p>"
            . "<p style=\"font-size: 13px; font-weight: bold;\">"
                . "It appears an Error occurred while loading this page."
                . "<br><br>"
                . "Our team has been informed, and will fix the problem as soon as possible."
                . "<br><br>"
                . "We apologize for the inconvenience."
            . "</p>";

        } else if(isset($_SERVER['SERVER_NAME']) && strpos($_SERVER['SERVER_NAME'], 'mi-vinoteca') !== false){

            $txt_erreur = ""
            . "<a href=\"/\"><img src=\"/images/templates/base/ES/logo.jpg\"></a>"
            . "<p style=\"font-size: 35px; font-weight: bold; color: #98003A;\">Oops...</p>"
            . "<p style=\"font-size: 13px; font-weight: bold;\">"
                . "parece que se ha producido un error al cargar la página."
                . "<br><br>"
                . "Nuestro equipo ha sido informado, y están haciendo lo posible para intervenir cuanto antes."
                . "<br><br>"
                . "Le pedimos disculpas por la molestia."
            . "</p>";

        } else {

            $txt_erreur = ""
            . "<a href=\"/\"><img src=\"/images/templates/base/FR/logo.jpg\"></a>"
            . "<p style=\"font-size: 35px; font-weight: bold; color: #98003A;\">Oups...</p>"
            . "<p style=\"font-size: 13px; font-weight: bold;\">"
                . "Il semblerait qu'il y ait eu une erreur lors de l'affichage de la page."
                . "<br><br>"
                . "Notre équipe en a été informée et va intervenir dans les plus brefs délais."
                . "<br><br>"
                . "Nous sommes désolés pour la gêne occasionnée."
            . "</p>";

        }

        echo ""
        . "<html>"
            . "<head>"
                . "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">"
                . "<title>".$GLOBALS['bbtitle']." ERREUR DE BASE DE DONNEE</title>"
                . "<style>P,BODY{FONT-FAMILY:tahoma,arial,sans-serif;FONT-SIZE:11px;}</style>"
            . "</head>"
            . "<body>"
                . "<div style=\"width: 100%; text-align: center; margin: 50px 0;\">"
                    . $txt_erreur;
                    if(isset($_COOKIE['admin_username']) && $_COOKIE['admin_username'] != ''){
                        echo ""
                        . "<center>"
                            . "<p style=\"width: 500px; font-size: 11px; color: red; margin: 50px 0;\">"
                                . "<strong>Erreur de base de données :</strong>"
                                . "<br><br>"
                                . $this->errdesc
                            . "</p>"
                        . "</center>";
                    }
        echo ""
                . "</div>"
            . "</body>"
        . "</html>";

        exit;
    }

  } // fin function halt

} // fin classe

error_reporting(7);

?>
