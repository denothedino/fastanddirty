<?php

### CONFIG ###

# *** insert your token ***
define('TOKEN', '$2y$15$6RDo5OQXGv1wTdWMoLopGu51SMiWKlR06tTxYCzkEtkk1I9uqSWpK');
$newsIds = [
  'server' => 4,
  'homepage' => 5,
  'team' => 7
];

### CONFIG END ###

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, X-Requested-With");


# *** simple authenticaion via token ***
if ($_GET['_token'] == TOKEN) {
  require_once __DIR__ . '/config.inc.php';
  
  try {
    $dbh = new \PDO('mysql:host='. $dbHost .';dbname='. $dbName, $dbUser, $dbPassword);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  } catch(PDOException $e) {
    throw new dbException($e);
    exit();
  }
  
  # *** return all news GET /news ***
  if ($_GET['_news'] == '') {
    $query = "
      SELECT p.username, topic, p.time, p.message, t.boardID 
      FROM wbb1_post AS p 
      INNER JOIN wbb1_thread AS t 
      ON t.threadID = p.threadID 
      WHERE t.boardID = ".$newsIds['server']." 
      OR t.boardID = ".$newsIds['homepage']." 
      OR t.boardID = ".$newsIds['team']."
      ORDER BY p.time DESC
      LIMIT 10"
    ;

    $stmt = $dbh->prepare($query);
    $stmt->execute();
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($res, JSON_FORCE_OBJECT);

  # *** return news by boardID GET /news/{id}
  } elseif ($_GET['_news'] != '' && in_array($_GET['_news'], $newsIds)) {
    $query = "
      SELECT p.username, topic, p.time, p.message, t.boardID
      FROM wbb1_post AS p
      INNER JOIN wbb1_thread AS t
      ON t.threadID = p.threadID 
      WHERE t.boardID = ?
      ORDER BY p.time DESC
      LIMIT 10;
    ";
    $stmt = $dbh->prepare($query);
    $stmt->bindParam(1, $_GET['_news']);
    $stmt->execute();
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC); 
    echo json_encode($res, JSON_FORCE_OBJECT);
    
  # *** return not found ***
  } else {
    header("HTTP/1.1 404 Not Found");
  }

  # *** not found ***
  $_GET['_news'] ??= header("HTTP/1.1 404 Not Found");
  exit();
} else {
  header("HTTP/1.1 401 Authorization Required");
  $json = [
    'message' => 'Unauthorized',
  ];
  echo json_encode($json);
  exit();
}
