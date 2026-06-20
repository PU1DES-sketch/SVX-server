<?php
$context = stream_context_create([
    'http' => ['timeout' => 2]
]);

$json = @file_get_contents("http://127.0.0.1:8080/status", false, $context);
$data = json_decode($json, true);

$nodes = $data['nodes'] ?? [];
$total = count($nodes);
$talkers = 0;

foreach ($nodes as $node) {
    if (!empty($node['isTalker'])) {
        $talkers++;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<meta http-equiv="refresh" content="5">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>PU1DES Network Control</title>
<style>
body{
    margin:0;
    font-family:Arial, Helvetica, sans-serif;
    background:#111827;
    color:#e5e7eb;
}
.header{
    background:#020617;
    padding:20px;
    border-bottom:1px solid #1f2937;
}
.header h1{
    margin:0;
    font-size:26px;
}
.header p{
    margin:5px 0 0 0;
    color:#9ca3af;
}
.container{
    padding:20px;
}
.cards{
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));
    gap:15px;
    margin-bottom:20px;
}
.card{
    background:#1f2937;
    border:1px solid #374151;
    border-radius:12px;
    padding:18px;
}
.card h2{
    margin:0;
    font-size:15px;
    color:#9ca3af;
}
.card .number{
    margin-top:10px;
    font-size:34px;
    font-weight:bold;
}
.online{
    color:#22c55e;
}
.tx{
    color:#facc15;
}
.offline{
    color:#ef4444;
}
table{
    width:100%;
    border-collapse:collapse;
    background:#1f2937;
    border-radius:12px;
    overflow:hidden;
}
th,td{
    padding:12px;
    border-bottom:1px solid #374151;
    text-align:left;
}
th{
    background:#020617;
    color:#9ca3af;
    font-size:14px;
}
.badge{
    padding:5px 10px;
    border-radius:20px;
    font-size:13px;
    font-weight:bold;
}
.badge-online{
    background:#14532d;
    color:#86efac;
}
.badge-tx{
    background:#713f12;
    color:#fde68a;
}
.badge-idle{
    background:#374151;
    color:#d1d5db;
}
.footer{
    margin-top:20px;
    color:#6b7280;
    font-size:13px;
}
pre{
    white-space:pre-wrap;
}
</style>
</head>
<body>

<div class="header">
    <h1>PU1DES Network Control</h1>
    <p>Dashboard do SvxReflector - atualização automática a cada 5 segundos</p>
</div>

<div class="container">

    <div class="cards">
        <div class="card">
            <h2>Status do Servidor</h2>
            <div class="number online">ONLINE</div>
        </div>

        <div class="card">
            <h2>Repetidoras Conectadas</h2>
            <div class="number"><?php echo $total; ?></div>
        </div>

        <div class="card">
            <h2>Transmitindo Agora</h2>
            <div class="number tx"><?php echo $talkers; ?></div>
        </div>

        <div class="card">
            <h2>API</h2>
            <div class="number online">OK</div>
        </div>
    </div>

    <div class="card">
        <h2>Repetidoras Online</h2>
        <br>

        <?php if ($total === 0): ?>
            <p>Nenhuma repetidora conectada.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Indicativo</th>
                        <th>Status</th>
                        <th>TG Atual</th>
                        <th>TGs Monitoradas</th>
                        <th>Software</th>
                        <th>Versão</th>
                        <th>Protocolo</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($nodes as $callsign => $info): ?>
                    <?php
                    $isTalker = !empty($info['isTalker']);
                    $tg = $info['tg'] ?? '-';
                    $monitored = isset($info['monitoredTGs']) ? implode(', ', $info['monitoredTGs']) : '-';
                    $sw = $info['sw'] ?? '-';
                    $swVer = $info['swVer'] ?? '-';
                    $projVer = $info['projVer'] ?? '-';
                    $proto = '-';

                    if (isset($info['protoVer']['majorVer'])) {
                        $proto = $info['protoVer']['majorVer'] . '.' . $info['protoVer']['minorVer'];
                    }
                    ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($callsign); ?></strong></td>
                        <td>
                            <?php if ($isTalker): ?>
                                <span class="badge badge-tx">TRANSMITINDO</span>
                            <?php else: ?>
                                <span class="badge badge-online">ONLINE</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($tg); ?></td>
                        <td><?php echo htmlspecialchars($monitored); ?></td>
                        <td><?php echo htmlspecialchars($sw); ?></td>
                        <td><?php echo htmlspecialchars($swVer . ' / ' . $projVer); ?></td>
                        <td><?php echo htmlspecialchars($proto); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <div class="footer">
        PU1DES Network Control v0.1 | Dados via SvxReflector /status
    </div>

</div>

</body>
</html>
