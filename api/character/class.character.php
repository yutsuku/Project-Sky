<?php
/**
* World of Warcraft Armory for SkyFireEMU 4.0.6
*
* http://www.projectskyfire.org/
* https://github.com/ProjectSkyfire/SkyFireEMU
*
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
* @version 1.0
* @author moh <moh@yutsuku.net>
*/
class character {

	const ARMORYDB = 'armory';
	private $mysqli;
	
	public function __construct($db_host='localhost',$db_user='root',$db_pass='',$db_name='world',$db_port=3306) {
		if(!$this->mysqli) {
			if($db_host&&$db_user&&$db_pass) {
				$this->mysqli=new mysqli($db_host, $db_user, $db_pass, $db_name, $db_port);
					if (mysqli_connect_error()) {
						throw new Exception( 'Connectinting to the database failed');
					}
				//  or die ('Could not connect to the database server' . mysqli_connect_error())
			}
		}
	}
	
	public function __destruct() {
		if($this->mysqli) {
			$this->mysqli->close();
		}
	}
	
	public function search($input=null) {
		if(is_numeric($input) && strlen($input)>0) {
			$sql="guid='%d'";
		} else {
			if(strlen($input)<1) return;
			$sql="name=N'%s'";
		}
		// set data to unicode
		$this->mysqli->query("SET NAMES utf8");
		$this->mysqli->query("SET CHARACTER SET utf8");
		
		$input = $this->mysqli->real_escape_string($input);
		if ($result = $this->mysqli->query("SELECT guid, name, race, class, gender, level, totaltime, leveltime, totalKills, chosenTitle, activespec FROM characters.characters WHERE ". sprintf($sql, $input)  ." LIMIT 1")) {
			if($result->num_rows==0) return;
			while ($row = $result->fetch_array(MYSQL_ASSOC)) {
				$data=$row;
			}
		}
		if ( !isset($data) ) return;
		return $data;
		
	}
	
	public function talents($guid=null,$spec=0) {
		if(!isset($guid)) return;
		$guid = $this->mysqli->real_escape_string($guid);
		
		if ($result = $this->mysqli->query("SELECT spell FROM characters.character_talent WHERE guid='" . $guid . "' and spec='" . $spec . "' LIMIT 25")) {
		if($result->num_rows==0) return;
			while ($row = $result->fetch_array(MYSQL_ASSOC)) {
				$data[]=$row['spell'];
			}
		}
		if ( !isset($data) ) return;
		
		$spells=implode(',',$data);
		if ($result = $this->mysqli->query("SELECT specId FROM armory.talent_tbc where (rank1 IN ($spells) or rank2 IN ($spells) or rank3 IN ($spells))")) {
		if($result->num_rows==0) return;
			while ($row = $result->fetch_array(MYSQL_ASSOC)) {
				$spectab[]=$row['specId'];
			}
		}
		if ( !isset($spectab) ) return;
		
		$speccount=array_count_values($spectab);
		$tabs = implode(',', array_keys($speccount));
		
		if ($result = $this->mysqli->query("SELECT id, orderIndex FROM armory.talenttab_dbc where id IN ($tabs)")) {
		if($result->num_rows==0) return;
			while ($row = $result->fetch_array(MYSQL_ASSOC)) {
				$tabindex[$row['id']]=$row['orderIndex'];
			}
		}
		if ( !isset($tabindex) ) return;
		
		$_spec[0] = 0;
		$_spec[1] = 0;
		$_spec[2] = 0;
		
		foreach ( $tabindex as $key => $tree ) {
			$_spec[$tree] = $speccount[$key];
		}
		
		return implode('/', $_spec);
	}
	
	public function achievements($guid=null) {
		if(!isset($guid)) return;
		$guid = $this->mysqli->real_escape_string($guid);
		if ($result = $this->mysqli->query("SELECT achievement FROM characters.character_achievement where guid='" . $guid . "' LIMIT 2700")) {
		if($result->num_rows==0) return;
			while ($row = $result->fetch_array(MYSQL_ASSOC)) {
				$data[]=$row['achievement'];
			}
		}
		if ( !isset($data) ) return;
		
		$parsed = implode(',', $data);
		if ($result = $this->mysqli->query("SELECT points FROM armory.achievement_dbc where id IN ($parsed) limit 2700")) {
		if($result->num_rows==0) return;
			while ($row = $result->fetch_array(MYSQL_ASSOC)) {
				$points[]=$row['points'];
			}
		}
		if ( !isset($points) ) return;
		
		$achi = 0;
		for ( $i = 0, $size = count($points); $i < $size; ++$i ) {
			$achi = $points[$i] + $achi;
		}
		return $achi;
	}
	
	public function professions($guid=null) {
		if(!isset($guid)) return;
		$guid = $this->mysqli->real_escape_string($guid);
		if ($result = $this->mysqli->query("SELECT skill,value,max FROM characters.character_skills WHERE guid='" . $guid . "' LIMIT 25")) {
		if($result->num_rows==0) return;
			while ($row = $result->fetch_array(MYSQL_ASSOC)) {
				$pdata[]=$row;
			}
		}
		if ( !isset($pdata) ) return;
		
		// get professions ids from database (primary)
		if ($result = $this->mysqli->query("SELECT id, Name FROM " . self::ARMORYDB . ".skillline_dbc where CategoryId='11' LIMIT 15")) {
			if($result->num_rows==0) return;
			while ($row = $result->fetch_array(MYSQL_ASSOC)) {
				$profession[$row['id']] = $row['Name'];
			}
		}
		
		for($i = 0, $size = count($pdata); $i < $size; ++$i) {
			if ( isset( $profession[$pdata[$i]['skill']] ) ) {
				$charprof[] = array( $profession[$pdata[$i]['skill']], $pdata[$i]['value'], $pdata[$i]['max'] );
			}
		}
		if ( !isset($charprof) ) return;
		return $charprof;
	}
	
	public function guild($guid=null) {
		if(!isset($guid)) return;
		$guid = $this->mysqli->real_escape_string($guid);
		// check if character is in guild
		if ($result = $this->mysqli->query("SELECT guildid, rank FROM characters.guild_member WHERE guid='" . $guid . "' LIMIT 1")) {
			if($result->num_rows==0) return;
			while ($row = $result->fetch_array(MYSQL_ASSOC)) {
				$data=$row;
			}
		}
		// get guild name
		if ($result = $this->mysqli->query("SELECT name FROM characters.guild WHERE guildid=" . $data['guildid'] . " LIMIT 1")) {
			if($result->num_rows==0) return;
			while ($row = $result->fetch_array(MYSQL_ASSOC)) {
				$data['name']=$row['name'];
			}
		}
		return $data;
	}
	
}