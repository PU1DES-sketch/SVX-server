<?php
require_once "auth.php";
require_login();

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_GET['id'])) {
    die("ERRO: ID não informado");
}

$id = (int)$_GET['id'];

$db = new SQLite3('/opt/pu1des-reflector/data/database.sqlite');

$stmt = $db->prepare("DELETE FROM repetidoras WHERE id = :id");
$stmt->bindValue(':id', $id, SQLITE3_INTEGER);

$result = $stmt->execute();

if (!$result) {
    die("ERRO AO REMOVER: " . $db->lastErrorMsg());
}

$changes = $db->changes();

echo "ID recebido: " . $id . "<br>";
echo "Linhas removidas: " . $changes . "<br>";

exec("php /opt/pu1des-reflector/scripts/gerar_config.php 2>&1", $out1);
exec("cp /opt/pu1des-reflector/config/svxreflector.generated.conf /usr/local/etc/svxlink/svxreflector.conf 2>&1", $out2);
exec("systemctl restart svxreflector 2>&1", $out3);

echo "<pre>";
echo "GERAR CONFIG:\n";
print_r($out1);
echo "\nCOPIAR CONFIG:\n";
print_r($out2);
echo "\nRESTART:\n";
print_r($out3);
echo "</pre>";

echo '<br><a href="repetidoras.php">Voltar</a>';
?>
