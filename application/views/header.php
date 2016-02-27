<!DOCTYPE html>
<html lang="en">
    <head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Cotfield</title>

        <link rel="shortcut icon" href="<?php echo asset_url();?>images/favicon.ico" />

        <script type="text/javascript" src="<?php echo asset_url();?>js/jquery-1.11.0.min.js"></script>
        <script type="text/javascript" src="<?php echo asset_url();?>js/jqueryui/jquery-ui.min.js"></script>
        <script type="text/javascript" src="<?php echo asset_url();?>js/jquery.form.js"></script>
        <script type="text/javascript" src="<?php echo asset_url();?>js/jquery.mCustomScrollbar.min.js"></script>
        <script type="text/javascript" src="<?php echo asset_url();?>js/jquery.mousewheel.min.js"></script>
        <script type="text/javascript" src="<?php echo asset_url();?>js/md5.js"></script>
        <script type="text/javascript" src="<?php echo asset_url();?>js/moment.min.js"></script>
        <script type="text/javascript" src="<?php echo asset_url();?>js/utility.js"></script>
        <script type="text/javascript" src="<?php echo asset_url();?>js/patterns.js"></script>


        <link rel="stylesheet" media="all" href="<?php echo asset_url();?>css/bootstrap.min.css">
        <link rel="stylesheet" media="all" href="<?php echo asset_url();?>css/jquery-ui.min.css"/>
        <link rel="stylesheet" media="all" href="<?php echo asset_url();?>css/jquery-ui.theme.min.css"/>
        <link rel="stylesheet" media="all" href="<?php echo asset_url();?>css/jquery.mCustomScrollbar.css"/>



        <link rel="stylesheet" media="all" href="<?php echo asset_url();?>css/style.css" />
	<link rel="stylesheet" media="all" href="<?php echo asset_url();?>css/layout.css" />
	<link rel="stylesheet" media="all" href="<?php echo asset_url();?>css/doc.css" />
	<link rel="stylesheet" media="all" href="<?php echo asset_url();?>css/fonts.css" />
	<link rel="stylesheet" media="all" href="<?php echo asset_url();?>css/theme.css" />

	<!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		<script src="<?php echo asset_url();?>js/html5shiv.js"></script>
		<script src="<?php echo asset_url();?>js/respond.min.js"></script>
	<![endif]-->

        <script type="text/javascript">
            $(document).ready(function(){
                $(window).bind("keydown", function(e) {

                    var code = e.keyCode || e.which;
                    if (code  == 13)
                    {
                        //window.location='<?php //echo site_url();?>products/all';
                        //e.preventDefault();
                        //return false;
                        //if(!$('#filter').is(':focus'))
                        {
                            $('#filter').focus();
                            $('#filter').val('');
                        }
                        e.preventDefault();
                        return false;
                    }

                });
            });
        </script>
    </head>
    <body>
        <div id="template-wrapper">
