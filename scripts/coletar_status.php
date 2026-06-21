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

$db->exec("UPDATE status_nodes SET online=0, is_talker=0");

foreach ($nodes as $call => $info) {
    $tg = (string)($info['tg'] ?? '');
    $isTalker = !empty($info['isTalker']) ? 1 : 0;
    $monitored = isset($info['monitoredTGs']) ? implode(',', $info['monitoredTGs']) : '';
    $sw = $info['sw'] ?? '';
    $swVer = $info['swVer'] ?? '';
    $projVer = $info['projVer'] ?? '';
    $proto = '';

    if (isset($info['protoVer']['majorVer'])) {
        $proto = $info['protoVer']['majorVer'] . '.' . $info['protoVer']['minorVer'];
    }

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

    $stmt = $db->prepare("
        INSERT INTO status_nodes
        (callsign, online, is_talker, tg, monitored_tgs, sw, sw_ver, proj_ver, proto_ver, atualizado_em)
        VALUES
        (:callsign, 1, :is_talker, :tg, :monitored_tgs, :sw, :sw_ver, :proj_ver, :proto_ver, CURRENT_TIMESTAMP)
        ON CONFLICT(callsign) DO UPDATE SET
        online=1,
        is_talker=:is_talker,
        tg=:tg,
        monitored_tgs=:monitored_tgs,
        sw=:sw,
        sw_ver=:sw_ver,
        proj_ver=:proj_ver,
        proto_ver=:proto_ver,
        atualizado_em=CURRENT_TIMESTAMP
    ");

    $stmt->bindValue(':callsign', $call);
    $stmt->bindValue(':is_talker', $isTalker, SQLITE3_INTEGER);
    $stmt->bindValue(':tg', $tg);
    $stmt->bindValue(':monitored_tgs', $monitored);
    $stmt->bindValue(':sw', $sw);
    $stmt->bindValue(':sw_ver', $swVer);
    $stmt->bindValue(':proj_ver', $projVer);
    $stmt->bindValue(':proto_ver', $proto);
    $stmt->execute();
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
