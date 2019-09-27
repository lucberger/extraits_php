<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=ISO-8859-1"/>
	<title>Nombre de lignes</title>
	<style type="text/css">
		@page { margin: 2cm }
		p { margin-bottom: 0.25cm; line-height: 120% }
		pre.cjk { font-family: "Nimbus Mono L", monospace }
		td p { margin-bottom: 0chttp://www.huffpostmaghreb.com/farhat-othman/lhomosexualite-nest-pas-i_b_5172924.htmlm }

table, th, td {
  border: 0px solid black;
  border-collapse: collapse;}

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
table#t01 th	{
    background-color: #0099CC;
    color: white;
}

</style>
</head>
<body lang="fr-FR" dir="ltr">
<pre class="western">
</pre>
<pre class="western"></pre><p style="margin-bottom: 0cm; line-height: 100%">
</p>
</body>
</html>
<?php
/////////////////////////////////////////////////////////////////////////////////////////////////
// Include
/////////////////////////////////////////////////////////////////////////////////////////////////
{
include_once 'ScanFolderCSV.php';
include_once 'maliste_combo.php';
include_once 'dblogin.php';
}
/////////////////////////////////////////////////////////////////////////////////////////////////
// Version
/////////////////////////////////////////////////////////////////////////////////////////////////
$Ver="24082018";

// set to non 0 to force backup
$ReadTotRows=0;
/*
echo date("H:i:s"). ' Votre adresse IP est : '.$_SERVER['REMOTE_ADDR'] .'<br />';
echo $sPName." ".$sPVer.":".date("H:i:s").":".$s."\n";
echo date("H:i:s :").__DIR__.__FILE__.": ".$s."\n".'<br />';
*/
$link=connexion_DB($host ,$user ,$pass, $db);
$now = strtotime("now"); // 'Y-m-d');


/////////////////////////////////////////////////////////////////////////////////////////////////
// Titre et check if any new csv files to import
/////////////////////////////////////////////////////////////////////////////////////////////////
{
echo "<br>- Importation des .CSV / Nombre de lignes ----------------------- <br>" ;
//echo "<b>Importation des .CSV / Nombre de lignes</b><br/><br/>";
if (isset($LocCSVING)) { $ReadTotRows=$ReadTotRows + ScanFolderCSVING($LocCSVING, $link); echo '...<br/>';}
if (isset($LocCSVBelfius)) { $ReadTotRows=$ReadTotRows + ScanFolderCSVBelfius($LocCSVBelfius, $link); echo '...<br/>'; }
if (isset($LocCSVBNP)) {$ReadTotRows=$ReadTotRows + ScanFolderCSVBNP($LocCSVBNP, $link);echo '...<br/>';}
if (isset($LocCSVRabo)) {$ReadTotRows=$ReadTotRows + ScanFolderCSVRabo($LocCSVRabo, $link);echo '...<br/>';}
if (isset($LocCSVfortuneo_fr)) {$ReadTotRows=$ReadTotRows + ScanFolderCSVfortuneoFR($LocCSVfortuneo_fr, $link);echo '...<br/>';}
if (isset($LocCSVArgenta)) {$ReadTotRows=$ReadTotRows + ScanFolderCSVArgenta($LocCSVArgenta, $link);echo '...<br/>';}
if (isset($LocCSVKEYTRADE)) {$ReadTotRows=$ReadTotRows + ScanFolderCSVKEYTRADE($LocCSVKEYTRADE, $link);echo '...<br/>';}
//ScanFolderCSVDB
if (isset($LocCSVDB)) {$ReadTotRows=$ReadTotRows + ScanFolderCSVDB($LocCSVDB, $link);echo '...<br/>';}
echo "- End importation des .CSV / Nombre de lignes ----------------------- <br>" ;
}
/////////////////////////////////////////////////////////////////////////////////////////////////
// call CheckRecord()
/////////////////////////////////////////////////////////////////////////////////////////////////
$void=CheckRecord($link);
//echo "Retval ".$void."<br>";
echo "<br>";

/////////////////////////////////////////////////////////////////////////////////////////////////
// Call ComputeSolde 
/////////////////////////////////////////////////////////////////////////////////////////////////
{  /*
  // $compte="BE15 0012 2393 9330";//$Def_FrCpte
  //echo "Default is :".$Def_FrCpte."&#13;&#10";
  ComputeSolde($link, $Def_FrCpte);  //compute newers
  //ComputeSolde($link, $compte,TRUE);
  ComputeSolde($link, "BE24651158161738");  //compute newers
  ComputeSolde($link, "363-0273521-21"); 
  ComputeSolde($link, "377-0125914-45"); 
  */
{ echo "Compute solde for each compte in 'Du compte'<br>";
$sql="
SELECT DISTINCT  CompteNames.Numero_de_compte f1, CompteNames.Compte_Name  f2, '' f3 , CompteNames.Note f4
FROM  CompteNames
WHERE CompteNames.Note NOT LIKE '' 
";
$query = mysqli_query($link, $sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysqli_error($link));
while ($data = mysqli_fetch_array($query)) { //$Def_FrCpte
	$ZeCompte=trim($data["f1"]);
	ComputeSolde($link, $ZeCompte); 
	}
}  // end  compute solde for each compte in 'Du compte'
}
/////////////////////////////////////////////////////////////////////////////////////////////////
// SELECT and table
/////////////////////////////////////////////////////////////////////////////////////////////////
{
  {// get last record for $Def_FrCpte
  $sql="
  SELECT Extraits.Numero_de_compte, Numero_de_mouvement, Montant, solde, solde_computed 
  FROM Extraits
  WHERE  Extraits.Numero_de_compte LIKE '$Def_FrCpte%'  
  ORDER BY Numero_de_mouvement DESC 
  LIMIT 1
  ";
    $query=mysqli_query($link, $sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysqli_error($link));
  while ($data = mysqli_fetch_array($query)) {
    $curMvmt=$data["Numero_de_mouvement"];
    $curSolde=$data["solde_computed"];
    }
 }
 /*
$sql= "
SELECT DISTINCT 
Extraits.Numero_de_compte as f1, 
Extraits.Nom_du_compte as f2, 
concat (LEFT(CompteNames.Compte_Name,30), '..')   as f3 ,
CompteNames.Note as f4, 
COUNT(*) as Lignes 
FROM Extraits inner JOIN CompteNames ON Extraits.Numero_de_compte =CompteNames.Numero_de_compte
GROUP BY CompteNames.Note , Extraits.Numero_de_compte, Nom_du_compte 
";
*/
$sql= "
SELECT DISTINCT 
Extraits.Numero_de_compte as f1, 

concat (LEFT(CompteNames.Compte_Name,30), '..')   as f3 ,
CompteNames.Note as f4, 
COUNT(*) as Lignes 
FROM Extraits inner JOIN CompteNames ON Extraits.Numero_de_compte =CompteNames.Numero_de_compte
GROUP BY f1
";
//   list with data
// compute sum for each compte  ?<th>Solde</th>
echo '<table id="t01">';
echo "<tr>
<th>Compte</th>
<th>Nom</th>
<th>Lignes</th>
<th>Du .. au</th> 
<th></th> 
<th>jours<br>depuis</th>
</tr>";

$i_TotR=0; //add lines

$SecInDay=24*3600;  // 86400
$SecInYear=365*24*3600; // 31536000

$query = mysqli_query($link, $sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysqli_error($link));
while($row = mysqli_fetch_array($query)){   //Creates a loop to loop through results
  // str_pad($input, 10, "-=", STR_PAD_LEFT)
  $i_TotR=$i_TotR+$row['Lignes'];
  {//check if def compte -> more infos & bold
  if (trim($row['f1'])==trim($Def_FrCpte)) { 
  $bBold=true;
  }
  else {
  $bBold=false;
  }
  }
  { 
  if ($bBold) {//show def compte in bold
    $cpte="<b>".$row['f1']." - ".$row['f3']." - ".$row['f4'].". Last mvmt $curMvmt, solde $curSolde";
    } 
    else {
    $cpte=      $row['f1']." - ".$row['f3']." - ".$row['f4'];
    }
    }
 { 
   if ($bBold) {
  echo "<tr><td style=font-weight:bold>".$row['f1']."</td><td>".$cpte."</td><td style=font-weight:bold align='right'>".$row['Lignes']."</td>";
  //echo "<td> </td>" ;
  }
  else {
  echo "<tr><td>"                       .$row['f1']."</td><td>".$cpte."</td><td align='right'>".$row['Lignes']."</td>";
  //echo "<td> </td>" ;
  }
} 
{ //Du .. au 
   if ($bBold) {
  $DcBegEnd= GetBegEnd($link, $row['f1'] );
  // 2002-08-02 .. 2010-01-01   
  echo "<td style=font-weight:bold> ".$DcBegEnd['Beg'];   //2012-04-23
  echo " .. ". $DcBegEnd['End'];
  echo "</td><td style=font-weight:bold>".
  // 30 Nov -0001 .. 13 Oct 2010 ///////
  date_format(date_create_from_format("!Y-m-d",$DcBegEnd['Beg']),' d M Y')   ." .. ";
  $dt_E=date_create_from_format("!Y-m-d",$DcBegEnd['End']); // input is 2012-04-23, out is formatted DateTime object
  echo date_format($dt_E,'d M Y'); // 23 Apr 2012
  echo "</td>";
  //nbr of days since last extract/// 
  echo "<td align='right' style=font-weight:bold >";
  $du = ($now - strtotime($DcBegEnd['End'])) / $SecInDay;
  echo number_format($du,0);
  echo "</td>";
}
else {
  $DcBegEnd= GetBegEnd($link, $row['f1'] );
  // 2002-08-02 .. 2010-01-01   
  echo "<td> ".$DcBegEnd['Beg'];   //2012-04-23
  echo " .. ". $DcBegEnd['End'];
  echo "</td><td>".
  // 30 Nov -0001 .. 13 Oct 2010 ///////
  date_format(date_create_from_format("!Y-m-d",$DcBegEnd['Beg']),' d M Y')   ." .. ";
  $dt_E=date_create_from_format("!Y-m-d",$DcBegEnd['End']); // input is 2012-04-23, out is formatted DateTime object
  echo date_format($dt_E,'d M Y'); // 23 Apr 2012
  echo "</td>";
  //nbr of days since last extract/// 
  echo "<td align='right'>";
  $du = ($now - strtotime($DcBegEnd['End'])) / $SecInDay;
  echo number_format($du,0);
  echo "</td>";
}
  "</td>
  </tr>";
}
  }
echo "<br/>".date("d M Y H:i ", $now);
echo "Il y a au total ".$i_TotR." lignes d'extraits.";

echo "</table>";

}
/////////////////////////////////////////////////////////////////////////////////////////////////
// button
/////////////////////////////////////////////////////////////////////////////////////////////////
{
echo '<br/><br/><FORM METHOD="LINK" ACTION="index.php"><INPUT TYPE="submit" VALUE="&nbsp&nbsp&nbsp Retour&nbsp&nbsp&nbsp"></FORM>';
}
/////////////////////////////////////////////////////////////////////////////////////////////////
// backup if new records / show sql
/////////////////////////////////////////////////////////////////////////////////////////////////
{
	echo "- Backup & info        ----------------------- <br>" ;
	echo "Version ".$Ver;
	if ($ReadTotRows>0) {
	echo"<br/>whoami: "; echo `whoami`;  echo"<br/>";
	echo "<br/>Backup of tables is done to file:<br/>".MakeBackup($MySQLDumpProgLoc,$BackupDir,$host,$user,$pass,$db); echo"<br/>";
  }
	echo "<br>SQL used to generate table is:<br><pre>".$sql."<br>";
	//echo "=== End call postproc<br/>";
	echo "- End backup & info        ----------------------- <br>" ;
  }

/////////////////////////////////////////////////////////////////////////////////////////////////
// clean
/////////////////////////////////////////////////////////////////////////////////////////////////
  mysqli_free_result ($query);
  mysqli_close ($link);
  ?>
