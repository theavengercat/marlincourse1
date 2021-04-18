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
				
			$hash = md5(uniqid().time());	
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

function getAllUsers() {
	global $dbh;
	$stmt = $dbh->prepare("SELECT * FROM users");
	$stmt->setFetchMode(PDO::FETCH_ASSOC);
	$stmt->execute();
	return $stmt->fetchAll();
	
}

function isAdmin() {
	global $dbh;
	$user = getuserById($_SESSION['id']);
	return (bool)$user['admin'];
	
}

function editUser(array $user = array(), int $id) {
	global $dbh;
		if($user) {
		$user['id'] = $id;
		foreach($user as $k => $v ) {
			$str .= "{$k} = :{$k},";
		}
		$str = substr($str,0,-1);
		$stm = $dbh->prepare("UPDATE users SET {$str} WHERE id = :id");
		$res = $stm->execute($user);
			
		}
	
	
}

function editCredentionals(array $user = array(), int $id) {
	global $dbh;
		if($user) {


		$userOur = getuserById($id);
		
		foreach(getAllUsers() as $v) {
			if(($user['email'] == $v['email']) and ($userOur['email'] != $user['email'])) {
				
				$_SESSION['message'] = "Email занят, выберите пожалуйста другой.";
				return;
			}
		}
		$user['password'] = md5(md5($user['password']));
		$user['id'] = $id;
		foreach($user as $k => $v ) {
			$str .= "{$k} = :{$k},";
		}
		$str = substr($str,0,-1);
		$stm = $dbh->prepare("UPDATE users SET {$str} WHERE id = :id");
		$res = $stm->execute($user);
		$_SESSION['message'] = "Успешно";
			
		} else 
			$_SESSION['message'] = 'Данные не были отправлены';
	
	
}

function editStatus(array $user = array(), int $id) {
	global $dbh;
		if($user) {
		$user['id'] = $id;
		foreach($user as $k => $v ) {
			$str .= "{$k} = :{$k},";
		}
		$str = substr($str,0,-1);
		$stm = $dbh->prepare("UPDATE users SET {$str} WHERE id = :id");
		$res = $stm->execute($user);
		$_SESSION['message'] = "Успешно";
			
		} else 
			$_SESSION['message'] = 'Данные не были отправлены';
	
	
}

function getAllStatuses() {
	global $dbh;
	$stmt = $dbh->prepare("SELECT * FROM status");
	$stmt->setFetchMode(PDO::FETCH_ASSOC);
	$stmt->execute();
	return $stmt->fetchAll();
}

function deleteAccount($id) {
	global $dbh;
	$stmt = $dbh->prepare("DELETE FROM users WHERE id = :id");
	$stmt->setFetchMode(PDO::FETCH_ASSOC);
	$stmt->execute(array('id'=>$id));
	unlink($_SERVER['DOCUMENT_ROOT'].'/img/avatars/'.$id.'.png');
	$_SESSION['message'] = "Аккаунт удален успешно";
	if($id == $_SESSION['id']) session_destroy();
	header("Location: index.php");

}

