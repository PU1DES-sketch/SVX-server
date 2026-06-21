<?php
require_once "auth.php";
require_login();

$id = (int)($_GET['id'] ?? 0);
$db = new SQLite3('/opt/pu1des-reflector/data/database.sqlite');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $callsign = strtoupper(trim($_POST['callsign']));
    $senha = trim($_POST['senha']);
    $descricao = trim($_POST['descricao']);
    $cidade = trim($_POST['cidade']);
    $estado = strtoupper(trim($_POST['estado']));

    $stmt = $db->prepare("UPDATE repetidoras SET callsign=:callsign, senha=:senha, descricao=:descricao, cidade=:cidade, estado=:estado WHERE id=:id");
    $stmt->bindValue(':callsign', $callsign);
    $stmt->bindValue(':senha', $senha);
    $stmt->bindValue(':descricao', $descricao);
    $stmt->bindValue(':cidade', $cidade);
    $stmt->bindValue(':estado', $estado);
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    $stmt->execute();

    exec("php /opt/pu1des-reflector/scripts/gerar_config.php");

    header("Location: repetidoras.php");
    exit;
}

$stmt = $db->prepare("SELECT * FROM repetidoras WHERE id=:id");
$stmt->bindValue(':id', $id, SQLITE3_INTEGER);
$row = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

if (!$row) die("Repetidora não encontrada");
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Editar Repetidora</title>
<style>
body{margin:0;font-family:Arial;background:#111827;color:#e5e7eb}
.header{background:#020617;padding:20px}
.container{padding:20px}
.card{background:#1f2937;padding:20px;border-radius:12px;max-width:600px}
input{width:100%;padding:12px;margin:8px 0 15px;border-radius:8px;border:1px solid #374151;background:#111827;color:white}
button,.btn{background:#2563eb;color:white;padding:12px 16px;border-radius:8px;border:0;text-decoration:none}
.btn-gray{background:#374151}
</style>
</head>
<body>
<div class="header"><h1>Editar Repetidora</h1></div>
<div class="container">
<div class="card">
<form method="post">
<label>Indicativo</label>
<input name="callsign" value="<?php echo htmlspecialchars($row['callsign']); ?>" required>

<label>Senha</label>
<input name="senha" value="<?php echo htmlspecialchars($row['senha']); ?>" required>

<label>Descrição</label>
<input name="descricao" value="<?php echo htmlspecialchars($row['descricao']); ?>">

<label>Cidade</label>
<input name="cidade" value="<?php echo htmlspecialchars($row['cidade']); ?>">

<label>Estado</label>
<input name="estado" value="<?php echo htmlspecialchars($row['estado']); ?>" maxlength="2">

<button type="submit">Salvar</button>
<a class="btn btn-gray" href="repetidoras.php">Cancelar</a>
</form>
</div>
</div>
</body>
</html>
