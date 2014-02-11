<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
error_reporting(E_ALL); 
ini_set("display_errors", 1);
include_once('conf.php');
include_once('character/class.character.php');

try {
	$char=new character($DB['host'], $DB['user'], $DB['pass']);
	$charinfo = $char->search("Móh");
	if ( isset($charinfo['guid']) ) {
		echo $char->talents($charinfo['guid']) . "\n";
		echo $char->achievements($charinfo['guid']) . "\n";
		print_r($char->professions($charinfo['guid']));
		print_r($char->guild($charinfo['guid']));
		print_r($charinfo);
	}
}

catch ( Exception $e) {
	echo $e->getMessage();
	exit;
}


?>
<!-- TODO 

	[x]	class
	[x]	level
	[x]	race
	[x]	talents
	[x]	professions
	[x]	guild
	[x]	guild rank
	[x]	played since level
	<REMOVED>	date created - impossible to obtain required data
	[x]	whorepoints
	[  ]	iLvl
	[  ]	location

-->
<!--
[01:17]	moh: i was thinking if i should grab whorepoints and kills
[01:18]	HIGHWAY99: yea pvp stats for sure
[01:18]	HIGHWAY99: though im no pvp'er
[01:18]	HIGHWAY99: toon class / level / race
[01:18]	HIGHWAY99: professions
[01:18]	HIGHWAY99: major professions anyways
[01:20]	HIGHWAY99: maybe guild name (possibly guild rank) and time played, time played since 85
[01:20]	b0ne: lol
[01:20]	HIGHWAY99: oh date created
[01:20]	b0ne: gonna have a heck of an output
[01:21]	HIGHWAY99: could have one for output for pvpstats, one for just getting profs, and other for player details
[01:21]	HIGHWAY99: oh and average iLevel
[01:22]	HIGHWAY99: ofc wouldnt hurt to have last known location (zone)
[01:22]	b0ne: oh, a good one would be a/s/l too, sexual preferrence
[01:22]	b0ne: bra size
[01:22]	b0ne: :D
[01:22]	HIGHWAY99: lol
[01:22]	b0ne: sry just aggrivatin
[01:22]	HIGHWAY99: squeeze!
[01:23]	HIGHWAY99: usually what i go off of for guessing rather someone's hacking or not, is name / guildname / class / race / zone / character level
-->