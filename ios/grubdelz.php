<?php 
function iOS($data, $deviceToken, $pass = 'grubdealz17') {
	$ctx = stream_context_create();
	$files = "pem_development_push.pem";
	stream_context_set_option($ctx, 'ssl', 'local_cert', $files);
	stream_context_set_option($ctx, 'ssl', 'passphrase', $pass);
	$fp = stream_socket_client(
		'ssl://gateway.sandbox.push.apple.com:2195', $err,
		$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
	if (!$fp)
		exit("Failed to connect: $err $errstr" . PHP_EOL);
	$body['aps'] = array(
		'alert' => array(
		    'title' => $data['title'],
            'body' => $data['desc'],
		 ),
		'sound' => 'default',
		'data' => $data['data'],
	);

	$payload = json_encode($body);
	if (is_array($deviceToken)) {
		foreach ($deviceToken as $key => $token) {
			$msg = chr(0) . pack('n', 32) . pack('H*', $token) . pack('n', strlen($payload)) . $payload;
			$result = fwrite($fp, $msg, strlen($msg));
		}
	}else{
		$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
		$result = fwrite($fp, $msg, strlen($msg));
	}
	fclose($fp);
	return empty($result) ? false : True;
}

if (isset($_POST['submit'])) {
	$title = $_POST['title'];
	$description = $_POST['description'];
	$tokens = explode(',', $_POST['token']);

	$paramData = [];
	foreach ($_POST['param'] as $key => $value) {
		if (!empty($value['key'])) {
			$paramData[$value['key']] = $value['value'];
		}
	}

	$data = array (
	            'title' => $title,
	            'desc' => $description,
	            'data' => $paramData
	        );	
              
	$flag = iOS($data, $tokens);
}

 ?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>IOS push</title>
		<link href="https://netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
	</head>
	<body>
		<div class="container">
			<div class="row">
				<div class="col-md-8">
				<?php if (isset($flag) && !empty($flag)): ?>
					<div class="alert">
						<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
						<strong>Success!</strong> The push has been sent
					</div>
				<?php endif ?>
					
					<form action="" method="POST" role="form" class="form-horizontal">
						<legend>Send Push</legend>
						
						<!-- <div class="form-group">
							<label class="control-label col-sm-4 col-md-3" for="">Certificate Password</label>
							<div class="col-sm-8 col-md-8">
								<input type="text" name="pass" class="form-control" id="pass" placeholder="Certificate Password">
							</div>
						</div> -->

						<div class="form-group">
							<label class="control-label col-sm-4 col-md-3" for="">Device Token</label>
							<div class="col-sm-8 col-md-8">
								<input type="text" name="token" class="form-control" id="token" placeholder="Multiple token seperated by (,)">
							</div>
						</div>

						<div class="form-group">
							<label class="control-label col-sm-4 col-md-3" for="">Title</label>
							<div class="col-sm-8 col-md-8">
								<input type="text" name="title" class="form-control" id="title" placeholder="Title of Push">
							</div>
						</div>

						<div class="form-group">
							<label class="control-label col-sm-4 col-md-3" for="">Description</label>
							<div class="col-sm-8 col-md-8">
								<textarea class="form-control" rows="5" name="description" id="description" placeholder="Description of Push"></textarea>
							</div>
						</div>
					<h2>Params</h2>
						<div id="params">
						<?php for( $i = 1; $i <= 5 ; $i++){ ?>
							<div class="form-group">
								<div class="col-sm-4 col-md-4">
									<input type="text" name="param[<?php echo $i ?>][key]" class="form-control" id="param-<?php echo $i ?>-key" placeholder="param <?php echo $i ?> key">
								</div>
								<div class="col-sm-8 col-md-8">
									<input type="text" name="param[<?php echo $i ?>][value]" class="form-control" id="param-<?php echo $i ?>-value" placeholder="param <?php echo $i ?> value">
								</div>
							</div>
						<?php } ?>
						</div>	
						<button type="submit" name="submit" class="btn btn-primary pull-right">Submit</button>
					</form>
				</div>
				<div class="col-md-4">
					
				</div>
			</div>
		</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery.js"></script>
<!-- Bootstrap JavaScript -->
<script src="https://netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

<script type="text/javascript">
	
</script>
</body>
</html>