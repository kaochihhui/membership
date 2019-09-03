<?php 

include_once "utils.php";

// Execute a SQL query and return true if at least one record was affected, false otherwise
function int_run_sql($conn, $sql, $types, $params)
{
	$stmt = $conn->prepare($sql);
    $result = int_sql($stmt, $sql, $types, $params);
	if (gettype($result) == "object") { $stmt->close(); return true; }
	else { return $result; }
}

function int_sql($stmt, $sql, $types, $params)
{
	if (gettype($stmt) != "object") { lg("ERROR: The QUERY " . $sql . " with params " . implode(" ", $params) . " couldn't PREPARE!"); return false; }
	
	// Using reflection to bind an unknow number of parameters
	array_unshift($params, $types); 
	$ref = new ReflectionClass('mysqli_stmt'); 
	$method = $ref->getMethod("bind_param"); 
	$binding_succesful = $method->invokeArgs($stmt, refValues($params)); 	

	if (!$binding_succesful) { lg("ERROR: The QUERY " . $sql . " with params " . implode(" ", $params) . " couldn't BIND!"); return false; }
	if (!$stmt->execute()) { lg("ERROR: The QUERY " . $sql . " with params " . implode(" ", $params) . " couldn't EXECUTE!"); return false; }
	if ($stmt->affected_rows == 0) { lg("The QUERY " . $sql . " with params " . implode(" ", $params) . " didn't do anything!"); return false; }

    $result = $stmt->get_result();
    if (gettype($result) == "object") { return $result; }
    else { return ($stmt->errno == 0); }
}

// Execute a SQL query and return an array with the result if there are any, an empty array otherwise
function int_get_sql_result($conn, $sql, $types, $params)
{
	$data = array();	

	$stmt = $conn->prepare($sql);
	$result = int_sql($stmt, $sql, $types, $params);
	
	if (gettype($result) != "object") { lg("ERROR: The QUERY " . $sql . " with params " . implode(" ", $params) . " FAILED!"); return $data; }	
	
	// Check if the query returns rows
	$row_count = $result->num_rows;
	if ($row_count == 0) { lg("No results from the query " . $sql . " !"); return $data; }
	else { lg("Number of rows: " . $row_count); }
	
	// Put the data in one array
	while ($row = $result->fetch_assoc()) { array_push($data, $row); }
	lg_arr($data);

	$stmt->close();

	// Return the array with the data from the query
	return $data;	
}

function sqls_user_check($conn, $user_id, $user_pass)
{
	$types = 'ss';
	$params = array(
		san_email($conn, $user_id),
		san_string($conn, $user_pass));

	$sql = "SELECT tbl_user.timestamp 
		FROM tbl_user
		WHERE (((tbl_user.email) = ?) 
		AND ((tbl_user.password) = ?))";

	return int_get_sql_result($conn, $sql, $types, $params);
}

function int_sqls_user_exists($conn, $user_id)
{
	$types = 's';
	$params = array(san_email($conn, $user_id));

	$sql = "SELECT tbl_user.email 
		FROM tbl_user
		WHERE (((tbl_user.email) = ?))";

    $rows = int_get_sql_result($conn, $sql, $types, $params);
    return (count($rows) > 0);
}

function int_sqli_register_new_member($conn, $user_email, $user_pass)
{
	$types = 'sss';
	$params = array(
        san_email($conn, $user_email), 
        san_string($conn, $user_pass),
		today_minus(0));
    
    $sql = "INSERT INTO tbl_user (email, password, name, surname, city, country, membership_date, 
            profile_picture_id, purchase_id, gmt_difference, processed_cf) 
        VALUES (?, ?, default, default, default, default, ?,
            default, '700', default, '2')";

    return int_run_sql($conn, $sql, $types, $params);
}

function int_sqls_user_name($conn, $user_id)
{
	$types = 's';
	$params = array(san_email($conn, $user_id));

	$sql = "SELECT tbl_user.name 
		FROM tbl_user
		WHERE (((tbl_user.email) = ?))";

	return int_get_sql_result($conn, $sql, $types, $params);
}

function int_sqls_user_pass($conn, $user_id)
{
	$types = 's';
	$params = array(san_email($conn, $user_id));

	$sql = "SELECT tbl_user.password 
		FROM tbl_user
		WHERE (((tbl_user.email) = ?))";

	return int_get_sql_result($conn, $sql, $types, $params);
}

function sqls_accountability_core($conn, $user_id, $user_pass)
{
	$types = 'ss';
	$params = array(
		san_email($conn, $user_id), 
		san_string($conn, $user_pass));

	$sql = "SELECT tu.name, tu.surname, tu.partner_email, tu.activity_name, tu.activity_icon_id, tn.news_icon_id, 
		tn.news_title, tn.news, tn.news_url, tn.mobile_url, ti.icon_url, tup.picture_url, tus.level, tus.points, tus.days_in_a_row
		FROM tbl_user tu
		LEFT JOIN tbl_news tn ON tu.news_id = tn.news_id
		LEFT JOIN tbl_icons ti ON tu.activity_icon_id = ti.icon_id
		LEFT JOIN tbl_user_pictures tup ON tu.email = tup.email
		LEFT JOIN tbl_user_score tus  ON tu.email = tus.email
		WHERE (((tu.email) = ?) 
		AND ((tu.password) = ?))";

	return int_get_sql_result($conn, $sql, $types, $params);
}

function sqls_user_check_report_learn_video($conn, $user_id, $user_pass, $learn_id)
{
	$types = 'ssi';
	$params = array(
		san_email($conn, $user_id), 
		san_string($conn, $user_pass),
		san_int($conn, $learn_id));

	$sql = "SELECT tbl_user_score_detail.mama_learn_id 
		FROM tbl_user INNER JOIN tbl_user_score_detail ON tbl_user.email = tbl_user_score_detail.email
		WHERE (((tbl_user.email) = ?) 
		AND ((tbl_user.password) = ?)
		AND ((tbl_user_score_detail.mama_learn_id) = ?))";

	return int_get_sql_result($conn, $sql, $types, $params);
}

function sqls_user_last_learn_video_watched($conn, $user_id, $user_pass)
{
	$types = 'ss';
	$params = array(
		san_email($conn, $user_id), 
		san_string($conn, $user_pass));

	$sql = "SELECT tbl_user_score_detail.mama_learn_id 
		FROM tbl_user INNER JOIN tbl_user_score_detail ON tbl_user.email = tbl_user_score_detail.email
		WHERE (((tbl_user.email) = ?) 
		AND ((tbl_user.password) = ?))
        ORDER BY tbl_user_score_detail.mama_learn_id DESC
        LIMIT 1";

	return int_get_sql_result($conn, $sql, $types, $params);
}

function sqls_user_last_week_learn_video_watched($conn, $user_id)
{
	$types = 's';
	$params = array(san_email($conn, $user_id));

	$sql = "SELECT tbl_mama_videos.locked AS last_week_watched
		FROM tbl_user_score_detail
		INNER JOIN tbl_mama_videos ON tbl_mama_videos.mama_vframe_id = tbl_user_score_detail.mama_learn_id
		WHERE tbl_user_score_detail.email = ?
		ORDER BY tbl_mama_videos.locked DESC";

	return int_get_sql_result($conn, $sql, $types, $params);
}

function sqls_locked_video($conn, $video_id)
{
    $types = 's';
    $params = array(san_int($conn, $video_id));

    $sql = "SELECT * FROM tbl_mama_videos tmv 
        WHERE tmv.locked >= 1 AND tmv.mama_vframe_id = ? 
        ORDER BY tmv.locked, tmv.mama_vframe_id";
    
    return int_get_sql_result($conn, $sql, $types, $params);
}

function sqls_week_reminded($conn, $user_id, $user_pass)
{
	$types = 'ss';
	$params = array(
		san_email($conn, $user_id), 
		san_string($conn, $user_pass));

	$sql = "SELECT tbl_user.week_reminded 
		FROM tbl_user
		WHERE (((tbl_user.email) = ?) 
		AND ((tbl_user.password) = ?))";

	return int_get_sql_result($conn, $sql, $types, $params);
}

function sqlu_week_reminded($conn, $user_id, $user_pass, $week)
{
	$types = 'iss';
	$params = array(
		san_int($conn, $week),
		san_email($conn, $user_id), 
		san_string($conn, $user_pass));

	$sql = "UPDATE tbl_user
		SET tbl_user.week_reminded = ?
		WHERE (((tbl_user.email) = ?) 
		AND ((tbl_user.password) = ?))";

	return int_run_sql($conn, $sql, $types, $params);
}

function sqlu_user_password($conn, $user_id, $user_pass)
{
	$types = 'ss';
	$params = array(
        san_string($conn, $user_pass),
		san_email($conn, $user_id));

	$sql = "UPDATE tbl_user
		SET tbl_user.password = ?
		WHERE ((tbl_user.email) = ?)";

	return int_run_sql($conn, $sql, $types, $params);
}

function sqlu_user_timestamp($conn, $user_id, $user_pass)
{
	$types = 'sss';
	$params = array(
		today_minus(0),
		san_email($conn, $user_id), 
		san_string($conn, $user_pass));

	$sql = "UPDATE tbl_user
		SET tbl_user.timestamp = ?
		WHERE (((tbl_user.email) = ?) 
		AND ((tbl_user.password) = ?))";

	return int_run_sql($conn, $sql, $types, $params);
}

function sqls_user_all($conn, $user_id)
{
	$types = 's';
	$params = array(san_email($conn, $user_id));

	$sql = "SELECT tbl_user.* 
		FROM tbl_user
		WHERE tbl_user.email = ?";

	return int_get_sql_result($conn, $sql, $types, $params);
}

function sqls_user_learn_details($conn, $user_id, $user_pass)
{
	$types = 'sss';
	$params = array(
		san_email($conn, $user_id), 
		san_string($conn, $user_pass),
		min_date());

	$sql = "SELECT tbl_user.membership_date, tbl_user.membership_type
		FROM tbl_user
		WHERE (((tbl_user.email) = ?) 
		AND ((tbl_user.password) = ?) 
		AND ((tbl_user.membership_date) > ?))";

	return int_get_sql_result($conn, $sql, $types, $params);
}

function sqls_user_subscribed($conn, $user_id)
{
	$types = 's';
	$params = array(san_email($conn, $user_id));

	$sql = "SELECT tbl_user.subscribed
		FROM tbl_user
		WHERE (((tbl_user.email) = ?) 
		AND ((tbl_user.subscribed) > 0))";

	return int_get_sql_result($conn, $sql, $types, $params);
}

function sqls_membership_type($conn, $user_email)
{
	$types = 's';
	$params = array(san_email($conn, $user_email));

	$sql = "SELECT tbl_user.membership_type
		FROM tbl_user
		WHERE tbl_user.email = ?";

	return int_get_sql_result($conn, $sql, $types, $params);
}

function sqls_user_subscription_status($conn, $user_id)
{
    $types = 's';
    $params = array(san_email($conn, $user_id));

    $sql = "SELECT tbl_user.subscribed, tbl_user.subscription_date
        FROM tbl_user
        WHERE ((tbl_user.email) = ?)";

    return int_get_sql_result($conn, $sql, $types, $params);
}

function sqli_user_subscribe($conn, $user_id, $user_name)
{
    $types = 'sss';
    $params = array(
        san_email($conn, $user_id),
		san_string($conn, $user_name),
        today_minus(0));

    $sql = "INSERT INTO `tbl_user` (`email`, `password`, `name`, `surname`, `city`, `country`, `timestamp`, `partner_email`, 
            `activity_name`, `activity_icon_id`, `profile_picture_id`, `mama_diario_id`, `news_id`, `subscribed`, `subscription_date`, 
            `membership_date`, `week_reminded`, `gmt_difference`, `purchase_id`, `processed_cf`) 
        VALUES (?, default, ?, default, default, default, default, default, 
            default, default, default, default, default, '1', ?, 
            default, default, default, default, default)";

    return int_run_sql($conn, $sql, $types, $params);
}

function sqlu_user_subscribe($conn, $user_id)
{
	$types = 'ss';
    $params = array(
        today_minus(0),
        san_email($conn, $user_id));

	$sql = "UPDATE `tbl_user` 
		SET `subscribed` = 1, `subscription_date` = ? 
		WHERE ((tbl_user.email) = ?)";

	return int_run_sql($conn, $sql, $types, $params);
}

function int_sqlu_user_unsubscribe($conn, $user_id)
{
	$types = 's';
	$params = array(san_email($conn, $user_id));

	$sql = "UPDATE `tbl_user` 
		SET `subscribed` = 0 
		WHERE (((tbl_user.email) = ?)";

	return int_run_sql($conn, $sql, $types, $params);
}

function sqls_user_details($conn, $user_id, $user_pass)
{
	$types = 'ss';
	$params = array(
		san_email($conn, $user_id), 
		san_string($conn, $user_pass));

	$sql = "SELECT tu.name, tu.surname, tu.timestamp, tu.partner_email, tu.config_url, tu.learning_url, tu.shop_url, tu.activity_name, 
		tu.profile_picture_id, tu.activity_icon_id, tu.mama_diario_id, tu.news_id, tu.subscribed, tu.membership_date
		FROM tbl_user tu
		WHERE (((tu.email) = ?) 
		AND ((tu.password) = ?))";

	return int_get_sql_result($conn, $sql, $types, $params);
}

function int_sqls_user_score($conn, $user_id)
{
	$types = 's';
	$params = array(
		san_email($conn, $user_id));

	$sql = "SELECT tus.level, tus.max_level_reached, tus.points, tus.last_firm_update, tus.days_in_a_row, tus.timestamp 	
		FROM tbl_user_score tus
		WHERE tus.email = ?";

	return int_get_sql_result($conn, $sql, $types, $params);
}

function int_sqli_initialize_user_score($conn, $user_id, $date, $points)
{
	$types = 'sis';
	$params = array(
		san_email($conn, $user_id), 
        san_int($conn, $points),
        san_date($conn, $date));

	$sql = "INSERT INTO `tbl_user_score` (`email`, `level`, `points`, `days_in_a_row`, `last_firm_update`, `timestamp`) 
		VALUES (?, '0', ?, '0', ?, CURRENT_TIMESTAMP)";

	return int_run_sql($conn, $sql, $types, $params);
}

// function sqls_user_score_detail($conn, $user_id, $user_pass, $from_date)
// {
// 	$types = 'sss';
// 	$params = array(
// 		san_email($conn, $user_id), 
// 		san_string($conn, $user_pass),
// 		san_date($conn, $from_date));

// 	$sql = "SELECT tbl_user_score_detail.date, tbl_user_score_detail.score, tbl_user_score_detail.notes, 
// 		tbl_user_score_detail.earned, tbl_user_score_detail.mama_diario_id, tbl_user_score_detail.mama_learn_id, 
// 		tbl_user_score_detail.mama_vframe_id
// 		FROM tbl_user INNER JOIN tbl_user_score_detail ON tbl_user.email = tbl_user_score_detail.email
// 		WHERE (((tbl_user.email) = ?) 
// 		AND ((tbl_user.password) = ?) 
// 		AND ((tbl_user_score_detail.date) >= ?))
// 		ORDER BY tbl_user_score_detail.date ASC";

// 	return int_get_sql_result($conn, $sql, $types, $params);
// }

function sqls_user_score_detail_activity($conn, $user_id, $user_pass, $date)
{
	$types = 'sss';
	$params = array(
		san_email($conn, $user_id), 
		san_string($conn, $user_pass),
		san_date($conn, $date));

	$sql = "SELECT tusd.score, tusd.notes, tusd.activity_name, ti.icon_id, ti.icon_url
		FROM tbl_user tu 
		INNER JOIN tbl_user_score_detail tusd ON tu.email = tusd.email
		INNER JOIN tbl_icons ti ON tusd.activity_icon_id = ti.icon_id
		WHERE (((tu.email) = ?) 
		AND ((tu.password) = ?) 
		AND ((tusd.mama_diario_id) = -1) 
		AND ((tusd.mama_learn_id) = -1) 
		AND ((tusd.date) = ?))";

	return int_get_sql_result($conn, $sql, $types, $params);
}

function sqls_user_score_detail_activity_from_to($conn, $user_id, $user_pass, $from_date, $to_date)
{
	$types = 'ssss';
	$params = array(
		san_email($conn, $user_id), 
		san_string($conn, $user_pass),
		san_date($conn, $from_date),
		san_date($conn, $to_date));

	$sql = "SELECT tbl_user_score_detail.date, tbl_user_score_detail.score, tbl_user_score_detail.notes, 
		tbl_user_score_detail.earned, tbl_user_score_detail.mama_diario_id, tbl_user_score_detail.mama_learn_id 
		FROM tbl_user INNER JOIN tbl_user_score_detail ON tbl_user.email = tbl_user_score_detail.email
		WHERE (((tbl_user.email) = ?) 
		AND ((tbl_user.password) = ?) 
		AND ((tbl_user_score_detail.mama_diario_id) = -1) 
		AND ((tbl_user_score_detail.mama_learn_id) = -1) 
		AND ((tbl_user_score_detail.date) >= ?)
		AND ((tbl_user_score_detail.date) <= ?))
		ORDER BY tbl_user_score_detail.date DESC";

	return int_get_sql_result($conn, $sql, $types, $params);
}

function sqls_last_watched_daily_video($conn, $user_id)
{
	$types = 's';
	$params = array(san_email($conn, $user_id));

	$sql = "SELECT tbl_user_score_detail.date
		FROM tbl_user INNER JOIN tbl_user_score_detail ON tbl_user.email = tbl_user_score_detail.email
		WHERE (((tbl_user.email) = ?)
		AND ((tbl_user_score_detail.score) >= 0)
		AND ((tbl_user_score_detail.mama_diario_id) > 0))
		ORDER BY tbl_user_score_detail.date DESC
		LIMIT 1";

	return int_get_sql_result($conn, $sql, $types, $params);
}

function sqls_unwatched_daily_video($conn, $user_id)
{
	$types = 's';
	$params = array(san_email($conn, $user_id));

	$sql = "SELECT tbl_user_score_detail.date
		FROM tbl_user INNER JOIN tbl_user_score_detail ON tbl_user.email = tbl_user_score_detail.email
		WHERE (((tbl_user.email) = ?)
		AND ((tbl_user_score_detail.score) < 0)
		AND ((tbl_user_score_detail.mama_diario_id) > 0))";

	return int_get_sql_result($conn, $sql, $types, $params);
}

function sqld_unwatched_daily_video($conn, $user_id)
{
	$types = 's';
	$params = array(san_email($conn, $user_id));

	$sql = "DELETE FROM tbl_user_score_detail
		INNER JOIN tbl_user ON tbl_user.email = tbl_user_score_detail.email
		WHERE (((tbl_user.email) = ?)
		AND ((tbl_user_score_detail.score) < 0)
		AND ((tbl_user_score_detail.mama_diario_id) > 0))";

	return int_run_sql($conn, $sql, $types, $params);
}

function sqls_user_earned_on_daily_video($conn, $user_id, $user_pass)
{
	$types = 'sss';
	$params = array(
		san_email($conn, $user_id), 
		san_string($conn, $user_pass),
		today_minus(0));

	$sql = "SELECT tbl_user_score_detail.earned
		FROM tbl_user INNER JOIN tbl_user_score_detail ON tbl_user.email = tbl_user_score_detail.email
		WHERE (((tbl_user.email) = ?) 
		AND ((tbl_user.password) = ?) 
		AND ((tbl_user_score_detail.mama_diario_id) > 0)
		AND (tbl_user_score_detail.date) = ?)";

	return int_get_sql_result($conn, $sql, $types, $params);
}

function int_sqls_daily_reported($conn, $user_id, $date)
{
	$types = 'ss';
	$params = array(
		san_email($conn, $user_id), 
		san_date($conn, $date));

	$sql = "SELECT tbl_user_score_detail.mama_diario_id, tbl_user_score_detail.score
		FROM tbl_user INNER JOIN tbl_user_score_detail ON tbl_user.email = tbl_user_score_detail.email
		WHERE (((tbl_user.email) = ?) 
		AND ((tbl_user_score_detail.mama_diario_id) > 0)
		AND (tbl_user_score_detail.date) = ?)";

	return int_get_sql_result($conn, $sql, $types, $params);
}

function sqls_video_with_id($conn, $id)
{
    $types = 'i';
    $params = array(san_int($conn, $id));

    $sql = "SELECT * FROM tbl_mama_videos WHERE tbl_mama_videos.mama_vframe_id = ?";

    return int_get_sql_result($conn, $sql, $types, $params);
}

function sqls_video_with_webpage($conn, $webpage)
{
    $types = 's';
    $params = array(san_string($conn, $webpage));

    $sql = "SELECT * FROM tbl_mama_videos WHERE tbl_mama_videos.webpage = ?";

    return int_get_sql_result($conn, $sql, $types, $params);
}

function sqls_daily_video($conn, $date)
{
	$types = 's';
	$params = array(san_date($conn, $date));

	$sql = "SELECT *
		FROM tbl_mama_videos 
		WHERE tbl_mama_videos.`date` = ?";

	return int_get_sql_result($conn, $sql, $types, $params);
}

function sqls_daily_video_earned($conn, $user_id, $date)
{
	$types = 'ss';
	$params = array(
		san_email($conn, $user_id), 
        san_date($conn, $date));

	$sql = "SELECT tmv.mobile_url, tmv.mama_vframe_url, tmv.mama_description, tusd.earned
		FROM tbl_mama_videos tmv
		LEFT JOIN tbl_user_score_detail tusd ON tmv.mama_vframe_id = tusd.mama_diario_id 
		WHERE tusd.`email` = ? 
		AND tmv.`date` = ?";

	return int_get_sql_result($conn, $sql, $types, $params);
}

function sqli_new_video($conn, $id, $video_url, $thumbnail_url, $description, $date, $locked, $course, $module, $web_url, $mobile_url, $points)
{
    $types = 'issssissssi';
    $params = array(
        san_int($conn, $id),
        san_string($conn, $video_url),
        san_string($conn, $thumbnail_url),
        san_string($conn, $description),
        san_date($conn, $date),
        san_int($conn, $locked),
        san_string($conn, $course),
        san_string($conn, $module),
        san_string($conn, $web_url),
        san_string($conn, $mobile_url),
        san_int($conn, $points)
    );

    $sql = "INSERT INTO tbl_mama_videos (mama_vframe_id, mama_url, mama_vframe_url, mama_description, date, locked, course_name, module_name, webpage, mobile_url, mama_points) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"; 

    return int_run_sql($conn, $sql, $types, $params);
}

function sqlu_video($conn, $id, $video_url, $thumbnail_url, $description, $date, $locked, $course, $module, $web_url, $mobile_url, $points)
{
    $types = 'ssssissssii';
    $params = array(
        san_string($conn, $video_url),
        san_string($conn, $thumbnail_url),
        san_string($conn, $description),
        san_date($conn, $date),
        san_int($conn, $locked),
        san_string($conn, $course),
        san_string($conn, $module),
        san_string($conn, $web_url),
        san_string($conn, $mobile_url),
        san_int($conn, $points),
        san_int($conn, $id)
    );

    $sql = "UPDATE tbl_mama_videos
        SET mama_url = ?, mama_vframe_url = ?, mama_description = ?, date = ?, locked = ?, course_name = ?, module_name = ?, webpage = ?, mobile_url = ?, mama_points = ?
        WHERE tbl_mama_videos.mama_vframe_id = ?"; 

    return int_run_sql($conn, $sql, $types, $params);
}

function int_sqlu_earned_today($conn, $user_id, $earned)
{
	$types = 'iss';
	$params = array(
		san_int($conn, $earned),
		san_email($conn, $user_id), 
		today_minus(0));

	$sql = "UPDATE `tbl_user_score_detail` 
		SET `earned` = ? 
		WHERE `tbl_user_score_detail`.`email` = ? 
		AND `tbl_user_score_detail`.`date` = ?";

	return int_run_sql($conn, $sql, $types, $params);
}

function int_sqlu_daily_score($conn, $user_id, $date, $score)
{
	$types = 'iss';
	$params = array(
		san_int($conn, $score),
		san_email($conn, $user_id), 
		san_date($conn, $date));

	$sql = "UPDATE `tbl_user_score_detail` 
		SET `score` = ? 
		WHERE `tbl_user_score_detail`.`email` = ? AND `tbl_user_score_detail`.`date` = '$date' 
		AND `tbl_user_score_detail`.`mama_diario_id` > 0";

	return int_run_sql($conn, $sql, $types, $params);
}

// This is when a user clicks on the link, but is not yet reported on his app, so no points are yet generated
function int_sqli_user_report_daily_video_watched($conn, $user_id, $video_id, $date)
{
	$types = 'ssi';
	$params = array(
		san_email($conn, $user_id), 
		san_date($conn, $date),
		san_int($conn, $video_id));

	$sql = "INSERT INTO `tbl_user_score_detail` (`email`, `date`, `score`, `notes`, `earned`, `mama_diario_id`, `timestamp`) 
		VALUES (?, ?, '0', NULL, '0', ?, CURRENT_TIMESTAMP)";

	return int_run_sql($conn, $sql, $types, $params);
}

function int_sqli_user_report_daily_video_not_watched($conn, $user_id, $video_id, $date)
{
	$types = 'ssi';
	$params = array(
		san_email($conn, $user_id), 
		san_date($conn, $date),
		san_int($conn, $video_id));

	$sql = "INSERT INTO `tbl_user_score_detail` (`email`, `date`, `score`, `notes`, `earned`, `mama_diario_id`, `timestamp`) 
		VALUES (?, ?, '-1', NULL, '0', ?, CURRENT_TIMESTAMP)";

	return int_run_sql($conn, $sql, $types, $params);
}

function int_sqli_user_score_detail_daily_video($conn, $user_id, $video_id, $points)
{
	$types = 'ssii';
	$params = array(
		san_email($conn, $user_id),
		today_minus(0), 
		san_int($conn, $points),
		san_int($conn, $video_id));

	$sql = "INSERT INTO `tbl_user_score_detail` (`email`, `date`, `score`, `notes`, `earned`, `mama_diario_id`, `timestamp`) 
		VALUES (?, ?, '0', NULL, ?, ?, CURRENT_TIMESTAMP)";

	if (!int_run_sql($conn, $sql, $types, $params)) { lg("ERROR: Couldn't add points for reporting the daily video " . $video_id . " for the user " . $user_id); return false; }	

	// Update also the user score table!
	return int_sqlu_user_score_add_points($conn, $user_id, $points);
}

function int_sqli_activity_score_detail($conn, $user_id, $date, $name, $icon_id, $score, $points)
{
	$types = 'ssisii';
	$params = array(
		san_email($conn, $user_id),
		san_date($conn, $date), 
		san_int($conn, $icon_id),
		san_string($conn, $name),
		san_int($conn, $score),
		san_int($conn, $points));

	$sql = "INSERT INTO `tbl_user_score_detail` (`email`, `date`, `activity_icon_id`, `activity_name`, `score`, `notes`, `earned`, `mama_diario_id`, `mama_learn_id`, `timestamp`) 
		VALUES (?, ?, ?, ?, ?, NULL, ?, '-1', '-1', CURRENT_TIMESTAMP)";

	if (!int_run_sql($conn, $sql, $types, $params)) { lg("ERROR: Couldn't add points for reporting the activity of " . $date . " for the user " . $user_id); return false; }	

	// Update also the user score table!
	return int_sqlu_user_score_add_points($conn, $user_id, $points);
}

function int_sqli_learn_score($conn, $user_id, $learn_video_id, $points)
{
	$types = 'ssii';
	$params = array(
		san_email($conn, $user_id), 
		today_minus(0),
		san_int($conn, $points),
		san_int($conn, $learn_video_id));

	$sql = "INSERT INTO `tbl_user_score_detail` (`email`, `date`, `score`, `notes`, `earned`, `mama_learn_id`, `timestamp`) 
		VALUES (?, ?, '0', NULL, ?, ?, CURRENT_TIMESTAMP)";

	if (!int_run_sql($conn, $sql, $types, $params)) { lg("ERROR: Couldn't add points for reporting the learning of the week " . $learn_video_id . " for the user " . $user_id); return false; }	

	// Update also the user score table!
	return int_sqlu_user_score_add_points($conn, $user_id, $points);
}

function int_sqlu_mark_daily_video_as_accessed($conn, $user_id, $video_id)
{
    $types = 'ss';
    $params = array(
        san_email($conn, $user_id),
        san_int($conn, $video_id));

    $sql = "UPDATE `tbl_user_score_detail` SET `earned` = '-1' WHERE `email` = ? AND `mama_diario_id` = ?";

    return int_run_sql($conn, $sql, $types, $params);
}

function int_sqlu_activity_notes($conn, $user_id, $date, $notes)
{
	$types = 'sss';
	$params = array(
		san_string($conn, $notes),
		san_email($conn, $user_id), 
		san_date($conn, $date));

	$sql = "UPDATE `tbl_user_score_detail` 
		SET `notes` = ? 
		WHERE `tbl_user_score_detail`.`email` = ? 
		AND `tbl_user_score_detail`.`date` = ? 
		AND `tbl_user_score_detail`.`mama_diario_id` = -1 
		AND `tbl_user_score_detail`.`mama_learn_id` = -1";

	return int_run_sql($conn, $sql, $types, $params);
}

function int_sqlu_activity_score($conn, $user_id, $date, $score)
{
	$types = 'iss';
	$params = array(
		san_int($conn, $score), 
		san_email($conn, $user_id), 
		san_date($conn, $date));

	$sql = "UPDATE `tbl_user_score_detail` 
		SET `score` = ? 
		WHERE `tbl_user_score_detail`.`email` = ? 
		AND `tbl_user_score_detail`.`date` = ? 
		AND `tbl_user_score_detail`.`mama_diario_id` = -1 
		AND `tbl_user_score_detail`.`mama_learn_id` = -1";

	return int_run_sql($conn, $sql, $types, $params);
}

function sqls_user_resource_detail($conn, $email, $resource_id)
{
    $types = 'si';
    $params = array(
        san_email($conn, $email),
        san_int($conn, $resource_id));

    $sql = "SELECT * FROM `tbl_user_resources` tur WHERE tur.email = ? AND tur.resource_id = ?";

    return int_get_sql_result($conn, $sql, $types, $params);
}

function sqls_user_resources($conn, $user_id)
{
	$types = 's';
	$params = array(san_email($conn, $user_id));

	$sql = "SELECT *
		FROM tbl_user_resources tur
		INNER JOIN tbl_resources tr ON tur.resource_id = tr.resource_id
		WHERE tur.email = ?
		ORDER BY tur.resource_id";

	return int_get_sql_result($conn, $sql, $types, $params);
}

function int_sqls_user_can_purchase($conn, $user_id)
{
	$types = 'ss';
	$params = array(
        san_email($conn, $user_id),
        san_email($conn, $user_id));

	$sql = "SELECT *
		FROM (
			SELECT tr.resource_id, tr.resource_name, tr.resource_icon_id, tr.resource_description, tr.resource_url, tr.resource_points, tr.required_level
			FROM tbl_resources tr
			WHERE tr.resource_id 
			NOT IN (
    		    SELECT tur.resource_id
				FROM tbl_user_resources tur
				WHERE tur.email = ?
			)
		) not_owned_resources
		JOIN tbl_user_score tus
		WHERE tus.email = ? 
		    AND not_owned_resources.required_level <= tus.`level` 
		    AND not_owned_resources.resource_points <= tus.points
		ORDER BY not_owned_resources.resource_id ASC";	

	return int_get_sql_result($conn, $sql, $types, $params);
}

function int_sqls_user_cant_purchase($conn, $user_id)
{
	$types = 'ss';
	$params = array(
        san_email($conn, $user_id),
        san_email($conn, $user_id));

	$sql = "SELECT *
		FROM (
			SELECT tr.resource_id, tr.resource_name, tr.resource_icon_id, tr.resource_description, tr.resource_url, tr.resource_points, tr.required_level
			FROM tbl_resources tr
			WHERE tr.resource_id 
			NOT IN (
    		    SELECT tur.resource_id
				FROM tbl_user_resources tur
				WHERE tur.email = ?
			)
		) not_owned_resources
		JOIN tbl_user_score tus
		WHERE tus.email = ? 
		    AND (not_owned_resources.required_level > tus.`level` 
			OR not_owned_resources.resource_points > tus.points)
		ORDER BY not_owned_resources.resource_id ASC";	

	return int_get_sql_result($conn, $sql, $types, $params);
}

function int_sqlu_user_score_level($conn, $user_id, $level, $max_level_reached, $days_in_a_row)
{
	$types = 'iiis';
	$params = array(
		san_int($conn, $level),
		san_int($conn, $max_level_reached),
        san_int($conn, $days_in_a_row),
		san_email($conn, $user_id));

	$sql = "UPDATE `tbl_user_score` 
		SET `level` = ?, `max_level_reached` = ?, `days_in_a_row` = ? 
		WHERE `tbl_user_score`.`email` = ?";

	return int_run_sql($conn, $sql, $types, $params);
}

function int_sqlu_user_last_firm_update($conn, $user_id, $date)
{
	$types = 'ss';
	$params = array(
        san_date($conn, $date),
		san_email($conn, $user_id));

	$sql = "UPDATE `tbl_user_score` 
		SET `last_firm_update` = ? 
		WHERE `tbl_user_score`.`email` = ?";

	return int_run_sql($conn, $sql, $types, $params);
}

function int_sqlu_user_score_add_points($conn, $user_id, $points_to_add)
{
    $rows = int_sqls_user_score($conn, $user_id);
    if (count($rows) == 0) 
    { 
        lg("No score yet for the user " . $user_id);
        return int_sqli_initialize_user_score($conn, $user_id, min_date(), $points_to_add);
    }
    else
    {
        $types = 'is';
        $params = array(
            san_int($conn, $points_to_add),
            san_email($conn, $user_id));
            
        $sql = "UPDATE `tbl_user_score` 
		    SET `points` = `points` + ? 
		    WHERE `tbl_user_score`.`email` = ?";

        return int_run_sql($conn, $sql, $types, $params);
    }
}

function int_sqlu_user_score_days_in_a_row($conn, $user_id, $days)
{
	$types = 'is';
	$params = array(
		san_int($conn, $days),
		san_email($conn, $user_id));

	$sql = "UPDATE `tbl_user_score` 
		SET `days_in_a_row` = ? 
		WHERE `tbl_user_score`.`email` = ?";

	return int_run_sql($conn, $sql, $types, $params);
}

function sqls_mama_videos_today($conn, $from_video_id)
{
	$types = 'is';
	$params = array(
		san_int($conn, $from_video_id),
		today_minus(0));

	$sql = "SELECT tbl_mama_videos.mama_vframe_id 
		FROM `tbl_mama_videos` 
		WHERE `mama_vframe_id` >= ? 
		AND `date` = ?";

	return int_get_sql_result($conn, $sql, $types, $params);
}

function sqls_learn_video_week($conn, $learn_id)
{
	$types = 'i';
	$params = array(san_int($conn, $learn_id));

	$sql = "SELECT tbl_mama_videos.locked AS learn_week
		FROM `tbl_mama_videos` 
		WHERE `mama_vframe_id` = ?";

	return int_get_sql_result($conn, $sql, $types, $params);
}

function sqls_max_learning_week($conn, $membership_type)
{
	$types = 'i';
	$params = array(san_int($conn, $membership_type));

	$sql = "SELECT tbl_mama_videos.locked
		FROM `tbl_mama_videos` 
        WHERE `membership_type` <= ?
        ORDER BY `locked` DESC
        LIMIT 1";

	return int_get_sql_result($conn, $sql, $types, $params);
}

function sqls_next_learning_videos($conn, $last_watched_id, $last_unlocked_week, $membership_type)
{
    $types = "iii";
    $params = array(
        san_int($conn, $last_unlocked_week),
        san_int($conn, $last_watched_id),
        san_int($conn, $membership_type)
    );

    $sql = "SELECT tmv.mama_vframe_id, tmv.mama_vframe_url, tmv.mobile_url, tmv.course_name, tmv.module_name, tmv.mama_description
        FROM `tbl_mama_videos` tmv
        WHERE `locked` <= ?
        AND `locked` > 0
        AND `mama_vframe_id` >= ?
        AND `membership_type` <= ?
        ORDER BY `mama_vframe_id` ASC
        LIMIT 2";
    
    return int_get_sql_result($conn, $sql, $types, $params);
}

function sqls_learn_video_detail($conn, $learn_week, $membership_type)
{
	$types = 'ii';
	$params = array(
        san_int($conn, $learn_week),
        san_int($conn, $membership_type)
    );

	$sql = "SELECT tmv.mama_vframe_id, tmv.mama_vframe_url, tmv.mobile_url, tmv.module_name, tmv.mama_description
		FROM `tbl_mama_videos` tmv
		WHERE `locked` = ?
        AND `membership_type` <= ?
		ORDER BY `mama_vframe_id` ASC
		LIMIT 1";

	return int_get_sql_result($conn, $sql, $types, $params);
}

function sqls_user_activity_icon($conn, $user_id, $user_pass)
{
	$types = 'ss';
	$params = array(
		san_email($conn, $user_id), 
		san_string($conn, $user_pass));

	$sql = "SELECT tbl_icons.icon_url, tbl_icons.timestamp
		FROM tbl_user INNER JOIN tbl_icons ON tbl_user.activity_icon_id = tbl_icons.icon_id
		WHERE (((tbl_user.email) = ?) 
		AND ((tbl_user.password) = ?))";

	return int_get_sql_result($conn, $sql, $types, $params);
}

function sqls_user_picture($conn, $user_id)
{
	$types = 's';
	$params = array(san_email($conn, $user_id));

	$sql = "SELECT tup.picture_id, tup.picture_url
		FROM tbl_user_pictures tup
		WHERE tup.email = ?";

	return int_get_sql_result($conn, $sql, $types, $params);
}

function sqli_user_picture($conn, $user_id, $picture_id, $picture_url)
{
    $types = 'iss';
    $params = array(
        san_int($conn, $picture_id),
        san_email($conn, $user_id),
        san_string($conn, $picture_url));

    $sql = "INSERT INTO `tbl_user_pictures` (`picture_id`,`email`, `picture_url`, `timestamp`) 
        VALUES (?, ?, ?, CURRENT_TIMESTAMP);";

    return int_run_sql($conn, $sql, $types, $params);
}

function sqlu_user_picture($conn, $user_id, $picture_url)
{
    $types = 'ss';
    $params = array(
        san_string($conn, $picture_url),
        san_email($conn, $user_id));

    $sql = "UPDATE tbl_user_pictures SET picture_url = ? WHERE email = ?";

    return int_run_sql($conn, $sql, $types, $params);
}

function int_sqls_partner_credentials($conn, $user_id, $user_pass)
{
	$types = 'ss';
	$params = array(
		san_email($conn, $user_id), 
		san_string($conn, $user_pass));

	$sql = "SELECT tbl_user_partner.email, tbl_user_partner.password
		FROM tbl_user INNER JOIN tbl_user AS tbl_user_partner ON tbl_user.partner_email = tbl_user_partner.email
		WHERE (((tbl_user.email) = ?) 
		AND ((tbl_user.password) = ?))";

	return int_get_sql_result($conn, $sql, $types, $params);
}

// function sqls_user_resource_ids($conn, $user_id, $user_pass)
// {
// 	$types = 'ss';
// 	$params = array(
// 		san_email($conn, $user_id), 
// 		san_string($conn, $user_pass));

// 	$sql = "SELECT tbl_user_resources.resource_id
// 		FROM tbl_user INNER JOIN tbl_user_resources ON tbl_user.email = tbl_user_resources.email
// 		WHERE (((tbl_user.email) = ?) 
// 		AND ((tbl_user.password) = ?))";

// 	return int_get_sql_result($conn, $sql, $types, $params);
// }

function sqls_user_news($conn, $user_id, $user_pass)
{
	$types = 'ss';
	$params = array(
		san_email($conn, $user_id), 
		san_string($conn, $user_pass));

	$sql = "SELECT tbl_news.news_icon_id, tbl_news.news, tbl_news.news_url
		FROM tbl_user INNER JOIN tbl_news ON tbl_user.news_id = tbl_news.news_id
		WHERE (((tbl_user.email) = ?) 
		AND ((tbl_user.password) = ?))";

	return int_get_sql_result($conn, $sql, $types, $params);
}

function sqlu_activity_icon($conn, $user_id, $user_pass, $icon_id)
{
	$types = 'iss';
	$params = array(
		san_int($conn, $icon_id),
		san_email($conn, $user_id), 
		san_string($conn, $user_pass));

	$sql = "UPDATE tbl_user
		SET tbl_user.activity_icon_id = ?
		WHERE (((tbl_user.email) = ?) 
		AND ((tbl_user.password) = ?))";

	return int_run_sql($conn, $sql, $types, $params);
}

function sqlu_picture_id($conn, $user_id, $picture_id)
{
	$types = 'iss';
	$params = array(
		san_int($conn, $picture_id), 
		san_email($conn, $user_id));

	$sql = "UPDATE tbl_user
		SET tbl_user.profile_picture_id = ?
		WHERE tbl_user.email = ?";

	return int_run_sql($conn, $sql, $types, $params);
}

// action_id = 100 means watched the webinar
// action_id = 200 means visited the deferred offer page
function int_sqls_offer_201908_status($conn, $user_id)
{
	$types = 's';
	$params = array(san_email($conn, $user_id));

	$sql = "SELECT tbl_offer_201908.action_id 
		FROM tbl_offer_201908
        WHERE (((tbl_offer_201908.email) = ?))
		ORDER BY `action_id` DESC
		LIMIT 1";

	return int_get_sql_result($conn, $sql, $types, $params);
}

function sqls_offer_201908_action($conn, $action_id, $date)
{
	$types = 'is';
	$params = array(
        san_int($conn, $action_id),
        san_date($conn, $date)
    );

	$sql = "SELECT *
		FROM tbl_user tu
		RIGHT JOIN tbl_offer_201908 to1908 ON tu.email = to1908.email
		WHERE to1908.action_id = ?
        AND to1908.date = ?
        AND to1908.notified = 0";

	return int_get_sql_result($conn, $sql, $types, $params);
}

function sqls_offer_201908_action_fixed_date($conn, $action_id)
{
	$types = 'i';
	$params = array(
        san_int($conn, $action_id)
    );

	$sql = "SELECT *
		FROM tbl_user tu
		RIGHT JOIN tbl_offer_201908 to1908 ON tu.email = to1908.email
		WHERE to1908.action_id = ?
        AND to1908.notified = 0";

	return int_get_sql_result($conn, $sql, $types, $params);
}

function sqlu_offer_201908_action_taken($conn, $email, $action_id)
{
	$types = 'si';
	$params = array(
        san_email($conn, $email),
        san_int($conn, $action_id)
    );

	$sql = "UPDATE tbl_offer_201908
		SET tbl_offer_201908.notified = 1
        WHERE tbl_offer_201908.email = ?
        AND tbl_offer_201908.action_id = ?";

	return int_run_sql($conn, $sql, $types, $params);
}

function sqli_offer_201908_new_action($conn, $email, $action_id, $date)
{
    $types = 'sis';
    $params = array(
        san_email($conn, $email),
        san_int($conn, $action_id),
        san_date($conn, $date));

    $sql = "INSERT INTO `tbl_offer_201908` (`email`,`action_id`, `date`, `notified`) 
        VALUES (?, ?, ?, 0);";

    return int_run_sql($conn, $sql, $types, $params);
}

function sqlu_user_offer_on($conn, $email, $date)
{
	$types = 'ss';
	$params = array(
        san_date($conn, $date),
        san_email($conn, $email)
    );

	$sql = "UPDATE tbl_user
		SET tbl_user.offer_on = ?
        WHERE tbl_user.email = ?";

	return int_run_sql($conn, $sql, $types, $params);
}

function sqlu_user_check_temp($conn, $email, $value)
{
	$types = 'is';
	$params = array(
        san_int($conn, $value),
        san_email($conn, $email)
    );

	$sql = "UPDATE tbl_user
		SET tbl_user.temp_check = ?
        WHERE tbl_user.email = ?";

	return int_run_sql($conn, $sql, $types, $params);
}

?>