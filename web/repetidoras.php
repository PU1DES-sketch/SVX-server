<?php
require_once "auth.php";
require_login();

$db = new SQLite3('/opt/pu1des-reflector/data/database.sqlite');

$totalCadastradas = (int)$db->querySingle("SELECT COUNT(*) FROM repetidoras WHERE ativo=1");

$ctx = stream_context_create([
    'http' => ['timeout' => 0.2]
]);

$json = @file_get_contents("http://127.0.0.1:8080/status", false, $ctx);
$api = json_decode($json, true);
$nodesOnline = $api['nodes'] ?? [];

$totalOnline = count($nodesOnline);
$totalOffline = max(0, $totalCadastradas - $totalOnline);

$result = $db->query("SELECT * FROM repetidoras ORDER BY callsign ASC");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<title>Repetidoras - PU1DES</title>
<style>
body{margin:0;font-family:Arial;background:#111827;color:#e5e7eb}
.header{background:#020617;padding:20px;border-bottom:1px solid #1f2937}
.container{padding:10px}
a{color:#93c5fd;text-decoration:none}
.cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:10px;margin-bottom:12px}
.stat{background:#020617;border:1px solid #374151;border-radius:10px;padding:10px}
.stat-title{font-size:12px;color:#9ca3af}
.stat-num{font-size:26px;font-weight:bold;margin-top:4px}

.card{background:#1f2937;border:1px solid #374151;border-radius:10px;padding:10px}
table{width:100%;border-collapse:collapse;margin-top:15px}
th,td{padding:6px;border-bottom:1px solid #374151;text-align:left;font-size:13px}
th{background:#020617;color:#9ca3af}
.badge{padding:5px 10px;border-radius:20px;font-size:13px;font-weight:bold}
.on{background:#14532d;color:#86efac}
.off{background:#7f1d1d;color:#fecaca}
.btn{display:inline-block;background:#2563eb;color:white;padding:5px 8px;border-radius:6px;margin:2px;font-size:12px}
.btn-red{background:#dc2626}
.btn-gray{background:#374151}
</style>
</head>
<body>

<div class="header">
<h1>Repetidoras</h1>
<p><a href="index.php">Dashboard</a> | <a href="logout.php">Sair</a></p>
</div>

<div class="container">
<div class="card">
<h2>Repetidoras Cadastradas</h2>
<div class="cards">
  <div class="stat"><div class="stat-title">Cadastradas</div><div class="stat-num"><?php echo $totalCadastradas; ?></div></div>
  <div class="stat"><div class="stat-title">Online</div><div class="stat-num" style="color:#22c55e"><?php echo $totalOnline; ?></div></div>
  <div class="stat"><div class="stat-title">Offline</div><div class="stat-num" style="color:#ef4444"><?php echo $totalOffline; ?></div></div>
</div>


<a class="btn" href="repetidora_nova.php">+ Nova Repetidora</a>
<br><br>
<input type="text" id="busca" placeholder="Buscar repetidora..." onkeyup="filtrarTabela()" style="width:100%;padding:8px;border-radius:6px;border:1px solid #374151;background:#111827;color:white;margin-bottom:10px;">

<table>
<thead>
<tr>
<th>ID</th>
<th>Indicativo</th>
<th>Descrição</th>
<th>Cidade</th>
<th>Estado</th>
<th>Status</th>
<th>Criado em</th>
<th>Ações</th>
</tr>
</thead>
<tbody>

<?php while ($row = $result->fetchArray(SQLITE3_ASSOC)): ?>
<tr>
<td><?php echo htmlspecialchars($row['id']); ?></td>
<td><strong><?php echo htmlspecialchars($row['callsign']); ?></strong></td>
<td><?php echo htmlspecialchars($row['descricao'] ?? ''); ?></td>
<td><?php echo htmlspecialchars($row['cidade'] ?? ''); ?></td>
<td><?php echo htmlspecialchars($row['estado'] ?? ''); ?></td>
<td>
<?php if ((int)$row['ativo'] === 1): ?>
<span class="badge on">ATIVA</span>
<?php else: ?>
<span class="badge off">DESATIVADA</span>
<?php endif; ?>
</td>
<td><?php echo htmlspecialchars($row['criado_em']); ?></td>
<td>
<a class="btn btn-gray" href="repetidora_editar.php?id=<?php echo $row['id']; ?>">Editar</a>


<td>

<a class="btn btn-gray"
href="repetidora_editar.php?id=<?php echo $row['id']; ?>">
Editar
</a>

<a class="btn btn-red"
href="remover_repetidora.php?id=<?php echo $row['id']; ?>"
onclick="return confirm('Remover repetidora?');">
Remover
</a>



<a class="btn"
href="bloquear_node.php?callsign=<?php echo urlencode($row['callsign']); ?>&tempo=300"
onclick="return confirm('Bloquear esta repetidora por 5 minutos?');">
Kick 5min
</a>

<a class="btn btn-red"
href="bloquear_node.php?callsign=<?php echo urlencode($row['callsign']); ?>&tempo=86400"
onclick="return confirm('Bloquear esta repetidora por 24 horas?');">
Ban 24h
</a>

</td>

</tr>
<?php endwhile; ?>

</tbody>
</table>

</div>
</div>

<script>
function filtrarTabela(){
  var input = document.getElementById("busca");
  var filtro = input.value.toUpperCase();
  var tabela = document.querySelector("table");
  var linhas = tabela.getElementsByTagName("tr");
  for(var i=1; i<linhas.length; i++){
    var texto = linhas[i].innerText.toUpperCase();
    linhas[i].style.display = texto.indexOf(filtro) > -1 ? "" : "none";
  }
}
</script>
</body>
</html>
