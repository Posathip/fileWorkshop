<?php
$ip = 'tcp://119.59.105.58';
$port = 1883;
$connection = @fsockopen($ip, $port, $errno, $errstr, 5);
if (!$connection) {
    echo "Connection failed: $errstr ($errno)\n";
} else {
    echo "Connection successful!\n";
    fclose($connection);
}
?>
