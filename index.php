<?php 
 
define('DS', DIRECTORY_SEPARATOR); 
define('BASE_URL', 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']));
define('MAIL_CONFIG_FILE', __DIR__ . DS . 'mailconfig.json'); 
define('MAIL_LIST_PATH', __DIR__ . DS . 'maillist.txt'); 
define('SEPARATOR', ','); 

require 'lib' . DS . 'functions.php'; 
require 'lib' . DS . 'class.smtp.php'; 
require 'lib' . DS . 'class.phpmailer.php'; 

if($_SERVER['REQUEST_METHOD'] == 'GET'){
	header('Content-Type: text/html; charset=utf-8');
	$mailConfig = json_decode(file_get_contents(MAIL_CONFIG_FILE), true); 
	require 'page.htm'; 
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
	if(isset($_POST['sendMessage'])){
		
		$config  	  = json_decode(file_get_contents(MAIL_CONFIG_FILE), true); 
		$emailList    = explode(SEPARATOR, file_get_contents(MAIL_LIST_PATH)); 
		$mailer  	  = getMailer($config); 
		$subject	  = $_POST['subject']; 
		$messageBody  = $_POST['message']; 
		$combinations = findCombinations($messageBody); 


		foreach ($emailList as $email) {
			
			$message = substitude($messageBody,$combinations); 


			list($error, $delivered) = sendMail($mailer, array(
			    'sendTo'  => $email, 
			    'subject' => $subject,  
			    'body' 	  => $message, 
			    'altBody' => $message  
			)); 

			header('Content-Type: text/html; charset=utf-8');

			if($error){
				echo "Произошла ошибка, проверте настройки соединения"; 
				echo $email; 
				return false; 
			}
		}

		echo "Рассылка закончена успешно"; 

	}

	if(isset($_POST['config'])){
		$settings = json_encode(array(
			'username' =>  $_POST['username'],
			'password' =>  $_POST['password'],
			'host' 	   =>  $_POST['host'],
			'port' 	   =>  $_POST['port'],
			'email'    =>  $_POST['email'],
			'protocol' =>  $_POST['protocol'],
		)); 

		file_put_contents(MAIL_CONFIG_FILE, $settings); 
		header('Location: ' . BASE_URL); 
	}

	if(isset($_POST['database'])){
		if (move_uploaded_file($_FILES["file"]["tmp_name"], MAIL_LIST_PATH)) {
	        
	        $mailList = parseFile(MAIL_LIST_PATH);
	        file_put_contents(MAIL_LIST_PATH, $mailList); 
	        header('Location: ' . BASE_URL); 
	    } else {
	        echo "ERROR";
	    }
	}

}

