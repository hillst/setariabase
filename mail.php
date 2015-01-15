<?php
$message = "huehue";    
$subject = "subject";
$mailmaster = "webmaster@setaria.mocklerlab.org";
$headers = 'From: ' .$mailmaster . "\r\n";
$fd = fopen("maillist.txt", "r");
while (($email = fgets($fd, 4096)) !== false) {
    mail($email, $subject, $message, $headers);
}
fclose($fd);
?>
