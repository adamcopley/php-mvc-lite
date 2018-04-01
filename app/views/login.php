<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="favicon.ico">

    <title>Sign in</title>

  </head>

  <body>

    <div class="container" align="center">
        <br>
        <br>
		<?php echo $message; ?>
        <br>
        <br>
      <form action="/auth/login" method="POST" class="form-signin">
        <h2 class="form-signin-heading">Please sign in</h2>
        <label for="inputEmail" class="sr-only">Email address</label>
        <input type="email" name="email" id="inputEmail" size="30" class="input-sm" placeholder="Email address" required autofocus><br>
        <label for="inputPassword" class="sr-only">Password</label>
        <input type="password" name="password" id="inputPassword" size="30" class="input-sm" placeholder="Password" required>
		<input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>" />
        <div class="checkbox">
          <label>
            <input type="checkbox" value="remember-me"> Remember me
          </label>
        </div>
        <button class="btn btn-primary" type="submit">Sign in</button>
      </form>

    </div> <!-- /container -->

  </body>
</html>
