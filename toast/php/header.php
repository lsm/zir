<?php defined(_ZIR) or die('<meta http-equiv="refresh" content="0;url=login.php">')?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php global $title; echo $title?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="STYLESHEET" type="text/css" href="css/style_global.css">
<script type="text/javascript" src="js/mootools.v1.00.all.compressed.js"></script>
<script type="text/javascript" src="js/site.js"></script>
<script type="text/javascript">
var indexLevel = 1;
function dragContainerInit(el){
	var fadeIn = el.parentNode.effect('opacity', {duration: 600});
	var dragContainerOptions = {
		handle: el,
		onStart: function(){
			var fadeIn = el.parentNode.effect('opacity', {duration: 600});
			fadeIn.start(1,.3);
			indexLevel++;
			el.parentNode.style.zIndex = indexLevel;
		}.bind(this),
		onComplete: function(){
			var fadeIn = el.parentNode.effect('opacity', {duration: 600});
			fadeIn.start(.3,1);
		}.bind(this)
	};
	el.style.cursor = 'move';
	el.parentNode.makeDraggable(dragContainerOptions);
}
window.onload=function()
{
	/* setup draggables */
	var draggables = document.getElementsBySelector('.dragger');
	draggables.each(function(el){dragContainerInit(el);});
}
/*
function mouseOverButton(ObjId)
{
	var obj = document.getElementById(ObjId);
	obj.style.visibility='visible';
	obj.style.opacity='0.5';
}
function mouseOutButton(ObjId)
{
	var obj = document.getElementById(ObjId);
	obj.style.visibility='visible';
	obj.style.opacity='1';
}
*/
function mouseOverImg(ObjId, img)
{
	var obj = document.getElementById(ObjId);
	obj.src=img;
}
function mouseOutImg(ObjId, img)
{
	var obj = document.getElementById(ObjId);
	obj.src=img;
}
</script>
</head>
<body>