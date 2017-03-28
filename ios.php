<?php 
/**	
* iOS Method for sending Push
* By Tarikul Islam (www.tarikul.com)
* 
* @throws ConnectError
* @param $data array
* @param $deviceToken array|string 
* @return Bool
*/
 function iOS($data, $deviceToken) {
	$ctx = stream_context_create();
	// ck.pem is your certificate file
	$files = "pem_development_push.pem";
	#var_dump($files); die();
	stream_context_set_option($ctx, 'ssl', 'local_cert', $files);
	stream_context_set_option($ctx, 'ssl', 'passphrase', 'grubdealz17');

	// Open a connection to the APNS server
	$fp = stream_socket_client(
		'ssl://gateway.sandbox.push.apple.com:2195', $err,
		$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

	if (!$fp)
		exit("Failed to connect: $err $errstr" . PHP_EOL);

	// Create the payload body
	$body['aps'] = array(
		'alert' => array(
		    'title' => $data['title'],
            'body' => $data['desc'],
		 ),
		'sound' => 'default',
		'data' => $data['data'],
	);

	// Encode the payload as JSON
	$payload = json_encode($body);

	// Build the binary notification
	if (is_array($deviceToken)) {
		foreach ($deviceToken as $key => $token) {
			$msg = chr(0) . pack('n', 32) . pack('H*', $token) . pack('n', strlen($payload)) . $payload;
			$result = fwrite($fp, $msg, strlen($msg));
		}
	}else{
		// Build the binary notification
		$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
		// Send it to the server
		$result = fwrite($fp, $msg, strlen($msg));
	}
	fclose($fp);
	return empty($result) ? false : True;
}

$title = "testing from tarikul";
$description = "testing descriptions";
$tokens = '6a5a261a7e20bf9801f878f6dc91c0fd807183555cbaa7bc360adb634b573cf8';
$data = array (
            'title' => $title,
            'desc' => $description,
            'data' =>[
            	'type'=>2, 
            	'category_type' => 'deal',              	
            	'category_id' => 5,              	
            	]
        );
                
iOS($data, $tokens);

?>