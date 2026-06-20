<?php

$db = new SQLite3('/opt/pu1des-reflector/data/database.sqlite');

$base = <<<CONF
###################################################################
#
# Configuration file for the SvxReflector SvxLink conference node
#
###################################################################

[GLOBAL]
TIMESTAMP_FORMAT="%c"
LISTEN_PORT=5300
TG_FOR_V1_CLIENTS=999
HTTP_SRV_PORT=8080
COMMAND_PTY=/dev/shm/reflector_ctrl
CERT_CA_HOOK=/usr/local/share/svxlink/ca-hook.py

[ROOT_CA]

[ISSUING_CA]

[SERVER_CERT]
COMMON_NAME=PU1DES-REFLECTOR
ORG_UNIT=PU1DES
COUNTRY=BR

CONF;

$users = "[USERS]\n";
$passwords = "\n[PASSWORDS]\n";

$res = $db->query("SELECT callsign, senha FROM repetidoras WHERE ativo=1 ORDER BY callsign ASC");

while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
    $callsign = trim($row['callsign']);
    $senha = trim($row['senha']);

    if ($callsign === '' || $senha === '') {
        continue;
    }

    $users .= $callsign . "=" . $callsign . "\n";
    $passwords .= $callsign . "=" . $senha . "\n";
}

$tgs = "\n[TG#999]\n";

$config = $base . $users . $passwords . $tgs;

file_put_contents('/opt/pu1des-reflector/config/svxreflector.generated.conf', $config);

echo "Configuração gerada com sucesso.\n";
