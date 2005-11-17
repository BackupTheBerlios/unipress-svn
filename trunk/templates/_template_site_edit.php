<?php
/*
 * Created on 06.09.2005
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
?>
<?php

// Zielseiten Template
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>
      Bereiche / Zielseiten bearbeiten
    </title>
<!--
    <script src="js/popup.js" type="text/JavaScript">
    </script>
    <script src="js/sentry.js" type="text/JavaScript">
    </script>
 -->  
    <link rel="stylesheet" type="text/css" href="css/nentry.css" />
  </head>
  <body onload="startup();">
    <div id="container">
      
        <div id="tipcontainer">
          <div id="navilayer">
            <span id="menu">Men&uuml;</span><br />
<?php echo $menu_links; ?>
          </div>
<?php 
	include "_template_help_site.php"; 
?>
        </div>
      <form action="<?php echo $html['form_aim']; ?>" enctype="multipart/form-data" method="post" onsubmit="return checkform();" onreset="return ResetCheck()">
        <div id="tablelayer"> 
			<table summary="form table (as layout)" width="100%">
<?php 
	
	include (T_PATH."sites_edit.php");

?>

            </table>
           </div><!-- tablediv //-->
           
        <div id="sendbuttons">
          <input value="OK, Eintragen" style="background-color:lightgreen"  type="submit" class="buttons" />&nbsp;<input value="Eingaben l&ouml;schen" type="reset" class="buttons" />
        </div>
      </form>
<?php echo $o; ?>
    </div>
  </body>
</html>




