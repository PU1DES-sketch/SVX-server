<?php
require_once 'auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['user'] ?? '';
    $pass = $_POST['pass'] ?? '';

    if ($user === $ADMIN_USER && password_verify($pass, $ADMIN_PASS_HASH)) {
        $_SESSION['logged_in'] = true;
        header('Location: index.php');
        exit;
    } else {
        $error = 'Usuário ou senha inválidos';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<title>Login - PU1DES</title>
<style>
body{background:#111827;color:white;font-family:Arial;display:flex;align-items:center;justify-content:center;height:100vh;margin:0}
.box{background:#1f2937;padding:30px;border-radius:12px;width:320px}
input,button{width:100%;padding:12px;margin-top:10px;border-radius:8px;border:0}
button{background:#2563eb;color:white;font-weight:bold;cursor:pointer}
.error{color:#f87171;margin-top:10px}
</style>
</head>
<body>
<div class="box">
<h2>PU1DES Control</h2>
<form method="post">
<input name="user" placeholder="Usuário" required>
<input name="pass" type="password" placeholder="Senha" required>
<button type="submit">Entrar</button>
</form>
<?php if($error): ?><div class="error"><?php echo $error; ?></div><?php endif; ?>
</div>
</body>
</html>
