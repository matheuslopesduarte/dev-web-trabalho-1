<?php

if (extension_loaded('mysqli')) {
    echo "A extensão MySQLi está habilitada.";
} else {
    echo "A extensão MySQLi não está habilitada.";
}


phpinfo();

foreach ($_SERVER as $key => $value) {
    echo "$key: $value<br>";
}
?>
