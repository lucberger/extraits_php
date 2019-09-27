<?php
/////////////////////////////////////////////////////////////////////////////////
// CSV & files location, programs locations, ...
/////////////////////////////////////////////////////////////////////////////////
{
$LocCSVING ='/var/www/html/csv/extraits_ING';
$LocCSVBNP='/var/www/html/csv/extraits_BNP';
$LocCSVRabo='/var/www/html/csv/extraits_RaboBank';
$LocCSVfortuneo_fr='/var/www/html/csv/fortuneo_FR';
$LocCSVArgenta='/var/www/html/csv/extraits_argenta';
$LocCSVBelfius='/var/www/html/csv/extraits_belfius';
$LocCSVKEYTRADE='/var/www/html/csv/extraits_KEYTRADE';
//$LocCSVDB='/var/www/html/csv/extraits_DB';  must define key //  even date + amount not OK  :(  
/////////////////////////////////////////////////////////////////////////////////
// backup dir MUST BE FINISHED WITH / (linux)  or \ (Windows) 
/////////////////////////////////////////////////////////////////////////////////
//$BackupDir="C:\Users\jacqueline\Documents\";

// https://unix.stackexchange.com/questions/117242/giving-www-data-permision-to-dropbox-subfolder
//$BackupDir='/home/luc/Dropbox/backupExtraits';
$BackupDir='/var/www/html/backupExtraits';

}
/////////////////////////////////////////////////////////////////////////////////
//MySQLDump location 
/////////////////////////////////////////////////////////////////////////////////
{
//$MySQLDumpProgLoc='c:\wamp\bin\mysql\mysql5.6.17\bin\mysqldump.exe'; //windows
$MySQLDumpProgLoc='/usr/bin/mysqldump';
}
/////////////////////////////////////////////////////////////////////////////////
//default separator for 'recherche' word list. if not set, will be set to ',' in index.php
/////////////////////////////////////////////////////////////////////////////////
$SearchSepa=",";
/////////////////////////////////////////////////////////////////////////////////
//DB access parameters
/////////////////////////////////////////////////////////////////////////////////
{
$host = "localhost";
$user = "luc";
$pass = "bobo";
$db   = "Extraits";
}
//defaults displayed accounts
{
//$Def_FrCpte= "BE15 0012 2393 9330"; //"310-0072179-97";
$Def_FrCpte= "BE15001223939330"; //"310-0072179-97";
$Def_ToCpte="%";
}
//general info on top of page 
{
// deleted, is there, graph.php does not display !
//echo "Les format des dates est Ann&eacute;e-Mois-Jour (AAAA-MM-DD) !<br>";
}
?>
