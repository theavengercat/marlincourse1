<?php

function getuserByEmail(string $email) {
	global $dbh;
	$stmt = $dbh->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
	$stmt->setFetchMode(PDO::FETCH_ASSOC);
	$stmt->execute(array('email'=>$email));
	return $stmt->fetch();
}

function getuserById(int $id) {
	global $dbh;
	$stmt = $dbh->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
	$stmt->setFetchMode(PDO::FETCH_ASSOC);
	$stmt->execute(array('id'=>$id));
	return $stmt->fetch();
}

function createuser(string $email, string $pass) {
	global $dbh;
	$user = getuserByEmail($email);

		if(!$user) {
			
			$stmt = $dbh->prepare("INSERT INTO users (email,password) VALUES (:email,:password)");
			$stmt->execute(array('email'=>$email, 'password'=>md5(md5($pass))));
			$insertId = $dbh->lastInsertId();

			$hash = md5(uniqid().time());
			$stmt = $dbh->prepare("UPDATE users set hash = :hash where id=:id");
			$stmt->execute(array('hash'=>$hash, 'id'=>$insertId));

			$_SESSION['id'] = $insertId;
			$_SESSION['hash'] = $hash;
			$_SESSION['message'] = 'Регистрация успешна. Через несколько секунд вы будете перенаправлены на страницу.';
			header("refresh: 3; url=/index.php");
		} else {			
			$_SESSION['message'] = 'Этот эл. адрес уже занят другим пользователем.';
		}
	
	
}

function login(string $email, string $pass) {
	global $dbh;
	$user = getuserByEmail($email);
		if($user) {
			if(md5(md5($pass)) == $user['password']) {
				
				
			$_SESSION['id'] = $user['id'];
			$_SESSION['hash'] = $hash;
			$_SESSION['message'] = 'Авторизация успешна. Через несколько секунд вы будете перенаправлены на страницу.';
			header("refresh: 3; url=/index.php");
			
			} else {
				$_SESSION['message'] = 'Логин или пароль неверны. Попробуйте еще раз.';
			}
		} else {			
			$_SESSION['message'] = 'Этот эл. адрес не зарегистрирован на портале.';
		}
	
	
}

