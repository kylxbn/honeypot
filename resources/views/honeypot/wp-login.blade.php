<!DOCTYPE html>
<html lang="en-US">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Log In &lsaquo; {{ config('honeypot.company.name') }} &#8212; WordPress</title>
<style>
html{background:#f0f0f1}
body{background:#f0f0f1;color:#3c434a;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif;font-size:13px;line-height:1.4;margin:0;}
#login{width:320px;padding:5% 0 5%;margin:auto;}
#login h1 a{background-image:url(https://s.w.org/style/images/about/WordPress-logotype-wmark.png);background-size:84px;background-repeat:no-repeat;background-position:center top;color:#3c434a;display:block;height:84px;text-indent:-9999px;width:84px;margin:0 auto 25px;}
.login form{margin-top:0;padding:26px 24px;background:#fff;border:1px solid #c3c4c7;box-shadow:0 1px 3px rgba(0,0,0,.04);border-radius:2px;}
.login form .forgetmenot{float:left;}
.login .button.button-primary{float:right;background:#2271b1;border-color:#2271b1;color:#fff;font-size:14px;padding:0 20px;height:40px;border-radius:3px;cursor:pointer;border:1px solid #2271b1;line-height:38px;}
.login .button.button-primary:hover{background:#135e96;border-color:#135e96;}
label{display:block;font-size:14px;font-weight:600;margin-bottom:4px;color:#3c434a;}
input[type=text],input[type=password]{background:#fff;border:1px solid #8c8f94;border-radius:3px;box-shadow:0 0 0 transparent;color:#2c3338;font-family:inherit;font-size:16px;line-height:1.33333333;margin:0;outline:0;padding:3px 10px;width:100%;box-sizing:border-box;height:40px;}
.forgetmenot label{font-size:13px;font-weight:400;}
.login #nav{font-size:13px;text-align:center;margin-top:1em;}
.login #nav a{color:#50575e;text-decoration:none;}
.login #nav a:hover{color:#135e96;}
.login #backtoblog{font-size:13px;text-align:center;margin-top:.5em;}
.login #backtoblog a{color:#50575e;text-decoration:none;}
#login_error{background:#fff0f0;border-left:4px solid #d63638;padding:12px;}
p.submit{overflow:hidden;padding:0;}
</style>
</head>
<body class="login">

<div id="login">
  <h1><a href="https://wordpress.org/">WordPress</a></h1>

  @if(session('login_error'))
  <div id="login_error"><strong>Error:</strong> {{ session('login_error') }}</div>
  @endif

  <form name="loginform" id="loginform" action="/wp-login.php" method="post">
    <p>
      <label for="user_login">Username or Email Address</label>
      <input type="text" name="log" id="user_login" class="input" size="20" autocapitalize="off" autocomplete="username" required>
    </p>
    <p>
      <label for="user_pass">Password</label>
      <input type="password" name="pwd" id="user_pass" class="input" size="20" autocomplete="current-password" required>
    </p>

    <p class="forgetmenot">
      <label><input name="rememberme" type="checkbox" id="rememberme" value="forever"> Remember Me</label>
    </p>

    <p class="submit">
      <input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" value="Log In">
      <input type="hidden" name="redirect_to" value="/wp-admin/">
      <input type="hidden" name="testcookie" value="1">
      @csrf
    </p>
  </form>

  <p id="nav">
    <a href="/wp-login.php?action=lostpassword">Lost your password?</a>
  </p>
  <p id="backtoblog">
    <a href="/">&larr; Go to {{ config('honeypot.company.name') }}</a>
  </p>
</div>

</body>
</html>
