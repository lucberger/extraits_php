<?php
/*
TODO
export/backup
include file std functions
*/
error_reporting(E_ALL);
// if(isset($_POST['add']))
$tmp="/tmp/p";
$Ver="2019sep25";

//echo basename(__FILE__, '.php')." Version ".$Ver."<br>";
/*
mysql> describe Extraits;
+-----------------------+---------------+------+-----+------------+-------+
| Field                 | Type          | Null | Key | Default    | Extra |
+-----------------------+---------------+------+-----+------------+-------+
| Numero_de_compte      | varchar(20)   | NO   | PRI |            |       |
| Nom_du_compte         | varchar(40)   | YES  |     | NULL       |       |
| Compte_partie_adverse | varchar(20)   | YES  |     | NULL       |       |
| Numero_de_mouvement   | varchar(20)   | NO   | PRI | 0          |       |
| ANNEE + REFERENCE     | varchar(10)   | YES  | MUL | NULL       |       |
| Date_comptable        | date          | NO   | PRI | 0000-00-00 |       |
| Date_valeur           | date          | NO   | PRI | 0000-00-00 |       |
| Montant               | float         | YES  |     | NULL       |       |
| Devise                | varchar(1000) | YES  |     | NULL       |       |
| Libelles              | varchar(2000) | YES  |     | NULL       |       |
| Details_du_mouvement  | varchar(2000) | YES  |     | NULL       |       |
| Message               | varchar(2000) | YES  |     | NULL       |       |
+-----------------------+---------------+------+-----+------------+-------+

mysql> describe CompteNames;
+------------------+--------------+------+-----+---------+-------+
| Field            | Type         | Null | Key | Default | Extra |
+------------------+--------------+------+-----+---------+-------+
| Numero_de_compte | varchar(20)  | NO   | PRI | NULL    |       |
| Compte_Name      | varchar(200) | NO   |     | NULL    |       |
| Note             | varchar(200) | YES  |     | NULL    |       |
+------------------+--------------+------+-----+---------+-------+
*/
/*   test

//$LocCSV = "/home/luc/Documents/SharedVirtualBox/BanqueExtraits/extraits_ING";
$LocCSV = '/var/www/ex/extraits_ING';
$host = "localhost";
$user = "luc";
$pass = "boreal";
$db   = "Extraits";
*/
//echo $Ver;  //if displayed, kill the graph output by inserting before the data !

/////////////////////////////////////////////////
//  connexion_DB
/////////////////////////////////////////////////
function connexion_DB($host, $user, $pass, $db) {
$link=mysqli_init();
if (!$link) {
  print_r(error_get_last());
  die("mysqli_init failed");
  }
if (!mysqli_real_connect($link,$host, $user, $pass, $db))  {
  die("Connect Error: ".mysqli_connect_error());
  }
  else   {
    //msg( "Connection established !");
    //msg( "Host info: " . mysqli_get_host_info($link) . "");
    // echo "Host info: " . mysqli_get_host_info($link) . "";

/*
// NEXT CODE CRASH THE GRAPHS !
// Modification du jeu de résultats en utf8 
if (!mysqli_set_charset($link, "utf8")) {
    printf("Erreur lors du chargement du jeu de caractères utf8 : %s\n", mysqli_error($link));
    exit();
} else {
    printf("Jeu de caractères courant : %s\n", mysqli_character_set_name($link));
}
*/

  }
  return $link;
  } 
/////////////////////////////////////////////////  
//  msg
/////////////////////////////////////////////////
function Msg($s) {
$sPName = "Extraits.php";
$sPVer = "0.0";
// // echo ". $sPName $sPVer  $s. \n";
// // echo  .$sPName.  .$sPVer. .$s. ": ";
//echo $sPName." ".$sPVer.":".date("H:i:s").": ".$s."\n".'<br />';
//echo __DIR__.__FILE__.": ".$sPVer.":".date("H:i:s").": ".$s."\n".'<br />';
echo date("H:i:s :").__DIR__.__FILE__.": ".$s."\n".'<br/>';

}
/////////////////////////////////////////////////
//  sExecSQL
/////////////////////////////////////////////////
function sExecSQL($link,$s, $Silent=True) {
  if ($Silent) {} else {msg("sExecSQL: ".$s."\n");}
  $rs = mysqli_query($link,$s);
  if (!$rs)  {
    print_r(error_get_last());
    msg ("mysqli_query: \n".$s. "\n Could not execute query");
    //echo  $s;
    trigger_error(mysqli_error($link), E_USER_ERROR);
    }
    else {
    if ($Silent){} else{msg("... done"); }
    }
}
/////////////////////////////////////////////////
//  ReadCSVFileING    reference .csv   !
/////////////////////////////////////////////////
function ReadCSVFileING($l, $f) {//reference csv for this applic  !
//read file and push into Extraits, ING .csv format
//echo ("<br/");


//date is text, left aligned dd/mm/yyyy : 03/07/2017
//dc $data[4]
//dv $data[5]
// DateTime date_create_from_format ( string $format , string $time [, DateTimeZone $timezone ] )


$row = 0;
$sNotDef="";//empty 
if (($handle=fopen($f, "r"))!=FALSE) {
    echo ($f."<br/>");
    while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
        $num = count($data);
	$row++;
        /*
Numero_de_compte ,0        //compte Source
Nom_du_compte  ,1          //Nom
Compte_partie_adverse ,2   //compte Cible
Numero_de_mouvement ,3     //Mvmt
Date_comptable , 4         //format is 13/03/2015   d/ m/ y
Date_valeur,5
Montant ,6
Devise ,7
Libelles ,8
Details_du_mouvement ,9
Message ,   10
        */
	if ($data[1]=='Nom du compte') 	{ //msg("Data '".$data[1] ."' on row " .$row." not valid, skipping...");		
		}
	else 	{
		if (empty($data[4])) {$data[4]='01/01/1900';} else {}
		//if (empty($data[5])) {$data[5]='01/01/1900';} else {}
		if (empty($data[5])) {$data[5]=$data[4];} else {}  //if not set, use Dv
		
		$Dc=date_create_from_format('j/m/Y', $data[4]); // from str be format, return  datetime object
		$Dv=date_create_from_format('j/m/Y', $data[5]);
		
		//Numero_de_mouvement= concat(EXTRACT(YEAR FROM Date_comptable) , LPAD (Numero_de_mouvement,4,'0') )
		//create mvmy YYYYmmm    where mmm is left padded movement
		//  was  $mv= $data[3]+0;
		
		//$mv=date("Y",strtotime($data[4])).str_pad($data[3],4,'0',STR_PAD_LEFT )  ;
		//return object :  date_create_from_format ("d/m/Y", $data[4] ) 
		// strtotime  read  ONLY mm-dd-yyyy !!

	$dc_dt= date_create_from_format ( 'j/m/Y', $data[4]   );
	// create mvt = 'année  numero de mouvement'  : YYYYnnnn
	$mv=date("Y", date_timestamp_get($dc_dt) ).str_pad($data[3],4,'0',STR_PAD_LEFT )  ;
	$Dc=date_format($Dc, 'Y-m-d'); //output str US fmt from datetime object 
	$Dv=date_format($Dv, 'Y-m-d');

// ING send msg as only Dc, empty value, no currency
if  ( IsNullOrEmptyString($data[6]) )
{	$mo=0;
}
else
{	$mo= str_replace(",",".",$data[6])+0;
}

		$d8 = mysqli_real_escape_string($l, trim($data[8]));
		$d9 = mysqli_real_escape_string($l, trim($data[9]));
		$d10 = mysqli_real_escape_string($l, trim( $data[10]));
		$s="INSERT IGNORE INTO Extraits (Numero_de_compte,Nom_du_compte,Compte_partie_adverse,Numero_de_mouvement ,Date_comptable,Date_valeur,Montant,Devise,Libelles,Details_du_mouvement,Message) SELECT '".$data[0]."','".$data[1]."','".$data[2]."',".$mv.",'".$Dc . "','".$Dv . "',".$mo . ",'".$data[7] . "','".$d8 . "','".$d9."','".$d10 . "';";
		//Msg ($s);
		sExecSQL($l,$s, True);
		//fill comptes name table
		if (trim($data[2])==$sNotDef ){//if 'Compte_partie_adverse' is empty do nothing
		  }
		  else{ //insert num and 'detail' value
		  $s="INSERT IGNORE INTO CompteNames (Numero_de_compte,Compte_Name) SELECT
			'".$data[2]."', '" . mysqli_real_escape_string($l,$d9)."';
			";
		      }
		//echo $s."<br/>";
		sExecSQL($l,$s, True);
		}
	 }
	 }
	else
	{Msg(print_r(error_get_last()));
	return -1;
	}
    fclose($handle);
    //msg ("Read ".$row." lines");
return $row ;
}//  ReadCSVFileING

/////////////////////////////////////////////////
//  ReadCSVFileBELFIUS
/////////////////////////////////////////////////
function ReadCSVFileBELFIUS ($l, $f) {
// read file and push into Extraits,  .csv format
$row = 0;
$b_loop=TRUE;            
$sNotDef="nd";
if (($handle=fopen($f, "r"))!=FALSE) {
    echo ($f."<br/>");
    while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
        $num = count($data);//amt of columns, $data[x] where x = 0 -> $num
	/*Field REFNAME                                               BELFIUS field desc ( col 0..13 )
------------------------------------------------------------------------------------------------------
Numero_de_compte ,0                                         Compte  (0)
Nom_du_compte  ,1
Compte_partie_adverse ,2                                    Compte contrepartie (4)   !  VOIR  'Nom contrepartie contient' (5)
Numero_de_mouvement ,3                                      Numéro d'extrait (2)-Numéro de transaction (3)     pad with 0 for sort ?
Date_comptable , 4     //  format is 13/03/2015   d/ m/ y   Date de comptabilisation (1)
Date_valeur,5                                               Date valeur  (9)
Montant ,6                                                  Montant   (10)
Devise ,7                                                   Devise   (11)
SEARCH  Libelles ,8                                            Transaction  (8)
SEARCH  Details_du_mouvement ,9                             'Nom contrepartie contient'(5)+Rue et numéro(6)+Code postal et localité (7)
SEARCH  Message    10                                       'BIC' (12) et 'code pays' (13)

NOTES --
'Compte contrepartie' (4)  et 'Nom contrepartie contient'  (5)  can be used to fill comptenames table
NOT USED :  */
        $row++;
	//echo "<br> count data ==> ".$num;
	//echo "<br>=> ".$row." ".$data[1]." ".$data[2]." ".$data[3]." ".$data[4];
        if ($row<14) {  // data starts a line 15
            //msg("row ". $row.", data '".$data[0]." not valid, skipping...");
            //msg("-");
        }
        else {
            $b_loop=FALSE;
            //fill to get  00000_00000 as 'Mvmt'  
	     // if no data in Numéro d'extrait (2)-Numéro de transaction (3) , skip
	    if ($data[2]=='')  {  
	      //echo  $data[2]."<br/>" ;
	      echo "<br> skipped ==> ".$data[2].$data[3];
	      }
	    else {
				//  sept 2019 :  mv=YEAR $Dc - Numéro d'extrait (2)-Numéro de transaction (3) !
				// ex.:  2019_00011_00014
				$Dc=date_create_from_format('j/m/Y', $data[1]);//d compta
        $Dv=date_create_from_format('j/m/Y', $data[9]);//d valeur
        $dc_dt= date_format($Dc, 'Y');
        $Dc=date_format($Dc, 'Y-m-d');
        $Dv=date_format($Dv, 'Y-m-d');
				$mv=$dc_dt.'_'.str_pad($data[2], 5, "0", STR_PAD_LEFT).'_'.str_pad($data[3], 5, "0", STR_PAD_LEFT); 
        //  OLD $mv
        //$mv= str_pad($data[2], 5, "0", STR_PAD_LEFT).'_'.str_pad($data[3], 5, "0", STR_PAD_LEFT); 
	      //echo "<br> a==> ".$mv;	
	      // INSERT data 
            if (empty($data[4])) {$data[4]=$sNotDef;} else {} //Compte contrepartie 
            if (empty($data[5])) {$data[5]=$sNotDef;} else {} //Nom contrepartie contient

            $mo= str_replace(",",".",$data[10])+0; //montant
        //echo  "<br> ==> ".$Dv;
            $d8 = mysqli_real_escape_string($l, trim($data[8]));
            $d9 = mysqli_real_escape_string($l, trim($data[5]).trim($data[6]).trim($data[7]) );
            $d10 = mysqli_real_escape_string($l, trim( $data[12]).trim($data[13]));
            $s="INSERT IGNORE INTO Extraits
            (Numero_de_compte,Nom_du_compte,Compte_partie_adverse,Numero_de_mouvement ,Date_comptable,Date_valeur,Montant,Devise,Libelles,Details_du_mouvement,Message) SELECT
            '".$data[0]."',  '".$data[0]."','".$data[4]."','"       .$mv."','".               $Dc . "','".$Dv . "',".$mo . ",'".$data[11] . "','".$d8 . "','".$d9."','".$d10 . "';";

            //echo "<br> b==> ".$s; echo "<br/>";    echo "<br/>";
            sExecSQL($l,$s, True);

            
            if ($data[4]==$sNotDef) {
              }
              else{ 
                $s="INSERT IGNORE INTO CompteNames (Numero_de_compte,Compte_Name) SELECT
                  '".$data[4]."', '" . mysqli_real_escape_string($l,trim($data[5])) . "';
                  ";
		  //echo $s;
		  sExecSQL($l,$s, True);
		  }
              }
	    } //else
        } //while (($data = fgetcsv($handle, 0, ";")) !== FALSE
     } //handle
    else { Msg(print_r(error_get_last()));
    return -1;
    }
fclose($handle);
//msg ("Read ".$row." lines");
return $row ;
}//  ReadCSVFileBELFIUS

/////////////////////////////////////////////////
//  ReadCSVFileBNP
/////////////////////////////////////////////////
function ReadCSVFileBNP($l, $f) {

$DODEBUG=FALSE;
//$DODEBUG=TRUE;
if ($DODEBUG) { echo "debug "; }
//  link, file ,  BNP .csv format
// read file and push into Extraits
/*   UPDATED 29 03 2017  réapparition de 'CONTREPARTIE DE LA TRANSACTION'
/*   UPDATED 04 12 2017  montant est un vrai chiffre

Extraits fields names           table BNP FIELDSname   col number from 0    ( in Calc)

Numero_de_compte ,0		NUMERO DE COMPTE                7    (H)
Nom_du_compte  ,1               -
Compte_partie_adverse ,2        CONTREPARTIE DE LA TRANSACTION  5    (F)
Numero_de_mouvement ,3		Numéro de séquence              0    (A)
Date_comptable , 4   		Date d'exécution                1    (B)   // 13/03/2015   d/ m/ y		
    
Date_valeur,5			DATE VALEUR                     2    (C)
Montant ,6		        MONTANT                         3    (D) 1.216,72  !
Devise ,7			DEVISE DU COMPTE                4    (E)
Libelles ,8		    	Détails                         6    (G)  // can be num compte OR  msg as "FRAIS MENSUELS D'EQUIPEMENT" 
Details_du_mouvement ,9		 
Message    10        -
 */
//echo " > Reading  ".$f ;
$row = 0;
$sNotDef="";
if (($handle=fopen($f, "r")) !== FALSE) {    
    // echo 'File: ' . ($f."<br/>");
    while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
        $num = count($data);
        // alert if change of nbr of rcolumns!
        $ColCount=8;
        if ($num!=$ColCount) { Msg ("CSV: Column count has changed: was ".$ColCount.", is ".$num); return -1;
        }
        else {
	$row++;  //echo "<br> count data ==> ".$num;
	
	{ //  show  READ LINE
	if ($DODEBUG) { echo "<br>READ LINE =======:  <br>=> ".$row." / ".$data[0]." /  ".$data[1]."  / ".$data[2]." /  ".$data[3]."  / ".$data[4]."  / ".$data[5];	}
	}
	if (strlen(trim($data[0]))>9) { // skip column header  'Nº de séquence'   msg("Data >".$data[0] ."< on row " .$row." not valid, skipping...");
	if ($DODEBUG) { echo "  ## SKIPPED";echo "======="; }
	}
	else { 
	if ($DODEBUG) { echo "  ## READ";echo "======="; }
	  if (trim($data[6])==''){echo ' empty line '.$Numero_de_mouvement; //do nothing if no data on Numero_de_compte:last empty line 
	  }
	  else { //read and insert data
	    $Numero_de_mouvement= $data[0];//Numéro de séquence
	    if (strlen($Numero_de_mouvement)<6) {// skip if no seq nbr : 'YYYY-'  
	      //echo ' skipped '.$Numero_de_mouvement. "<br/>"; 
	      } 
	      else {
	      { /*  echo line 
	    echo 'row '. $row  . "<br/>";
	    echo $data[1] . "<br/>";
	    echo '>>'. $data[0] . "<br/>";
	    echo $data[1] . "<br/>";
	    echo $data[2] . "<br/>";
	    */  }
	    $Date_comptable=date_create_from_format('j/m/Y', $data[2]);//DATE VALEUR
	    $Date_valeur=date_create_from_format('j/m/Y', $data[1]);//Date d'exécution
	    $Nom_du_compte=''; //$data[7]

	    $Date_comptable=date_format($Date_comptable, 'Y-m-d');//date_format($Date_comptable, 'Y-m-d');
	    $Date_valeur=date_format($Date_valeur, 'Y-m-d');
	
	    $Montant= $data[3];//MONTANT  1.216,72 
	    /*
	    $Montant= str_replace(".","",$data[3]);//MONTANT  1.216,72 
	    $Montant= str_replace(",",".",$Montant);//MONTANT  1.216,72 
	    */
	
	    //echo $Numero_de_mouvement . "<br/>";	    
	    $Devise=$data[4];
	    $Compte_partie_adverse = mysqli_real_escape_string($l, trim($data[5]));
	    $Details_du_mouvement = mysqli_real_escape_string($l, trim($data[6]));

	    $Numero_de_compte=preg_replace('/\s+/', '', $data[7]);// http://stackoverflow.com/a/1279798/54964
	    //$Numero_de_compte=$data[6];
	    $Message = ''; // mysqli_real_escape_string($l, trim($data[5]));
	    $Libelles="_BNP_";
	    
	    $s="INSERT IGNORE INTO Extraits (Numero_de_compte ,Nom_du_compte,Compte_partie_adverse ,
	    Numero_de_mouvement ,Date_comptable ,Date_valeur,Montant ,
	    Devise, Libelles ,Details_du_mouvement ,Message) 
	    SELECT 
	    '".$Numero_de_compte."','.$Nom_du_compte.','$Compte_partie_adverse',
	    '".$Numero_de_mouvement."','".$Date_comptable."','".$Date_valeur."',".$Montant.",
	    '".$Devise."','".$Libelles."','".$Details_du_mouvement."','".$Message."';
	    ";
	    //echo  $s."<br/>";
	    sExecSQL($l,$s, True);
	    /////////////////////////////////////////////////
	    // INTO CompteNames (Numero_de_compte,Compte_Name)
	    /////////////////////////////////////////////////
	    {  //if (trim($data[5])==$sNotDef){ //compte adverse //delete then insert 
	    $s="
	    DELETE FROM `CompteNames` WHERE 
	    Numero_de_compte LIKE  '%".$Compte_partie_adverse."%';
	    ";
	    //echo $s;
	    //sExecSQL($l,$s, True);
	    $s="
	    INSERT IGNORE INTO CompteNames (Numero_de_compte,Compte_Name) SELECT
	      '".$Compte_partie_adverse."', '" . $Details_du_mouvement . "';
	      ";
	    //echo $s;
	    sExecSQL($l,$s, True);
	    //
	    } //($data[4]==$sNotDef)
	    
	    } //end else skip no seq 
	  } //end else read and insert data
	  } //end else skip first line
    }//while read
	} //change col count
	}//if (($handle=fopen($f, "r")) !== FALSE
else { Msg ("File open fails");
      Msg(print_r(error_get_last()));
      return -1;
      }
fclose($handle);
//msg ("Read ".$row." lines");
return $row ;
}//  ReadCSVFileBNP


/////////////////////////////////////////////////
//  ReadCSVFileRabo
/////////////////////////////////////////////////
function ReadCSVFileRabo($l, $f) {
//  link, file ,  RABO .csv format
// read file and push into Extraits
/*
Extraits fields names  N         Rabo FIELDSname   		col.
------------------------------------------------------------------------
Numero_de_compte ,     0	 PICK IN FIRST LINE, Col 1 !      na
Nom_du_compte  ,       1		"RaboBank"  ?
Compte_partie_adverse ,2         Compte de contrepartie		6

Numero_de_mouvement ,  3	* Nombre   			0	 
Date_comptable ,       4   	* Date de la transaction	1  		
Date_valeur,           5         Date valeur    		5	  

Montant ,              6	 Montant  			3       
Devise ,               7	 Devise 			4	  
Libelles ,             8	 Type de transaction  		2 	

Details_du_mouvement , 9	 Communication partie 1		8
Message               10         Communication partie 2 	9
     
     
    Nom de la contrepartie   7      to be used to populate   Compte_Name
                                           
 */
//echo " > Reading  ".$f ;
$row = 0;
$sNotDef="";
if (($handle=fopen($f, "r")) !== FALSE) {    
    //echo ($f."<br/>");
    while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
        $num = count($data);
	$row++;
	/////////////////////////////////////////////$d8////
	//  dump data
	/////////////////////////////////////////////////
	{//dump data;
	/*
	echo '<table id="t00">';
	foreach ($data as $key => $value) {
	  echo "<tr>";
	  echo "<td>";
	  echo $key;
	  echo "</td>";
	  echo "<td>";
	  echo $value;
	  echo "</td>";
	  echo "</tr>";
	  }
	  echo '</table>';
	//end dump data;
	*/
	}
	//num compte on first line
	if ($row==1) {
	  $NumCompte=$data[1];
	  $NomDuCompte="Rabo ".$NumCompte; 
	  //echo ("wrk with compte >".$NumCompte."<,    >".$NomDuCompte."<");
	  }
	  else
	    {
	    if  (($data[0])=='Nombre'){ //($row==2) {//data start on line 3
	    }
	    else { //read and insert data
	    //echo ("row ".$row."<br/>");
	    $mv= $data[0];
	    $Dc=date_create_from_format('j/m/Y', $data[1]);
	    $Dv=date_create_from_format('j/m/Y', $data[5]);
  
	    $Dc=date_format($Dc, 'Y-m-d');
	    $Dv=date_format($Dv, 'Y-m-d');
	    $mo= str_replace(".","",$data[3]);//MONTANT
	    $mo= str_replace(",",".",$mo);//MONTANT
  
	    $d8 = mysqli_real_escape_string($l, trim($data[2]));
	    $d9 = mysqli_real_escape_string($l, trim($data[8]));
	    $d10 ="_Rabo_".mysqli_real_escape_string($l, trim($data[9]));
  
	    $s="INSERT IGNORE INTO Extraits (Numero_de_compte ,Nom_du_compte,Compte_partie_adverse ,Numero_de_mouvement ,
	    Date_comptable ,Date_valeur,Montant ,
	    Devise, Libelles ,Details_du_mouvement ,Message) 
	    SELECT 
	    '".$NumCompte."','".$NomDuCompte."','".$data[6] ."','".$mv."',
	    '".$Dc . "','".$Dv . "',".$mo . ",
	    '".$data[4]."',     '".$d8."',     '".$d9."',     '".$d10."';
	    ";
	    //echo ($s);
	    sExecSQL($l,$s, True);
	
	    /////////////////////////////////////////////////
	    // INTO CompteNames (Numero_de_compte,Compte_Name)
	    /////////////////////////////////////////////////
	    if (trim($data[6])==$sNotDef){ 
	    }
	    else {//compte adverse 
	    $s="
	    DELETE FROM `CompteNames` WHERE 
	    Numero_de_compte LIKE  '%".mysqli_real_escape_string($l,trim($data[6]))."%';
	    ";
	    //echo $s;
	    sExecSQL($l,$s, True);
	    $s="
	    INSERT IGNORE INTO CompteNames (Numero_de_compte,Compte_Name) SELECT
	      '".mysqli_real_escape_string($l,trim($data[6]))."', '" . mysqli_real_escape_string($l,trim($data[7])) . "';
	      ";
	    //echo $s;
	    sExecSQL($l,$s, True);
	    } //($data[4]==$sNotDef)
	  } //end else read and insert data

	}//end else row1      
    }//while read
	}//if (($handle=fopen($f, "r")) !== FALSE
else { Msg ("File open fails");
      Msg(print_r(error_get_last()));
      return -1;
      }
fclose($handle);
//msg ("Read ".$row." lines");
return $row ;
}//  ReadCSVFileRabo

/////////////////////////////////////////////////
//  ReadCSVFilefortuneofr
/////////////////////////////////////////////////
function ReadCSVFilefortuneofr($l, $f) {
//read file and push into Extraits, fortuneoFR .csv format
//echo ("<br/");
// not enough data to have a full key ...
$row = 0;
$sNotDef="";//empty 
$CptSRC="Fortuneo_P.Calderon";  // ??????????
if (($handle=fopen($f, "r"))!=FALSE) {
    echo ($f."<br/>");
    while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
    	{//dump data;
	/*
	echo '<table id="t00">';
	foreach ($data as $key => $value) {
	  echo "<tr>";
	  echo "<td>";
	  echo $key;
	  echo "</td>";
	  echo "<td>";
	  echo $value;
	  echo "</td>";
	  echo "</tr>";
	  }
	  echo '</table>';
	//end dump data;
	*/
	}
   
        $num = count($data);
	$row++;
        /*
        			Cr�dit
Numero_de_compte ,0        	//  MANUAL  !
Nom_du_compte  ,1          	//
Compte_partie_adverse ,2   	//
Numero_de_mouvement ,3     	//
Date_comptable , 4         	//(0) Date op�ration           format is dd/mm/yyyy
Date_valeur,5			//(1) Date valeur
Montant ,6			//(3)  D�bit +  (4) Cr�dit
Devise ,7			// 'EUR'
Libelles ,8			//(2) libell�
Details_du_mouvement ,9
Message ,   10
        */
	if (substr($data[1],0,4)=='Date') { //msg("Data '".$data[1] ."' on row " .$row." not valid, skipping...");		
		}
	else 	{
		$mv= '';
		if (empty($data[0])) {$data[0]='01/01/1900';} else {}
		if (empty($data[1])) {$data[1]='01/01/1900';} else {}
echo $data[0];
echo $data[1];
		$Dc=date_create_from_format('j/m/Y', $data[0]);
		$Dv=date_create_from_format('j/m/Y', $data[1]);


		$Dc=date_format($Dc, 'Y-m-d');
		$Dv=date_format($Dv, 'Y-m-d');
		if (is_null($data[3])){$data[3]=0;}
		if (is_null($data[4])){$data[4]=0;}
		$data[3]= str_replace(".","",$data[3]);
		$data[3]= str_replace(",",".",$data[3]);
		$data[4]= str_replace(".","",$data[4]);
		$data[4]= str_replace(",",".",$data[4]);
		
		$mo=$data[3]+$data[4];

		$d8 = mysqli_real_escape_string($l, trim($data[2]));

		$s="INSERT IGNORE INTO Extraits (Numero_de_compte, Nom_du_compte, Compte_partie_adverse, Numero_de_mouvement ,Date_comptable,Date_valeur,Montant,Devise,
		Libelles,Details_du_mouvement,Message) SELECT 
		'".$CptSRC."','Phil','nd','".time()."',
		'".$Dc."','".$Dv."',".$mo.",'EUR',
		'".$d8."','','';";
		echo ($s);
		sExecSQL($l,$s, True);
		//fill comptes name table
		/*
		if (trim($data[2])==$sNotDef ){//if 'Compte_partie_adverse' is empty do nothing
		  }
		  else{ //insert num and 'detail' value
		  $s="INSERT IGNORE INTO CompteNames (Numero_de_compte,Compte_Name) SELECT
			'".$data[2]."', '" . mysqli_real_escape_string($l,$d9)."';
			";
		      }
		//echo $s."<br/>";
		sExecSQL($l,$s, True);
		*/
		}
	 }
	 }
	else
	{Msg(print_r(error_get_last()));
	return -1;
	}
    fclose($handle);
    //msg ("Read ".$row." lines");
return $row ;
}//  ReadCSVFilefortuneofr

/////////////////////////////////////////////////
//  ReadCSVFileArgenta
/////////////////////////////////////////////////
function ReadCSVFileArgenta($l, $f) {
//  link, file ,  RABO .csv format
// read file and push into Extraits
/*
db Extraits fields names  N       Argenta FIELDSname   		col.
------------------------------------------------------------------------
Numero_de_compte ,     0	 PICK IN FIRST LINE, Col 1 !    na
Nom_du_compte  ,       1	 PICK IN FIRST LINE, col2     	na
Compte_partie_adverse ,2         Compte de contrepartie		6

Numero_de_mouvement ,  3	Référence de l'opération        1	 
Date_comptable ,       4  	Date d'opération		5  		
Date_valeur,           5         Date valeur    		0	  

Montant ,              6	 Montant de l'opération		3  			3       
Devise ,               7	 Devise 			4	  
Libelles ,             8	 Description			2 	

Details_du_mouvement , 9	 Communication 1		7
Message               10         Communication 2		8 + 9


Used to populate   Compte_Name:
  Compte de contrepartie   6
  Nom de la contrepartie   7
 */
echo " > Reading  ".$f ;
$row = 0;
$sNotDef="";
if (($handle=fopen($f, "r")) !== FALSE) {    
    //echo ($f."<br/>");
    while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
        $num = count($data);
	$row++;
	/////////////////////////////////////////////////
	//  dump data
	/////////////////////////////////////////////////
	{//dump data;
	
	echo '<table id="t01">';
	
	/*
	foreach ($data as $key => $value) {
	  echo "<tr>";
	  echo "<td>";
	  echo $key;
	  echo "</td>";
	  echo "<td>";
	  echo $value;
	  echo "</td>";
	  echo "</tr>";
	  }
	  echo '</table>';
	//end dump data;
	*/
	}
	//num compte on first line
	if ($row==1) {
	  //echo "line 1";
	  //echo ("wrk with compte >".$Numero_de_compte."<,    >".$Nom_du_compte."<");
	  }
	  else
	    { 
	    //echo "read" ;
	       //if  (substr($data[0],0,4)=='Date')
	       //($row==2) {//data start on line 3}
	    $Numero_de_compte=trim($data[0]);
	    $Nom_du_compte="Argenta"; // trim($data[1]); 
	  
	    $Compte_partie_adverse=trim($data[8]);
	    $Nom_partie_adverse= mysqli_real_escape_string($l,trim($data[9]));   //trim($data[9]);
	    
	    //else { //read and insert data
	    //echo ("row ".$row."<br/>");
	    $Numero_de_mouvement= trim($data[3]);
	    $Date_comptable=date_create_from_format('j-m-Y', trim($data[1]));
	    $Date_valeur=date_create_from_format('j-m-Y', trim($data[2]));
  
	    $Date_comptable=date_format($Date_comptable, 'Y-m-d');
	    $Date_valeur=date_format($Date_valeur, 'Y-m-d');
	    $Montant= str_replace(".","",$data[5]);//MONTANT
	    $Montant= str_replace(",",".",$Montant);//MONTANT
	
	    $Devise=trim($data[6]);
	    $Libelles=trim($data[7]);   //'date de la transaction'
	    
	    $Details_du_mouvement=$Nom_partie_adverse;//" " ;// trim($data[10]);//nom de la contrepartie
	    
	    $Message = mysqli_real_escape_string($l, trim($data[10]));
	    //echo $Message ;
	    $Message= str_replace("'"," ",$Message);
	    //echo $Message ;
	    //$Message= str_replace("/"," ",$Message);
	    //echo " >>>>" . $Message ;

	    $s="INSERT IGNORE INTO Extraits (Numero_de_compte ,Nom_du_compte,Compte_partie_adverse ,
	    Numero_de_mouvement ,Date_comptable ,Date_valeur,Montant ,
	    Devise, Libelles ,Details_du_mouvement ,Message) 
	    SELECT 
	    '".$Numero_de_compte."','$Nom_du_compte','$Compte_partie_adverse',
	    '".$Numero_de_mouvement."','".$Date_comptable."','".$Date_valeur."',".$Montant.",
	    '".$Devise."','".$Libelles."','".$Details_du_mouvement."','".$Message."';
	    ";
	    //echo ($s);
	    sExecSQL($l,$s, True);
	    /////////////////////////////////////////////////
	    // INTO CompteNames (Numero_de_compte,Compte_Name)
	    /////////////////////////////////////////////////

	    if (trim($data[8])==$sNotDef){ 
	    //echo "skipped, no data Compte_partie_adverse" ;
	    }
	    else {//compte adverse 
	    $s="
	    DELETE FROM `CompteNames` WHERE 
	    Numero_de_compte LIKE  '%".$Compte_partie_adverse."%';
	    ";
	    //echo $s;
	    //sExecSQL($l,$s, True);
	    $s="
	    INSERT IGNORE INTO CompteNames (Numero_de_compte,Compte_Name) SELECT
	      '".$Compte_partie_adverse."', '". $Nom_partie_adverse. "';
	      ";
	    //echo $s;
	    sExecSQL($l,$s, True);
	    } //($data[4]==$sNotDef)
	    
	    
	   //end else read and insert data

	}//end else row1      
    }//while read
	}//if (($handle=fopen($f, "r")) !== FALSE
else { Msg ("File open fails");
      Msg(print_r(error_get_last()));
      return -1;
      }
fclose($handle);
//msg ("Read ".$row." lines");
return $row ;
}//  ReadCSVFileArgenta

/////////////////////////////////////////////////
//  ReadCSVFileKEYTRADE
/////////////////////////////////////////////////
function ReadCSVFileKEYTRADE($l, $f) {
//read file and push into Extraits, .csv format
//echo ("<br/");
$row = 0;
$sNotDef="";//empty 
if (($handle=fopen($f, "r"))!=FALSE) {
    //echo ($f."<br/>");
    //echo 'compte >' . $f ."<br/>" ;
    $d0=basename($f,'.csv' );
    //echo 'compte >' . $d0 ."<br/>" ;
    $d0= substr ( $d0 ,0 , 16 )  ;
    echo 'Fichier : ' . $f ;  //."<br/>" ;
    echo ',     compte (extrait du nom de fichier): ' . $d0 ."<br/>" ;
    $d1='Keytrade';
    while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
        $num = count($data);
	$row++;
/*field name                   csv column name            csv colum num
Numero_de_compte ,0        // extract from full path file name
Nom_du_compte  ,1          // 'keytrade'
Compte_partie_adverse ,2   //compte                          3
Numero_de_mouvement ,3     //Extrait                         0
Date_comptable , 4         //format is 12.10.2016  d/m/y     1
Date_valeur,5              //format is 12.10.2016  d/m/y     2
Montant ,6                 // Montant                        5
Devise ,7                  // Devise                         6
Libelles ,8                //Description                     4
Details_du_mouvement ,9
Message ,   10

Numero_de_compte  extract from file name :  MUST SAVE FILE WITH COMPTE AS NAME  !  :  
BE87651156809394.CSV
1234567890123456         16 LEFT CHAR
*/

	if (trim($data[0])=='Extrait')   { //msg("Data '".$data[1] ."' on row " .$row." not valid, skipping...");		
		}
	else 	{
		$mv= trim($data[0]);
//echo   $mv ."<br/>" ;		
		if ($mv !== $sNotDef) { //read 1 line too much  ...?
		$d3=trim($data[3]);
		if (empty($data[1])) {$data[1]='01/01/1900';} else {}
		if (empty($data[2])) {$data[2]='01/01/1900';} else {}
//echo   $data[1]."<br/>" ;
		$Dc=date_create_from_format('d.m.Y', $data[1]);
		$Dv=date_create_from_format('d.m.Y', $data[2]);

		$Dc=date_format($Dc, 'Y-m-d');
		$Dv=date_format($Dv, 'Y-m-d');

echo   $data[5]."<br/>" ;		
$mo= str_replace(".","",$data[5]);  // + 5.000,00   ->  5000,00
echo   $mo."<br/>" ;
$mo= str_replace(",",".",$mo)+0;   // + 5000,00   ->  5000.00
echo   $mo."<br/>" ;

		$d8 = mysqli_real_escape_string($l, trim($data[4]));
		$d9 = '';
		$d10 = '';
		//$d9 = mysqli_real_escape_string($l, trim($data[9]));
		//$d10 = mysqli_real_escape_string($l, trim( $data[10]));
		$s="INSERT IGNORE INTO Extraits (Numero_de_compte,Nom_du_compte,Compte_partie_adverse,
		Numero_de_mouvement ,Date_comptable,Date_valeur,
		Montant,Devise,Libelles,
		Details_du_mouvement,Message) SELECT '".$d0."','".$d1."','".$d3.
		"','".$mv."','".$Dc . "','".$Dv .
		"',".$mo . ",'".$data[6] . "','".$d8 .
		"','".$d9."','".$d10 . "';";
		//echo 'Row :' .$row ."<br/>";		
		//Msg ($s) ."<br/>";
		sExecSQL($l,$s, True);
		
		{//fill comptes name table
		if (trim($data[3])==$sNotDef ){//if 'Compte_partie_adverse' is empty do nothing
		  }
		  else{ //insert num and 'detail' value
		  $s="INSERT IGNORE INTO CompteNames (Numero_de_compte,Compte_Name) SELECT
			'".trim($data[3])."', '';
			";
		      }
		//echo $s."<br/>";
		sExecSQL($l,$s, True);
		}
		}
		} // mv not empty, avoid to read an last empty line  ?? ?
	 }
	 } // EOF
	else
	{ Msg(print_r(error_get_last()));
	return -1;
	}
    fclose($handle);
    //msg ("Read ".$row." lines");
return $row ;
}//  ReadCSVFileKEYTRADE

/////////////////////////////////////////////////
//  ReadCSVFileDB   Deutsche Bank
/////////////////////////////////////////////////
function ReadCSVFileDB($l, $f) {
/*  read file and push into Extraits, .csv format
echo ("<br/");
typical file name 
611-9437808-69 EUR.csv
1234567890123456789

field name                   csv column name            csv colum num
Numero_de_compte ,0        // extract from full path file name
Nom_du_compte  ,1          // 'DB'
Compte_partie_adverse ,2   //
Numero_de_mouvement ,3     //convert date col0 to  yyyymmdd
Date_comptable , 4         //format is 12/10/2016  d/m/y     0
Date_valeur,5              //
Montant ,6                 // Montant                        2
Devise ,7                  // Devise                         3
Libelles ,8                //Description                     1
Details_du_mouvement ,9
Message ,   10

Numero_de_compte  extract from file name :  MUST SAVE FILE WITH COMPTE AS NAME  !  :  
*/
$row = 0;
$sNotDef="";//empty 
if (($handle=fopen($f, "r"))!=FALSE) {
    //echo ($f."<br/>");
    //echo 'compte >' . $f ."<br/>" ;
    $d0=basename($f,'.csv' );
    //$d0= substr ( $d0 ,0 , 18 )  ;

    echo 'Fichier: ' . $f ;  //."<br/>" ;
    echo ', compte (extrait du nom de fichier): '.$d0."<br/>" ;
    $d1='DB';
    while (($data = fgetcsv($handle, 0, "|")) !== FALSE) {
        $num = count($data);
	$row++;
//echo "num col=".$num ."<br/>" ;	
	//if (trim($data[0])=='Extrait')   { //msg("Data '".$data[1] ."' on row " .$row." not valid, skipping...");		
	if (FALSE)   { //msg("Data '".$data[1] ."' on row " .$row." not valid, skipping...");		
		}
	else 	{
		//$mv=date('Ymd',date_create_from_format('d/m/Y', trim( $data[0]))  )   ;
		//Create unique mv with date and amount  ..  why not ?
		$mo= str_replace(",",".",$data[2])+0;
		$mv=date_format(date_create_from_format('d/m/Y', trim( $data[0])) ,'Ymd'). $mo;
echo "mv=".$mv ."<br/>" ;		
		if ($mv !== $sNotDef) { //read 1 line too much  ...?
		$d3="";
		//if (empty($data[1])) {$data[1]='01/01/1900';} else {}
		//if (empty($data[2])) {$data[2]='01/01/1900';} else {}
//echo   $data[1]."<br/>" ;
		$Dc=date_create_from_format('d/m/Y', $data[0]);
		$Dv=$Dc; //date_create_from_format('d.m.Y', $data[2]);

		$Dc=date_format($Dc, 'Y-m-d');
		$Dv=date_format($Dv, 'Y-m-d');
		

		$d8 = mysqli_real_escape_string($l, trim($data[1]));
		$d9 = '';
		$d10 = '';
		//$d9 = mysqli_real_escape_string($l, trim($data[9]));
		//$d10 = mysqli_real_escape_string($l, trim( $data[10]));
		$s="INSERT IGNORE INTO Extraits (Numero_de_compte,Nom_du_compte,Compte_partie_adverse,
		Numero_de_mouvement ,Date_comptable,Date_valeur,
		Montant,Devise,Libelles,
		Details_du_mouvement,Message) SELECT '".$d0."','".$d1."','".$d3.
		"','".$mv."','".$Dc . "','".$Dv .
		"',".$mo . ",'".$data[4] . "','".$d8 .
		"','".$d9."','".$d10 . "';";
		//echo 'Row :' .$row ."<br/>";		
		Msg ($s) ."<br/>";
		//sExecSQL($l,$s, True);
		
		{//fill comptes name table
		if (trim($data[3])==$sNotDef ){//if 'Compte_partie_adverse' is empty do nothing
		  }
		  else{ //insert num and 'detail' value
		  $s="INSERT IGNORE INTO CompteNames (Numero_de_compte,Compte_Name) SELECT
			'".trim($data[3])."', '';
			";
		      }
		//echo $s."<br/>";
		//sExecSQL($l,$s, True);
		}
		}
		} // mv not empty, avoid to read an last empty line  ?? ?
	 }
	 } // EOF
	else
	{ Msg(print_r(error_get_last()));
	return -1;
	}
    fclose($handle);
    //msg ("Read ".$row." lines");
return $row ;
}//  ReadCSVFileDB

//////////////////////////////////////////////////////////////////////////////////////////////////
//                                   ScanFolder
/////////////////////////////////////////////////

/////////////////////////////////////////////////
//  ScanFolderCSVBNP
/////////////////////////////////////////////////
function ScanFolderCSVBNP($LocCSV,$link){
//msg ( "Scanning  ".$LocCSV."") ;
$ReadFiles=0;
$ReadTotRows=0;
$path = realpath($LocCSV);
foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path)) as $filename)  {
  // load csv in Extraits_TMP
  $FLEN=strlen($filename) -4 ;
  // msg( substr($filename,(strlen($filename)-3),4);
  //msg ("===> Working with " . $filename . "");
  if (strtolower(substr($filename,$FLEN,4)) =='.csv')  {
    msg ("===> Working with " . $filename . "");
    $ReadFiles=$ReadFiles+1;
    $readrows=ReadCSVFileBNP($link,$filename);
    $ReadTotRows=$ReadTotRows + $readrows;
    $sNF= $filename .".done";
    // remove comment to rename
    rename($filename, $sNF);
    //msg ("Read " .$readrows. " lines");
    }  // end if if .csv
    else {
      //msg ("File is not .csv" );
    }// not .csv, skip
  } //end of foreach
echo "ScanFolderCSVBNP, read ".$ReadFiles." file(s), total " .$ReadTotRows. " lines";
return $ReadTotRows;
}//  ScanFolderCSVBNP

/////////////////////////////////////////////////
//  ScanFolderCSVING
/////////////////////////////////////////////////
function ScanFolderCSVING($LocCSV,$link){
//echo "<br/>"; echo (">ScanFolderCSVING:  ".$LocCSV) ;

$ReadFiles=0;
$ReadTotRows=0;
$path = realpath($LocCSV);
foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path)) as $filename)  {
  // load csv in Extraits_TMP
  $FLEN=strlen($filename) -4 ;
  //msg ("===> Working with " . $filename . "");
  if (strtolower(substr($filename,$FLEN,4)) =='.csv')  {
    $ReadFiles=$ReadFiles+1;
    $readrows=ReadCSVFileING($link,$filename);
    if ($readrows!=-1) {
    $ReadTotRows=$ReadTotRows + $readrows;
    $sNF= $filename .".done";
//echo "rename is disabled";
    rename($filename, $sNF);
    }
    //msg ("Read " .$readrows. " lines");
    }  // end if if .csv
    else {
      //msg ("File is not .csv" );
    }// not .csv, skip
  } //end of foreach
echo "ScanFolderCSVING, read ".$ReadFiles." file(s), total " .$ReadTotRows. " lines";
return $ReadTotRows;

}//  ScanFolderCSVING

/////////////////////////////////////////////////
//  ScanFolderCSVRabo
/////////////////////////////////////////////////
function ScanFolderCSVRabo($LocCSV,$link){
//echo "<br/>"; echo (">ScanFolderCSVRabo:  ".$LocCSV) ;
$ReadFiles=0;
$ReadTotRows=0;
$path = realpath($LocCSV);
foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path)) as $filename)  {
  // load csv in Extraits_TMP
  $FLEN=strlen($filename) -4 ;
  //msg ("===> Working with " . $filename . "");
  if (strtolower(substr($filename,$FLEN,4)) =='.csv')  {
    $ReadFiles=$ReadFiles+1;
    $readrows=ReadCSVFileRabo($link,$filename);
    if ($readrows!=-1) {
    $ReadTotRows=$ReadTotRows + $readrows;
    $sNF= $filename .".done";
    rename($filename, $sNF);
    }
    //msg ("Read " .$readrows. " lines");
    }  //end if if .csv
    else {
      //msg ("File is not .csv" );
    }//not .csv, skip
  } //end of foreach
echo "ScanFolderCSVRABO, read ".$ReadFiles." file(s), total " .$ReadTotRows. " lines";
return $ReadTotRows;
}//  ScanFolderCSVRabo

/////////////////////////////////////////////////
//  ScanFolderCSVfortuneoFR
/////////////////////////////////////////////////
function ScanFolderCSVfortuneoFR($LocCSV,$link){
// msg ( "Scanning  ".$LocCSV."") ;
$ReadFiles=0;
$ReadTotRows=0;
$path = realpath($LocCSV);
foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path)) as $filename)  {
  // load csv in Extraits_TMP
  $FLEN=strlen($filename) -4 ;
  //msg ("<br>===> Working with " . $filename . "");
  //msg (".");
  if (strtolower(substr($filename,$FLEN,4)) =='.csv')  {
    $ReadFiles=$ReadFiles+1;
    $readrows=ReadCSVFilefortuneoFR($link,$filename);
    if ($readrows!=-1) {
    $ReadTotRows=$ReadTotRows + $readrows;
    $sNF= $filename .".done";
    rename($filename, $sNF);
    }
    //msg ("Read " .$readrows. " lines");
    }  // end if if .csv
    else {   //msg ("File is not .csv" );
    }// not .csv, skip
  } //end of foreach
echo "ScanFolderCSVfortuneoFR, read ".$ReadFiles." file(s), total " .$ReadTotRows. " lines";
return $ReadTotRows;
}//  ScanFolderCSVfortuneoFR

/////////////////////////////////////////////////
//  ScanFolderCSVArgenta
/////////////////////////////////////////////////
function ScanFolderCSVArgenta($LocCSV,$link){
// msg ( "Scanning  ".$LocCSV."") ;
$ReadFiles=0;
$ReadTotRows=0;
$path = realpath($LocCSV);
foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path)) as $filename)  {
  // load csv in Extraits_TMP
  $FLEN=strlen($filename) -4 ;
  //msg ("<br>===> Working with " . $filename . "");
  //msg (".");
  if (strtolower(substr($filename,$FLEN,4)) =='.csv')  {
    $ReadFiles=$ReadFiles+1;
    $readrows=ReadCSVFileArgenta($link,$filename);
    if ($readrows!=-1) {
    $ReadTotRows=$ReadTotRows + $readrows;
    $sNF= $filename .".done";
    //echo "\n OLD ". $filename ;
    //echo "\n NEW " . $sNF  ;
    rename($filename, $sNF);
    }
    //msg ("Read " .$readrows. " lines");
    }  // end if if .csv
    else {   //msg ("File is not .csv" );
    }// not .csv, skip
  } //end of foreach
echo "ScanFolderCSVArgenta, read ".$ReadFiles." file(s), total " .$ReadTotRows. " lines";
return $ReadTotRows;
}//  ScanFolderCSVArgenta

/////////////////////////////////////////////////
//  ScanFolderCSVKEYTRADE
/////////////////////////////////////////////////
function ScanFolderCSVKEYTRADE($LocCSV,$link){
/*
  Num de compte from first 16 char of FILE NAME  !
*/

// msg ( "Scanning  ".$LocCSV."") ;
$ReadFiles=0;
$ReadTotRows=0;
$path = realpath($LocCSV);
foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path)) as $filename)  {
  // load csv in Extraits_TMP
  $FLEN=strlen($filename) -4 ;
  //msg ("<br>===> Working with " . $filename . "");
  //msg (".");
  if (strtolower(substr($filename,$FLEN,4)) =='.csv')  {
    $ReadFiles=$ReadFiles+1;
    $readrows=ReadCSVFileKEYTRADE($link,$filename);
    if ($readrows!=-1) {
    $ReadTotRows=$ReadTotRows + $readrows;
    //$FileName='ExtraitsDataExport_'.date_format(date_create(), 'Ymd_His');
    $sNF= $filename.".".date_format(date_create(), 'Ymd_His').".done";
    rename($filename, $sNF);
    }
    //msg ("Read " .$readrows. " lines");
    }  // end if if .csv
    else {   //msg ("File is not .csv" );
    }// not .csv, skip
  } //end of foreach
echo "ScanFolderCSVKEYTRADE, read ".$ReadFiles." file(s), total " .$ReadTotRows. " lines";
return $ReadTotRows;
}// ScanFolderCSVKEYTRADE

/////////////////////////////////////////////////
//  ScanFolderCSVBelfius
/////////////////////////////////////////////////
function ScanFolderCSVBelfius($LocCSV,$link){
// msg ( "Scanning  ".$LocCSV."") ;
$ReadFiles=0;
$ReadTotRows=0;
$path = realpath($LocCSV);
foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path)) as $filename)  {
  // load csv in Extraits_TMP
  $FLEN=strlen($filename) -4 ;
  //msg ("<br>===> Working with " . $filename . "");
  //msg (".");
  if (strtolower(substr($filename,$FLEN,4)) =='.csv')  {
    $ReadFiles=$ReadFiles+1;
    $readrows=ReadCSVFileBELFIUS($link,$filename);
    if ($readrows!=-1) {
    $ReadTotRows=$ReadTotRows + $readrows;
    $sNF= $filename .".done";
    rename($filename, $sNF);
    }
    //msg ("Read " .$readrows. " lines");
    }  // end if if .csv
    else {   //msg ("File is not .csv" );
    }// not .csv, skip
  } //end of foreach
echo "ScanFolderCSVBelfius, read ".$ReadFiles." file(s), total " .$ReadTotRows. " lines";
return $ReadTotRows;
}//  ScanFolderCSVBelfius

/////////////////////////////////////////////////
//  ScanFolderCSVDB
/////////////////////////////////////////////////
function ScanFolderCSVDB($LocCSV,$link){
/*
  Num de compte from first 14 char of FILE NAME  !
611-9437808-69 EUR
12345678901234 
*/

// msg ( "Scanning  ".$LocCSV."") ;
$ReadFiles=0;
$ReadTotRows=0;
$path = realpath($LocCSV);
foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path)) as $filename)  {
  // load csv in Extraits_TMP
  $FLEN=strlen($filename) -4 ;
  //msg ("<br>===> Working with " . $filename . "");
  //msg (".");
  if (strtolower(substr($filename,$FLEN,4)) =='.csv')  {
    $ReadFiles=$ReadFiles+1;
    $readrows=ReadCSVFileDB($link,$filename);
    if ($readrows!=-1) {
    $ReadTotRows=$ReadTotRows + $readrows;
    $sNF= $filename .".done";
    //rename($filename, $sNF);
    }
    //msg ("Read " .$readrows. " lines");
    }  // end if if .csv
    else {   //msg ("File is not .csv" );
    }// not .csv, skip
  } //end of foreach
echo "ScanFolderCSVDB, read ".$ReadFiles." file(s), total " .$ReadTotRows. " lines";
return $ReadTotRows;
}//ScanFolderCSVDB

//////////////////////////////////////////////////////////////////////////////////////////////////
// utilities
/////////////////////////////////////////////////
//  upload
/////////////////////////////////////////////////
function upload(){
$target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if($check !== false) {
        echo "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }
}
// Check if file already exists
if (file_exists($target_file)) {
    echo "Sorry, file already exists.";
    $uploadOk = 0;
}
// Check file size
if ($_FILES["fileToUpload"]["size"] > 500000) {
    echo "Sorry, your file is too large.";
    $uploadOk = 0;
}
// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" ) {
    echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    $uploadOk = 0;
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}
}

/////////////////////////////////////////////////
//  GetBegEnd
/////////////////////////////////////////////////
function GetBegEnd($link, $nCpte){
// return beg end val of Date_comptable as
// $out['Beg'] = "abc";  $out['End'] = "abc"
$LimitDn="2000/01/01";
$LimitUp=date("Y/m/d");
if ($nCpte==''||$nCpte=='%') {
//if ($nCpte=='') {
  $Out['Beg']="$LimitDn";
  $Out['End']="$LimitUp";
  } 
  else {
  $sql= "SELECT Extraits.Date_comptable FROM Extraits
  WHERE Extraits.Numero_de_compte LIKE '$nCpte'
  ORDER BY EXTRACT(YEAR_MONTH FROM Date_comptable) ASC, EXTRACT(DAY FROM Date_comptable) ASC LIMIT 1;" ;
  // echo $sql;
  $query = mysqli_query($link, $sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysqli_error($link));
  $Ret = mysqli_fetch_assoc($query);
  if (is_null($Ret['Date_comptable'])) {
    $Out['Beg']=$LimitDn;
    $Out['End']=$LimitUp; 
    }
  else {
    $Out['Beg']=$Ret['Date_comptable'];
    mysqli_free_result($query);
    // return $Out['Beg'] ;
    $sql= "SELECT Extraits.Date_comptable FROM Extraits
    WHERE Extraits.Numero_de_compte LIKE '$nCpte'
    ORDER BY EXTRACT(YEAR_MONTH FROM Date_comptable) DESC, EXTRACT(DAY FROM Date_comptable) DESC LIMIT 1;" ;
    // echo $sql;
    $query = mysqli_query($link, $sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysqli_error($link));
    $Ret = mysqli_fetch_assoc($query);
    $Out['End']=$Ret['Date_comptable'];
    mysqli_free_result($query);
    }
  }
return $Out;
}

/////////////////////////////////////////////////
//  time2string
/////////////////////////////////////////////////
function time2string($time) {
//http://stackoverflow.com/questions/12918158/timestamp-to-days-hours-minutes
    $d = floor($time/86400);
    $_d = ($d < 10 ? '0' : '').$d;

    $h = floor(($time-$d*86400)/3600);
    $_h = ($h < 10 ? '0' : '').$h;

    $m = floor(($time-($d*86400+$h*3600))/60);
    $_m = ($m < 10 ? '0' : '').$m;

    $s = $time-($d*86400+$h*3600+$m*60);
    $_s = ($s < 10 ? '0' : '').$s;

    $time_str = $_d.'_'.$_h.':'.$_m.':'.$_s;

    return $time_str;  //  dd_hh:mm:ss
}
////////////////////////////////////////////////
//  MakeBackup
/////////////////////////////////////////////////
function MakeBackup($MySQLDumpProgLoc,$BackupDir,$host,$user,$pass,$db) {
//http://www.theblog.ca/mysql-email-backup	
//file permission:
//http://stackoverflow.com/questions/2900690/how-do-i-give-php-write-access-to-a-directory

$sendto = "Webmaster <luc.berger@gmail.com>";
$sendfrom = "Automated Backup <backup@yourdomain.com>";
$sendsubject = "Daily Mysql Backup";
$bodyofemail = "Here is the daily backup.";

$backupfile = $BackupDir."/".$db."_".date("Y-m-d").".sql";

if($pass==""){$p="";}else{$p="-p$pass";}
# 2>&1
$cmd="$MySQLDumpProgLoc -h $host -u $user $p $db > $backupfile";

echo "->Backup exec cmd is :"; echo $cmd; echo "<br/>";
//echo"<br/>whoami: "; echo `whoami`;  echo"<br/>";
exec("($cmd) 2>&1", $output, $result); echo "<br/>";
echo "->cmd return :"; var_dump($result); echo "<br/>";
//echo "prt output:";
echo "->var_dump :"; var_dump($output); echo "<br/>";

return $backupfile;
/*
system($cmd, $retvar);
return "system() return ".$retvar.", backup file ".$backupfile;
*/


// Mail the file
/*
include ( 'Mail.php' );
include( 'Mail/mime.php' );

$message = new Mail_mime();
$text = "$bodyofemail";
$message->setTXTBody( $text );
$message->AddAttachment( $backupfile );
$body = $message->get();
$extraheaders = array( "From"=> $sendfrom, "Subject"=> $sendsubject );
$headers = $message->headers( $extraheaders );
$mail = Mail::factory( "mail" );
$mail->send( $sendto, $headers, $body );

// Delete the file from your server
//unlink($backupfile);	
*/
}
/////////////////////////////////////////////////
//  MarkPartOfText
/////////////////////////////////////////////////
function MarkPartOfText($inp, $words) {  //Hilight
//return $inp with any word in $words matrix  <mark>$word</mark>   ==> HTML tag mark surrounding $word
//return $inp if not found
//http://stackoverflow.com/questions/8564578/php-search-text-highlight-function
  $replace=array_flip(array_flip($words)); // remove duplicates
  $pattern=array();
  foreach ($replace as $k=>$fword) {
     //$pattern[]='/\b('.$fword.')(?!>)\b/i';  
     $pattern[]='/('.$fword.')(?!>)/i';
     $replace[$k]='<mark>$1</mark>';
  }
  return preg_replace($pattern, $replace, $inp);
}

/////////////////////////////////////////////////
// Function for basic field validation (present and neither empty nor only white space
//https://stackoverflow.com/questions/381265/better-way-to-check-variable-for-null-or-empty-string
/////////////////////////////////////////////////
function IsNullOrEmptyString($str){
    return (!isset($str) || trim($str) === '');
}

/////////////////////////////////////////////////
//  ComputeSolde
/////////////////////////////////////////////////
function ComputeSolde($link, $compte, $reverse=FALSE){
/*
REFERENCES
2016-0335    112694.18
2016-0162     18538.00	

--- info ---
! Assuming no missing extracts  !
! Assuming entered 'solde' (see from bank extract) and 'solde_computed' set to same value !
! if solde is really 0 we have trouble with this code, add a field 'done' ..  ?

.column          .desc 

solde           from bank extract
solde_computed  computed with montant at each line
------------
*/
//Begin computing from last not 0 'solde_computed':
//if $compte not set use $Def_FrCpte  //$compte='BE15 0012 2393 9330%';
//   DEBUG <<<<<<<<<<<<<<<<<<

echo "== ComputeSolde for ".$compte; echo": Updating";
if ($reverse){ // going to older extracts
echo " older ";
}
else { echo " newer ";
}
echo "extracts ============<br>";
// was 
// ORDER BY Numero_de_mouvement DESC

$sql="
SELECT  Extraits.Numero_de_compte, Date_comptable,Date_valeur, Numero_de_mouvement, Montant, solde, solde_computed 
FROM Extraits
WHERE
Extraits.Numero_de_compte LIKE '".$compte."%' 
AND NOT solde_computed =0
ORDER BY Numero_de_mouvement DESC
limit 1
";

{
/*
/////////////////////////////////////////////////////////////////////////////////////////////////
// if  CompteNames.Sort  field is defined , use it as SORT BY
$sql=" SELECT Sortby FROM SortBy WHERE Numero_de_compte LIKE  '$sel_FrCpte%' ";
$query = mysqli_query($link, $sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
 if(mysqli_num_rows($query)>0){
  $data = mysqli_fetch_array($query);
  $s_OB = $data["Sortby"] ;
  }
  else {
  $s_OB=" (EXTRACT(YEAR_MONTH FROM Date_comptable)+Numero_de_mouvement) DESC, EXTRACT(DAY FROM Date_comptable) DESC ";
  }
$sqlOrder=" ORDER BY " . $s_OB ;
*/
}

//echo "<br>".$sql."<br>";

//echo "<br>".$sql."<br>";
/*   DEBUG <<<<<<<<<<<<<<<<<<
echo "<br>Extrait     Mt     Solde  -----------------------<br>";  
*/
$query=mysqli_query($link, $sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysqli_error($link));
while ($data = mysqli_fetch_array($query)) {
  $curMvmt=$data["Numero_de_mouvement"];
  $solde=$data["solde_computed"];
  $Montant=$data["Montant"]; 

  //echo "Mvmt= ".$curMvmt."  mt= ".$Montant."  Solde= ".$solde ;  echo "<br>";


  if ($reverse){ // going to older extracts
  $sql="
  SELECT Extraits.Numero_de_compte, Numero_de_mouvement, Montant, solde, solde_computed 
  FROM Extraits
  WHERE
  Extraits.Numero_de_compte LIKE '".$compte."%' 
  and
    Numero_de_mouvement <'$curMvmt'
  ORDER BY Numero_de_mouvement  
  limit 1
  ";
  }
  else {  //default, going to more recent
  $sql="
  SELECT Extraits.Numero_de_compte, Numero_de_mouvement, Montant, solde, solde_computed 
  FROM Extraits
  WHERE
  Extraits.Numero_de_compte LIKE '".$compte."%' 
  and
    Numero_de_mouvement >'$curMvmt'
  ORDER BY Numero_de_mouvement  
  limit 1
  ";
  }
  //echo "<br>".$sql."<br>";
  } // end of loop
  $query=mysqli_query($link, $sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysqli_error($link));

  while ($data = mysqli_fetch_array($query)) {
    $curMvmt=$data["Numero_de_mouvement"];
    $Montant=$data["Montant"];  
    if ($reverse){ // going to older extracts
      $solde=$solde - $Montant;
    }
    else { //default, going to more recent
      $solde=$solde + $Montant;
    }
    $sql="
    UPDATE Extraits SET solde_computed=$solde 
    WHERE 
    Extraits.Numero_de_compte LIKE '".$compte."%' 
    AND 
    Numero_de_mouvement='$curMvmt'
    ";
    //echo "<br>".$sql."<br>";   //update record
    mysqli_query($link, $sql);
  
  //echo "<br>dernier extrait >$curMvmt<<br>";  
  echo "Mvmt= ".$curMvmt."  mt= ".$Montant."  Solde= ".$solde ; echo"  updated <br>";
  //echo "<br>";
    if ($reverse){ // going to older extracts
    $sql="
  SELECT Extraits.Numero_de_compte, Numero_de_mouvement, Montant, solde, solde_computed 
  FROM Extraits
  WHERE
  Extraits.Numero_de_compte LIKE '".$compte."%' 
  and
    Numero_de_mouvement <'$curMvmt'
  ORDER BY Numero_de_mouvement  
  limit 1
  ;";
      }
      else {
  $sql="
  SELECT Extraits.Numero_de_compte, Numero_de_mouvement, Montant, solde, solde_computed 
  FROM Extraits
  WHERE
  Extraits.Numero_de_compte LIKE '".$compte."%' 
  and
    Numero_de_mouvement >'$curMvmt'
  ORDER BY Numero_de_mouvement  
  limit 1
  ;";
  }
  $query=mysqli_query($link, $sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysqli_error($link));
  }

} //end of function ComputeSolde

/////////////////////////////////////////////////
//  CheckRecord
/////////////////////////////////////////////////
function CheckRecord($l) {  //check for presence of a record, in or out
// read Check table
$sql="SELECT * FROM `Check` " ;
////if ($DODEBUG) { 
//echo "using : ". $sql.'<br>' ;
////}
//echo "<br>- Check records ".date("d-m-Y")."----------------------- <br>" ;
echo "<br>- Check records ----------------------- <br>" ;
$abortWhile=FALSE;
$query = mysqli_query($l, $sql) or die('-> 01 Erreur SQL !<br />'.$sql.'<br />'.error_get_last());
while ($data = mysqli_fetch_array($query)) {
		$abortWhile=FALSE;
		//$CheckNom=$data["nom"]." (from ".$data["datebeg"]." to ".$data["dateend"].") "  ;
		$CheckNom=$data["nom"]." (".$data["datebeg"].")"  ;
	if (!$data["ignore"])  {
		//echo "Checked: ".$CheckNom ;
		////build request into Extraits
		$sql="SELECT * FROM Extraits WHERE " ;
		$sql = $sql."(". $data["condition"].")";   //condition
		$sql = $sql." AND (Date_comptable BETWEEN " ;   // beg date range
		switch ($data["datebeg"]) { // from ..
			case "month" :   //first day of current month : date('Y-m-01');
				$sel_d= date_format (new DateTime('first day of this month'), "Y-m-d");
				break;
			case "lastmonth" :   //last day of current month= okay, first day of current !
				$sel_d=date("Y-m-d", mktime(0,0,0,date("m")-1, 01,date("Y")));					
				break;
			default:
			  echo "DateBeg keyword  '".$data["datebeg"]."' not recognised, please check.";
			  $abortWhile=FALSE;
			  break;
			}
		$sql = $sql."'".$sel_d."' AND ";
		switch ($data["dateend"]) { // to ..
			case "month" :   //last day of current month
				$sel_d= date('Y-m-t');
				break;
			case "lastmonth" :   //last day of last month
				$sel_d=date("Y-m-d", mktime(0,0,0,date("m"), 1,date("Y")));
				break;
			default:
			  echo "DateEnd keyword  ".$data["dateend"]." not recognised, please check.";
			  $abortWhile=FALSE;
			  break;				
			}
		$sql = $sql."'".$sel_d  ."')" ;  // end date range
		//echo " sql check extraits : " . $sql ."<br>";
		////output result
		$queryCheck=mysqli_query($l, $sql) or die('Erreur SQL !<br />'.$sql.'<br />'.error_get_last());
		//$dataCheck = mysqli_fetch_array($queryCheck) ;
		//if ($queryCheck) {
		if( mysqli_num_rows($queryCheck) ) {  //if found record(s)
			while ($dataCheck = mysqli_fetch_array($queryCheck)) {
			//echo "Checked: " .$CheckNom  ;
			if (empty($dataCheck ["Numero_de_mouvement"])) {
				echo "NOK: Checked: ".$CheckNom ;
				echo " is NOT found  !<br>";
				}
			else {
				//echo "<br>------------------------ <br>" ;
				echo "OK : Checked: ".$CheckNom ;
				echo " is found: ";
				echo "&nbsp&nbspNumero_de_mouvement ".$dataCheck ["Numero_de_mouvement"].", " ;
				echo "&nbsp&nbspMontant ".$dataCheck ["Montant"].", " ;
				echo "&nbsp&nbspDate_comptable ".$dataCheck ["Date_comptable"]."<br>" ;
				//echo "&nbsp&nbspLibelles ".$dataCheck ["Libelles"]."<br>" ;
				//echo "&nbsp&nbspDetails_du_mouvement ".$dataCheck ["Details_du_mouvement"]."<br>" ;
				//echo "&nbsp&nbspMessage ".$dataCheck ["Message"]."<br>" ;
				//echo "&nbsp&nbsp------------------------------------------------ <br>" ;
				}
		}  // end while ($dataCheck = mysqli_fetch_array($queryCheck))
	} // end else sql found
	else {
		echo "NOK: Checked: ".$CheckNom ;
		echo " is NOT found  !<br>";
		}
}
	else {
		//echo "Ignored rule " .$CheckNom."<br>";
		} // end 'ignore' is true	
} // end loop in 'check' table : while ($data = mysqli_fetch_array($query)) 
echo "- End check records ----------------------- <br>" ;
}  // end CheckRecord()

/////////////////////////////////////////////////
//  end functions
/////////////////////////////////////////////////
?>
