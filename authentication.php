<?php

require __DIR__ . '/vendor/autoload.php';

//declare(strict_types=1);

use Firebase\JWT\JWT;

class Auth {

function issueToken($username) {
	$privateKeyFile = '/root/.ssh/rsa.private';
	$privateKey = openssl_pkey_get_private(
                file_get_contents($privateKeyFile)
        );

	$serverName ="obscurecheapdomain.cheap";
	$issuedAt = new DateTime('NOW');
	// TODO: possible issue with this
	// https://www.php.net/manual/en/class.datetime.php 
	$expire = $issuedAt->modify('+100 minutes')->getTimestamp();
	$tokenId    = base64_encode(openssl_random_pseudo_bytes(16));
	// Fixes clock synch issue, otherwise can't decode token right after encoded
	// TODO: There is some time issue with this
	$notBefore = $issuedAt->modify('-110 minutes')->getTimestamp();
	

	$data = array(
	'iss' => $serverName,
	'exp' => $expire,
	'jti' => $tokenId,
	'nbf' => $notBefore,
	'iat' => $issuedAt->getTimestamp(),
	'data' => [
		'username' => $username,
	] 
	);


	$jwt = JWT::encode(
		$data,
		$privateKey,
		'RS256'	
	);

	return $jwt;
}

function verifyToken($jwt) {
	$publicKeyFile = '/root/.ssh/rsa.public';

	$publicKey = openssl_pkey_get_public (
		file_get_contents($publicKeyFile)
	);


	// This will error out if anything goes wrong, including expiration
	$decoded = JWT::decode($jwt, $publicKey, array('RS256'));
	return $decoded;
}

}

?>
