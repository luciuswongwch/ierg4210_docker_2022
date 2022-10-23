<?php
	ob_start();
	session_start();

	include(dirname(__FILE__)."/db_config.php");

	try {
		$connect = new PDO("mysql:host=$host;dbname=$database", $username, $password);
		$connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$c0 = $connect->prepare("SELECT * FROM categories");

		$c0Outer = $connect->prepare("SELECT * FROM categories");

		$p0 = $connect->prepare("SELECT * FROM products");

		$u0 = $connect->prepare("SELECT * FROM users");

		$o0 = $connect->prepare("SELECT * FROM orders");

		$c1 = $connect->prepare("SELECT * FROM categories WHERE catid = ?");

		$p1 = $connect->prepare("SELECT * FROM products WHERE pid = ?");

		$p1a = $connect->prepare("SELECT * FROM products WHERE catid = ?");

		$u1 = $connect->prepare("SELECT * FROM users WHERE email=?");

		$u1a = $connect->prepare("SELECT * FROM users WHERE isAdmin = ?");

		$u1b = $connect->prepare("SELECT * FROM users WHERE userid = ?");

		$cred1 = $connect->prepare("SELECT * FROM credentials WHERE id=?");

		$o1 = $connect->prepare("SELECT * FROM orders WHERE oid=?");

		$o1a = $connect->prepare("SELECT * FROM orders WHERE txn_id=?");

		$o1b = $connect->prepare("SELECT * FROM orders WHERE user=?");

		$u2 = $connect->prepare("INSERT INTO users (userid, email, password, isAdmin, salt) VALUES (NULL, ?, ?, ?, ?)");

		$u3 = $connect->prepare("UPDATE users SET password = ? WHERE userid = ?");

		$c2 = $connect->prepare("INSERT INTO categories (catid, name) VALUES (NULL, ?)");

		$c3 = $connect->prepare("UPDATE categories SET name = ? WHERE catid = ?");

		$p2 = $connect->prepare("INSERT INTO products (pid, catid, name, price, description, imagePath) VALUES (NULL, ?, ?, ?, ?, ?)");

		$p3 = $connect->prepare("UPDATE products SET catid = ?, name = ?, price = ?, description = ?, imagePath = ? WHERE pid = ?");

		$p3a = $connect->prepare("UPDATE products SET catid = ?, name = ?, price = ?, description = ? WHERE pid = ?");

		$o2 = $connect->prepare("INSERT INTO orders (oid, digest, salt, txn_id, user, productList) VALUES (NULL, ?, ?, 'toBeUpdated', ?, ?)");

		$o3 = $connect->prepare("UPDATE orders SET txn_id = ?, productList = ? WHERE oid = ?");

		$c4 = $connect->prepare("DELETE FROM categories WHERE catid=?");

		$p4 = $connect->prepare("DELETE FROM products WHERE pid=?");
		
	}

	catch (PDOException $error) {
		echo $error->getMessage();
	}
?>
