<?php

function adminAuthentication($u1a) {
	if(!empty($_SESSION['auth'])) {
		return $_SESSION['auth']['isAdmin'];
	}
	if(!empty($_COOKIE['auth'])) {
		if($decoded_json = json_decode(stripcslashes($_COOKIE['auth']), true)) {
			if(time() > $decoded_json['expire']) {
				return false;
			} else {
				$u1a->execute(array(1));
				while($u1aData = $u1a->fetch()) {
					if ($decoded_json['hashedPassword'] == md5($u1aData['password'].$u1aData['salt'])) {
						$_SESSION['auth'] = $decoded_json;
						return $decoded_json['isAdmin'];
					};
				}
				return false;
			}
		}
	}
}

if (!adminAuthentication($u1a)) {
	header("Location: index.php");
}

?>