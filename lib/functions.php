<?php 

function findCombinations($textString){

	$combinations = array();  
	$matches 	  = null; 

	preg_match_all('/{(.*?)}/', $textString, $matches);

	$foundCombinations = $matches[0]; 

	if( ! count($foundCombinations) ) return $combinations; 

	foreach ($foundCombinations as $pattern) {
		$entry = array(); 
		$words = str_replace(array( '{', '}' ), '', $pattern);
		$words = explode('|', $words);

		foreach ($words as $word) {
			$entry[] = trim($word); 
		}

		$combinations[$pattern] = $entry; 
	}

	return $combinations; 

}; 

function randomValue(array $list){
	return $list[rand(0, count($list) - 1)]; 
}; 

function substitude($text, $combinations){

	foreach ($combinations as $pattern => $options) {
		$text = str_replace($pattern, randomValue($options), $text); 
	}

	return $text; 

}; 

function getMailer(array $config){

	$protocol = null; 

	switch ($config['protocol']) {
		case 'ssl':
			$protocol = 'ssl'; 
			break;
		case 'tls':
			$protocol = 'tls'; 
			break;
		default:
			$protocol = false; 
			break;
	}

	$mail = new PHPMailer;

	$mail->SMTPDebug = 0; //3;                               
	
	$mail->isSMTP();                                      
	$mail->Host = $config['host']; 
	$mail->SMTPAuth = true;                               
	$mail->Username = $config['username'];                  
	$mail->Password = $config['password'];                            
	$mail->SMTPSecure = $protocol;                              
	$mail->Port = $config['port'];                                     
	$mail->From = $config['email']; 
	$mail->FromName = 'Mail system';  

	return $mail; 
	
}

function sendMail($mail, $message){
	
	$mail->addAddress($message['sendTo']);
	$mail->Subject = $message['subject']; 
	$mail->Body    = $message['body']; 
	$mail->AltBody = $message['altBody']; 
	
	if(!$mail->send()) {
		echo "ERROR: " . $mail->ErrorInfo; 
		return array($mail->ErrorInfo, false);  
	} else {
		return array(false, 'Message has been sent');  
	}
}; 

function parseFile($filePath){
	$file = file_get_contents($filePath); 

	$separators = array(';', ',', ':', "\n");  
	$separator  = null; 
	$result     = array(); 
	foreach ($separators as $symbol) {
		if( strpos($file, $symbol) ){
			$separator = $symbol; 
			break; 
		}
	}

	if( ! $separator ) return null;   

	$list = explode($separator, $file); 

	if( empty($list) ) return null;  

	foreach ($list as $entry) {
		$email = trim($entry); 
		if( ! empty($email) ) 
			$result[] = $email; 
	}

	return implode(SEPARATOR, $result); 

}