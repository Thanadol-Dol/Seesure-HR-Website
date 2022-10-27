<?php include('server.php') ?>
<!DOCTYPE html>
<html>
<head>
  <title>SEESURE Login</title>
    <?php
        include('include_css.php');
    ?>
  <link rel="stylesheet" type="text/css" href="/assets/css/login_style.css">
</head>

<style>
    h3
    {
        font-size: 72px;
        margin-top: 50px;
    }
</style>

<body>
    <div>
  	<h3 align = 'center'>SEESURE</h3>
  </div>
    
  <div class="header">
  	<h2>Login</h2>
  </div>
	 
  <form method="post" action="login.php">
  	<?php  if (count($errors) > 0) : ?>
  <div class="error">
  	<?php foreach ($errors as $error) : ?>
  	  <p><?php echo $error ?></p>
  	<?php endforeach ?>
  </div>
<?php  endif ?>
  	<div class="input-group">
  		<label>Username</label>
  		<input type="text" name="username" >
  	</div>
  	<div class="input-group">
  		<label>Password</label>
  		<input type="password" name="password">
  	</div>
  	<div class="input-group">
  		<button type="submit" class="btn" name="login_user">Login</button>
  	</div>
  </form>
</body>
</html>