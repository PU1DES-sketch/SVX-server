<?php
$db = new SQLite3('/opt/pu1des-reflector/data/database.sqlite');

$ctx = stream_context_create(['http' => ['timeout' => 2]]);
$json = @file_get_contents("http://127.0.0.1:8080/status", false, $ctx);
$data = json_decode($json, true);

$nodes = $data['nodes'] ?? [];

$stateFile = '/opt/pu1des-reflector/data/last_state.json';
$old = [];

if (file_exists($stateFile)) {
    $old = json_decode(file_get_contents($stateFile), true) ?? [];
}

foreach ($nodes as $call => $info) {
    $tg = $info['tg'] ?? '';
    $isTalker = !empty($info['isTalker']);

    if (!isset($old[$call])) {
        logEvent($db, $call, 'CONECTOU', $tg, 'Repetidora conectou ao refletor');
    }

    $oldTalker = !empty($old[$call]['isTalker']);

    if (!$oldTalker && $isTalker) {
        logEvent($db, $call, 'TX_INICIOU', $tg, 'Transmissão iniciada');
    }

    if ($oldTalker && !$isTalker) {
        logEvent($db, $call, 'TX_PAROU', $tg, 'Transmissão finalizada');
    }
}

foreach ($old as $call => $info) {
    if (!isset($nodes[$call])) {
        $tg = $info['tg'] ?? '';
        logEvent($db, $call, 'DESCONECTOU', $tg, 'Repetidora desconectou do refletor');
    }
}

file_put_contents($stateFile, json_encode($nodes, JSON_PRETTY_PRINT));

function logEvent($db, $call, $evento, $tg, $detalhes) {
    $stmt = $db->prepare("INSERT INTO eventos (callsign, evento, tg, detalhes) VALUES (:callsign, :evento, :tg, :detalhes)");
    $stmt->bindValue(':callsign', $call);
    $stmt->bindValue(':evento', $evento);
    $stmt->bindValue(':tg', $tg);
    $stmt->bindValue(':detalhes', $detalhes);
    $stmt->execute();
}
