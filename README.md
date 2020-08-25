# Disclaimer

# Encriptar y desencriptar cosas
Para asegurar que en la base de datos, los diarios se guardan encriptados.

## Clave
`$_SESSION['crypt_key'] = md5($username . $password);`

## Encriptar
`$content = openssl_encrypt($content, "AES-128-CBC", $_SESSION['crypt_key'], 0, '0000000000000000');`

## Desencriptar
`$content = openssl_decrypt($content, "AES-128-CBC", $_SESSION['crypt_key'], 0, '0000000000000000');`
