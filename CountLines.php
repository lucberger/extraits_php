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

include_once 'ScanFolderCSV.php';
include_once 'maliste_combo.php';
include_once 'dblogin.php';

$LastUsedCpt="0" ;

//echo date("H:i:s"). ' Votre adresse IP est : '.$_SERVER['REMOTE_ADDR'] .'<br />';
//echo $sPName." ".$sPVer.":".date("H:i:s").":".$s."\n";
//echo date("H:i:s :").__DIR__.__FILE__.": ".$s."\n".'<br />';

$link=connexion_DB($host ,$user ,$pass, $db);
// echo date("H:i:s :")."Scanning folder ".$LocCSV.': ';
// $t=ScanFolderCSV($LocCSV, $link);
// echo $t ;

if (isset($LocCSV)) {
	echo ScanFolderCSVING($LocCSV, $link);
echo '...<br/>';
}
if (isset($LocCSVBelfius)) {
	echo ScanFolderCSVBelfius($LocCSVBelfius, $link);
echo '...<br/>';
}
if (isset($LocCSVBNP)) {
echo ScanFolderCSVBNP($LocCSVBNP, $link);
echo '...<br/>';
}
/////////////////////////////////////////////////
//  sql
/////////////////////////////////////////////////
$sql= "
SELECT Numero_de_compte as f1, Nom_du_compte as f2,  COUNT(*) as Lignes
FROM Extraits
GROUP BY Nom_du_compte, Numero_de_compte
;";

//   list with data
echo '<table id="t01">';

echo "<tr>
<th>Compte</th>
<th>Nom</th>
<th>Lignes</th>
<th>Du .. au</th>
</tr>";
$i_TotR=0;
$query = mysqli_query($link, $sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
while($row = mysqli_fetch_array($query)){   //Creates a loop to loop through results
  // echo "<tr><td>".$row['Numero_de_compte']."</td><td>".$row['Nom_du_compte']."</td><td>".$row['Lignes'];
  // str_pad($input, 10, "-=", STR_PAD_LEFT)
  $i_TotR=$i_TotR+$row['Lignes'];
  echo "<tr><td>".$row['f1']."</td><td>".$row['f2']."</td><td>".$row['Lignes']."</td>";
  $DcBegEnd= GetBegEnd($link, $row['f1'] );
  echo "<td> ".$DcBegEnd['Beg'];
  //echo " .. ". $DcBegEnd['End']."</td>";
  echo " .. ". $DcBegEnd['End'];
  //$date=date_create_from_format("j-M-Y","15-Mar-2013"); //2012-01-16
  //$da =$DcBegEnd['End']; //date_create_from_format("Y-m-d",$DcBegEnd['End']);
  //echo " >" .$da; //date_create(trim(
  //echo strftime ("%B %D %Y", $da);
  //strftime ( "%d %b %y",  d )
  
  
  echo "</td>";
  "</td>
  </tr>";
  }
echo "Il y a au total ".$i_TotR." lignes d'extraits.";
echo "</table>";

echo '<FORM METHOD="LINK" ACTION="index.php"><INPUT TYPE="submit" VALUE="&nbsp&nbsp&nbsp Retour&nbsp&nbsp&nbsp"></FORM>';
mysqli_free_result ($query);
mysqli_close ($link);
?>
