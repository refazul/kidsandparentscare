<!DOCTYPE html>
<!--[if lt IE 7 ]> <html lang="en" class="ie6 ielt8"> <![endif]-->
<!--[if IE 7 ]>    <html lang="en" class="ie7 ielt8"> <![endif]-->
<!--[if IE 8 ]>    <html lang="en" class="ie8"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html lang="en"> <!--<![endif]-->
<head>
<meta charset="utf-8">
<title>Kids and Parents Care</title>
    <link rel="stylesheet" href="<?php echo asset_url();?>css/login.css" />
    <link rel="shortcut icon" href="<?php echo asset_url();?>images/favicon.ico" />
    <script type="text/javascript" src="<?php echo asset_url();?>js/jquery-1.11.0.min.js"></script>
    <script type="text/javascript" src="<?php echo asset_url();?>js/jquery.form.js"></script>
    <script type="text/javascript" src="<?php echo asset_url();?>js/md5.js"></script>
</head>
<body>
<div class="container">
	<section id="content">
		<form id="login-form" action="<?php echo site_url();?>login/authenticate" method="POST">
			<h1>Kids and Parents Care</h1>
                        <img class="profile-img" src="<?php echo site_url()?>assets/images/avatar_2x.png"/>
			<div>
                            <input type="text" placeholder="Username" required="" id="username" autocomplete="off" name="user"/>
			</div>
			<div>
                            <input type="password" placeholder="Password" required="" id="password" autocomplete="off" name="pass"/>
			</div>
			<div>
				<input type="submit" value="Log in" />				
			</div>
		</form><!-- form -->
	</section><!-- content -->
</div><!-- container -->
<script type="text/javascript">
    $(document).ready(function(){
        $('#login-form').ajaxForm({
            
            /* set data type json */
            dataType:  'json',

            /* reset before submitting */
            beforeSubmit: function(formData) {                
                formData[1].value = CryptoJS.MD5(formData[1].value).toString();
                //console.log(formData);                
            },

            /* progress bar call back*/
            uploadProgress: function(event, position, total, percentComplete) {                                        
            },

            /* complete call back */
            complete: function(data) {
                console.log(data);
                window.location=window.location;
            }
        });
    });
</script>
</body>
</html>