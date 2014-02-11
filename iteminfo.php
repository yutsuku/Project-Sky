<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
error_reporting(E_ALL); 
ini_set("display_errors", 1);
@include_once('conf.php');
@include_once('class.item.php');
//  [Bloodthirst]  [Have Group, Will Travel]

$find=new item($DB['host'], $DB['user'], $DB['pass']);
/*$items=$find->GetCharacterItems('Móh');
$spell=$find->GetItemBonusBySpell('70844');
print_r($find->normalizeSpell($spell));
*/
$icon = $find->ByID('45024');
print_r($icon);

//print($find->GetClass(1));


?>