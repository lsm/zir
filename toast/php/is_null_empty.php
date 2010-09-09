<?php

function isnull($x)
{
	for ($i=0;$i<1000;$i++)
	{
		if (!is_null($i)) echo $x;
	}
}

function isempty($x)
{
	for ($i=0;$i<1000;$i++)
	{
		if (!empty($i)) echo $x;
	}
}

$z = 1;

isnull($z);
isempty($z);