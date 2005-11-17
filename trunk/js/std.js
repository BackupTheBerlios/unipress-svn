/* little tiny bad stack for hide/show */
stack = new Array();
stack2 = new Array();
var i=0;
var k=0;
    
	 /* show actual div */
function zeige(id)
{
    verstecke();
    document.getElementById(id).style.visibility="visible";
    stack[i++] = id;
}
/* hide all divs */
function verstecke()
{
    for (j=0; j<i;j++) {
        document.getElementById(stack[j]).style.visibility="hidden";
    }
    i=0;
}
/* show stacked divs*/
function zeige2()
{
    for (j=0; j<k;j++) {
        document.getElementById(stack2[j]).style.visibility="visible";
    }
   k=0;
}
