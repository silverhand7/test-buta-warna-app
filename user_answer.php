<?php 
include "config.php";

// echo $_POST['question_id']. "<br>";
$user =  $_SESSION['userData']['id'];
// echo $_POST['answer'];

//cek benar atau salah
$cek = $db->query("SELECT * FROM questions WHERE id = '". $_POST['question_id']. "' AND answer = '" . $_POST['answer']. "'");
if($cek->rowCount() <> 0){
	$result = "true";
} else {
	$result = "false";
}

//kalau jawaban pernah dijawab oleh user akan error
$is_answered = $db->query("SELECT * FROM answers WHERE user_id = '$user' AND question_id = '".$_POST['question_id']."' AND stat = '1' ");
if($is_answered->rowCount() != 0){
	echo "failed";
} else {
	//jika tidak pernah dijawab insert ke table answers
	$insert = $db->query("INSERT INTO answers VALUES ('', '".$_POST['question_id']."', '".$user."', '".$_POST['answer']."', '$result', '1')");	
}



//return soal baru yang belum pernah dijawab
$arr = '';
$ans = $db->query("SELECT * FROM answers WHERE user_id = '$user' AND stat = '1' ");
if($ans->rowCount() <> 0){
	foreach($ans as $data) {
		$arr .= "'".$data['question_id'] ."', ";
	}
	$answered = rtrim($arr,", ");

	$not_answered = $db->query("SELECT * FROM questions  WHERE id NOT IN ($answered) ");
	
	if($not_answered->rowCount() <> 0){

		$rand = [];
		foreach($not_answered as $not){
			array_push($rand, $not['id']);
		}

		$quest = $rand[array_rand($rand)];

		//select next question
		$next = $db->query("SELECT * FROM questions  WHERE id = '$quest' ")->fetch();
		//multiple choices
		$choices = $db->query("SELECT * FROM multiple_choice WHERE question_id = '$quest'");
		foreach($choices as $c){
			array_push($next, $c['value']);
		}
		
		echo json_encode($next);
	} 

	
}


 ?>