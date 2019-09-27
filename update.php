<?php
/////////////////////////////////////////////////////////////////////////////////////////////////
// updated from
// https://t4tutorials.com/how-to-update-record-in-php-and-mysql-database  
/////////////////////////////////////////////////////////////////////////////////////////////////
$Ver="20190904";
?>
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
<?php

{ session_start();
include_once 'ScanFolderCSV.php';
//include_once 'maliste_combo.php';
include_once 'dblogin.php';
}

$DODEBUG=FALSE;
//$DODEBUG=TRUE;
if ($DODEBUG) {  
  //echo "From:>".$sel_FrCpte."<<br>" ;
  //echo "To  :>".$sel_ToCpte ."<<br>" ;
  echo  "Version ".$Ver."<br>" ;
  echo '==> dump $_GET';
  echo '<table id="t01">';
  foreach ($_GET as $key => $value) {
	  echo "<tr>";
	  echo "<td>";
	  echo $key;
	  echo "=</td>";
	  echo "<td>";
	  echo $value;
	  echo "</td>";
	  echo "</tr>";
      }
  echo '</table>';
  echo '==> end dump $_GET<br>';
  }

//mysql_select_db($db,mysqli_connect($host,$user,''));
$link=connexion_DB($host ,$user ,$pass, $db);
$id=$_GET['edit'];
?>
<html><body>
<form method="post">
<table>
<?php
// $books_query=mysql_query("select * from Extraits where Numero_de_mouvement='$id'");
// $books_rows=mysql_fetch_array($books_query);

$sql="SELECT * FROM Extraits WHERE Numero_de_mouvement LIKE '$id'";
echo $sql; echo "<br>";

$query=mysqli_query($link, $sql) or die('Erreur SQL !<br />'.$sql.'<br />'.error_get_last());
$row = mysqli_fetch_array($query);

// echo $sql ;echo "<br>";
// echo $row['Message']; echo "<br>";

echo  "Version ".$Ver."<br><br>" ;
echo "Record ref: ".$id ; echo "<br>"; echo "<br>";

echo "
<h1>Edit 'Message' for </h1>
<tr><td>De  </td><td>".$row['Numero_de_compte']      . "</tr>
<tr><td>Vers</td><td>".$row['Compte_partie_adverse'] . "</tr>
<tr><td>Mvmt</td><td>".$row['Numero_de_mouvement']   . "</tr>
<tr><td>Compt</td><td>".$row['Date_comptable']      . "</tr>
<tr><td>Value</td><td>".$row['Date_valeur']         . "</tr>
<tr><td>Eur</td><td>".$row['Montant']                 . "</tr>
<tr><td>Lib</td><td>".trim($row['Libelles'])         . "</tr>
<tr><td>Det</td><td>".trim($row['Details_du_mouvement'])."</tr>
" ;
?>

<tr><td>Msg:</td><td><input type="text" name="TextBoxForMsg" value="<?php echo $row['Message'];  ?>"></td></tr>
<tr><td></td><td><input type="submit" name="submit" value="save"></td></tr>
</table></form></body></html>

<?php 
if (isset($_POST['submit'])){
$TitleVariable=$_POST['TextBoxForMsg'];
$sql="UPDATE Extraits SET Message='$TitleVariable' WHERE Numero_de_mouvement='$id'"  ;
//echo $sql ;
mysqli_query($link, $sql);
header('location:index.php');
}
?>
