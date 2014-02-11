<?php 
$a = 0;
$f=array(1=>'Soulbound',2=>'Conjured',4=>'Lootable',8=>'Heroic',16=>'Deprecated Item',32=>'Totem',64=>'Activatable with right-click',256=>'Wrapper',1024=>'Gifts',2048=>'Item is party loot and can be looted by all',4096=>'Item is refundable',8192=>'Charter (Arena or Guild)',32768=>'PvP reward item',524288=>'Unique equipped',4194304=>'Throwable',8388608=>'Special Use',134221824=>'Bind on Account',268435456=>'For enchant scrolls',536870912=>'Millable',2147483648=>'Bind on Pickup tradeable');
foreach($f as $k => $v) {
	if($a & $k) {
		$Flags[$k]=$v;
	}
}
print_r($Flags);
?>