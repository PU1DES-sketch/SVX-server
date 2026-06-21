<?php
require_once "auth.php";
require_login();

$callsign = strtoupper(trim($_GET['callsign'] ?? ''));
$tempo = (int)($_GET['tempo'] ?? 0);

if ($callsign === '' || $tempo <= 0) {
    die("Dados inválidos");
}

$cmd = "NODE BLOCK " . $callsign . " " . $tempo;

file_put_contents('/dev/shm/reflector_ctrl', $cmd . PHP_EOL);

$db = new SQLite3('/opt/pu1des-reflector/data/database.sqlite');

$evento = ($tempo >= 86400) ? 'BAN_MANUAL' : 'KICK_MANUAL';
$detalhes = "Administrador executou: " . $cmd;

$stmt = $db->prepare("INSERT INTO eventos (callsign, evento, tg, detalhes) VALUES (:callsign, :evento, '', :detalhes)");
$stmt->bindValue(':callsign', $callsign);
$stmt->bindValue(':evento', $evento);
$stmt->bindValue(':detalhes', $detalhes);
$stmt->execute();

header("Location: repetidoras.php");
exit;
