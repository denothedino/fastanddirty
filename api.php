<?php

# *** NEWS API < DIRTY SOLUTION > ***

require_once __DIR__ . '/config.inc.php';
$apiKey = '$2y$12$a4hUvAdhI/y4Ws4t1HhBZuW1PQ4K1bVqCd8RigfgsMH.rKC.AxNzm';

try {
	$dbh = new PDO('mysql:host='.$dbHost.';dbname='.$dbName, $dbUser, $dbPassword);
} catch(PDOException $e) {
	die('<pre>Error: '. $e->getMessage());
}

$valid = $_GET['key'];

$valid ??= die();

if ($valid === $apiKey) {
	$sqlQuery = "SELECT p.* FROM wbb1_post AS p JOIN wbb1_thread AS t ON p.threadID = t.threadID WHERE t.boardID = 4";	
	$stmt = $dbh->prepare($sqlQuery);	
	$stmt->execute();	
	$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
	header('Content-Type: application/json');
	echo json_encode($res);
} else {
	die('Come on die.');
}
