<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_xclick">
<input type="hidden" name="business" value="attimee@gmail.com">
<input type="hidden" name="item_name" 
value="Test item_name max 127 char">
<input type="hidden" name="item_number" 
value="<?php echo time();?>">
<input type="hidden" name="amount" value="1.00">
<input type="hidden" name="no_shipping" value="2">
<input type="hidden" name="no_note" value="1">
<input type="hidden" name="currency_code" value="USD">
<input type="hidden" name="bn" value="IC_Sample">
<input type="image" src="https://www.paypal.com/
en_US/i/btn/x-click-but23.gif" 
name="submit" alt="Make payments with payPal - it's fast, 
	free and secure!">
	<img alt="" 
	src="https://www.paypal.com/en_US/i/scr/pixel.gif" 
	width="1" height="1">
	</form>
