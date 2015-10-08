<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=windows-1252"/>
    <title>Extraits</title>
    <style type="text/css">
        @page { margin: 2cm }
        p { margin-bottom: 0.25cm; line-height: 120% }
        pre.cjk { font-family: "Nimbus Mono L", monospace }
        td p { margin-bottom: 0 }
table, th, td {
  border: 0px solid black;
  border-collapse: collapse;}

  
table#t00 {
    width:0%;
    }
table#t00 tr:nth-child(even) {
    background-color: #FFFF99;
}
table#t00 tr:nth-child(odd) {
   background-color:#fff;
}
table#t00 th    {
    background-color: #0099CC;
    color: white;
}

table#t01 {
    width:100%;
    }
table#t01, th, td {
  border: 1px solid black;
  border-collapse: collapse;
}
table#t01 tr:nth-child(even) {
    background-color: #FFFF99;
}
table#t01 tr:nth-child(odd) {
   background-color:#fff;
}
table#t01 th    {
    background-color: #0099CC;
    color: white;
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
/////////////////////////////////////////////////////////////////////////////////////////////////
// Version
/////////////////////////////////////////////////////////////////////////////////////////////////
$Ver="1.0";
/*
http://luc-4790k/extraits/Extraits_00.php?j_b=18&m_b=010&a_b=2010&j_e=16&m_e=09&a_e=2015&FrCpte=BE15001223939330&ToCpte=&rbFrTS=t&rbToTS=t&rbDCT=t&sSel=
*/
/////////////////////////////////////////////////////////////////////////////////////////////////
// include
/////////////////////////////////////////////////////////////////////////////////////////////////
{
include_once 'ScanFolderCSV.php';
include_once 'maliste_combo.php';
include_once 'dblogin.php';
}
{ //list dblogin.php constant
/*
echo $LocCSV ."<br/>";
echo $LocCSVBelfius;
echo $LocCSVBNP ."<br/>";
echo $host ."<br/>";
echo  $user  ."<br/>";
echo $pass  ."<br/>";
echo $db   ."<br/>";
//"BE15 0012 2393 9330"; //"310-0072179-97";BE15%2B0012%2B2393%2B9330%2B
echo $Def_FrCpte ."<br/>";
echo $Def_ToCpte ."<br/>";
*/
}
/////////////////////////////////////////////////////////////////////////////////////////////////
//  set connexion_DB, extract $_GET  params
/////////////////////////////////////////////////////////////////////////////////////////////////
{
$link=connexion_DB($host ,$user ,$pass, $db);
//var_dump ( $link) ;

if (empty($_GET)){$b_Sel=FALSE;}else{$b_Sel=TRUE;}  //if (isset($_GET)){$b_Sel=TRUE;}else{$b_Sel=FALSE;}
//if ($b_Sel){echo '. &nbsp Selections.<br/>';} else {echo '. &nbsp Pas de selection.<br/>';}

if ($b_Sel){
  $sel_FrCpte=urldecode($_GET['FrCpte']);  // from compte
  $sel_ToCpte=urldecode($_GET['ToCpte']);  // to compte
  $sel_sSel=urldecode($_GET['sSel']);      //search text in lib, det, et msg
  $sel_rbDCT=$_GET['rbDCT'];    //radio button Debit-Credit-Tout     d - c - t
  $sel_rbToTS   = $_GET['rbToTS'];    //radio button From Ttcomptes - Sel compte     t - s
  $sel_rbFrTS = $_GET['rbFrTS'];  //radio button De Ttcomptes - Sel compte     t - s
  //$sel_rbToCN = $_GET['rbToCN'];  //radio button par nom ou num cpte  C N
  }
  else {
  $sel_FrCpte=($Def_FrCpte);
  $sel_ToCpte=($Def_ToCpte);
  $sel_sSel='';
  $sel_rbDCT='t';
  $sel_rbToTS='t'; //vers compte : tous
  $sel_rbFrTS='s'; //de compte selectionné
  //$sel_rbToCN='n'; //par NOM de compte (tt les comptes associes a ce nom
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
//  show all passed $_GET   ( $_GET $_POST   $_SERVER )  various
/////////////////////////////////////////////////////////////////////////////////////////////////
{
echo "From:>".$sel_FrCpte."<<br>" ;
echo "To:>".$sel_ToCpte ."<<br>" ;
/*
//echo 'dump $_GET';
echo '<table id="t00">';
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
//echo 'end dump $_GET<br>';
*/

}
/////////////////////////////////////////////////////////////////////////////////////////////////
// first date avail
/////////////////////////////////////////////////////////////////////////////////////////////////
{
$DcBegEnd= GetBegEnd($link, $sel_FrCpte );
// echo  print_r ($DcBegEnd );
//var_dump ($DcBegEnd) ;
//var_dump( $sel_FrCpte) ;
$Dc= $DcBegEnd['Beg'];
// msg ( $Dc );
$id_moisb=substr($Dc,5,2);
$id_jourb=substr($Dc,8,8);
$Dc_Abeg=substr($Dc,0,4);
$Dc_BegRec=$Dc;
if ($b_Sel){
  $sel_dbeg=$_GET['a_b']."/".$_GET['m_b']."/".$_GET['j_b']; // used in sql select
  //$sel_dbeg=$Dc;
  $Dc_selb=$_GET['a_b']  ;                                  // used in 2x combo box years select
  $id_jourb=$_GET['j_b'];                                   // used in 'from' combo box select
  $id_moisb=$_GET['m_b'];                                   // used in 'from' combo box select
  }
  else {
  $Dc_selb=$Dc_Abeg;
  $sel_dbeg= $Dc ;
  }
// msg ('sel_beg set to '. $sel_dbeg);
}
/////////////////////////////////////////////////////////////////////////////////////////////////
// last date avail
/////////////////////////////////////////////////////////////////////////////////////////////////
{
$Dc= $DcBegEnd['End'];
// msg ('date comptable end'.$Dc);

$id_moise=substr($Dc,5,2);
$id_joure=substr($Dc,8,8);
$Dc_Aend=substr($Dc,0,4);
$Dc_EndRec=$Dc;
if ($b_Sel){
  $sel_dend=$_GET['a_e']."/".$_GET['m_e']."/".$_GET['j_e'];
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
// dates in 3 combo boxes  d m y
/////////////////////////////////////////////////////////////////////////////////////////////////
{
echo "<FORM>";
echo '<table><table border="0">
<tr>
<td><b>Du</b> &nbsp&nbsp
        <select name="j_b">',listbox_jour ($id_jourb),'</select>
        <select name="m_b">',listbox_mois ($id_moisb),'</select>
        <select name="a_b">',listbox_an($Dc_Abeg,$Dc_Aend,$Dc_selb),'</select>
<b>Au</b> &nbsp&nbsp
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
SELECT DISTINCT Extraits.Numero_de_compte as f1, CompteNames.Compte_Name as f2 ,CompteNames.Note as f3
FROM Extraits LEFT JOIN CompteNames ON  Extraits.Numero_de_compte =CompteNames.Numero_de_compte
ORDER BY CompteNames.Compte_Name 

SELECT Numero_de_compte as f1, Nom_du_compte as f2, '' as f3
FROM Extraits
GROUP BY Numero_de_compte

SELECT DISTINCT Extraits.Numero_de_compte as f1, Extraits.Nom_du_compte as f2, CompteNames.Compte_Name as f3 ,CompteNames.Note as f4
FROM Extraits LEFT JOIN CompteNames ON  Extraits.Numero_de_compte =CompteNames.Numero_de_compte
ORDER BY CompteNames.Compte_Name 

SELECT  distinct Extraits.Numero_de_compte  as f1 , CompteNames.Compte_Name as f2 ,CompteNames.Note as f3
FROM `CompteNames` 
LEFT JOIN Extraits ON  Extraits.Numero_de_compte =CompteNames.Numero_de_compte
order by  `Compte_Name`
*/
$sql="
SELECT DISTINCT Extraits.Numero_de_compte as f1, Extraits.Nom_du_compte as f2, CompteNames.Compte_Name as f3 ,CompteNames.Note as f4
FROM Extraits LEFT JOIN CompteNames ON  Extraits.Numero_de_compte =CompteNames.Numero_de_compte
ORDER BY CompteNames.Compte_Name 
";

$query = mysqli_query($link, $sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
while ($data = mysqli_fetch_array($query)) {
    echo "<option value=".urlencode($data["f1"]);
    if(trim($data["f1"])==$sel_FrCpte){echo ' selected';}
    echo '>',$data["f1"].'&nbsp &nbsp &nbsp &nbsp'.$data["f2"]." ".$data["f3"]." ".$data["f4"]."",'</option>';
}
echo '</select>';
}
/////////////////////////////////////////////////////////////////////////////////////////////////
// TO COMPTE  combo box list avail comptes partie adverse
/////////////////////////////////////////////////////////////////////////////////////////////////
{
/*
SELECT DISTINCT Extraits.Compte_partie_adverse as f1,Compte_Name as f2   FROM Extraits left JOIN CompteNames ON  Extraits.Compte_partie_adverse=CompteNames.Numero_de_compte 
WHERE Extraits.Numero_de_compte LIKE '$sel_FrCpte'
ORDER BY CompteNames.Compte_Name
===
SELECT `Numero_de_compte` as f1 , `Compte_Name` as f2 
FROM `comptenames` 
order by  `Compte_Name`
===
*/
echo '<br/><b>Si \'Vers sel. compte\' est s&eacutelectionn&eacute, vers compte</b> <select name="ToCpte">';
$sql="
SELECT `Numero_de_compte` as f1 , `Compte_Name` as f2 
FROM `CompteNames` 
order by  `Compte_Name`
;";
// $sql=mysqli_real_escape_string ($link, $sqla);
//echo "<br>".$sql."<br>";
$query=mysqli_query($link, $sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
while ($data = mysqli_fetch_array($query)) {
  echo "<option value=".urlencode($data["f2"]);
  if($data["f2"]==$sel_ToCpte) {echo ' selected';}
  echo '>',''.$data["f2"].'&nbsp &nbsp &nbsp &nbsp'.$data["f1"],'</option>';
  }
echo '</select>';
}
/////////////////////////////////////////////////////////////////////////////////////////////////
// date boxes and buttons before output  (page header)
/////////////////////////////////////////////////////////////////////////////////////////////////
{
echo '<table>';
echo '<tr>';

//echo '<td><b><input type="radio" name="rbFrTS" value="t"';if($sel_rbFrTS=='t'){echo ' checked="checked" ';} echo '>DE tt COMPTE' ;
/* hard to get name AND num compte from 1 combo box  !
echo '<td><b><input type="radio" name="rbToCN" value="c"';if($sel_rbToCN=='c'){echo ' checked " ';} echo '>par NUMERO de compte' ;
echo '<td><b><input type="radio" name="rbToCN" value="n"';if($sel_rbToCN=='n'){echo ' checked " ';} echo '>par NOM compte' ;
*/
echo '<td><b><input type="radio" name="rbFrTS" value="t"';if($sel_rbFrTS=='t'){echo ' checked " ';} echo '>DE tt COMPTES' ;
echo '<td><b><input type="radio" name="rbFrTS" value="s"';if($sel_rbFrTS=='s'){echo ' checked " ';} echo '>DE sel. COMPTE' ;
echo '<td> ' ;

echo '<td><b><input type="radio" name="rbToTS" value="t"';if($sel_rbToTS=='t'){echo ' checked " ';} echo '>Vers tt comptes' ;
echo '<td><b><input type="radio" name="rbToTS" value="s"';if($sel_rbToTS=='s'){echo ' checked " ';} echo '>Vers sel. compte' ;
echo '<td> ' ;

echo '<td><b><input type="radio" name="rbDCT" value="d"';if($sel_rbDCT=='d'){echo ' checked " ';} echo '>D&eacutebit' ;
echo '<td><b><input type="radio" name="rbDCT" value="c"';if($sel_rbDCT=='c'){echo ' checked " ';} echo '>Cr&eacutedit';
echo '<td><b><input type="radio" name="rbDCT" value="t"';if($sel_rbDCT=='t'){echo ' checked " ';} echo '>Tout'  ;
echo '<td> ' ;

echo '<td><b>Rechercher</b> <input type="text" name="sSel" value="'.urlencode($sel_sSel).'"></td>';
echo '<td><input type="submit" method="POST" value="&nbsp&nbsp&nbsp Mise a jour&nbsp&nbsp&nbsp&nbsp"></td>';
echo '<td> ' ;

// echo '<td><FORM ACTION="Extraits_00.php"><INPUT TYPE="submit" VALUE="&nbsp&nbsp&nbsp R&eacute;initialiser&nbsp&nbsp&nbsp"></FORM></td>';
echo '<td><FORM ACTION="Extraits_00.php"><INPUT TYPE="submit" VALUE="&nbsp&nbsp&nbsp R&eacute;initialiser&nbsp&nbsp&nbsp"></FORM></td>';
echo '<td><FORM ACTION="CountLines.php"><INPUT TYPE="submit" VALUE="&nbsp &nbsp Nb de lignes / Import &nbsp"></FORM></td>';
echo '</tr></table>';

mysqli_free_result ($query);
}
/////////////////////////////////////////////////////////////////////////////////////////////////
// out selection sql
/////////////////////////////////////////////////////////////////////////////////////////////////
{
$sqlSelect= "
SELECT Extraits.Numero_de_compte, Nom_du_compte, Compte_partie_adverse, CompteNames.Compte_Name, Numero_de_mouvement, Date_comptable, Date_valeur, Montant, Libelles, Details_du_mouvement, Message
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
$sqlWhere="
WHERE
(
Extraits.Numero_de_compte LIKE '$sel_FrCpte%' 
AND (CompteNames.Compte_Name LIKE '$sel_ToCpte'  $sqlOpt01 )
AND Date_comptable BETWEEN '$sel_dbeg' AND '$sel_dend'
AND ( 
Libelles LIKE '%$sel_sSel%' OR 
Details_du_mouvement LIKE '%$sel_sSel%' OR 
Message LIKE '%$sel_sSel%' OR 
Compte_partie_adverse LIKE '%$sel_sSel%'
)
$s_rbDCT
)
";
$sqlOrder="
ORDER BY EXTRACT(YEAR_MONTH FROM Date_comptable) DESC, EXTRACT(DAY FROM Date_comptable) DESC, Numero_de_mouvement DESC 
";
$sql=$sqlSelect.$sqlFrom.$sqlWhere.$sqlOrder;
//msg('<br>'.$sql.'<br>');
//echo '<br>'.$sql.'<br><br>';
}
/////////////////////////////////////////////////////////////////////////////////////////////////
// Description and out of result table, compute sum, Version display
/////////////////////////////////////////////////////////////////////////////////////////////////
{
$sqlSelect="
SELECT sum(Montant) as SumMontant 
";	
$sqlSum=$sqlSelect.$sqlFrom.$sqlWhere;
//msg('<br>'.$sql.'<br>');
//echo '<br>'.$sqlSum.'<br><br>';	
}
{
$query = mysqli_query($link, $sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
$num=mysqli_num_rows($query);

$querySum = mysqli_query($link, $sqlSum) or die('Erreur SQL !<br />'.$sqlSum.'<br />'.mysql_error());
//$numSum=mysqli_num_rows($querySum);
$rowSum = mysqli_fetch_array($querySum);
$Mt=number_format(($rowSum['SumMontant']),2,',', ' ');

//echo $sql; echo "<br><br>";
// $s_rbDCT="";$s_rbDCT_txt=''
echo "<b>Pour le compte $sel_FrCpte ($Dc_BegRec..$Dc_EndRec),
$s_rbDCT_txt
vers $sel_ToCpte, du $sel_dbeg au $sel_dend,
cherchant '$sel_sSel',
il y a $num lignes.
Le montant total est de   $Mt eur.
</b>";

mysqli_free_result ($querySum);
//list with data
echo '<table id="t01">';
echo "<tr>
<th>Compte&nbsp&nbspSource&nbsp&nbsp&nbsp</th>
<th>Compte&nbsp&nbspCible&nbsp&nbsp&nbsp</th>
<th>Cible Nom</th>
<th>Mvmt</th>
<th>D&nbspcomptable</th>
<th>Date&nbspvaleur</th>
<th>Montant</th>
<th>Lib,&nbspDetails,&nbspMessage</th>
</tr>";
while($row = mysqli_fetch_array($query)){   //Creates a loop to loop through results
  echo "
  <tr>
  <td>".$row['Numero_de_compte'] . "</td>
  <td>".$row['Compte_partie_adverse'] . "</td>
  <td>".$row['Compte_Name'] . "</td>
  <td>".$row['Numero_de_mouvement'] . "</td>
  <td>".$row['Date_comptable'] . "</td>
  <td>".$row['Date_valeur'] . "</td>
  <td>".$row['Montant'] . "</td>
  <td>".'[Lib:]'.trim($row['Libelles']).'[Det:]'.trim($row['Details_du_mouvement']).'[Msg:]'.trim($row['Message'])."</td>
  </tr>";
  }
echo "</table>";
echo "Version ".$Ver;
mysqli_free_result ($query);
mysqli_close ($link);
}
?>
