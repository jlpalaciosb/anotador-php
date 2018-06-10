Encriptación y desencriptación de los diarios en la base de datos

$_SESSION['user_password_md5'] = md5($user_login_password . "xxx");    //clave para encriptar y desencriptar los diarios

$content = openssl_encrypt($content, "AES-128-CBC", $_SESSION['user_password_md5'], 0, '0000000000000000'); //encriptar
$content = openssl_decrypt($content, "AES-128-CBC", $_SESSION['user_password_md5'], 0, '0000000000000000'); //desencriptar



Se asegura que en la base de datos owner es la primera palabra del campo dateuser
Entonces ya no nos preocupamos por los permisos

Se eliminaron los throw catch de muchas partes