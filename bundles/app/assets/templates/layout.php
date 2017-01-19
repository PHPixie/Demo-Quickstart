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
	</div>
</nav>


<?php $this->childContent(); ?>


<!-- Bootstrap dependencies -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.3.7/js/tether.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js"></script>

</body>
</html>