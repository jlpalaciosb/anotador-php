# Encriptar y desencriptar cosas
Para asegurar que en la base de datos, los diarios se guardan encriptados.

## Clave
`$_SESSION['user_md5'] = md5($user_login_password . "xxx");`

## Encriptar
`$content = openssl_encrypt($content, "AES-128-CBC", $_SESSION['user_md5'], 0, '0000000000000000');`

## Desencriptar
`$content = openssl_decrypt($content, "AES-128-CBC", $_SESSION['user_md5'], 0, '0000000000000000');`
