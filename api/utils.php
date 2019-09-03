<?php 

include_once "globals.php";

function today_minus($day_count)
{
	return date('Y-m-d', time() - ($day_count * 24 * 60 * 60));
}

function timestamp_to_datetime($timestamp)
{
	$date_time = new DateTime();
	$date_time->setTimestamp($timestamp);
	return $date_time;
}

function date_to_datetime($date)
{
	return timestamp_to_datetime(strtotime($date));
}

function min_date()
{
	return "1000-01-01";
}

function get_level_days($level)
{
	switch ($level)
	{
		case 0: case 1: return $GLOBALS['level1_days'];
		case 2: return $GLOBALS['level2_days'];
		default: return $GLOBALS['level3_days'];
	}
}

function get_activity_multiplier_per_level($level)
{
	switch ($level)
	{
		case 0: return 0;
		case 1: return $GLOBALS['level1_multiplier'];
		case 2: return $GLOBALS['level2_multiplier'];
		default: return $GLOBALS['level3_multiplier'];
	}
}

function get_partner_multiplier($partner_days)
{
	switch (true)
	{
		case $partner_days > 3: return $GLOBALS['partner_4day_multiplier'];
		case $partner_days == 3: return $GLOBALS['partner_3day_multiplier'];
		case $partner_days == 2: return $GLOBALS['partner_2day_multiplier'];
		case $partner_days == 1: return $GLOBALS['partner_1day_multiplier'];
		default: return $GLOBALS['having_partner'];
	}
}

function lg($text)
{
	if ($GLOBALS['log']) { print($text . "|><|"); }
}

function lg_arr($data)
{	
	if ($GLOBALS['log']) 
	{
		$row_count = count($data);
		$to_log = "";

		for ($i = 0; $i < $row_count; $i++) 
		{
			$to_log .= ("Entry # " . $i);
			foreach ($data[$i] as $x => $x_value) { $to_log .= (" - " . $x . " => " . $x_value); }
			$to_log .= " | ";
		}
		lg($to_log);
	}	
}

function san($conn, $original, $filter)
{
	$cleaned = mysqli_real_escape_string($conn, $original);
	$cleaned = filter_var($cleaned, $filter);
	if ($cleaned != $original) { lg("Clean ERROR: " . $original . " is different than cleaned: " . $cleaned); }
	return $cleaned;
}

function san_email($conn, $original)
{
	return san($conn, $original, FILTER_SANITIZE_EMAIL);
}

function san_string($conn, $original)
{
	return san($conn, $original, FILTER_SANITIZE_STRING);
}

function san_int($conn, $original)
{
	return san($conn, $original, FILTER_SANITIZE_NUMBER_INT);
}

function san_float($conn, $original)
{
	return san($conn, $original, FILTER_SANITIZE_NUMBER_FLOAT);
}

function san_date($conn, $original)
{
	$date = date_parse(san_string($conn, $original));
	if (gettype($date) != "array") { lg("The provided date: " . $original . " is doesn't have a rigth FORMAT!"); return ""; }
	if (!checkdate($date['month'], $date['day'], $date['year']))  { lg("The provided date: " . $original . " is not a VALID date!"); return ""; }
	return ($date['year'] . '-' . $date['month'] . '-' . $date['day']);
}

function value_or_default($value, $default)
{
	return isset($value) ? $value : $default;
}

function refValues($arr)
{
    if (strnatcmp(phpversion(),'5.3') >= 0) // Reference is required for PHP 5.3+
    {
        $refs = array();
        foreach($arr as $key => $value) { $refs[$key] = &$arr[$key]; }
        return $refs;
    }
    return $arr;
}

?>