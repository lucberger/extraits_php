<?php
/*
http://www.expreg.com/expreg_article.php?art=combobox

upd 30 10 2017
*/
{$listemois= array(
        'janvier 1',
        'f&eacute;vrier 2',
        'mars 3',
        'avril 4',
        'mai 5',
        'juin 6',
        'juillet 7',
        'ao&ucirc;t 8',
        'septembre 9',
        'octobre 10',
        'novembre 11',
        'd&eacute;cembre 12'
        );
}

function listbox_jour ($jour=''){
    for ($i=1;$i<32;$i++)
    {
    if ($i<10) $i='0'.$i;
    echo '<option value="',$i,'"';
    if($i==$jour)  {
    echo ' selected';
    }
    echo '>',$i,'</option>';
    }
}

function listbox_mois ($mois='') {
global $listemois;
    for ($i=1;$i<13;$i++)
    {
    $j = $i-1;
    if ($i<10) $i='0'.$i;
    echo '<option value="',$i,'"';
    if($i==$mois){
    echo ' selected';
    }
    echo '>',$listemois[$j],'</option>';
    }
}

function listbox_an ($lo=2000,$hi=2020, $an) {
//list $lo .. $hi
//select $an
    for ($i=$lo;$i<=$hi;$i++)
    {
    echo '<option value="',$i,'"';
    if($i==$an){
    echo ' selected';
    }
    echo '>',$i,'</option>';
    }
}
/*
function extractYMD ( $YMD) {
// 1954-02-27  Y-M-D
// return date as date
substr ( string $string , int $start [, int $length ]   
}
*/
?>
