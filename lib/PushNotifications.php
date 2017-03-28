<?php 
namespace App\Lib;
// Server file
class PushNotifications {

	// (Android)API access key from Google API's Console.
	private static $API_ACCESS_KEY = 'AAAA3gjF5y8:APA91bHy88IuYTwfQEKraQtC5fK4dd5ymtYd6-KgVOaccSh6bkrDU9kpmhMMEUkFpGmKNIcFkGgMpacBMra7j0yC_TnjmPsw_p9LVjp1QRXgHqg0CxFWv1eNJhom9xqdrlOf4TnKVDq5bq9yB2mPnSMVvUWd7q3gIA';
	// (iOS) Private key's passphrase.
	private static $passphrase = '1234';
	// (Windows Phone 8) The name of our push channel.
    private static $channelName = "joashp";
	
	// Change the above three vriables as per your app.

	public function __construct() {
		exit('Init function is not allowed');
	}
	
        // Sends Push notification for Android users
	public static function android($data, $reg_id) {
	        // $url = 'https://android.googleapis.com/gcm/send';
	        $url = 'https://fcm.googleapis.com/fcm/send';
	       
	        $message = array();
	        $message['data']['title'] = $data['mtitle'];
	        $message['data']['is_background'] = false ;
	        $message['data']['message'] = $data['mdesc'];
	        $message['data']['image'] ='';
	        $message['data']['payload'] = $data['data'];
	        $message['data']['timestamp'] = date('Y-m-d G:i:s');
	        
	        $headers = array(
	        	'Authorization: key=' .self::$API_ACCESS_KEY,
	        	'Content-Type: application/json'
	        );
	        // if device token is array
/*--------------------------------------------------------*/
			/*$regIdChunk=array_chunk($AndroidDeviceToken,1000);
    			foreach($regIdChunk as $RegId){
					$url = 'https://android.googleapis.com/gcm/send';
					$fields = array(
				                'registration_ids'  => $RegId,
				                'data'              => array("message" => $message,"matchID" =>$matchID, "action" =>$type),
				                );
				 
					$headers = array( 
				                    'Authorization: key=' . $key,
				                    'Content-Type: application/json'
				                );
				}*/
/*-------------------------------------------------------------------------*/	

	        /*$fields = array(
	            'registration_ids' => array($reg_id),
	            'data' => $message,
	        );*/

	         $fields = array(
	            'to' => $reg_id,
	            'data' => $message,
	        );
	
	    	return self::useCurl($url, $headers, json_encode($fields));
    	}
	
	// Sends Push's toast notification for Windows Phone 8 users
	public static function WP($data, $uri) {
		$delay = 2;
		$msg =  "<?xml version=\"1.0\" encoding=\"utf-8\"?>" .
		        "<wp:Notification xmlns:wp=\"WPNotification\">" .
		            "<wp:Toast>" .
		                "<wp:Text1>".htmlspecialchars($data['mtitle'])."</wp:Text1>" .
		                "<wp:Text2>".htmlspecialchars($data['mdesc'])."</wp:Text2>" .
		            "</wp:Toast>" .
		        "</wp:Notification>";
		
		$sendedheaders =  array(
		    'Content-Type: text/xml',
		    'Accept: application/*',
		    'X-WindowsPhone-Target: toast',
		    "X-NotificationClass: $delay"
		);
		
		$response = self::useCurl($uri, $sendedheaders, $msg);
		
		$result = array();
		foreach(explode("\n", $response) as $line) {
		    $tab = explode(":", $line, 2);
		    if (count($tab) == 2)
		        $result[$tab[0]] = trim($tab[1]);
		}
		
		return $result;
	}
	
        // Sends Push notification for iOS users
	public static function iOS($data, $devicetoken) {

		$deviceToken = $devicetoken;

		$ctx = stream_context_create();
		// ck.pem is your certificate file
		$files = ROOT .DS. "src" . DS  . "Lib" . DS . "fireguardPush.pem";
		#var_dump($files); die();
		stream_context_set_option($ctx, 'ssl', 'local_cert', $files);
		stream_context_set_option($ctx, 'ssl', 'passphrase', self::$passphrase);

		// Open a connection to the APNS server
		$fp = stream_socket_client(
			'ssl://gateway.sandbox.push.apple.com:2195', $err,
			$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

		if (!$fp)
			exit("Failed to connect: $err $errstr" . PHP_EOL);

		// Create the payload body
		$body['aps'] = array(
			'alert' => array(
			    'title' => $data['mtitle'],
                'body' => $data['mdesc'],
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
		
		
		// Close the connection to the server
		fclose($fp);

		/*if (!$result)
			return 'Message not delivered' . PHP_EOL;
		else
			return 'Message successfully delivered' . PHP_EOL;*/
		return empty($result) ? false : True;

	}
	
	// Curl 
	private static function useCurl($url, $headers, $fields = null) {
	        // Open connection
	        $ch = curl_init();
	        if ($url) {
	            // Set the url, number of POST vars, POST data
	            curl_setopt($ch, CURLOPT_URL, $url);
	            curl_setopt($ch, CURLOPT_POST, true);
	            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	     
	            // Disabling SSL Certificate support temporarly
	            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	            if ($fields) {
	                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
	            }
	     
	            // Execute post
	            $result = curl_exec($ch);
	            if ($result === FALSE) {
	                die('Curl failed: ' . curl_error($ch));
	            }
	     
	            // Close connection
	            curl_close($ch);
	
	            return $result;
        }
    }
    
}
?>