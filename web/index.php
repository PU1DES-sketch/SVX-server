<?php

$context = stream_context_create([
    'http' => [
        'timeout' => 2
    ]
]);

$json = @file_get_contents(
    "http://127.0.0.1:8080/status",
    false,
    $context
);

$data = json_decode($json, true);

$total_nodes = isset($data['nodes']) ? count($data['nodes']) : 0;

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="refresh" content="5">
<title>PU1DES Reflector</title>

<style>
body{
    font-family: Arial;
    background:#1e1e1e;
    color:white;
    margin:20px;
}
.card{
    background:#2d2d2d;
    padding:20px;
    border-radius:10px;
    margin-bottom:15px;
}
table{
    width:100%;
    border-collapse:collapse;
}
th,td{
    border:1px solid #444;
    padding:10px;
}
th{
    background:#333;
}
</style>

</head>
<body>

<h1>PU1DES Reflector</h1>

<div class="card">
<h2>Status</h2>
Servidor Online
</div>

<div class="card">
<h2>Repetidoras Conectadas</h2>
<b><?php echo $total_nodes; ?></b> conectadas
</div>

<div class="card">
<h2>Nós</h2>

<?php

if($total_nodes == 0){

    echo "Nenhuma repetidora conectada.";

}else{

    echo "<table>";
    echo "<tr><th>Callsign</th></tr>";

    foreach($data['nodes'] as $call=>$info){

        echo "<tr>";
        echo "<td>$call</td>";
        echo "</tr>";

    }

    echo "</table>";
}
?>

</div>

</body>
</html>
