<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=windows-1252"/>
    <title>Extraits</title>
    <style type="text/css">
        @page { margin: 2cm }
        p { margin-bottom: 0.25cm; line-height: 120% }
        pre.cjk { font-family: "courier", monospace }
        td p { margin-bottom: 0 }
table, th, td {
  border: 0px solid black;
  border-collapse: collapse;
  }
table#t01 {
    width:100%;
    }
table#t01 th	{
    background-color: #996600;
    color: white;
  }
table#t01, th, td {
  border: 1px solid black;
  border-collapse: collapse;
  }
table#t01 tr:nth-child(even) {
    background-color: #FFFFCC;
  }
table#t01 tr:nth-child(odd) {
   background-color:#FFFFFF;
  }

</style>
</head>
<body lang="fr-FR" dir="ltr">
</pre>
<pre class="western"></pre><p style="margin-bottom: 0cm; line-height: 100%">
</p>
</body>
</html>
<?php
/*
http://luc-4790k/extraits/Extraits_00.php?j_b=18&m_b=010&a_b=2010&j_e=16&m_e=09&a_e=2015&FrCpte=BE15001223939330&ToCpte=&rbFrTS=t&rbToTS=t&rbDCT=t&sSel=
*/
/////////////////////////////////////////////////////////////////////////////////////////////////
// include
/////////////////////////////////////////////////////////////////////////////////////////////////
{ session_start();
include_once 'ScanFolderCSV.php';
include_once 'maliste_combo.php';
include_once 'dblogin.php';
$SecInDay=24*3600;  // 86400
}
//echo basename(__FILE__, '.php')." Version ".$Ver."<br>";
//echo basename()." Version ".$Ver."<br>";
/////////////////////////////////////////////////////////////////////////////////////////////////
// Version
/////////////////////////////////////////////////////////////////////////////////////////////////
$Ver="20190902";
/////////////////////////////////////////////////////////////////////////////////////////////////
//  set connexion_DB, extract $_GET  params
/////////////////////////////////////////////////////////////////////////////////////////////////
{$link=connexion_DB($host ,$user ,$pass, $db);
//var_dump ( $link) ;

// $b_Sel is true if something is set in the received header
if (empty($_GET)){$b_Sel=FALSE;}else{$b_Sel=TRUE;}  
//if (isset($_GET)){$b_Sel=TRUE;}else{$b_Sel=FALSE;}
//if ($b_Sel){echo '. &nbsp Selections.<br/>';} else {echo '. &nbsp Pas de selection.<br/>';}

if ($b_Sel){  //$b_Sel is true if something is set in the received header
  $sel_FrCpte =trim(urldecode($_GET['FrCpte']));  // from compte
  $sel_ToCpte =urldecode($_GET['ToCpte']);  // to compte
  $sel_sSel   =urldecode($_GET['sSel']);      //search text in lib, det, et msg
  $sel_rbDCT  =$_GET['rbDCT'];               //radio button Debit-Credit-Tout     d - c - t
  $sel_rbToTS =$_GET['rbToTS'];            //radio button To Ttcomptes - Sel compte     t - s
  $sel_rbFrTS =$_GET['rbFrTS'];            //radio button From Ttcomptes - Sel compte     t - s
  //$sel_rbToCN = $_GET['rbToCN'];         //radio button par nom ou num cpte  C N
  $sel_lrech  =$_GET['lrech'];              //utiliser les mots ou la liste
  $sel_rblrech=$_GET['rblrech'];

  $sel_dbeg=$_GET['a_b']."/".$_GET['m_b']."/".$_GET['j_b']; //used in sql  SELECT
  $sel_dend=$_GET['a_e']."/".$_GET['m_e']."/".$_GET['j_e'];
  $framelist=$_GET['framelist'] ;
  }
  else { //init //default value
  /////////////////////////////////////////////////////////////////////////////////////////////////
  // set default value range for 'date comptable' pre-selection:
  // frame_select, frame_all, frame_12month,frame_6month,  frame_begyear, frame_begpension
  /////////////////////////////////////////////////////////////////////////////////////////////////
  $framelist='frame_3months';
  $sel_FrCpte=trim($Def_FrCpte);
  $sel_ToCpte=($Def_ToCpte);
  $sel_sSel='';
  $sel_rbDCT='t';
  $sel_rbToTS='t'; //vers compte : tous
  $sel_rbFrTS='s'; //de compte selectionné
  //$sel_rbToCN='n'; //par NOM de compte (tt les comptes associes a ce nom
  $sel_lrech= '';
  $sel_rblrech= 's'; //s: utiliser les mots  -t: liste
  }
  //echo $Def_FrCpte;
switch ($sel_rbDCT) {
    case "d":
        $s_rbDCT="AND Montant<0 ";$s_rbDCT_txt='d&eacutebit uniquement, ';break;
    case "c":
        $s_rbDCT="AND Montant>0 ";$s_rbDCT_txt='cr&eacutedit uniquement, ';break;
    case "t":
        $s_rbDCT="";$s_rbDCT_txt='';break;
}
//radio buttons: tt compte / sel.compte
if($sel_rbToTS=='t'){
  $sel_ToCpte="%";
  }
  else {
  }

if($sel_rbFrTS=='t'){
  $sel_FrCpte="%";
  }
  else {
  }
/* 
if ($sel_sSel!='') {
  $sel_ToCpte='%';
}
*/
}
/////////////////////////////////////////////////////////////////////////////////////////////////
//  show all passed $_GET   ( $_GET $_POST   $_SERVER ) 
/////////////////////////////////////////////////////////////////////////////////////////////////
$DODEBUG=FALSE;
//$DODEBUG=TRUE;
if ($DODEBUG) {  
  //echo "From:>".$sel_FrCpte."<<br>" ;
  //echo "To  :>".$sel_ToCpte ."<<br>" ;
  echo '==> dump $_GET';
  echo '<table id="t01">';
  foreach ($_GET as $key => $value) {
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
  echo '==> end dump $_GET<br>';
  }
/////////////////////////////////////////////////////////////////////////////////////////////////
// first date avail  28 Dec 2015  only get sld dates
/////////////////////////////////////////////////////////////////////////////////////////////////
{
//$DcBegEnd= GetBegEnd($link, $sel_FrCpte );
$DcBegEnd= GetBegEnd($link, "%" );
// echo  print_r ($DcBegEnd );
//var_dump ($DcBegEnd) ;
//var_dump( $sel_FrCpte) ;
$Dc= $DcBegEnd['Beg'];// yyy/mm/dd
// msg ( $Dc );
$id_moisb=substr($Dc,5,2);//extract month
$id_jourb=substr($Dc,8,8);//extract day
$Dc_Abeg=substr($Dc,0,4); //extract year
//$Dc_BegRec=$Dc; //human readable date 

if ($b_Sel){
  //$sel_dbeg=$_GET['a_b']."/".$_GET['m_b']."/".$_GET['j_b']; // used in sql select
  //$sel_dbeg=$Dc;
  $Dc_selb=$_GET['a_b'] ;                                  // used in 2x combo box years select
  $id_jourb=$_GET['j_b'];                                   // used in 'from' combo box select
  $id_moisb=$_GET['m_b'];                                   // used in 'from' combo box select
  }
  else {
  //get full range
  $Dc_selb=$Dc_Abeg;
  $sel_dbeg= $Dc ;
  /*
  //get last 365 days
  $Dc_selb=date_add(date_create(),-365);  //current day -365 
  $sel_dbeg= $Dc ;
  */
  }
// msg ('sel_beg set to '. $sel_dbeg);
}
/////////////////////////////////////////////////////////////////////////////////////////////////
// last date avail    only get seld dates
/////////////////////////////////////////////////////////////////////////////////////////////////
{
$Dc= $DcBegEnd['End'];
$id_moise=substr($Dc,5,2);
$id_joure=substr($Dc,8,8);
$Dc_Aend=substr($Dc,0,4);
$Dc_EndRec=$Dc;

if ($b_Sel){
  //$sel_dend=$_GET['a_e']."/".$_GET['m_e']."/".$_GET['j_e'];//used sql
  $Dc_sele=$_GET['a_e'];
  $id_joure=$_GET['j_e'];
  $id_moise=$_GET['m_e'];
  }
  else {
  $Dc_sele=$Dc_Aend;
  $sel_dend= $Dc ;
  $sel_sSel="";
  }
}
/////////////////////////////////////////////////////////////////////////////////////////////////
// Date Comptable   combo box selection:  debut de pension/ .. ,  default range
/////////////////////////////////////////////////////////////////////////////////////////////////
{
//$framelist=$_GET['framelist']; //echo $framelist ;
//echo $framelist .'<br/>';

// text in combo box
$DateRangeTxt1=" Selection ->";
$DateRangeTxt2=" Tout ";
$DateRangeTxt3=" 12 mois glissants ";

$DateRangeTxt4=" Annee courante ";
$DateRangeTxt5=" Depuis pension: mars2016 ";

$DateRangeTxt6=" 6 mois glissants ";

$DateRangeTxt7=" 3 mois glissants ";

echo "<FORM>";
echo '<table><table border="0">
<tr>
<td><b>Date Comptable : </b> &nbsp
<select name=framelist>
<option value=frame_select';    if($framelist=='frame_select')  {echo ' selected';} echo'>'.$DateRangeTxt1.'</option>
<option value=frame_all';       if($framelist=='frame_all')     {echo ' selected';} echo'>'.$DateRangeTxt2.'</option>
<option value=frame_12months';  if($framelist=='frame_12months'){echo ' selected';} echo'>'.$DateRangeTxt3.'</option>
<option value=frame_6months';   if($framelist=='frame_6months') {echo ' selected';} echo'>'.$DateRangeTxt6.'</option>
<option value=frame_3months';   if($framelist=='frame_3months') {echo ' selected';} echo'>'.$DateRangeTxt7.'</option>
<option value=frame_begyear';   if($framelist=='frame_begyear') {echo ' selected';} echo'>'.$DateRangeTxt4.'</option>
<option value=frame_begpension';if($framelist=='frame_begpension') {echo ' selected';} echo'>'.$DateRangeTxt5.'</option>
</select>
';
echo "</FORM>";
$yearplusone=date("Y")+1;
$DcBegEnd= GetBegEnd($link, "%" );

switch ($framelist) {
    case 'frame_select': { //selection
	$DateRangeName=$DateRangeTxt1;
        break;}
    case 'frame_all':{ //all
	//get full range
	$DateRangeName=$DateRangeTxt2;	
	$Dc= $DcBegEnd['Beg'];// yyy/mm/dd
	//$Dc= $DcBegEnd['End'];// yyy/mm/dd
	//echo $Dc;
	$id_moisb=substr($Dc,5,2);//extract month beg
	$id_jourb=substr($Dc,8,8);//extract day beg
	$Dc_Abeg=substr($Dc,0,4); //extract year
	$Dc_selb=substr($Dc,0,4); //extract year
	//$Dc_BegRec=$Dc; //human readable date 
	$sel_dbeg=$Dc_Abeg."/".$id_moisb."/".$id_jourb; // used in sql select
	
	$Dc= $DcBegEnd['End'];
	$id_moise=substr($Dc,5,2);
	$id_joure=substr($Dc,8,8);
	$Dc_Aend=substr($Dc,0,4);
	$Dc_EndRec=$Dc;
        break;}

    case 'frame_12months': {//date-1year .. current :  365 jours glissants
	$DateRangeName=$DateRangeTxt3;
	$id_jourb=date("d");
	$id_moisb=date("m");
	$Dc_Abeg=2000;
	$Dc_Aend=$yearplusone;
	
	$Dc_selb=date("Y")-1;                           // set year minus 1
	$sel_dbeg=$Dc_selb."/".$id_moisb."/".$id_jourb; // YYYY/MM/DD
	
	$Dc= $DcBegEnd['End'];
	$id_moise=substr($Dc,5,2);
	$id_joure=substr($Dc,8,8);
	$Dc_Aend=substr($Dc,0,4);
	$Dc_EndRec=$Dc;	
        break;}

    case 'frame_6months': {//date-6 months . current     $date = new DateTime();  $interval = new DateInterval('P6M');
//line 593:  AND Date_comptable BETWEEN '$sel_dbeg' AND '$sel_dend'

	$DateRangeName=$DateRangeTxt6;
	$id_jourb=date("d");
	
	$Dc_Abeg=2000;
	
	//make date object with today
	$iUnixTime=time();
	//remove 6 month in seconds 24*3600*30
	$iseconds=6*(24*3600*30);
	//set $sel_dbeg
	$iMinus=$iUnixTime-$iseconds;
	//formatted date
	$sel_dbeg= date("Y/m/d",$iMinus)  ;
		
	//end date is last record
	$Dc= $DcBegEnd['End'];
	$id_moise=substr($Dc,5,2);
	$id_joure=substr($Dc,8,8);
	$Dc_Aend=substr($Dc,0,4);
	$Dc_EndRec=$Dc;		
        break;}

    case 'frame_3months': {//date-6 months . current     $date = new DateTime();  $interval = new DateInterval('P6M');
//line 593:  AND Date_comptable BETWEEN '$sel_dbeg' AND '$sel_dend'

	$DateRangeName=$DateRangeTxt7;
	$id_jourb=date("d");
	
	$Dc_Abeg=2000;
	
	//make date object with today
	$iUnixTime=time();
	//remove 6 month in seconds 24*3600*30
	$iseconds=3*(24*3600*30);
	//set $sel_dbeg
	$iMinus=$iUnixTime-$iseconds;
	//formatted date
	$sel_dbeg= date("Y/m/d",$iMinus)  ;
		
	//end date is last record
	$Dc= $DcBegEnd['End'];
	$id_moise=substr($Dc,5,2);
	$id_joure=substr($Dc,8,8);
	$Dc_Aend=substr($Dc,0,4);
	$Dc_EndRec=$Dc;		
        break;}

    case 'frame_begyear': { //begin of current year .. current
	$DateRangeName=$DateRangeTxt4;    
	$id_jourb=01;
	$id_moisb=01;
	$Dc_Abeg=2000;
	$Dc_Aend=$yearplusone;
	$Dc_selb=date("Y");
	$sel_dbeg=$Dc_selb."/".$id_moisb."/".'01';

	$id_joure=30;
	$id_moise=12;
	$Dc_Abeg=2000;
	$Dc_Aend=$yearplusone;
	$Dc_sele=date("Y");
        break;}
        
    case 'frame_begpension':{ //1 mars 2016
	$DateRangeName=$DateRangeTxt5;    
	$id_jourb=01;
	$id_moisb=03;
	$Dc_Abeg=2000;
	$Dc_Aend=$yearplusone;
	$Dc_selb=2016;
	$sel_dbeg=$Dc_selb."/".$id_moisb."/".'01';
	
	//$id_joure=30;
	//$id_moise=12;
	$Dc_Abeg=2000;
	//$Dc_Aend=$yearplusone;
	$Dc_sele=date("Y");
	
	$Dc= $DcBegEnd['End'];
	$id_moise=substr($Dc,5,2);
	$id_joure=substr($Dc,8,8);
	$Dc_Aend=substr($Dc,0,4);
	//$Dc_EndRec=$Dc;		
    break;}
}
/////////////////////////////////////////////////////////////////////////////////////////////////
//function listbox_an ($lo=2000,$hi=2020, $an)
//  show  selected fom to dates
/////////////////////////////////////////////////////////////////////////////////////////////////

echo '
si selection ->, du 
        <select name="j_b">',listbox_jour ($id_jourb),'</select>
        <select name="m_b">',listbox_mois ($id_moisb),'</select>
        <select name="a_b">',listbox_an($Dc_Abeg,$Dc_Aend,$Dc_selb),'</select>
 au &nbsp&nbsp
        <select name="j_e">',listbox_jour ($id_joure),'</select>
        <select name="m_e">',listbox_mois ($id_moise),'</select>
        <select name="a_e">',listbox_an ($Dc_Abeg,$Dc_Aend,$Dc_sele),'</select>
</td>
</tr>
</table>';
}
/////////////////////////////////////////////////////////////////////////////////////////////////
// FROM COMPTE  combo box list avail comptes source
/////////////////////////////////////////////////////////////////////////////////////////////////
{
echo '<b>Du compte </b> <select name="FrCpte">';
/*
SELECT DISTINCT Extraits.Numero_de_compte as f1, Extraits.Nom_du_compte as f2, CompteNames.Compte_Name as f3 ,CompteNames.Note as f4
FROM Extraits LEFT JOIN CompteNames ON  Extraits.Numero_de_compte =CompteNames.Numero_de_compte
ORDER BY CompteNames.Compte_Name 
---orig
SELECT DISTINCT Extraits.Numero_de_compte as f1, Extraits.Nom_du_compte as f2, CompteNames.Compte_Name as f3 ,CompteNames.Note as f4
FROM Extraits inner JOIN CompteNames ON  Extraits.Numero_de_compte =CompteNames.Numero_de_compte
ORDER BY CompteNames.Note ,  Extraits.Numero_de_compte 
-----orig above

SELECT DISTINCT  CompteNames.Numero_de_compte f1, CompteNames.Compte_Name  f2,  CompteNames.Note f4
FROM  CompteNames
where Compte_Name not like '' 

*/
$sql="
SELECT DISTINCT  CompteNames.Numero_de_compte f1, CompteNames.Compte_Name  f2, '' f3 , CompteNames.Note f4
FROM  CompteNames
WHERE CompteNames.Note NOT LIKE '' 
";
if ($DODEBUG) { 
echo '<br>'.$sql ;
}
$query = mysqli_query($link, $sql) or die('Erreur SQL !<br />'.$sql.'<br />'.error_get_last());
while ($data = mysqli_fetch_array($query)) { //$Def_FrCpte
    echo "<option VALUE=".urlencode($data["f1"]);
    if(trim($data["f1"])==trim($sel_FrCpte)){echo ' selected';}
    if(trim($data["f1"])==$Def_FrCpte) { $def='Def: ';}  else { $def='';}
    // echo '> Def: ',$data["f1"].'&nbsp &nbsp &nbsp &nbsp'.$data["f2"]." ".$data["f3"]." ".$data["f4"]."",'</option>';
    echo '>',$def.$data["f1"].'&nbsp &nbsp &nbsp &nbsp'.$data["f2"]." ".$data["f3"]." ".$data["f4"]."",'</option>';
}
echo '</select>';
}
/////////////////////////////////////////////////////////////////////////////////////////////////
// TO COMPTE  combo box list avail comptes partie adverse
/////////////////////////////////////////////////////////////////////////////////////////////////
{
echo '<br/><b>Si \'VERS compte selectionn&eacute\' est s&eacutelectionn&eacute, vers compte</b> <select name="ToCpte">';
/*
SELECT DISTINCT Extraits.Numero_de_compte as f1, CompteNames.Compte_Name as f2 ,CompteNames.Note as f3, '' as f4 
FROM Extraits LEFT JOIN CompteNames ON  Extraits.Numero_de_compte =CompteNames.Numero_de_compte
ORDER BY  Extraits.Numero_de_compte 

SELECT `Numero_de_compte` as f1 , `Compte_Name` as f2 
FROM `CompteNames` 
WHERE  'Compte_Name'  <> ''
ORDER BY  f1
*/
$sql="
SELECT DISTINCT Extraits.Numero_de_compte as f1, CompteNames.Compte_Name as f2 ,CompteNames.Note as f3, '' as f4 
FROM Extraits RIGHT JOIN CompteNames ON  Extraits.Numero_de_compte =CompteNames.Numero_de_compte
ORDER BY  f2 
;";
// $sql=mysqli_real_escape_string ($link, $sqla);
//echo "<br>".$sql."<br>";
$query=mysqli_query($link, $sql) or die('Erreur SQL !<br />'.$sql.'<br />'.error_get_last());
while ($data = mysqli_fetch_array($query)) {
  echo "<option VALUE=".urlencode($data["f2"]);
  if($data["f2"]==$sel_ToCpte) {echo ' selected';}
  echo '>',''.$data["f1"].'&nbsp &nbsp &nbsp &nbsp'.$data["f2"],'</option>';
  }
echo '</select>';
}
/////////////////////////////////////////////////////////////////////////////////////////////////
// date boxes, buttons before output  (=page header)  combo recherche 
/////////////////////////////////////////////////////////////////////////////////////////////////
{
if (isset($SearchSepa)) {}
  else { $SearchSepa=",";
  }
echo '<table>';
echo '<tr>';
//echo '<td><b><input type="radio" name="rbFrTS" VALUE="t"';if($sel_rbFrTS=='t'){echo ' checked="checked" ';} echo '>DE tt COMPTE' ;
/* hard to get name AND num compte from 1 combo box  !
echo '<td><b><input type="radio" name="rbToCN" VALUE="c"';if($sel_rbToCN=='c'){echo ' checked " ';} echo '>par NUMERO de compte' ;
echo '<td><b><input type="radio" name="rbToCN" VALUE="n"';if($sel_rbToCN=='n'){echo ' checked " ';} echo '>par NOM compte' ;
*/

echo '<td BGCOLOR="99FF99"><input type="radio" name="rbFrTS" VALUE="t"';if($sel_rbFrTS=='t'){echo ' checked " ';} echo '>De TOUS mes comptes ' ;
echo '<td BGCOLOR="99FF99"><b><input type="radio" name="rbFrTS" VALUE="s"';if($sel_rbFrTS=='s'){echo ' checked " ';} echo '>Du compte selectionn&eacute' ;
echo '<td> ' ;

echo '<td BGCOLOR="6699FF"><b><input type="radio" name="rbToTS" VALUE="t"';if($sel_rbToTS=='t'){echo ' checked " ';} echo '>Vers TOUS les comptes' ;
echo '<td BGCOLOR="6699FF"><input type="radio" name="rbToTS" VALUE="s"';if($sel_rbToTS=='s'){echo ' checked " ';} echo '>VERS compte selectionn&eacute' ;
echo '<td> ' ;

echo '<td BGCOLOR="FF99FF"><input type="radio" name="rbDCT" VALUE="d"';if($sel_rbDCT=='d'){echo ' checked " ';} echo '>D&eacutebit' ;
echo '<td BGCOLOR="FF99FF"><input type="radio" name="rbDCT" VALUE="c"';if($sel_rbDCT=='c'){echo ' checked " ';} echo '>Cr&eacutedit';
echo '<td BGCOLOR="FF99FF"><b><input type="radio" name="rbDCT" VALUE="t"';if($sel_rbDCT=='t'){echo ' checked " ';} echo '>Tout'  ;
echo '<td> ' ;

echo '<td BGCOLOR="FFA07A"><input type="radio" name="rblrech" VALUE="t"';if($sel_rblrech=='t'){echo ' checked " ';} echo '>Recherche enregistr&eacutee' ;
echo '<td BGCOLOR="FFA07A"><b><input type="radio" name="rblrech" VALUE="s"';if($sel_rblrech=='s'){echo ' checked " ';} echo '>Rechercher ces mots:';
echo '<td BGCOLOR="FFA07A"><b>Rechercher ces mots (s&eacutepar&eacutes par '.$SearchSepa.' )</b> <input type="text" name="sSel" VALUE="'.urlencode($sel_sSel).'"></td>';
{
/////////////////////////////////////////////////////////////////////////////////////////////////
// Recherche liste 
// si non selectionnéé, $sel_lrech contient ' pas de liste'
// si selectionnéé, $sel_lrech contient le nom de la recherche (field 'recherche_nom')
/////////////////////////////////////////////////////////////////////////////////////////////////

echo '<br/><b>Recherche enregistr&eacutee :</b> <select name="lrech">';
$sql="
SELECT recherche_nom  f1 , note f2 , mots f3 FROM Recherche
WHERE NOT disabled 
;";
// $sql=mysqli_real_escape_string ($link, $sqla);
//echo "<br>".$sql."<br>";
if ($DODEBUG) { 
echo '<br />'.$sql ;
}
$query=mysqli_query($link, $sql) or die('Erreur SQL !<br />'.$sql.'<br />'.error_get_last());
while ($data = mysqli_fetch_array($query)) {
  echo "<option VALUE=".urlencode($data["f1"]);
  if($data["f1"]==$sel_lrech) {echo ' selected';}
  echo '>',''.$data["f1"].': &nbsp &nbsp'.$data["f2"].'&nbsp &nbsp &nbsp &nbsp'.$data["f3"],'</option>';
  }
echo '</select>';
}
{  //  action buttons
echo '<td BGCOLOR="f4e242"><input type="submit" method="POST" VALUE=">> Rechercher <<"></td>';

echo '<td align="center" BGCOLOR="a7b3f2">
<FORM target="_blank" ACTION="graph_record.php">
<INPUT TYPE="submit" VALUE="Graphique Montants">
</FORM>';

echo '<FORM target="_blank" ACTION="graph_month.php">
<INPUT TYPE="submit" VALUE="Graphique /Mois">
</FORM>';

echo '<FORM target="_blank" ACTION="graph_year.php">
<INPUT TYPE="submit" VALUE="Graphique /Ann&eacute;e&nbsp">
</FORM></td>';

echo '<td align="center" BGCOLOR="3855ff"><FORM target="_blank" ACTION="graph_solde.php">
<INPUT TYPE="submit" VALUE="Graph. solde">
</FORM>';

//  ' solde par mois' is not pertinent ?
//echo '<FORM target="_blank" ACTION="graph_solde_mois.php"><INPUT TYPE="submit" VALUE="&nbsp &nbsp Graph. solde&nbsp par mois"></FORM></td>';

echo '<td align="center" BGCOLOR="a51a95">
<FORM METHOD="LINK" target="_blank" ACTION="CountLines.php">
<INPUT TYPE="submit"  VALUE="Importation CSV | Nb de lignes">
</FORM>';

echo '</td>';

//nom_table  not used, use session var

//echo '&nbsp &nbsp &nbsp<a href="mysql2csv.php?nom_table=<"ma_table1">Exportation de ces donn&eacutees vers un fichier .csv</a>';
echo '<FORM METHOD="LINK" target="_blank" ACTION="mysql2csv.php?nom_table=<"ma_table1"><INPUT TYPE="submit" VALUE="&nbsp &nbsp Exportation de ces lignes vers un fichier .csv"></FORM>'; 

}

echo '</tr></table>';

mysqli_free_result ($query);
}
/////////////////////////////////////////////////////////////////////////////////////////////////
// SQL building out selection 
/////////////////////////////////////////////////////////////////////////////////////////////////
{
// if rblrech, replace  $sel_sSel  by content of the corresponding field mots
if  ($sel_rblrech=='t'){
  $sql= "
    SELECT mots f1 , recherche_nom f2 FROM Recherche
    WHERE recherche_nom  LIKE '$sel_lrech'
    ";
    if ($DODEBUG) { echo '<br />'.$sql ;
      }
  $query = mysqli_query($link, $sql) or die('Erreur SQL !<br />'.$sql.'<br />'.error_get_last());
  //$RecLines=mysqli_num_rows($query);
  $rowSum = mysqli_fetch_array($query);
  $sel_sSel= $rowSum['f1']  ;
  $sel_description= $rowSum['f2'].": "  ;
  }
else {
    $sel_description="";
  }
  
  /*
$sqlSelect= "
SELECT Extraits.Numero_de_compte, Nom_du_compte, Compte_partie_adverse, CompteNames.Compte_Name, Numero_de_mouvement, Date_comptable, Date_valeur, Montant, Libelles, Details_du_mouvement, Message, solde_computed  
";
*/
$sqlSelect= "
SELECT Extraits.Numero_de_compte, Extraits.Nom_du_compte, Extraits.Compte_partie_adverse, CompteNames.Compte_Name, Extraits.Numero_de_mouvement, Extraits.Date_comptable, Extraits.Date_valeur, Extraits.Montant, Extraits.Libelles, Extraits.Details_du_mouvement, Extraits.Message, Extraits.solde_computed  
";
$sqlFrom= "
FROM Extraits
left JOIN  CompteNames ON  Extraits.Compte_partie_adverse =CompteNames.Numero_de_compte
";
/*
if ($sel_rbToCN=='n') {
	$sqlOpt00=" (CompteNames.Compte_Name LIKE '$sel_ToCpte'";
	}
else{
	$sqlOpt00=" (CompteNames.Numero_de_compte LIKE '$sel_ToCpte'";
}
*/
if ($sel_ToCpte=='%'){
	$sqlOpt01=" OR CompteNames.Compte_Name IS NULL ";
	}
	else {
	$sqlOpt01="";
	}

	
/////////////////////////////////////////////////////////////////////////////////////////////////
// EASTER EGG if $sel_sSel is like  "balles", open baballes.html
/*
if  ($sel_sSel=='balles'){
//<a href="/ba.html" target="_blank">Text</a>
//echo '<FORM METHOD="LINK" target="_blank" ACTION="ba.html><INPUT TYPE="submit"></FORM>'; 
//include("location:./ba.html");
//header("Location: ba.html");

$url = "ba.html";
echo "<script type=\"text/javascript\">\
<!--\
open('$pageurl');history.back();\
//-->\
</script>\
";

}
*/
/////////////////////////////////////////////////////////////////////////////////////////////////
// if $sel_sSel= "lumin,lamp,electra", search terms between separator
{
$SearchTerms = explode($SearchSepa, $sel_sSel);
$sqlWhere="
WHERE
(
Extraits.Numero_de_compte LIKE '$sel_FrCpte%' 
AND (CompteNames.Compte_Name LIKE '$sel_ToCpte'  $sqlOpt01 )
AND Date_comptable BETWEEN '$sel_dbeg' AND '$sel_dend'
AND (";
$DoOnce=TRUE;
foreach ($SearchTerms as $i => $value) {
  if ($DoOnce) {
    $sqlWhere=$sqlWhere."  ( 
    Libelles LIKE '%$value%' OR 
    Details_du_mouvement LIKE '%$value%' OR 
    Message LIKE '%$value%' OR 
    Compte_partie_adverse LIKE '%$value%'
    )
    ";
    $sqlWhere=$sqlWhere." ".$s_rbDCT;
    $DoOnce=FALSE;
    }
    else {
    $sqlWhere=$sqlWhere."  OR
    ( 
    Libelles LIKE '%$value%' OR 
    Details_du_mouvement LIKE '%$value%' OR 
    Message LIKE '%$value%' OR 
    Compte_partie_adverse LIKE '%$value%'
    )
    ";
    $sqlWhere=$sqlWhere." ".$s_rbDCT;
   }
  }

$sqlWhere=$sqlWhere. ") )";
}

{
/////////////////////////////////////////////////////////////////////////////////////////////////
// if  CompteNames.Sort  field is defined , use it as SORT BY
$sql=" SELECT Sortby FROM SortBy WHERE Numero_de_compte LIKE  '$sel_FrCpte%' ";
$query = mysqli_query($link, $sql) or die('Erreur SQL !<br />'.$sql.'<br />'.error_get_last());
 if(mysqli_num_rows($query)>0){
  $data = mysqli_fetch_array($query);
  $s_OB = $data["Sortby"] ;
  }
  else {
    //$s_OB=" (EXTRACT(YEAR_MONTH FROM Date_comptable)+Numero_de_mouvement) DESC, EXTRACT(DAY FROM Date_comptable) DESC ";
    $s_OB=" CONCAT (EXTRACT(YEAR_MONTH FROM Date_comptable), Numero_de_mouvement)  DESC, EXTRACT(DAY FROM Date_comptable) DESC ";
 }
$sqlOrder=" ORDER BY " . $s_OB ;
}

{
/////////////////////////////////////////////////////////////////////////////////////////////////
// set $sql and session variable for other php pages
$sql=$sqlSelect.$sqlFrom.$sqlWhere.$sqlOrder;
$_SESSION['sqlSelect'] = $sqlSelect;
$_SESSION['sqlFrom'] = $sqlFrom;
$_SESSION['sqlWhere'] = $sqlWhere;
$_SESSION['sqlOrder'] = $sqlOrder;
$_SESSION['sSQL'] = $sql;
}
//msg('<br>'.$sql.'<br>');
//echo '<br>'.$sql.'<br><br>';
}
/////////////////////////////////////////////////////////////////////////////////////////////////
// info line: info search and mean/month, out of result table, compute sum, Version display
/////////////////////////////////////////////////////////////////////////////////////////////////
{
$sqlSelect="
SELECT sum(Montant) as SumMontant 
";	
$sqlSum=$sqlSelect.$sqlFrom.$sqlWhere;
//msg('<br>'.$sql.'<br>');
//echo '<br>'.$sqlSum.'<br><br>';	

//query with data
$query = mysqli_query($link, $sql) or die('Erreur SQL !<br />'.$sql.'<br />'.error_get_last());
$RecLines=mysqli_num_rows($query);
//query for sum
$querySum = mysqli_query($link, $sqlSum) or die('Erreur SQL !<br />'.$sqlSum.'<br />'.error_get_last());
$rowSum = mysqli_fetch_array($querySum);
/////////////////////////////////////////////////////////////////////////////////////////////////
{  // build info line
$Mt= $rowSum['SumMontant']  ;
//    yyyy/mm/dd
$nJours = (strtotime($sel_dend)-strtotime($sel_dbeg))/$SecInDay;
if ($DODEBUG) { 
echo '<br>';
echo 'a '.$sel_dbeg.'  b  '.strtotime($sel_dbeg).'<br>';
echo 'c '.$sel_dend.'  d  '.strtotime($sel_dend).'<br>';
}
$nMois=$nJours/30;
$MtMois=$Mt/$nMois ;

$InfoLine="";
if ($sel_FrCpte=="%"){$InfoLine=$InfoLine."De TOUS mes comptes";  }
  else  { $InfoLine="(Ver ".$InfoLine.$Ver.") Pour le compte ".$sel_FrCpte. " ";  }

//$InfoLine=$InfoLine.", du ".$sel_dbeg." au " .$sel_dend. ",";
$InfoLine=$InfoLine.", ".$DateRangeName." :".$sel_dbeg.".." .$sel_dend. ", ";

$InfoLine=$InfoLine.$s_rbDCT_txt;

if ($sel_ToCpte=="%"){
$InfoLine=$InfoLine." vers TOUS mes comptes";
}
else {
$InfoLine=$InfoLine." vers ".($sel_ToCpte);
}
//$InfoLine=$InfoLine.($sel_sSel=="")?(""):(", cherchant '".$sel_sSel."'") ;
if ($sel_sSel==""){
}
else{
//$InfoLine=$InfoLine.", cherchant >".$sel_sSel." (".$sel_description.")< " ;
$InfoLine=$InfoLine.", cherchant >".$sel_description.$sel_sSel."< " ;
}
$InfoLine=$InfoLine.", il y a ".$RecLines." lignes. ";
$InfoLine=$InfoLine."<br/>Le montant total est de ".number_format( $Mt, 2, ',' ,' ')." eur. ";
//$InfoLine=$InfoLine."Le montant total est de ". number_format($Mt,2) ." eur. ";
$InfoLine=$InfoLine." Intervalle ".number_format($nJours,0)." jours "." (~= ".number_format($nMois)." mois): ".number_format($MtMois ,2)." eur/mois";

/*
$InfoLine=$InfoLine." Intervalle ".number_format($nJours,0)." jours "." (~= ".number_format($nMois)." mois): ".number_format($MtMois ,2)." eur/mois, la moyenne est ".number_format($Mt/$RecLines,2)." eur/ligne.";
*/
if ($RecLines) {
$InfoLine=$InfoLine.", la moyenne est ".number_format($Mt/$RecLines,2)." eur/ligne.";
}
else
{
$InfoLine=$InfoLine.".";
}
echo "<b>".$InfoLine;
$_SESSION['InfoLine'] = $InfoLine;

echo "</b>";
mysqli_free_result ($querySum);
//list with data
echo '<table id="t01">';
echo "<tr>
<th>*Compte&nbsp&nbspSource&nbsp&nbsp&nbsp</th>
<th>Compte&nbsp&nbspCible&nbsp&nbsp&nbsp</th>
<th>Cible Nom</th>
<th>*Mvmt</th>
<th>*Dt&nbspcomptable</th>
<th>*Dt&nbspvaleur&nbsp&nbsp&nbsp</th>
<th>Montant</th>
<th>Lib,&nbspDetails,&nbspMessage</th>
<th>Solde</th>
</tr>";
while($row = mysqli_fetch_array($query)){   //Creates a loop through results
  echo "
  <tr>
  <td>".$row['Numero_de_compte'] . "</td>
  <td>".$row['Compte_partie_adverse'] . "</td>
  <td>".$row['Compte_Name'] . "</td>
  <td>".$row['Numero_de_mouvement'] . "</td>
  <td>".$row['Date_comptable'] . "</td>
  <td>".$row['Date_valeur'] . "</td>";
{//set 'montant' color positive negative
  $Mt=$row['Montant']+0;
  switch ($Mt) { //#FF0000    #550000  // different colors for amounts from..to  ?
  case -50>$Mt:
    echo "<td><font color=#FF0000>".$row['Montant']."</font></td>";
    break;
  case 0>$Mt:
    echo "<td><font color=#FF0000>".$row['Montant']."</font></td>";
    break;
  default: 
    echo "<td><font color=blue>".$row['Montant']."</font></td>";
    }
  }
  {//build and eventually highlight text
  $tt="<td>".'[Lib:]'.trim($row['Libelles']).'[Det:]'.trim($row['Details_du_mouvement']).'[Msg:]'.trim($row['Message'])."</td>";
if (strlen($sel_sSel) == 0) {
  }
  else {
  $ll=explode($SearchSepa, $sel_sSel);
  $tt=MarkPartOfText($tt, $ll);
  }
  echo $tt;  // send   'Lib, Details, Message'  str
  }
  //solde
  $tt="<td>".$row['solde_computed']."</td> ";
  echo $tt;
  
  /////////////////////////////////////////////////////////////////////////////////////////////////
  //  add radio button to edit record: call update.php and pass key and compte
  //  http://192.168.0.11/extraits/update.php?edit=2019-0572
  $tt=  "<td> <label> <FORM target='_blank' ACTION='update.php'>
      <input type='submit' name='edit' value='".$row['Numero_de_mouvement']."' checked='checked'> </label> </td>";
  echo $tt;


  // end of table's row
  echo "</tr>";
  }  // end of while($row = mysqli_fetch_array($query))

echo "</table>";
echo "Version ".$Ver;
echo "<br><br>SQL used to generate table is:<br><br><pre>".$sql."<br><br>";
mysqli_free_result ($query);
mysqli_close ($link);
}
}
?>
