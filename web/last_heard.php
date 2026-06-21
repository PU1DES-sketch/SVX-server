<?php
require_once "auth.php";
require_login();

$db = new SQLite3('/opt/pu1des-reflector/data/database.sqlite');

$res = $db->query("SELECT * FROM eventos ORDER BY id DESC LIMIT 100");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<meta http-equiv="refresh" content="10">
<title>Last Heard - PU1DES</title>
<style>
body{margin:0;font-family:Arial;background:#111827;color:#e5e7eb}
.header{background:#020617;padding:20px;border-bottom:1px solid #1f2937}
.container{padding:20px}
a{color:#93c5fd;text-decoration:none}
.card{background:#1f2937;border:1px solid #374151;border-radius:12px;padding:18px}
table{width:100%;border-collapse:collapse;margin-top:15px}
th,td{padding:12px;border-bottom:1px solid #374151;text-align:left}
th{background:#020617;color:#9ca3af}
.badge{padding:5px 10px;border-radius:20px;font-size:13px;font-weight:bold}
.conectou{background:#14532d;color:#86efac}
.desconectou{background:#7f1d1d;color:#fecaca}
.tx{background:#713f12;color:#fde68a}
</style>
</head>
<body>

<div class="header">
<h1>Last Heard</h1>
<p>
<a href="index.php">Dashboard</a> |
<a href="repetidoras.php">Repetidoras</a> |
<a href="status_rede.php">Status da Rede</a> |
<a href="last_heard.php">Last Heard</a> |
<a href="logout.php" style="color:#fca5a5">Sair</a>
</p>
</div>

<div class="container">
<div class="card">
<h2>Últimos Eventos</h2>

<table>
<tr>
<th>ID</th>
<th>Data/Hora</th>
<th>Repetidora</th>
<th>Evento</th>
<th>TG</th>
<th>Detalhes</th>
</tr>

<?php while($row = $res->fetchArray(SQLITE3_ASSOC)): ?>
<?php
$classe = '';
if ($row['evento'] === 'CONECTOU') $classe = 'conectou';
if ($row['evento'] === 'DESCONECTOU') $classe = 'desconectou';
if ($row['evento'] === 'TX_INICIOU' || $row['evento'] === 'TX_PAROU') $classe = 'tx';
?>
<tr>
<td><?php echo htmlspecialchars($row['id']); ?></td>
<td><?php echo htmlspecialchars($row['criado_em']); ?></td>
<td><strong><?php echo htmlspecialchars($row['callsign']); ?></strong></td>
<td><span class="badge <?php echo $classe; ?>"><?php echo htmlspecialchars($row['evento']); ?></span></td>
<td><?php echo htmlspecialchars($row['tg']); ?></td>
<td><?php echo htmlspecialchars($row['detalhes']); ?></td>
</tr>
<?php endwhile; ?>

</table>
</div>
</div>

</body>
</html>
