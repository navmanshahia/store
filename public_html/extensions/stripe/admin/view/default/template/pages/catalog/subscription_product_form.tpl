<?php include($tpl_common_dir . 'action_confirm.tpl'); ?>

<?php echo $summary_form; ?>

<?php echo $product_tabs ?>
<div id="content" class="panel panel-default">

	<div class="panel-heading col-xs-12">
		<div class="primary_content_actions pull-left">
			<?php if ($product_id) { ?>
			<div class="btn-group mr10 toolbar">
				<a class="btn btn-white lock-on-click tooltips" href="<?php echo $clone_url; ?>" data-toggle="tooltip" title="<?php echo $text_clone; ?>" data-original-title="<?php echo $text_clone; ?>">
				<i class="fa fa-tags"></i>
				</a>
			</div>
			<?php } ?>
		</div>
		<?php include($tpl_common_dir . 'content_buttons.tpl'); ?>
	</div>

	<?php echo $form['form_open']; ?>
	<div class="panel-body panel-body-nopadding tab-content col-xs-12">
		
		<?php foreach ($form['fields'] as $section => $fields) { ?>
		<label class="h4 heading"><?php echo ${'tab_' . $section}; ?></label>         
			<?php foreach ($fields as $name => $field) { ?>
			<?php 
				//Logic to calculate fields width
				$widthcasses = "col-sm-7";
				if ( is_int(stripos($field->style, 'large-field')) ) {
					$widthcasses = "col-sm-7";
				} else if ( is_int(stripos($field->style, 'medium-field')) || is_int(stripos($field->style, 'date')) ) {
					$widthcasses = "col-sm-5";
				} else if ( is_int(stripos($field->style, 'small-field')) || is_int(stripos($field->style, 'btn_switch')) ) {
					$widthcasses = "col-sm-3";
				} else if ( is_int(stripos($field->style, 'tiny-field')) ) {
					$widthcasses = "col-sm-2";
				}
				$widthcasses .= " col-xs-12";
			?>
		<div class="form-group <?php if (!empty($error[$name])) { echo "has-error"; } ?>">
			<label class="control-label col-sm-3 col-xs-12" for="<?php echo $field->element_id; ?>"><?php echo ${'entry_' . $name}; ?></label>
			<div class="input-group afield <?php echo $widthcasses; ?> <?php echo ($name == 'description' ? 'ml_ckeditor' : '')?>">
				<?php if($name == 'keyword') { ?>
				<span class="input-group-btn">
					<?php echo $keyword_button; ?>
				</span>
				<?php } ?>
				<?php echo $field; ?>
			</div>
			<?php if (!empty($error[$name])) { ?>
			<span class="help-block field_err"><?php echo $error[$name]; ?></span>
			<?php } ?>
		</div>
		<?php }
		} ?>
	</div>
	<div class="panel-footer col-xs-12">
		<div class="text-center">
			<button class="btn btn-primary lock-on-click">
			<i class="fa fa-save fa-fw"></i> <?php echo $form['submit']->text; ?>
			</button>
			<a class="btn btn-default" href="<?php echo $cancel; ?>">
			<i class="fa fa-arrow-left fa-fw"></i> <?php echo $form['cancel']->text; ?>
			</a>
		</div>
	</div>
	</form>
</div>

<?php echo $resources_scripts; ?>

<script type="text/javascript">

$(document).ready(function () {
	$('#productFrm_generate_seo_keyword').click(function(){
		var seo_name = $('#productFrm_product_descriptionname').val().replace('%','');
		$.get('<?php echo $generate_seo_url;?>&seo_name='+seo_name, function(data){
			$('#productFrm_keyword').val(data).change();
		});
	});

	$('#donation_type').on('change', function(){
		var value = $(this).val();
		var wrp = $('#donation_min_amt, #donation_max_amt').parents('.form-group');
		if(value == 'fixed' || value == 'selectable'){
			wrp.hide();
		}else{
			wrp.show();
		}
	});
	$('#donation_type').change();
});

</script>