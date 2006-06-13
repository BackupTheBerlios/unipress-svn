<?php
/*
 * Created on 21.02.2006
 *
 * 
 */

// Set language to German
setlocale(LC_ALL, 'de_DE');

// Specify location of translation tables
bindtextdomain("unipress", "../locale");

// Choose domain
textdomain("unipress");

// Translation is looking for in ./locale/de_DE/LC_MESSAGES/myPHPApp.mo now

// Print a test message
echo gettext("Welcome to My PHP Application");

// Or use the alias _() for gettext()
echo _("Have a nice day");
?> 