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
class item {
	
	const DIR_ICONS = 'inv_icons/';
	const ARMORYDB = 'armory';
	private $mysqli;
	
	public function __construct($db_host='localhost',$db_user='root',$db_pass='',$db_name='world',$db_port=3306) {
		if(!$this->mysqli) {
			if($db_host&&$db_user&&$db_pass) {
				$this->mysqli=new mysqli($db_host, $db_user, $db_pass, $db_name, $db_port) or die ('Could not connect to the database server' . mysqli_connect_error());
			}
		}
	}
	
	public function __destruct() {
		if($this->mysqli) {
			$this->mysqli->close();
		}
	}
	/**
	* @access public
	* @param int $id
	* @return array on success or FALSE
	*/
	public function ByID($id=NULL) {
	if(!is_numeric($id)) return;
	$id = $this->mysqli->real_escape_string($id);
	// behold, wall of text
	if ($result = $this->mysqli->query("SELECT 
	entry,class,subclass,name,displayid,Quality,Flags,BuyCount,BuyPrice,SellPrice,InventoryType,
	AllowableClass,AllowableRace,ItemLevel,RequiredLevel,requiredhonorrank,RequiredReputationFaction,
	RequiredReputationRank,maxcount,stackable,ContainerSlots,
	stat_type1,stat_value1,stat_type2,stat_value2,stat_type3,stat_value3,stat_type4,stat_value4,stat_type5,stat_value5,
	stat_type6,stat_value6,stat_type7,stat_value7,stat_type8,stat_value8,stat_type9,stat_value9,stat_type10,stat_value10,
	ScalingStatDistribution,ScalingStatValue,DamageType,delay,
	spellid_1,spelltrigger_1,spellcharges_1,spellppmRate_1,spellcooldown_1,spellcategory_1,spellcategorycooldown_1,
	spellid_2,spelltrigger_2,spellcharges_2,spellppmRate_2,spellcooldown_2,spellcategory_2,spellcategorycooldown_2,
	spellid_3,spelltrigger_3,spellcharges_3,spellppmRate_3,spellcooldown_3,spellcategory_3,spellcategorycooldown_3,
	spellid_4,spelltrigger_4,spellcharges_4,spellppmRate_4,spellcooldown_4,spellcategory_4,spellcategorycooldown_4,
	spellid_5,spelltrigger_5,spellcharges_5,spellppmRate_5,spellcooldown_5,spellcategory_5,spellcategorycooldown_5,
	bonding,description,RandomProperty,RandomSuffix,block,itemset,MaxDurability,TotemCategory,
	socketColor_1,socketContent_1,socketColor_2,socketColor_3,socketBonus,RequiredDisenchantSkill,Duration,DisenchantID
	FROM world.item_template WHERE entry='".$id."' LIMIT 1")) {
			while ($row = $result->fetch_array(MYSQL_ASSOC)) {
				$data=$row;
			}
			$icon=$this->GetIcon($data['displayid']);
			($icon ? $data['icon'] = $icon : $data['icon'] = self::DIR_ICONS . 'default-icon.gif');
			return $data;
		}
	}
	/**
	* @access public
	* @param int $id
	* @return string on success or FALSE, can also return empty string if $id == 0
	*/
	public function bonding($id=NULL) {
		if(!is_numeric($id)) return;
		$b=array('','Binds when picked up','Binds when equipped','Binds when used','Quest item','Quest item');
		if(count($b)>$id) { return $b[$id]; }
	}
	/**
	* @access public
	* @param int $class [, int $subclass]
	* @return string on success or FALSE
	*/
	public function GetClass($class=NULL,$subclass=NULL) {
		if(!is_numeric($class)) return;
		$c=array('Consumable','Container','Weapon','Gem','Armor','Reagent','Projectile','Trade Goods','Generic','Recipe','Money','Quiver','Quest','Key','Permanent','Miscellaneous','Glyph');
		if(!$subclass) {
			if(count($c)>$class) { return $c[$class];}
		}
		if(!is_numeric($subclass)) return;
		$s=array(
			0 => array('Consumable','Potion','Elixir', 'Flask','Scroll','Food & Drink','Item Enhancement','Bandage','Other'),
			1 => array('Bag','Soul Bag','Herb Bag','Enchanting Bag','Engineering Bag','Gem Bag','Mining Bag','Leatherworking Bag','Inscription Bag'),
			2 => array('Axe','Axe','Bow','Gun','Mace','Mace','Polearm','Sword','Sword','Obsolete','Staff','Exotic','Exotic','Fist Weapon','Miscellaneous','Dagger','Thrown','Spear','Crossbow','Wand','Fishing Pole'),
			3 => array('Red','Blue','Yellow','Purple','Green','Orange','Meta','Simple','Prismatic'),
			4 => array('Miscellaneous','Cloth','Leather','Mail','Plate','Buckler','Shield','Libram','Idol','Totem','Sigil'),
			5 => array('Reagent'),
			6 => array('Wand','Bolt','Arrow','Bullet','Thrown'),
			7 => array('Trade Goods','Parts','Explosives','Devices','Jewelcrafting','Cloth','Leather','Metal & Stone','Meat','Herb','Elemental','Other','Enchanting','Materials','Armor Enchantment','Weapon Enchantment'),
			8 => array('Generic'),
			9 => array('Book','Leatherworking','Tailoring','Engineering','Blacksmithing','Cooking','Alchemy','First Aid','Enchanting','Fishing','Jewelcrafting'),
			10 => array('Money'),
			11 => array('Quiver','Quiver','Quiver','Ammo Pouch'),
			12 => array('Quest'),
			13 => array('Key','Lockpick'),
			14 => array('Permanent'),
			15 => array('Junk','Reagent','Pet','Holiday','Other','Mount'),
			16 => array('Warrior','Paladin','Hunter','Rogue','Priest','Death Knight','Shaman','Mage','Warlock','Druid')
		);
		if(count($s)>$class && count($s[$class])>$subclass) { return $s[$class][$subclass]; }
	}
	/**
	* @access public
	* @param int $id
	* @return string on success or FALSE
	*/
	public function Quality($id=NULL) {
		if(!is_numeric($id)) return;
		$q=array('Poor','Common','Uncommon','Rare','Epic','Legendary','Artifact','Bind to Account');
		if(count($q)>$id) { return $q[$id];}
	}
	/**
	* @access public
	* @param int $id
	* @return array on success or FALSE
	*/
	public function Flags($id=NULL) {
		if(!is_numeric($id)) return;
		$Flags=NULL;
		$f=array(1=>'Soulbound',2=>'Conjured',4=>'Lootable',8=>'Heroic',16=>'Deprecated Item',32=>'Totem',64=>'Activatable with right-click',256=>'Wrapper',1024=>'Gifts',2048=>'Item is party loot and can be looted by all',4096=>'Item is refundable',8192=>'Charter (Arena or Guild)',32768=>'PvP reward item',524288=>'Unique equipped',4194304=>'Throwable',8388608=>'Special Use',134221824=>'Bind on Account',268435456=>'For enchant scrolls',536870912=>'Millable',2147483648=>'Bind on Pickup tradeable');
		foreach($f as $k => $v) {
			if($id & $k) {
				$Flags[$k]=$v;
			}
		}
		return $Flags;
	}
	/**
	* @access public
	* @param int $id
	* @return string on success or FALSE, can also return FALSE if $id == 0
	*/
	public function InvType($id=NULL) {
		if(!is_numeric($id)) return;
		$s=array(NULL,'Head','Neck','Shoulder','Shirt','Chest','Waist','Legs','Feet','Wrists','Hands','Finger','Trinket','One-Hand','Shield','Ranged','Back','Two-Hand','Bag','Tabard','Robe','Main hand','Off hand','Holdable','Ammo','Thrown','Ranged right','Quiver','Relic');
		if(count($s)>$id) { return $s[$id];}
	}
	/**
	* @access public
	* @param int $id
	* @return array on success or FALSE
	*/
	public function dmg($id=NULL) {
		if(!is_numeric($id)) return;
		$id = $this->mysqli->real_escape_string($id);
		if ($result = $this->mysqli->query("SELECT * FROM " . self::ARMORYDB . ".item_damage WHERE entry='".$id."' LIMIT 1")) {
			$data=$result->num_rows;
			if($data==0) {
				$wowhead=$this->wowhead($id);
				if(is_array($wowhead)) {
					if ($result = $this->mysqli->query("INSERT INTO " . self::ARMORYDB . ".item_damage (entry,min,max,dps) VALUES(" . $id . ",". $wowhead['dmgmin1'] . "," . $wowhead['dmgmax1'] . "," . $wowhead['dps'] . ")")) {
						return array('entry'=>$id,'min'=>$wowhead['dmgmin1'],'max'=>$wowhead['dmgmax1'],'dps'=>$wowhead['dps'],'database'=>0);
					}
				}
			} else {
				if ($result = $this->mysqli->query("SELECT * FROM " . self::ARMORYDB . ".item_damage WHERE entry='" . $id . "' LIMIT 1")) {
						while ($row = $result->fetch_array(MYSQL_ASSOC)) {
							$data=$row;
						}
					$data['database']=1;
					return $data;
				}
			}
		}
	}
	/**
	* @access public
	* @param int $id
	* @return array on success or FALSE
	*/
	public function wowhead($id=NULL) {
		if(!is_numeric($id)) return;
		$handle=file_get_contents(sprintf('http://www.wowhead.com/item=%d&xml', $id));
		if(!$handle) return;
		$e=explode('</jsonEquip>', $handle);
		$e=explode('<jsonEquip>',$e[0]);
		$e=explode('<![CDATA[',$e[1]);
		$e=explode(']]>', $e[1]);
		$e=str_replace('"','',$e[0]);
		$e=explode(',', $e);
		foreach($e as $k => $v) {
			$t=explode(':', $v);
			$a[$t[0]] = $t[1];
		}
		return $a;
	}
	
	public function GetDisplayId($itemEntry=NULL) {
		if(!is_numeric($itemEntry)) return;
		$itemEntry = $this->mysqli->real_escape_string($itemEntry);
		if ($result = $this->mysqli->query("SELECT displayid FROM world.item_template WHERE entry='".$itemEntry."' LIMIT 1")) {
			if($result->num_rows==0) return;
			while ($row = $result->fetch_array(MYSQL_ASSOC)) {
				$data=$row['displayid'];
			}
			return $data;
		}
	}
	
	public function GetIcon($displayId=NULL) {
		if(!is_numeric($displayId)) return;
		$displayId = $this->mysqli->real_escape_string($displayId);
		if ($result = $this->mysqli->query("SELECT icon FROM " . self::ARMORYDB . ".itemdisplayinfo_dbc WHERE id='".$displayId."' LIMIT 1")) {
			if($result->num_rows==0) return;
			while ($row = $result->fetch_array(MYSQL_ASSOC)) {
				$data=self::DIR_ICONS . $row['icon'] . '.png';
			}
			return $data;
		}
	}
	/**
	* @access public
	* @param int $spellid
	* @return array on success or FALSE
	* @comment Use this function to get item enchants,gem bonues and all that shit that you can put on your item
	*/
	public function GetSocketBonus($id) {
		if(!is_numeric($id)) return;
		$id = $this->mysqli->real_escape_string($id);
		if ($result = $this->mysqli->query("SELECT bonus,entryId FROM " . self::ARMORYDB . ".spellitemenchantment_dbc WHERE id='".$id."' LIMIT 1")) {
			if($result->num_rows==0) return;
			while ($row = $result->fetch_array(MYSQL_ASSOC)) {
				$data=$row;
			}
			return $data;
		}
	}
	/**
	* @access public
	* @param mixed $input
	* @return array on success or FALSE
	*/
	public function GetCharacterItems($input=NULL) {
		if(is_numeric($input) && strlen($input)>0) {
			$sql="guid='%d'";
		} else {
			if(strlen($input)<1) return;
			$sql="name=N'%s'";
		}
		
		// set data to unicode
		$this->mysqli->query("SET NAMES utf8");
		$this->mysqli->query("SET CHARACTER SET utf8");
		
		// STEP ONE: Get player ID
		$input = $this->mysqli->real_escape_string($input);
		if ($result = $this->mysqli->query("SELECT guid as player_id,name FROM characters.characters WHERE ". sprintf($sql, $input)  ." LIMIT 1")) {
			if($result->num_rows==0) return;
			while ($row = $result->fetch_array(MYSQL_ASSOC)) {
				$sql_step_1=$row;
			}
			// STEP TWO: Get player's items ID
			if ($result = $this->mysqli->query("SELECT slot,item FROM characters.character_inventory WHERE guid=". $sql_step_1['player_id'] ." AND bag=0 AND slot BETWEEN 0 AND 18 ORDER BY slot ASC")) {
				while ($row = $result->fetch_array(MYSQL_ASSOC)) {
					$sql_step_2[$row['item']]=$row['slot'];
				}
				// prepare data for next query
				$f=" OR guid=%d";
				$ff="AND guid=%d";
				$l=0;$q='';
				foreach($sql_step_2 as $slot => $v) {
					if($l==0) {
						$l=1;
						$q.=sprintf($ff,$slot);
					} else {
						$q.=sprintf($f,$slot);
					}
				}
				// STEP THREE: Get detailes information about those items
				if ($result = $this->mysqli->query("SELECT guid,itemEntry,creatorGuid,giftCreatorGuid,count,duration,enchantments,randomPropertyId,durability,text FROM characters.item_instance WHERE owner_guid=". $sql_step_1['player_id'] . " " . $q . "")) {
					while ($row = $result->fetch_array(MYSQL_ASSOC)) {
						$sql_step_3[]=$row;
					}
					// lets make easy to understand output, and add enchants and all that shit that you can put in your inventory
					foreach($sql_step_3 as $key=>$item) {
						$sql_step_3[$key]['slot'] = $sql_step_2[$item['guid']];
						$sql_step_3[$key]['details']=$this->ByID($item['itemEntry']);
						if($sql_step_3[$key]['details']['socketBonus'] != 0) { $sql_step_3[$key]['socket']=$this->GetSocketBonus($sql_step_3[$key]['details']['socketBonus']); }
						$enchID = explode(' ', $sql_step_3[$key]['enchantments']);
						for($i=0,$size=count($enchID);$i<$size;++$i) {
							if($enchID[$i]==0) continue;
							if($i==15) {  $sql_step_3[$key]['socket']['active']=1; }
							$ench=$this->GetSocketBonus($enchID[$i]);
							if($i==15) continue;
							$sql_step_3[$key]['ench'][$i]=
							array(
								$ench['bonus'],
								($ench['entryId']!=0 ? $ench['entryId'] : null),
								($ench['entryId']!=0 ? $this->GetIcon($this->GetDisplayId($ench['entryId'])) : null)
								);
						}
					}
					return $sql_step_3;
				}
			}
		}
	} // GetCharacterItems(); // holy fuck that was long
	
	public function GetSet($items) {
		$sets=array();
		for($i=0,$size=count($items);$i<$size;$i++) {
			if($items[$i]['details']['itemset'] == 0) continue;
			if(!in_array($items[$i]['details']['itemset'],$sets)) {
				$sets[]=$items[$i]['details']['itemset'];
			}
		}
		// no sets found, return FALSE
		$size=count($sets);
		if($size<1) return;
		$f=" OR id=%d";
		for($i=0;$i<$size;$i++) {
			if($i==0) { $q='id=' . $sets[$i]; } else {
				$q.=sprintf($f,$sets[$i]);
			}
		}
		if ($result = $this->mysqli->query("SELECT id,name,entryRef1,entryRef2,entryRef3,entryRef4,entryRef5,entryRef6,entryRef7,entryRef8,entryRef9,bonus1,bonus2,bonus3,bonus4,bonus_extra1,bonus_extra2,bonus_extra3,bonus_extra4,pieces1,pieces2,pieces3,pieces4,pieces_extra1,pieces_extra2,pieces_extra3,pieces_extra4 FROM " . self::ARMORYDB . ".itemset_dbc WHERE " . $q)) {
			if($result->num_rows==0) return;
			$counter=0;
			while ($row = $result->fetch_array(MYSQL_ASSOC)) {
				$data[$counter]=$row;
				// get items names
				for($i=1;$i<=9;$i++) {
					if($row['entryRef' . $i] == 0) continue;
					if ($result1 = $this->mysqli->query("SELECT name FROM world.item_template WHERE entry=" . $row['entryRef' . $i])) {
						if($result1->num_rows==0) return;
						while ($row1 = $result1->fetch_array(MYSQL_ASSOC)) {
							$data[$counter]['entryRefName' . $i]=$row1['name'];
						}
					}
				}
				$counter++;
			}
			return $data;
		}
		
	}
	
	public function GetItemBonusBySpell($id=NULL) {
		if(!is_numeric($id)) return;
		$id = $this->mysqli->real_escape_string($id);
		
		if ($Spell_obj = $this->mysqli->query("SELECT durationIndex,name,description FROM " . self::ARMORYDB . ".spell_dbc WHERE id='".$id."' LIMIT 1")) {
			if($Spell_obj->num_rows==0) return;
			while ($row = $Spell_obj->fetch_array(MYSQL_ASSOC)) {
				$Spell=$row;
			}
			if ($Effect_obj = $this->mysqli->query("SELECT effectAuraPeriod,effectBasePoints,effectChainAmplitude,effectDieSides FROM " . self::ARMORYDB . ".spelleffect_dbc WHERE effectSpellId='".$id."' LIMIT 2")) {
				if($Effect_obj->num_rows==0) return;
				while ($row = $Effect_obj->fetch_array(MYSQL_ASSOC)) {
					$Effect['effect'][]=$row;
				}
				
				if($Spell['durationIndex'] != 0 ) {
					if ($Duration_obj = $this->mysqli->query("SELECT Field1,Field2 FROM " . self::ARMORYDB . ".spellduration_dbc WHERE id='".$Spell['durationIndex']."' LIMIT 1")) {
						if($Duration_obj->num_rows==0) return;
						while ($row = $Duration_obj->fetch_array(MYSQL_ASSOC)) {
							$Duration=$row;
						}
					} // Duration_obj
					return array_merge($Spell,$Effect,$Duration);
				}
				
			} // Effect_obj
		} // Spell_obj
		return array_merge($Spell,$Effect);
	}
	
	// help function for GetItemBonusBySpell();
	public function normalizeSpell($arr) {
		$flags=array('$s1' => false, '$s2' => false, '$t1' => false, '$t2' => false, '$d' => false, '$70845d' => false, '$m1' => false, '$o1' => false, '$z' => false);
		foreach($flags as $k=>$v) {
			if ( strpos($arr['description'], $k) ) $flags[$k] = true; 
		}
		$replace=array();
		if ( $flags['$s1'] ) { $replace[0][]='$s1'; $replace[1][]=$arr['effect'][0]['effectBasePoints']; }
		if ( $flags['$s2'] ) { $replace[0][]='$s2'; $replace[1][]=$arr['effect'][1]['effectBasePoints']; }
		if ( $flags['$t1'] ) { $replace[0][]='$t1'; $replace[1][]=$arr['effect'][0]['effectAuraPeriod']/1000; }
		if ( $flags['$t2'] ) { $replace[0][]='$t2'; $replace[1][]=$arr['effect'][1]['effectAuraPeriod']/1000; }
		if ( $flags['$d'] ) { $replace[0][]='$d'; $replace[1][]=$this->ct($arr['Field1']); }
		if ( $flags['$70845d'] ) { $replace[0][]='$70845d'; $replace[1][]=$this->ct($arr['effect'][0]['effectBasePoints']); }
		$arr['descriptionf'] = str_replace($replace[0], $replace[1], $arr['description']);
		return $arr;
	}
	
	// help function
	public function ct($time) {
		if($time>= 3600000) { return $time/1000/60/60 . ' hour'; }
		elseif($time>=60000) { return $time/1000/60 . ' min'; }
		elseif($time<0) { return $time*-1/10 . ' sec'; }
		else { return $time/1000 . ' sec'; }
	}

}
?>