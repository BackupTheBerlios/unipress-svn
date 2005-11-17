<?php
/*
 * Created on 30.08.2005
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>
      Pressemitteilung erfassen
    </title>

    <script src="js/popup.js" type="text/JavaScript">
    </script>
    <script src="js/nentry.js" type="text/JavaScript">
    </script>
    
    <link rel="stylesheet" type="text/css" href="css/nentry.css" />
  </head>
  <body onload="startup();">
    <div id="container">
      <form action="<?php echo $html['form_aim']; ?>" enctype="multipart/form-data" method="post" onsubmit="return checkform();" onreset="return ResetCheck()">
        <div id="tipcontainer">
          <div id="navilayer">
            <span id="menu">Men&uuml;</span> <br />
             <?php echo $menu_links; ?>
          </div>
		<?php include "_template_help_entry.php"; ?>
        </div>
        <div id="tablelayer">
          <table summary="form table (as layout)" width="100%">
            <tr onfocus="this.style.backgroundColor='#FFFFF0';" onmouseover="zeige('tquelle');this.style.backgroundColor='#FFFFF0';" onmouseout="this.style.backgroundColor=''">
              <td id="tc1" width="150">
                <label for="quelle" accesskey="q"><u>Q</u>uelle des Artikels:</label>
              </td> 
				        <td><div class="attention" id="aquelle">
						  <img src="images/help/attention.gif" height="10" width="10" alt="Fehler:" title="Bitte geben Sie eine Quelle an oder erstellen Sie eine neue" /> 
						  </div>
              </td>
              <td>
                <div id="Fquelle">
                  <select id="quelle" name="quelle" size="1" onfocus="zeige('tquelle')">
                  
						  <option value="" selected="selected">
                      --Bitte w&auml;hlen Sie--
                    </option> 
						   <option value="0">
                      Neue Quelle
                    </option>
                    <option value="1">
                      Ostseezeitung
                    </option>
                    
                  </select> <script type="text/javascript">
                  //<![CDATA[
                         document.write('<input type="button" name="neu_quelle" value="neu"  onclick="qneu2();"  class="buttons" />');
                         //]]>
                  </script>
                </div>
                <script type="text/javascript">
                //<![CDATA[
                         document.write(' <div id="Fquelleneu" onfocus="zeige(\'tquelleneu\')"><input type="text"  value="Geben Sie die neue Quelle ein" name="quelleneu" id="quelleneu" size="30"  /><\/div>');
                         //]]>
                </script> <!-- <input type="button" name="neu_quelle" value="neu"  onclick="qneu();"  class="buttons" />-->
              </td>

            </tr>
            <tr onfocus="zeige('tdatei');" onmouseover="zeige('tdatei');this.style.backgroundColor='#FFFFF0';" onblur="this.style.backgroundColor=''" onmouseout="this.style.backgroundColor=''">
              <td id="tc2">
                <label for="datei" accesskey="d"><u>D</u>atei ausw&auml;hlen:</label>
              </td>
				  <td><div class="attention" id="adatei"><img src="images/help/attention.gif" height="10" width="10" alt="Fehler:" /> </div>
              </td>
              <td>
                <input type="file" name="datei" onfocus="zeige('tdatei')" />
              </td>
              <td>
              </td>
            </tr>
            <tr onfocus="zeige('ttitel');" onmouseover="zeige('ttitel');this.style.backgroundColor='#FFFFF0';" onmouseout="this.style.backgroundColor=''">
              <td id="tc3">
                <label for="titel" accesskey="t"><u>T</u>itel:</label>
              </td>
				  <td><div class="attention" id="atitel"><img title="Bitte füllen Sie dieses Feld aus" src="images/help/attention.gif" height="10" width="10" alt="Fehler:" /> </div>
              </td>
              <td>
                <input type="text" name="titel" id="titel" size="30" onfocus="zeige('ttitel')" />
              </td>
        
            </tr>
            <tr onfocus="zeige('tdatei');" onmouseover="zeige('tkeywords');this.style.backgroundColor='#FFFFF0';" onmouseout="this.style.backgroundColor=''">
              <td id="tc4">
                <label for="keywords" accesskey="s"><u>S</u>tichw&ouml;rter:</label>
              </td>
				  <td><div class="attention" id="akeywords"><img src="images/help/attention.gif" height="10" width="10" alt="Fehler:" /> </div>
              </td>
              <td>
                <input type="text" name="keywords" id="keywords" size="30" onfocus="zeige('tkeywords')" />
              </td>
           
            </tr>
            <tr onfocus="zeige('tdatei');" onmouseover="zeige('tlinks');this.style.backgroundColor='#FFFFF0';" onmouseout="this.style.backgroundColor=''">
              <td id="tc6">
                <label for="link" accesskey="l"><u>L</u>ink (ohne http://)*:</label>
              </td>
				  <td><div class="attention" id="alinks"><img src="images/help/attention.gif" height="10" width="10" alt="Fehler:" /> </div>
              </td>
              <td>
                <input type="text" name="link" id="link" size="30" onfocus="zeige('tlinks')" />
              </td>
         
            </tr>
            <tr onfocus="zeige('tinst');" onmouseover="zeige('tinst');this.style.backgroundColor='#FFFFF0';" onmouseout="this.style.backgroundColor=''">
              <td id="tc5" valign="top">
                <label for="inst" accesskey="b"><u>B</u>ereich(e):</label>
              </td>
				  <td><div class="attention" id="ainst"><img src="images/help/attention.gif" height="10" width="10" alt="Fehler:" /> </div>
              </td>
              <td>
                <select id="inst" name="inst[]" size="5" multiple="multiple" onfocus="zeige('tinst')">
                  <option name="0">
                    W&auml;hlen Sie mind. einen Eintrag
                  </option>
                  <option name="1">
                    MD
                  </option>
                  <option name="2">
                    NT
                  </option>
                  <option name="3">
                    AT
                  </option>
                  <option name="4">
                    AE
                  </option>
                  <option name="5">
                    GS
                  </option>
                  <option name="6">
                    INF
                  </option>
                  <option name="7">
                    Fakult&auml;t
                  </option>
                </select>
              </td>
           
            </tr>
          </table>
        </div>
        <div id="sendbuttons">
          <input value="OK, Eintragen" style="background-color:lightgreen"  type="submit" class="buttons" /> <input value="Eingaben l&ouml;schen" type="reset" class="buttons" />
        </div>
      </form> 
    </div>
  </body>
</html>

