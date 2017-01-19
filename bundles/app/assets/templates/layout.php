<!DOCTYPE html>
<html lang="en">
<head>
	<!-- Let's try Bootstrap 4 -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css">

	<!-- Include our own css -->
	<link rel="stylesheet" href="/bundles/app/main.css">

	<title><?=$_($this->get('pageTitle', 'Quickstart'))?></title>
</head>
<body>

<!-- Navigation -->
<nav class="navbar navbar-toggleable-md navbar-light bg-faded">
	<div class="container">
		<a class="navbar-brand  mr-auto" href="<?=$this->httpPath('app.frontpage')?>">Quickstart</a>

		<?php if($user): ?>
			<!-- If user is loged in display a welcome message and a Sign Out button -->
			<span class="navbar-text  mr-auto">
	      		Hi, <b><?=$_($user->name)?></b>
	    	</span>
			<ul class="navbar-nav">
				<li class="nav-item">
					<?php $url = $this->httpPath('app.action', ['processor' => 'auth', 'action' => 'logout']);?>
					<a class="nav-link" href="<?=$url?>">Sign Out</a>
				</li>
			</ul>

		<?php else: ?>
			<!-- Otherwise show a Sign In link -->
			<ul class="navbar-nav">
				<li class="nav-item">
					<?php $url = $this->httpPath('app.processor', ['processor' => 'auth']);?>
					<a class="nav-link" href="<?=$this->httpPath('app.processor', ['processor' => 'auth'])?>">Sign In</a>
				</li>
			</ul>

		<?php endif;?>
	</div>
</nav>


<?php $this->childContent(); ?>


<!-- Bootstrap dependencies -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.3.7/js/tether.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js"></script>
</body>
</html>