<?php
require_once "auth.php";
require_login();

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $callsign = strtoupper(trim($_POST['callsign'] ?? ''));
    $senha = trim($_POST['senha'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $cidade = trim($_POST['cidade'] ?? '');
    $estado = strtoupper(trim($_POST['estado'] ?? ''));

    if ($callsign === '' || $senha === '') {
        $erro = 'Indicativo e senha são obrigatórios.';
    } else {
        $db = new SQLite3('/opt/pu1des-reflector/data/database.sqlite');

        $stmt = $db->prepare("INSERT INTO repetidoras (callsign, senha, descricao, cidade, estado, ativo) VALUES (:callsign, :senha, :descricao, :cidade, :estado, 1)");
        $stmt->bindValue(':callsign', $callsign, SQLITE3_TEXT);
        $stmt->bindValue(':senha', $senha, SQLITE3_TEXT);
        $stmt->bindValue(':descricao', $descricao, SQLITE3_TEXT);
        $stmt->bindValue(':cidade', $cidade, SQLITE3_TEXT);
        $stmt->bindValue(':estado', $estado, SQLITE3_TEXT);

        $ok = @$stmt->execute();

        if ($ok) {
            header('Location: repetidoras.php');
            exit;
        } else {
            $erro = 'Erro ao cadastrar. Talvez esse indicativo já exista.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<title>Nova Repetidora - PU1DES</title>
<style>
body{margin:0;font-family:Arial;background:#111827;color:#e5e7eb}
.header{background:#020617;padding:20px;border-bottom:1px solid #1f2937}
.container{padding:20px}
.card{background:#1f2937;border:1px solid #374151;border-radius:12px;padding:18px;max-width:600px}
input{width:100%;padding:12px;margin:8px 0 15px;border-radius:8px;border:1px solid #374151;background:#111827;color:white}
button,.btn{display:inline-block;background:#2563eb;color:white;padding:12px 16px;border-radius:8px;border:0;text-decoration:none;cursor:pointer}
.btn-gray{background:#374151}
.erro{background:#7f1d1d;color:#fecaca;padding:12px;border-radius:8px;margin-bottom:15px}
label{color:#9ca3af}
</style>
</head>
<body>

<div class="header">
<h1>Nova Repetidora</h1>
<p><a style="color:#93c5fd" href="repetidoras.php">Voltar</a></p>
</div>

<div class="container">
<div class="card">

<?php if($erro): ?>
<div class="erro"><?php echo htmlspecialchars($erro); ?></div>
<?php endif; ?>

<form method="post">
<label>Indicativo</label>
<input name="callsign" placeholder="Ex: PP1ABC-R" required>

<label>Senha</label>
<input name="senha" placeholder="Senha do link" required>

<label>Descrição</label>
<input name="descricao" placeholder="Ex: Repetidora Pedra Azul">

<label>Cidade</label>
<input name="cidade" placeholder="Ex: Iconha">

<label>Estado</label>
<input name="estado" placeholder="Ex: ES" maxlength="2">

<button type="submit">Cadastrar</button>
<a class="btn btn-gray" href="repetidoras.php">Cancelar</a>
</form>

</div>
</div>

</body>
</html>
