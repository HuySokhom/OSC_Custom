<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2010 osCommerce

  Released under the GNU General Public License
*/

  require(DIR_WS_INCLUDES . 'counter.php');
?>
<style>
.footer{
	clear: both;
	width: 100%;
	background: #5c9;
	height: 80px;
	margin-top: 10px;
}
</style>
<div class="footer">
  <p align="center" style="padding-top: 23px"><?php echo FOOTER_TEXT_BODY; ?></p>
</div>

<?php /*
  if ($banner = tep_banner_exists('dynamic', 'footer')) {
?>

<div class="grid_24" style="text-align: center; padding-bottom: 20px;">
  <?php echo tep_display_banner('static', $banner); ?>
</div>

<?php
  }*/
?>

<script type="text/javascript">
$('.productListTable tr:nth-child(even)').addClass('alt');
</script>
