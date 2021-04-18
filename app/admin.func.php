<?php

function uploadFile($files, $id) {
	global $dbh;
	if (move_uploaded_file($files['photo']['tmp_name'], $_SERVER['DOCUMENT_ROOT'].'/img/avatars/'.$id.'.png')) {
		$_SESSION['message'] = 'Фотография успешно загружена.';
		$stmt = $dbh->prepare("UPDATE users set photo = :photo where id=:id");
		$stmt->execute(array('photo'=>'/img/avatars/'.$id.'.png', 'id'=>$id));
		return true;
	} else {
		$_SESSION['message'] = 'Фотография не была загружена.';
		return false;
	}
	
}




function createUserAdmin(array $user = array(), array $files = array()) {
	global $dbh;
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	if(isAdmin()) {
		
		
		
		if($user) {
			
		$user['password'] = md5(md5($user['password']));
			
		$prep = array();
		foreach($user as $k => $v ) {
			$prep[':'.$k] = $v;
		}
		$stm = $dbh->prepare("INSERT INTO users ( " . implode(', ',array_keys($user)) . ") VALUES (" . implode(', ',array_keys($prep)) . ")");
		echo "INSERT INTO users ( " . implode(', ',array_keys($user)) . ") VALUES (" . implode(', ',array_keys($prep)) . ")";
		$res = $stm->execute($prep);
		var_dump($res);
	
		$user['id'] = $dbh->lastInsertId();
		if($files) uploadFile($files, $user['id']);
			
		} else 
			$_SESSION['message'] = 'Данные не были отправлены';
	
	
		
	
		
	}
}