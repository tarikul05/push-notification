<?php 
var_dump($_REQUEST);
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
					<form action="" method="GET" role="form" class="form-horizontal">
						<legend>Send Push</legend>
						
						<div class="form-group">
							<label class="control-label col-sm-4 col-md-3" for="">Certificate Password</label>
							<div class="col-sm-8 col-md-8">
								<input type="text" name="pass" class="form-control" id="pass" placeholder="Certificate Password">
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
						<button type="submit" class="btn btn-primary pull-right">Submit</button>
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