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
$Title="Montant PAR MOIS: ";
session_start();
include_once 'dblogin.php';
include_once 'ScanFolderCSV.php';

$sql="SELECT EXTRACT(YEAR_MONTH FROM Date_comptable) dc, sum(Montant) mt ";
$sql=$sql.$_SESSION['sqlFrom'].$_SESSION['sqlWhere'];

$sql=$sql." GROUP BY EXTRACT(YEAR_MONTH FROM Date_comptable) ";
//echo $sql;

$link=connexion_DB($host ,$user ,$pass, $db);
$query = mysqli_query($link, $sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
$bFirst=true;
while($row = mysqli_fetch_array($query)){   
if ($bFirst) {
  //$sData="['" . $row['Date_comptable'] . "',". ($row['Montant'])."]";
  $sData="['" . $row['dc'] . "',". ($row['mt'])."]";
  $bFirst=false;
  }
  else {
    //$sData=",['" . $row['Date_comptable'] . "',". $row['Montant']."]";
    $sData=",['" . $row['dc'] . "',". $row['mt']."]";
    }
  echo $sData ;
  }

mysqli_free_result ($query);
mysqli_close ($link);
}
?>
])
        // Set chart options
       var options = {'title':
		      <?php { echo"'".$Title.$_SESSION['InfoLine']."'";  }  ?>
		     ,
		      //curveType: 'function',
                       'width':1400,
                       'height':500
                       
                       };

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
";
?>
</html>
