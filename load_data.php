<?php
include_once 'search.php';

$database = new webarchive();
$db = $database->getConnection();

$search = new Search($db);

if(!empty($_POST['search']) && $_POST['search'] == 'search') {
	$search->tbl_uploads();
}

?>