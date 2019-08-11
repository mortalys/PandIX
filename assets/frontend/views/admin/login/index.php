<!DOCTYPE html>
<html lang="en">
<head>
    
	
    <?php include $fullPath."/_core/head.inc"; ?>

</head>

<body>

    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <div class="login-panel panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Please Sign In</h3>
                    </div>
                    <div class="panel-body">
                        <form role="form" id="loginForm">
                            <fieldset>
                                <div class="form-group">
                                    <input class="form-control" placeholder="E-mail" name="loginForm_email" type="email" autofocus>
                                </div>
                                <div class="form-group">
                                    <input class="form-control" placeholder="Password" name="loginForm_password" type="password" value="">
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input name="remember" type="checkbox" value="Remember Me">Remember Me
                                    </label>
                                </div>                                
                                <input name="c" type="hidden" value="users.do.usersLogin">
                                <input name="formAlias" type="hidden" value="loginForm_">
                                <a href="#" class="btn btn-lg btn-success btn-block" id="loginForm_btn">Login</a>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>  
	<?php include $fullPath."/_core/footerScripts.inc"; ?>    
    
    <!-- page scripts : begin -->
    
        <script src="./assets/frontend/views/admin/login/login.js"></script>
        
    <!-- page scripts : end -->      

</body>

</html>
