<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" xml:lang="<?php echo $lang; ?>" <?php echo $this->getHookVar('hk_html_attribute'); ?>>
<head><?php echo $head; ?></head>
<body class="<?php echo str_replace("/", "-", $this->request->get['rt']) ?: 'home'; ?>">

<?php echo $this->getHookVar('top_page'); ?>

<div class="container-fixed" style="max-width: <?php echo $layout_width; ?>">

    <?php if ($maintenance_warning) { ?>
		<div class="alert alert-warning">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<strong><?php echo $maintenance_warning; ?></strong>
            <?php if ($act_on_behalf_warning) { ?>
				<br/><strong><?php echo $act_on_behalf_warning; ?></strong>
            <?php } ?>
		</div>
        <?php
    }
    if ($act_on_behalf_warning && !$maintenance_warning) { ?>
		<div class="alert alert-warning">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<strong><?php echo $act_on_behalf_warning; ?></strong>
		</div>
    <?php }
    echo ${$header}; ?>

    <?php if (!empty(${$header_bottom})) { ?>
		<!-- header_bottom blocks placeholder -->
		<div class="container-fluid">
            <?php echo ${$header_bottom}; ?>
		</div>
		<!-- header_bottom blocks placeholder -->
    <?php } ?>

	<div id="maincontainer">

        <?php
        //check layout dynamicaly
        $present_columns = 1;
        $center_padding = '';
        if (!empty(${$column_left})) {
            $present_columns++;
            $center_padding .= 'ct_padding_left';
        }
        if (!empty(${$column_right})) {
            $present_columns++;
            $center_padding .= ' ct_padding_right';
        }
        ?>

		<div class="container-fluid">
            <?php if (!empty(${$column_left})) { ?>
				<div class="column_left col-md-4 col-xs-12">
                    <?php echo ${$column_left}; ?>
				</div>
            <?php } ?>
            <?php $span = 12 - 4 * ($present_columns - 1); ?>

            <?php if (!empty(${$column_right})) { ?>
				<div class="column_right col-md-4 col-xs-12 col-md-push-<?php echo $span ?>  mt10">
                    <?php echo ${$column_right}; ?>
				</div>
            <?php } ?>

			<div class="col-md-<?php echo $span ?> col-xs-12 <?php if (!empty(${$column_right})) { ?> col-md-pull-4 <?php } ?> mt10">
                <?php if (!empty(${$content_top})) { ?>
					<!-- content top blocks placeholder -->
                    <?php echo ${$content_top}; ?>
					<!-- content top blocks placeholder (EOF) -->
                <?php } ?>

				<div class="<?php echo $center_padding; ?>">
                    <?php echo $content; ?>
				</div>

                <?php if (!empty(${$content_bottom})) { ?>
					<!-- content bottom blocks placeholder -->
                    <?php echo ${$content_bottom}; ?>
					<!-- content bottom blocks placeholder (EOF) -->
                <?php } ?>
			</div>
		</div>

	</div>

    <?php if (!empty(${$footer_top})) { ?>
		<!-- footer top blocks placeholder -->
		<div class="container-fluid">
			<div class="col-md-12">
                <?php echo ${$footer_top}; ?>
			</div>
		</div>
		<!-- footer top blocks placeholder -->
    <?php } ?>

	<!-- footer blocks placeholder -->
	<div id="footer">
        <?php echo ${$footer}; ?>
	</div>

</div>

<!--
AbanteCart is open source software and you are free to remove the Powered By AbanteCart if you want, but its generally accepted practise to make a small donation.
Please donate http://www.abantecart.com/donate
//-->

<?php
/*
	Placed at the end of the document so the pages load faster

	For better rendering minify all JavaScripts and merge all JavaScript files in to one singe file
	Example: <script type="text/javascript" src=".../javascript/footer.all.min.js" defer async></script>

Check Dan Riti's blog for more fine tunning suggestion:
https://www.appneta.com/blog/bootstrap-pagespeed/
		*/
?>
<script type="text/javascript" src="<?php echo $this->templateResource('/javascript/bootstrap.min.js'); ?>" defer></script>
<script type="text/javascript" src="<?php echo $this->templateResource('/javascript/common.js'); ?>" defer async></script>
<script type="text/javascript" src="<?php echo $this->templateResource('/javascript/respond.min.js'); ?>" defer async></script>
<script type="text/javascript" src="<?php echo $this->templateResource('/javascript/jquery.flexslider-min.js'); ?>" defer async></script>
<script type="text/javascript" src="<?php echo $this->templateResource('/javascript/easyzoom.js'); ?>" defer async></script>
<script type="text/javascript" src="<?php echo $this->templateResource('/javascript/jquery.validate.min.js'); ?>" defer async></script>
<script type="text/javascript" src="<?php echo $this->templateResource('/javascript/jquery.carouFredSel.min.js'); ?>" defer async></script>
<script type="text/javascript" src="<?php echo $this->templateResource('/javascript/jquery.mousewheel.min.js'); ?>" defer async></script>
<script type="text/javascript" src="<?php echo $this->templateResource('/javascript/jquery.touchSwipe.min.js'); ?>" defer async></script>
<script type="text/javascript" src="<?php echo $this->templateResource('/javascript/jquery.ba-throttle-debounce.min.js'); ?>" defer async></script>
<script type="text/javascript" src="<?php echo $this->templateResource('/javascript/jquery.onebyone.min.js'); ?>" defer async></script>
<script type="text/javascript" src="<?php echo $this->templateResource('/javascript/custom.js'); ?>" defer async></script>
<?php
if ($scripts_bottom && is_array($scripts_bottom)) {
    foreach ($scripts_bottom as $script) {
        ?>
		<script type="text/javascript" src="<?php echo $script; ?>" defer></script>
        <?php
    }
}
?>

</body>
</html>