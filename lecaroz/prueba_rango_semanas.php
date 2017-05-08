<?php

echo "<pre>";

echo date ('W', strtotime('1 january 2015')) . "\n";
echo date ('W', strtotime('1 january 2016')) . "\n";

echo date('d/m/Y W', strtotime('january 2015 first saturday')) . "\n";
echo date('d/m/Y W', strtotime('january 2016 first saturday')) . "\n";

for ($i = 1; $i <= 60; $i++)
{
	if (date('Y', strtotime('next saturday - ' . $i . ' week + 6 days')) > date('Y', strtotime('next saturday - ' . $i . ' week')))
	{
		echo "\n[{$i}]";
		echo "\nAÑO " . date('Y', strtotime('next saturday - ' . $i . ' week')) . " | " . date('Y', strtotime('next saturday - ' . $i . ' week + 6 days'));
		echo "\nPERIODO 01/01/" . date('Y', strtotime('next saturday - ' . $i . ' week + 6 days')) . '|' . date('d/m/Y', strtotime('next saturday - ' . $i . ' week + 6 days'));
		echo "\nSEMANA PHP " . date('W', strtotime('next saturday - ' . $i . ' week + 6 days'));
		echo "\nSEMANA CALCULADA 1";
		echo "\n----------------------------------------------------------------------";

		echo "\n[{$i}]";
		echo "\nAÑO " . date('Y', strtotime('next saturday - ' . $i . ' week')) . " | " . date('Y', strtotime('next saturday - ' . $i . ' week + 6 days'));
		echo "\nPERIODO " . date('d/m/Y', strtotime('next saturday - ' . $i . ' week')) . '|31/12/' . date('Y', strtotime('next saturday - ' . $i . ' week'));
		echo "\nSEMANA PHP " . date('W', strtotime('next saturday - ' . $i . ' week'));
		echo "\nSEMANA CALCULADA 53";
		echo "\n----------------------------------------------------------------------";
	}
	else
	{
		$year = date('Y', strtotime('next saturday - ' . $i . ' week'));

		$semana_extra = 0;

		if (date('Y', strtotime("january {$year} first saturday")) > date('Y', strtotime("january {$year} first saturday - 6 days")) && date('W', strtotime("january {$year} first saturday")) > 1)
		{
			$semana_extra = 1;
		}

		echo "\n[{$i}] NEXT SATURDAY - {$i} WEEK | NEXT SATURDAY - {$i} WEEK + 6 DAYS";
		echo "\nAÑO " . date('Y', strtotime('next saturday - ' . $i . ' week')) . " | " . date('Y', strtotime('next saturday - ' . $i . ' week + 6 days'));
		echo "\nPERIODO " . date('d/m/Y', strtotime('next saturday - ' . $i . ' week')) . '|' . date('d/m/Y', strtotime('next saturday - ' . $i . ' week + 6 days'));
		echo "\nSEMANA PHP " . date('W', strtotime('next saturday - ' . $i . ' week + 6 days'));
		echo "\nSEMANA CALCULADA " . (date('W', strtotime('next saturday - ' . $i . ' week + 6 days')) + $semana_extra);
		echo "\n----------------------------------------------------------------------";
	}
}

echo "</pre>";
