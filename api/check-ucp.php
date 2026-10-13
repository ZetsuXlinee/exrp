<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');

$HOST="116.212.73.51"; $USER="u236_EHRIQGvk9J"; $PASS="KpglPz.A6l2jB9PT23iOPG5c"; $DB="s236_testing"; $PORT=3306;
$name = trim($_GET['name'] ?? '');
if($name===''){ echo json_encode(['exists'=>false]); exit; }

$mysqli = new mysqli($HOST,$USER,$PASS,$DB,$PORT);
if($mysqli->connect_error){ echo json_encode(['exists'=>false,'error'=>$mysqli->connect_error]); exit; }

$name = trim($name);

// 1. Cek di player_characters dulu (karena briyan itu IC)
$q1 = $mysqli->prepare("SELECT Character_Name, ucp FROM player_characters WHERE LOWER(TRIM(Character_Name)) = LOWER(TRIM(?)) OR LOWER(TRIM(Character)) = LOWER(TRIM(?)) OR LOWER(TRIM(Name)) = LOWER(TRIM(?)) LIMIT 1");
$q1->bind_param("sss",$name,$name,$name);
$q1->execute(); $r1=$q1->get_result();
if($r1->num_rows>0){ echo json_encode(['exists'=>true,'source'=>'player_characters','name'=>$name]); exit; }

// 2. Cek di playerucp (UCP)
$q2 = $mysqli->prepare("SELECT ucp FROM playerucp WHERE LOWER(TRIM(ucp)) = LOWER(TRIM(?)) OR LOWER(TRIM(username)) = LOWER(TRIM(?)) OR LOWER(TRIM(name)) = LOWER(TRIM(?)) LIMIT 1");
$q2->bind_param("sss",$name,$name,$name);
$q2->execute(); $r2=$q2->get_result();
if($r2->num_rows>0){ echo json_encode(['exists'=>true,'source'=>'playerucp','name'=>$name]); exit; }

// 3. Cek di players
$q3 = $mysqli->prepare("SELECT Name FROM players WHERE LOWER(TRIM(Name)) = LOWER(TRIM(?)) LIMIT 1");
$q3->bind_param("s",$name);
$q3->execute(); $r3=$q3->get_result();

echo json_encode(['exists'=>$r3->num_rows>0,'source'=> $r3->num_rows>0?'players':'none','name'=>$name]);
