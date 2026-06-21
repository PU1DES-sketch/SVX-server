<?php
require_once "auth.php";
require_login();

$ctx = stream_context_create([
    'http' => ['timeout' => 0.2]
]);

$json = @file_get_contents("http://127.0.0.1:8080/status", false, $ctx);
$api = json_decode($json, true);
$nodesOnline = $api['nodes'] ?? [];

$db = new SQLite3('/opt/pu1des-reflector/data/database.sqlite');
$res = $db->query("SELECT * FROM repetidoras WHERE ativo=1 ORDER BY callsign ASC");

$repetidoras = [];
while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
    $repetidoras[] = $row;
}

$totalCadastro = count($repetidoras);
$totalOnline = count($nodesOnline);
$totalOffline = max(0, $totalCadastro - $totalOnline);

$agora = date("d/m/Y H:i:s");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<meta http-equiv="refresh" content="5">
<title> Network Control Center</title>
<style>
body{margin:0;font-family:Arial;background:#111827;color:#e5e7eb}
.header{background:#020617;padding:20px;border-bottom:1px solid #1f2937}
.header h1{margin:0;font-size:26px}
.header p{color:#9ca3af}
.container{padding:20px}
a{color:#93c5fd;text-decoration:none}
.cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:15px;margin-bottom:20px}
.card{background:#1f2937;border:1px solid #374151;border-radius:12px;padding:18px}
.card h2{margin:0;font-size:15px;color:#9ca3af}
.number{margin-top:10px;font-size:34px;font-weight:bold}
.online{color:#22c55e}
.offline{color:#ef4444}
.tx{color:#facc15}
table{width:100%;border-collapse:collapse;background:#1f2937;border-radius:12px;overflow:hidden}
th,td{padding:12px;border-bottom:1px solid #374151;text-align:left}
th{background:#020617;color:#9ca3af}
.badge{padding:5px 10px;border-radius:20px;font-size:13px;font-weight:bold}
.badge-online{background:#14532d;color:#86efac}
.badge-offline{background:#7f1d1d;color:#fecaca}
.badge-tx{background:#713f12;color:#fde68a}
.footer{margin-top:20px;color:#6b7280;font-size:13px}
</style>
</head>
<body>

<div class="header">
    <h1>PU1DES Network Control Center</h1>
    <p>Centro de Controle da Rede SvxLink</p>
    <p>
      <a href="index.php">Dashboard</a> |
      <a href="repetidoras.php">Repetidoras</a> |
<a href="status_rede.php">Status da Rede</a> |
<a href="last_heard.php">Last Heard</a> |
<a href="logs.php">Logs</a> |
<a href="logout.php" style="color:#fca5a5">Sair</a>
</p>
</div>

<div class="container">

<div class="cards">
    <div class="card">
        <h2>SvxReflector</h2>
        <div class="number online">ONLINE</div>
    </div>

    <div class="card">
        <h2>Repetidoras Cadastradas</h2>
        <div class="number"><?php echo $totalCadastro; ?></div>
    </div>

    <div class="card">
        <h2>Repetidoras Online</h2>
        <div class="number online"><?php echo $totalOnline; ?></div>
    </div>

    <div class="card">
        <h2>Repetidoras Offline</h2>
        <div class="number offline"><?php echo $totalOffline; ?></div>
    </div>
</div>

<div class="card">
<h2>Monitoramento das Repetidoras</h2>
<br>

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
<th>Descrição</th>
</tr>
</thead>
<tbody>

<?php foreach ($repetidoras as $rep): ?>
<?php
$call = $rep['callsign'];
$isOnline = isset($nodesOnline[$call]);
$info = $nodesOnline[$call] ?? [];

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
<td><strong><?php echo htmlspecialchars($call); ?></strong></td>
<td>
<?php if ($isTalker): ?>
<span class="badge badge-tx">TRANSMITINDO</span>
<?php elseif ($isOnline): ?>
<span class="badge badge-online">ONLINE</span>
<?php else: ?>
<span class="badge badge-offline">OFFLINE</span>
<?php endif; ?>
</td>
<td><?php echo htmlspecialchars($tg); ?></td>
<td><?php echo htmlspecialchars($monitored); ?></td>
<td><?php echo htmlspecialchars($sw); ?></td>
<td><?php echo htmlspecialchars($swVer . ' / ' . $projVer); ?></td>
<td><?php echo htmlspecialchars($proto); ?></td>
<td><?php echo htmlspecialchars($rep['descricao'] ?? ''); ?></td>
</tr>
<?php endforeach; ?>

</tbody>
</table>
</div>

<div class="footer">
Desenvolvido por PU1DES<br>
Última atualização: <?php echo $agora; ?> | Dados via SvxReflector /status
</div>
</div>
</body>
</html>
