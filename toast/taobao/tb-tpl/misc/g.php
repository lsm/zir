<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<script type="text/javascript" src="/static/mootools-release-1.11.js"></script>

<script>
/*	Script: clipboard.js
		Provides access to the OS clipboard so that data can be copied to it (using a flash plugin).
		
		Author:
		Aaron Newton <aaron [dot] newton [at] cnet [dot] com>
		Original source: http://www.jeffothy.com/weblog/clipboard-copy/
		
		Dependencies:
		Mootools - <Moo.js>, <Utilities.js>, <Common.js>, <Array.js>, <String.js>, <Element.js>, <Function.js>
		CNET - (optional) <element.forms.js>
		
		Class: Clipboard
		Provides access to the OS clipboard so that data can be copied to it (using a flash plugin).
*/
var Clipboard = {
	swfLocation: '/static/_clipboard.swf',
/*	Property: copyFromElement
		Copies the selected text in an element to the clipboard.
		
		Arguments:
		element - the element that has selected text.
	*/
	copyFromElement: function(element) {
		element = $(element);
		if(!element) {
			return;
		}
		if (window.ie) {
			alert('adding domready event for range');
			try {
				//window.addEvent('domready', function() {
					var range = element.createTextRange();
					alert('exec-ing copy');
					if(range) range.execCommand('Copy');
				//});
			}catch(e){
				dbug.log('cannot copy to clipboard: %s', o)
			}
		} else {
			var text = (element.getSelectedText)?element.getSelectedText():element.getValue();
			if (text) Clipboard.copy(text);
		}
	},
/*	Property: copy
		Copies a string to the clipboard.
		
		Arguments:
		text - (string) value to be copied to the clipboard.
	*/
	copy: function(text) {
		if(window.ie){
			//window.addEvent('domready', function() {
				var cb = new Element('textarea', {styles: {display: 'none'}}).injectInside(document.body);
				cb.setProperty('value', text).select();
				alert('copying');
				Clipboard.copyFromElement(cb);
				alert('removing');
				cb.remove();
			//});
		} else {
			var swf = ($('flashcopier'))?$('flashcopier'):new Element('div').setProperty('id', 'flashcopier').injectInside(document.body);
			swf.empty();
			swf.setHTML('<embed src="'+this.swfLocation+'" FlashVars="clipboard='+escape(text)+'" width="0" height="0" type="application/x-shockwave-flash"></embed>');
		}
	}
};
/* do not edit below this line */	 
/* Section: Change Log 

$Source: /cvs/main/flatfile/html/rb/js/global/cnet.global.framework/common/js.widgets/clipboard.js,v $
$Log: clipboard.js,v $
Revision 1.2  2007/05/16 21:09:26  newtona
fixed element reference in clipboard (added $())

Revision 1.1  2007/05/16 20:09:41  newtona
adding new js files to redball.common.full
product.picker.js now has no picklets; these are in the implementations/picklets directory
ProductPicker now detects if there is no doctyp and, if not, sets the position of the picker to be fixed (no IE6 support)
small docs update in element.cnet.js
added new picklet: CNETProductPicker_PricePath
added new picklet: NewsStoryPicker_Path
new file: clipboard.js (allows you to insert text into the OS clipboard)
new file: html.table.js (automates building html tables)
new file: element.forms.js (for managing text inputs - get selected text information, insert content around selection, etc.)
*/
</script>
</head>
<body>
<form action="" method="post">
<?php for ($i = 1; $i < 6; $i++) {?>
<p>照片<?php echo $i; ?>：<input type="text" name="photo[]" value="<?php if(isset($_POST['photo'][$i])) echo $_POST['photo'][$i] ?>" /></p>
<?php } ?>
<p>宝贝描述：<textarea name="desc" style="border: 1px solid;" cols="100" rows="10"><?php if(isset($_POST['desc'])) echo $_POST['desc'] ?></textarea></p>
<p><input type="submit" value="提交" /></p>
<p><input type="button" value="复制代码" onclick="Clipboard.copyFromElement('tplcode');"/></p>
</form>

<?php
error_reporting(E_ALL);
if ($_POST) {
    $pc = '';
    foreach ($_POST['photo'] as $p) {
        if (!empty($p))
            $pc .= '<P align=center><IMG style="BORDER-RIGHT: #000000 0px solid; BORDER-TOP: #000000 0px solid; BORDER-LEFT: #000000 0px solid; BORDER-BOTTOM: #000000 0px solid" src="'. $p .'" align=absMiddle></P>';
    }
    $dc = '';
    $desc = explode("\r\n", $_POST['desc']);
    foreach ($desc as $d) {
        if (!empty($d))
            $dc .= '<P align=center><FONT color=#ff6699>◎' . $d . '</FONT></P>';
    }
    $tpl = file_get_contents('tb-tpl-utf8.html');
    $tpl = str_replace('{$photo}', $pc, $tpl);
    $tpl = str_replace('{$desc}', $dc, $tpl);
}
if (isset($tpl)) echo $tpl;

?>
<p>代码：<textarea id="tplcode" onclick="copy(this);" name="code" style="border: 1px solid;" cols="100" rows="10"><?php if (isset($tpl)) echo $tpl ?></textarea></p>
</body>
</html>