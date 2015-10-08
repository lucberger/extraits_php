<?php
/*
TODO
export/backup
include file std functions
*/
error_reporting(E_ALL);
// if(isset($_POST['add']))
$tmp="/tmp/p";
/*   test
//$LocCSV = "/home/luc/Documents/SharedVirtualBox/BanqueExtraits/extraits_ING";
$LocCSV = '/var/www/ex/extraits_ING';
$host = "localhost";
$user = "luc";
$pass = "boreal";
$db   = "Extraits";
*/
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
    msg ("mysqli_query: \n".$s. "\n Could not execute query:");
    echo  $s;
    trigger_error(mysql_error(), E_USER_ERROR);
    }
    else {
    if ($Silent){} else{msg("... done"); }
    }
}
/////////////////////////////////////////////////
//  ReadCSVFileBNP
/////////////////////////////////////////////////
 function ReadCSVFileBNP($l, $f) {
//  link, file ,  BNP .csv format
// read file and push into Extraits
/*
Extraits fields names                table BNP FIELDSname   number
Numero_de_compte ,0		  NUMERO DE COMPTE   7
Nom_du_compte  ,1
    -
Compte_partie_adverse ,2
  -
Numero_de_mouvement ,3		  ANNEE + REFERENCE   0
Date_comptable , 4   		  DATE DE L'EXECUTION   1 // 13/03/2015   d/ m/ y		
    
Date_valeur,5			  DATE VALEUR   2
Montant ,6		          MONTANT   3
Devise ,7			  DEVISE DU COMPTE    4
Libelles ,8		    	CONTREPARTIE DE L'OPERATION    5  // can be num compte OR  msg as  "FRAIS MENSUELS D'EQUIPEMENT"  !!
Details_du_mouvement ,9		DETAIL    6
Message    10
     -
 */
//echo " > Reading  ".$f ;
$row = 0;
$sNotDef="";
if (($handle=fopen($f, "r")) !== FALSE) {    
    echo ($f."<br/>");
    while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
        $num = count($data);
	$row++;
	/////////////////////////////////////////////$d8////
	//  dump data
	/////////////////////////////////////////////////
	{/* //dump data;
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
	// end dump data;
	*/}
	// if colums header , skip
	if ($data[0]=='ANNEE + REFERENCE') { //msg("Data >".$data[0] ."< on row " .$row." not valid, skipping...");
	}
	else { 
	  if (trim($data[7])==''){//do nothing if no data on Numero_de_compte:last empty line 
	  }
	  else { //read and insert data
	    $mv= $data[0];//ANNEE + REFERENCE
	    $Dc=date_create_from_format('j/m/Y', $data[2]);//DATE VALEUR
	    $Dv=date_create_from_format('j/m/Y', $data[1]);//DATE DE L'EXECUTION
  
	    $Dc=date_format($Dc, 'Y-m-d');
	    $Dv=date_format($Dv, 'Y-m-d');
	    $mo= str_replace(",",".",$data[3]);//MONTANT
  
	    $d9 = mysqli_real_escape_string($l, trim($data[6]));
	    $d5 = mysqli_real_escape_string($l, trim($data[5]));
	    $d10 = " - "; // mysqli_real_escape_string($l, trim($data[5]));
  
	    $s="INSERT IGNORE INTO Extraits (Numero_de_compte ,Nom_du_compte,Compte_partie_adverse ,Numero_de_mouvement ,Date_comptable ,Date_valeur,Montant ,
	    Devise, Libelles ,Details_du_mouvement ,Message) 
	    SELECT 
	    '".$data[7]."','".$data[7]."','".$d5 ."','".$mv."',
	    '".$Dc . "','".$Dv . "',".$mo . ",
	    '".$data[4]."',     '"."_BNP_"."',     '".$d9."',     '".$d10."';
	    ";
	    //echo ( $s);
	    sExecSQL($l,$s, True);
	    /////////////////////////////////////////////////
	    // INTO CompteNames (Numero_de_compte,Compte_Name)
	    /////////////////////////////////////////////////
	    //if (trim($data[5])==$sNotDef){ //compte adverse //delete then insert 
	    $s="
	    DELETE FROM `CompteNames` WHERE 
	    Numero_de_compte LIKE  '%".mysqli_real_escape_string($l,trim($data[5]))."%';
	    ";
	    echo $s;
	    sExecSQL($l,$s, True);
	    $s="
	    INSERT IGNORE INTO CompteNames (Numero_de_compte,Compte_Name) SELECT
	      '".mysqli_real_escape_string($l,trim($data[5]))."', '" . mysqli_real_escape_string($l,trim($data[6])) . "';
	      ";
	    echo $s;
	    sExecSQL($l,$s, True);
	    //} //($data[4]==$sNotDef)
	  } //end else read and insert data
	  } //end else skip first line
	     
    }//while read
	}//if (($handle=fopen($f, "r")) !== FALSE
else { Msg ("File open fails");
      Msg(print_r(error_get_last()));
      return -1;
      }
fclose($handle);
//msg ("Read ".$row." lines");
return $row ;
}//function
/////////////////////////////////////////////////
//  ReadCSVFileING
/////////////////////////////////////////////////
function ReadCSVFileING($l, $f) {
//reference csv for this applic  !
//read file and push into Extraits, ING .csv format
//echo ("<br/");
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
		$mv= $data[3]+0;
		if (empty($data[4])) {$data[4]='01/01/1900';} else {}
		if (empty($data[5])) {$data[5]='01/01/1900';} else {}

		$Dc=date_create_from_format('j/m/Y', $data[4]);
		$Dv=date_create_from_format('j/m/Y', $data[5]);

		$Dc=date_format($Dc, 'Y-m-d');
		$Dv=date_format($Dv, 'Y-m-d');
		$mo= str_replace(",",".",$data[6])+0;

		$d8 = mysqli_real_escape_string($l, trim($data[8]));
		$d9 = mysqli_real_escape_string($l, trim($data[9]));
		$d10 = mysqli_real_escape_string($l, trim( $data[10]));
		$s="INSERT IGNORE INTO Extraits (Numero_de_compte,Nom_du_compte,Compte_partie_adverse,Numero_de_mouvement ,Date_comptable,Date_valeur,Montant,Devise,Libelles,Details_du_mouvement,Message) SELECT '".$data[0]."','".$data[1]."','".$data[2]."',".$mv.",'".$Dc . "','".$Dv . "',".$mo . ",'".$data[7] . "','".$d8 . "','".$d9."','".$d10 . "';";
		//Msg ( $s);
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
}
/////////////////////////////////////////////////
//  ReadCSVFileBelfius
/////////////////////////////////////////////////
function ReadCSVFileBelfius($l, $f) {
// read file and push into Extraits,  .csv format
$row = 0;
$b_loop=TRUE;			
$sNotDef="nd";
if (($handle=fopen($f, "r"))!=FALSE) {
    echo ($f."<br/>");
    while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
        $num = count($data);//amt of columns, $data[x] where x = 0 -> $num
/*    Field REFNAME                                               BELFIUS field desc ( col 0..13 )
------------------------------------------------------------------------------------------------------
Numero_de_compte ,0                                         Compte  (0)
Nom_du_compte  ,1
Compte_partie_adverse ,2                                    Compte contrepartie (4)   !  VOIR  'Nom contrepartie contient' (5)
Numero_de_mouvement ,3                                      Numéro d'extrait (2)-Numéro de transaction (3)     pad with 0 for sort ?
Date_comptable , 4     //  format is 13/03/2015   d/ m/ y   Date de comptabilisation (1)
Date_valeur,5                                               Date valeur  (9)
Montant ,6                                                  Montant   (10)
Devise ,7                                                   Devise   (11)
SEARCH  Libelles ,8										    Transaction  (8)
SEARCH  Details_du_mouvement ,9                             'Nom contrepartie contient'(5)+Rue et numéro(6)+Code postal et localité (7)
SEARCH  Message    10                                       'BIC' (12) et 'code pays' (13)

NOTES --
'Compte contrepartie' (4)  et 'Nom contrepartie contient'  (5)  can be used to fill comptenames table
NOT USED :
*/
// read line until i read 'Compte' . this is the start of real data
//echo $data[0]. '  ' .$data[1] ; echo '<br>';
		$row++;

		if (($row<14)) {  // data starts a line 15
			//msg("row ". $row.", data '".$data[0]." not valid, skipping...");
			//msg("-");
		}
		else {

			$b_loop=FALSE;
			//fill to get  00000_00000 as 'Mvmt'  
			$mv= str_pad($data[2], 5, "0", STR_PAD_LEFT).'_'.str_pad($data[3], 5, "0", STR_PAD_LEFT); 
// echo "<br> ==> ".$mv;
			if (empty($data[4])) {$data[4]=$sNotDef;} else {} //Compte contrepartie 
			if (empty($data[5])) {$data[5]=$sNotDef;} else {} //Nom contrepartie contient
			$Dc=date_create_from_format('j/m/Y', $data[1]);//d compta
			$Dv=date_create_from_format('j/m/Y', $data[9]);//d valeur
			$Dc=date_format($Dc, 'Y-m-d');
			$Dv=date_format($Dv, 'Y-m-d');
			$mo= str_replace(",",".",$data[10])+0; //montant
//echo  $data[1]."  ".$data[9];
			$d8 = mysqli_real_escape_string($l, trim($data[8]));
			$d9 = mysqli_real_escape_string($l, trim($data[5]).trim($data[6]).trim($data[7]) );
			$d10 = mysqli_real_escape_string($l, trim( $data[12]).trim($data[13]));
			$s="INSERT IGNORE INTO Extraits
			(Numero_de_compte,Nom_du_compte,Compte_partie_adverse,Numero_de_mouvement ,Date_comptable,Date_valeur,Montant,Devise,Libelles,Details_du_mouvement,Message) SELECT
			'".$data[0]."',  '".$data[0]."','".$data[4]."','"       .$mv."','".               $Dc . "','".$Dv . "',".$mo . ",'".$data[11] . "','".$d8 . "','".$d9."','".$d10 . "';";
			// echo $s;
			sExecSQL($l,$s, True);
			if ($data[4]==$sNotDef){
			  }
			  else{ 
			    $s="INSERT IGNORE INTO CompteNames (Numero_de_compte,Compte_Name) SELECT
			      '".$data[4]."', '" . mysqli_real_escape_string($l,trim($data[5])) . "';
			      ";
			      }
			//echo $s;
			sExecSQL($l,$s, True);
			
			} //else
		} //while (($data = fgetcsv($handle, 0, ";")) !== FALSE
	 } //handle
	else { Msg(print_r(error_get_last()));
	return -1;
	}
fclose($handle);
//msg ("Read ".$row." lines");
return $row ;
}
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
  }
  return $link;
  } 
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
    $ReadFiles=$ReadFiles+1;
    $readFile=ReadCSVFileBNP($link,$filename);
    $ReadTotRows=$ReadTotRows + $readFile;
    $sNF= $filename .".done";
    rename($filename, $sNF);
    //msg ("Read " .$readFile. " lines");
    }  // end if if .csv
    else {
      //msg ("File is not .csv" );
    }// not .csv, skip
  } //end of foreach
$return="ScanFolderCSVBNP, read ".$ReadFiles." file(s), total " .$ReadTotRows. " lines";

// msg ($return);
return $return;
}
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
    $readFile=ReadCSVFileING($link,$filename);
    if ($readFile!=-1) {
    $ReadTotRows=$ReadTotRows + $readFile;
    $sNF= $filename .".done";
    rename($filename, $sNF);
    }
    //msg ("Read " .$readFile. " lines");
    }  // end if if .csv
    else {
      //msg ("File is not .csv" );
    }// not .csv, skip
  } //end of foreach
$return="ScanFolderCSVING, read ".$ReadFiles." file(s), total " .$ReadTotRows. " lines";
// msg ($return);
return $return;
}
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
    $readFile=ReadCSVFileBelfius($link,$filename);
    if ($readFile!=-1) {
    $ReadTotRows=$ReadTotRows + $readFile;
    $sNF= $filename .".done";
    rename($filename, $sNF);
    }
    //msg ("Read " .$readFile. " lines");
    }  // end if if .csv
    else {   //msg ("File is not .csv" );
    }// not .csv, skip
  } //end of foreach
$return="ScanFolderCSVBelfius; read ".$ReadFiles." file(s), total " .$ReadTotRows. " lines";
// msg ($return);
return $return;
}
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
if ($nCpte=='' || $nCpte=='%') {
  $Out['Beg']="1900/01/01";
  $Out['End']="2100/01/01";
  } 
  else {
  $sql= "SELECT Extraits.Date_comptable FROM Extraits
  WHERE Extraits.Numero_de_compte LIKE '$nCpte'
  ORDER BY EXTRACT(YEAR_MONTH FROM Date_comptable) ASC, EXTRACT(DAY FROM Date_comptable) ASC LIMIT 1;" ;
  // echo $sql;
  $query = mysqli_query($link, $sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
  $Ret = mysqli_fetch_assoc($query);
  if (is_null($Ret['Date_comptable'])) {
    $Out['Beg']="1900/01/01";
    $Out['End']="2100/01/01"; 
    }
  else {
    $Out['Beg']=$Ret['Date_comptable'];
    mysqli_free_result($query);
    // return $Out['Beg'] ;
    $sql= "SELECT Extraits.Date_comptable FROM Extraits
    WHERE Extraits.Numero_de_compte LIKE '$nCpte'
    ORDER BY EXTRACT(YEAR_MONTH FROM Date_comptable) DESC, EXTRACT(DAY FROM Date_comptable) DESC LIMIT 1;" ;
    // echo $sql;
    $query = mysqli_query($link, $sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
    $Ret = mysqli_fetch_assoc($query);
    $Out['End']=$Ret['Date_comptable'];
    mysqli_free_result($query);
    }
  }
return $Out;
}
/////////////////////////////////////////////////
//  end functions
/////////////////////////////////////////////////
/*
include_once 'dblogin.php';
// test with direct command to php from console:
// luc@luc-MS-7502> php  /var/www/extraits/ScanFolderCSVING.php

// http://php.net/manual/fr/language.constants.predefined.php
// msg (" >> Running " .__DIR__.__FILE__);
// msg ("Connect toDB");


$DefCpt= '310-0072179-97'  ; // '363-1361797-54' ;
$link=connexion_DB($host ,$user ,$pass,$db);

// list($var1, $var2)  = GetBegEnd($link, $DefCpt );
// echo "$var1 $var2";
/*
$aaa= GetBegEnd($link, $DefCpt );
msg ( $DefCpt."\n");
msg ( $aaa['Beg']."\n");
msg ( $aaa['End']."\n");
// echo GetBegEnd($link, $DefCpt );
//$DefCpt= '363-1361797-54' ;
$DefCpt= '363-4842997-21' ;
$aaa= GetBegEnd($link, $DefCpt );
msg ($DefCpt."\n");
msg ( $aaa['Beg']."\n");
msg ( $aaa['End']."\n");
mysqli_close ($link);
ScanFolderCSVBNP($LocCSV,$link);

ScanFolderCSVING($LocCSV,$link);
/*
// insert records evt Compte_partie_adverse
$sql="INSERT IGNORE INTO CompteNames (Numero_de_compte) SELECT DISTINCT Extraits.Compte_partie_adverse FROM Extraits where Compte_partie_adverse;";
// $sql="SELECT DISTINCT Extraits.Compte_partie_adverse FROM Extraits where Compte_partie_adverse;";
sExecSQL($link,$sql,FALSE);
*/
?>
