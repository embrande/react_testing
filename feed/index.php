<?php
	include_once('../connect_36109638.php');
	require_once('../api-test.php');
?>

<?php
    if(isset($_GET['name']) and trim($_GET['name'])) {
        $use_name = $_GET['name'];
    } else {
        $use_name = '';
    }    

	$last_name = preg_replace("/[^A-Za-z0-9 ]/", '', substr( $use_name, 0, strpos( $use_name, "-")) );
	$first_name = preg_replace("/[^A-Za-z0-9 ]/", '', substr( $use_name, strpos( $use_name, "-") + 1) );

?>

<?php
	
	$query_test = new query_set("jags_on_the_road");
	$query_test->json_keys_column_names(array(
		"counselor-id"=>"admissions_counselor_ID",
		"counselor-name"=>array("admissions_counselor_ID_last_name", array("-"), "admissions_counselor_ID_first_name"),
		"event-date"=>"jags_on_the_road_event_date",
		"event-title"=>"jags_on_the_road_name",
		"event-start-time"=>"jags_on_the_road_start_time",
		"event-end-time"=>"jags_on_the_road_end_time",
	));
	$query_test->url_filter("admissions_counselor.first_name", $first_name, true);
	$query_test->url_filter("admissions_counselor.last_name", $last_name, true);
	$query = $query_test->query_data();
	$query_test->query_this($dbh, $query);
	
?>

<?
	include_once('../connect_close_36109638.php');
?>