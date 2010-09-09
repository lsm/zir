{header}



{[_users]}
{$testVar}
<li>{$id}</li>
<div>{$name}{$name}{$name}{$name}</div>
{[users_]}

{[_users]}
{$testVar}
<li>{$id}</li>
<div>{$name}{$name}{$name}{$name}</div>
{[users_]}

{[_users]}
{$testVar}
<li>{$id}</li>
<div>{$name}{$name}{$name}{$name}</div>
{[users_]}


<div class="regInput">
<li>Password:</li>
<input name="pass" type="password" id="pass"></div>

<div class="regInput">
<li>Confirm Password:</li>
<input name="pass_confirm" type="password" id="passConfirm"></div>

<div class="regInput">
<li>Email Address:</li>
<input name="email" type="text" id="email"
	value="<?php echo $this->email; ?>"></div>

<div class="regInput">
<li>Native Language:</li>
<select name="native_language">
{[_languages]}
	<option value="{$id}">{$name}</option>
{[languages_]}	
</select> (you can only choose 1 native language)</div>




{footer}
