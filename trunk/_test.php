<?php

// load main env, inkluding instanciation of $SQL = new MySQL 
require_once("init.php");
$SQL->DBG=0; // FIXME: ugly
    define("OK", "<span class='ok'>ok</span>");
    define("NOK","<span class='nok'>no</span>");
    $ERRORS = 0;
    
// PUSER
require_once(I_PATH . "press_user.class.php");
$PUSER = new press_user($db); // 	$db	 = &$VAR['db'];	

require_once(I_PATH . "press.class.php");

// PSITES
require_once(I_PATH . "press_sites.class.php");
$PSITES = new press_sites( $SQL ); // $SQL has to be a valid MySQL-Object
   
//PENTRIES
require_once(I_PATH . "press_entry.class.php");
$PENTRIES = new press_entry( $SQL ); // SQL has to be an valid Object

// setup test-database
$prefix="test_";          // needed for database
$PUSER->set_prefix($prefix);
include "doc/database.php";

	$SQL->create($table['user']);          // main user
	$SQL->create($table['press_user']);    // user extension
	$SQL->create($table['press_sites']);   // sites
	$SQL->create($table['press_entries']); // press_entries
	$SQL->create($table['press_keywords']);
	$SQL->create($table['press_ke_rel']);	// keyword-entry relation n:m
	$SQL->create($table['press_se_rel']);	// site-entry relation n:m
	$SQL->create($table['press_sources']);	// sources (press..)
// output
   
echo XHTMLHEAD;

function it($text, $var, $should, $emsg="") {
    // is var == true?
    if($emsg=="") $emsg = $PUSER->error_cmsg;
    #echo "<br />".$text." ... ";
    if( $var==$should ) {
        echo OK." ($should==$var) &nbsp; :: $text"; 
    } else { 
        GLOBAL $ERRORS;
        $ERRORS++;
        echo NOK;    
        GLOBAL $PUSER;
        echo " ($var!=$should) &nbsp; :: $text &nbsp; <span class='errormsg'>-&gt; ".$emsg."</span>";
    }
    echo " <br />";
}

/* * * * USER * * * */

	// create user
	echo "<h2>CREATE/EDIT USER</h2>";
/*	// inexistenten nutzer editieren
	$ret	=	$PUSER->edit_user(10, "geaendert", "neuespass", 1);
	it("edit user (should fail)",$ret1,false);
*/	
	// neuer nutzer
	$ret1	=   $PUSER->create_user("theo","passtest");
	it("create user",$ret1,true);
	
	// passwort �ndern
	$ret	=	$PUSER->edit_user($ret1, "theo", "testtest", 1);
	it("edit user ".$ret1." (new pass, admin)",$ret1,true);
	
	// nutzer nochmal anlegen
	$ret	=   $PUSER->create_user("theo","testtest");
	it("create user, already existing",$ret,false);
	
	// 2. nutzer anlegen
	$ret    =   $PUSER->create_user("theo2","test");
	it("create user, too short password",$ret,false);
	
	// 2. Nutzer nochmals anlegen
	$ret    =   $PUSER->create_user("theo2","testtest");
	it("create 2nd user, id incremented",$ret,$ret1+1 );
/**/

/* * * * auth * * * */
	echo "<h2>AUTHENTICATE USER</h2>";
	// FIXME: bad:
	$PUSER->error_cmsg="";
#	echo "<br>session: ".session_id()." user: theo pass: ".sha1("testtest")."<br>";
	
	$ret    =   $PUSER->auth("theo","testtest");
	it("right auth",$ret,true);
	
#	echo "<br>session: ".session_id()."<br>";
	$ret    =   $PUSER->auth("theo","passtest");
	it("wrong auth (old pass accepted, wenn nicht-OK)",$ret,false);
	
	$ret    =   $PUSER->auth("theo","testtest");
	it("right auth",$ret,true);
	
	$ret    =   $PUSER->auth();
#	echo "<br>session: ".session_id();
	it("same auth, but with session cannot auth because of wrong auth, session is destroyed",$ret,false);

	// angemeldet? und fehlerhafte wiederanmeldung
	$ret    =   $PUSER->auth("theo","testtest00");
	it("false auth",$ret,false);
	it("&nbsp; and session killed",session_id(),"");
	
	// neu anmelden
	$ret    =   $PUSER->auth("theo","testtest");
	it("right auth",$ret,true);
	
	// abmeldung
	$ret	=	$PUSER->logout();
	it("logout",$ret,true);
	
	$ret	=	$PUSER->logout();
	it("2nd logout (should fail)",$ret,false);
/*	*/

// endeUSER

/* * * * sites * * * */	
	echo "<h2>SITES</h2>";
	$t1 = count($PSITES->show_all());
	it("show all, count=0",$t1, 0);
	
	$t2name ="eins";
	$kuerzel1 = "AA";
	$kuerzel2 = "BB";
	$t2 = $PSITES->add($t2name,$kuerzel1);
	it("create 1st",$t2,1); // id=1

// 	FIXME: here is an error!
	$tx = $PSITES->add($t2name.time(),$kuerzel1);
	it("create other name but same kuerzel",$tx,false,$PSITES->error_msg ); 
		
	$t3 = $PSITES->add("zwei", $kuerzel2);
	it("create 2nd",$t3,$t2+1); // id++
	
	// wenn gleicher eintrag geschrieben wird, liefere id zur�ck des ersten eintrags
	$t4 = $PSITES->add("zwei", $kuerzel2);
	it("create 2nd, 2nd time; same id",$t4,$t3); // gleiche id? | 
#	it("create 2nd, 2nd time; -1 ",$t4,-1,"(whould be nice but isn't tragic)");
	
	$t5 = count($PSITES->show_all());
	it("show all, count=2",$t5,2);
	
	$s = $PSITES->get_name($t2);
	it("get_name",$s,$t2name,$PSITES->error_msg);
	
	$tname = "neu eins";
	$t = $PSITES->edit($t2,$tname, $kuerzel1);
	$s = $PSITES->get_name($t2);
	it("edited, get_name",$s,$tname,$PSITES->error_msg);

/* * * * entries * * * */
	echo "<h2>ENTRIES</h2>";
	echo "<h3>untested</h3>";
	$PENTRIES->set_title("Ein kleiner Titel");
	$PENTRIES->set_filename("image.jpg"); // i have to do an upload
	
	$psource = "Ostseezeitung";
	$PENTRIES->set_source($psource);
	
	$key[0] = "meier";
	$PENTRIES->add_keyword($key[0]);
	$key[1] = "M�LLer";
	$PENTRIES->add_keyword($key[1]);
	
	$PENTRIES->add_site("1");
	$PENTRIES->add_site("3");
	
	echo "<h3>testable, metas</h3>";
	$ret = $PENTRIES->set_link("heinz.de");
	it("bad link",$ret,false, $PENTRIES->error_cmsg);
	
	$ret = $PENTRIES->set_link("http://heiny.de");
	it("link ok http",$ret,true, $PENTRIES->error_cmsg);
	
	$ret = $PENTRIES->set_link("www.heiny.de");
	it("link ok www",$ret,true, $PENTRIES->error_cmsg);
	
	echo "<h3>Date</h3>";

	
	$ret = $PENTRIES->set_date("13-11");
	it("set date 13-11 (fail)",$ret,false, $PENTRIES->error_cmsg);

	$ret = $PENTRIES->set_date("11.13.");
	it("set date 11.13. (fail)",$ret,false, $PENTRIES->error_cmsg);
	
	$ret = $PENTRIES->set_date("11.13.2005");
	it("set date 11.13.2005 (fail)",$ret,false, $PENTRIES->error_cmsg);
	
	$ret = $PENTRIES->set_date("1.8.05");
	it("set date 1.8.05",$ret,"2005-08-01", $PENTRIES->error_cmsg);
		
	$ret = $PENTRIES->set_date("11-13");
	it("set date 11-13",$ret,strftime("%Y")."-11-13", $PENTRIES->error_cmsg);		
	
	$ret = $PENTRIES->set_date("1.8.2005");
	it("set date 1.8.2005",$ret,"2005-08-01", $PENTRIES->error_cmsg);
		
	$ret= $PENTRIES->write();
	it("write entry",$ret, true, $ret." - ".$PENTRIES->error_cmsg);
	
	
/* * * * Keywords * * * */
	echo "<h2>KEYWORDS</h2>";
	
	$sql = "SELECT id FROM ".$prefix."press_keywords WHERE keyword = '".strtolower($key[0])."'";
	$ret = $SQL->select ($sql);
	it("keyword written",$ret[0]['id'],1,"keyword not written!");
	
	$sql = "SELECT id FROM ".$prefix."press_keywords WHERE keyword = '".strtolower($key[1])."'";
	$ret = $SQL->select ($sql);
	it("keyword written, strtolower",$ret[0]['id'],2,"keyword not written!");
	
/* * * * Sources * * * */
	echo "<h2>SOURCES</h2>";
	$sql = "SELECT id FROM ".$prefix."press_sources WHERE name = '".$psource."'";
	$ret = $SQL->select ($sql);
	it("source exists?",$ret[0]['id'],1,"source not created???");
	
	
	
	/* Titel, id, Quelle (klartext)
	 * SELECT DISTINCT (e.id),e.title, s.name FROM test_press_keywords AS k LEFT JOIN test_press_ke_rel AS ke ON k.id=ke.kid LEFT JOIN  test_press_entries AS e ON ke.eid=e.id LEFT JOIN test_press_sources AS s ON e.source=s.id
	 * 
	 */
	
// DROPPER
if (!array_key_exists("nodrop",$_GET)) { 
	$SQL->drop_table($prefix."press_sites");
	$SQL->drop_table($prefix."user");
	$SQL->drop_table($prefix."press_user");
	$SQL->drop_table($prefix."press_entries");
	$SQL->drop_table($prefix."press_keywords");
	$SQL->drop_table($prefix."press_ke_rel");
	$SQL->drop_table($prefix."press_se_rel");
	$SQL->drop_table($prefix."press_setest.php?nodrop_rel");
	$SQL->drop_table($prefix."press_sources");

	echo "<p>Errors total: $ERRORS</p>";
	echo "<a href='?drop'>restart with drop</a> - <a href='?nodrop'>restart with nodrop</a>";
} else {
	$isok = ($ERRORS == 8 || $ERRORS == 0) ? OK : NOK;
	echo "<p>Errors total: $ERRORS - is ok? $isok</p>";
	echo "<a href='?drop'>restart with drop</a> - <a href='?nodrop'>restart with nodrop</a>"; 
}

echo "<p>Hint: klick -nodrop- twice, there should be 8 errors</p>";


echo XHTMLFOOT;
?>
