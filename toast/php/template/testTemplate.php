<html>
<head>
<title>{$pageTitle}</title>
</head>
<style>
.menu {
	font-family: Tahoma, sans-serif;
	font-size: 12;
	text-align: center;
}

a {
	text-decoration: none;
}

menu,a {
	color: white;
}
</style>
<body>
<!-- here we load nested template and put it in 2 places on page -->
<!-- LOAD TMENU menu.html -->

<p>Today: {$today}</p>
{testmenu}
<!-- BEGIN TABLE -->
<table border=1>
		<th colspan=5>{$tableHeader}</th>
	{[_blockTr]}
	<tr>
		{[_childBlock]}
		<td>{$cell}</td>
		{[childBlock_]}
		
		{[_childBlock]}
		<td>{$cell}</td>
		{[childBlock_]}
	</tr>
	{[blockTr_]}



</table>
<!-- END TABLE -->
<br>
{testmenu}

</body>
</html>
