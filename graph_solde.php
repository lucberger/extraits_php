<html>
  <head>
    <!--Load the AJAX API-->
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">

      // Load the Visualization API and the piechart package.
      google.load('visualization', '1.0', {'packages':['corechart']});

      // Set a callback to run when the Google Visualization API is loaded.
      google.setOnLoadCallback(drawChart);

      // Callback that creates and populates a data table,
      // instantiates the pie chart, passes in the data and
      // draws it.
      function drawChart() {
        // Create the data table.
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Date');
				data.addColumn('number', 'Solde');
        data.addRows([
<?php  { 
//https://developers.google.com/chart
session_start();
$Title="SOLDE - ";
include_once 'dblogin.php';
include_once 'ScanFolderCSV.php';

$sql='SELECT (Date_comptable) dc, (solde_computed) mt , Numero_de_mouvement ';
$sql=$sql.$_SESSION['sqlFrom'].$_SESSION['sqlWhere'];

$sql=$sql." GROUP BY Numero_de_mouvement,  (Date_comptable) ,  mt ";
//echo $sql;  // if set, this echo break the build of the data.addRows([   !
// see code source of page to check status


/////////////////  comment start/end here for only display of source query
$link=connexion_DB($host ,$user ,$pass, $db);
$query = mysqli_query($link, $sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
$bFirst=true;


while($row = mysqli_fetch_array($query)){
if ($bFirst) {
  $sData="['" . $row['dc'] . "',".($row['mt'])."]";
  $bFirst=false;
  }
  else {
    $sData=",['" . $row['dc'] . "',". $row['mt']."]";
    }
  echo $sData ;
 }
 

mysqli_free_result ($query);
mysqli_close ($link);
/////////////////  comment start/end here for only display of source query
}
?>
])
        // Set chart options
       var options = {'title':
		      <?php { echo"'".$Title.$_SESSION['InfoLine']."'"; } ?> ,
		      'width':1400,
          'height':500 };
        // Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.ScatterChart(document.getElementById('chart_div'));
        //var chart = new google.visualization.ComboChart(document.getElementById('chart_div'));
        //var chart = new google.visualization.Histogram(document.getElementById('chart_div'));
        chart.draw(data, options);
      }
    </script>
  </head>
  <body>
    <!--Div that will hold the pie chart-->
    <div id="chart_div"></div>
  </body>

<br/><br/><FORM METHOD="LINK" ACTION="index.php"><INPUT TYPE="submit" VALUE="&nbsp&nbsp&nbsp Retour&nbsp&nbsp&nbsp"></FORM>

<?php
echo "<br><br>SQL used to generate graph is :<br>

<pre>".$sql."<br><br>
<pre>".$Title.$_SESSION['InfoLine']."<br><br>
";
?>
</html>
