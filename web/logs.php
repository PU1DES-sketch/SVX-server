<?php
require_once "auth.php";
require_login();

$db = new SQLite3('/opt/pu1des-reflector/data/database.sqlite');

$res = $db->query("
SELECT *
FROM eventos
ORDER BY id DESC
LIMIT 500
");
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="refresh" content="10">
<title>Logs da Rede</title>
<style>
body{margin:0;font-family:Arial;background:#111827;color:#e5e7eb}
.header{background:#020617;padding:20px}
.container{padding:20px}
.card{background:#1f2937;padding:20px;border-radius:12px}
table{width:100%;border-collapse:collapse}
th,td{padding:10px;border-bottom:1px solid #374151;text-align:left}
th{background:#020617}
a{color:#93c5fd;text-decoration:none}
</style>
</head>
<body>

<div class="header">
<h1>Logs da Rede</h1>

<p>
<a href="index.php">Dashboard</a> |
<a href="repetidoras.php">Repetidoras</a> |
<a href="status_rede.php">Status da Rede</a> |
<a href="last_heard.php">Last Heard</a> |
<a href="logs.php">Logs</a> |
<a href="logout.php">Sair</a>
</p>
</div>

<div class="container">
<div class="card">

<table>
<tr>
<th>ID</th>
<th>Data</th>
<th>Indicativo</th>
<th>Evento</th>
<th>TG</th>
<th>Detalhes</th>
</tr>

<?php while($row = $res->fetchArray(SQLITE3_ASSOC)): ?>
<tr>
<td><?php echo $row['id']; ?></td>
<td><?php echo $row['criado_em']; ?></td>
<td><?php echo htmlspecialchars($row['callsign']); ?></td>
<td><?php echo htmlspecialchars($row['evento']); ?></td>
<td><?php echo htmlspecialchars($row['tg']); ?></td>
<td><?php echo htmlspecialchars($row['detalhes']); ?></td>
</tr>
<?php endwhile; ?>

</table>

</div>
</div>

</body>
</html>
