<html>
<head>
<script type="text/javascript" language="javascript" src="XHConn.js"></script>
<script>
var myConn = new XHConn();

if (!myConn) alert("XMLHTTP not available. Try a newer/better browser.");

var include_terminado = function (oXML) { document.getElementById('include').innerHTML = oXML.responseText; };

function include_dinamico (url)
{
	document.getElementById('include').innerHTML = "<img src='loading_ani2.gif' />";
	
	myConn.connect("include.php", "GET", "variable="+url, include_terminado);
}
</script>
</head>
<body>
Bueno esto es una prueba y lo k tu kieras <a href="#" onclick="include_dinamico('hola');">Link</a>

<div id="include">
Entraste a la pagina y aki es k va el include
</div>
</div>
</body>
</html>