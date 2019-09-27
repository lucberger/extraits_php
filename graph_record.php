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
        data.addColumn('number', 'Montant');
        data.addRows([
<?php  {
//https://developers.google.com/chart
$Title="Montant PAR EXTRAIT: ";
session_start();
include_once 'dblogin.php';
include_once 'ScanFolderCSV.php';
//echo $host ,$user ,$pass, $db;

$sql=$_SESSION['sqlSelect'].$_SESSION['sqlFrom'].$_SESSION['sqlWhere'];
$sql=$sql." ORDER BY EXTRACT(YEAR_MONTH FROM Date_comptable), EXTRACT(DAY FROM Date_comptable), Numero_de_mouvement";
//$sql=$sql." GROUP BY year(Date_comptable), month(Date_comptable)";
//echo $sql;
/*
$sqlOrder="
ORDER BY EXTRACT(YEAR_MONTH FROM Date_comptable) DESC, EXTRACT(DAY FROM Date_comptable) DESC, Numero_de_mouvement DESC 
";
$sql=$sqlSelect.$sqlFrom.$sqlWhere.$sqlOrder;
*/
$link=connexion_DB($host ,$user ,$pass, $db);
$query = mysqli_query($link, $sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
$bFirst=true;

while($row = mysqli_fetch_array($query)){   
if ($bFirst) {
  $sData="['" . $row['Date_comptable'] . "',". ($row['Montant'])."]";
  $bFirst=false;
  }
  else {
    $sData=",['" . $row['Date_comptable'] . "',". $row['Montant']."]";
    }
  echo $sData ;
  }
 
//$sData= "['2018-04-09',5982.49] ,['2018-04-17',6635.52] ,['2018-04-24',5707.81]"  ;


mysqli_free_result ($query);
mysqli_close ($link);
}
?>
])
        // Set chart options
       var options = {'title':
		      <?php { echo"'".$Title.$_SESSION['InfoLine']."'"; }  ?>    ,
		      //curveType: 'function',
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
echo "<br><br>SQL used to generate graph is :><br>

<pre>".$sql."<br><br>

<pre>".$Title.$_SESSION['InfoLine']."<br><br>
";
?>
</html>
