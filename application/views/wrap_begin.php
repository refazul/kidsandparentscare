<div id="module-container">
    <a style="float:right;font-weight:bold;text-decoration: none;margin-top:7px;margin-right:35px;" href="<?php echo site_url();?>logout">Log out</a>
    <div style="float:right;margin-top:10px;margin-right:35px;font-size:12px;text-decoration: none;">
        <div style='float:left;'>Logged in as :</div><div style='float:right;margin-left:10px;'><?php echo $this->session->userdata('full_name');?></div>
    </div>
    <div id="module">
