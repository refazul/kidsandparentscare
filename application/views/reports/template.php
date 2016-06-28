<script type='text/javascript'>
    BASE = '<?php echo base_url();?>';
</script>
<script type="text/javascript" src="<?php echo asset_url();?>js/dom.js"></script>
<script type="text/javascript" src="<?php echo asset_url();?>js/report.js"></script>
<script type="text/javascript">
    Report.report_build(BASE, '<?php echo $report_type;?>');
</script>
