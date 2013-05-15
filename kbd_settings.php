<style type="text/css">
a
{
	cursor:pointer;
}


medium.expected


{


	float:right;


}


</style>


<div class="wrap">

<div class="icon32" id="icon-options-general"><br></div><h2>Keep Backup Daily - Settings</h2>

<?php echo $settings['notification']; $wpurl = get_bloginfo('wpurl'); ?>

<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">

<input type="hidden" name="kbd_key" value="<?php echo $settings['kbd_key']; ?>">

<table class="form-table">

<tbody><tr valign="top">

<th scope="row">Backup Required</th>

<td id="front-static-pages"><fieldset>

	<p><label for="cron_d">

		<input type="radio" <?php echo ($settings['backup_required']=='cron_d'?'checked="checked"':''); ?> class="tog" id="cron_d" value="cron_d" name="backup_required">Daily</label>


		<?php echo $settings['cron_d']['expected_backup']; ?>


	</p>

	<p><label for="cron_w">

		<input type="radio" <?php echo ($settings['backup_required']=='cron_w'?'checked="checked"':''); ?> class="tog" id="cron_w" value="cron_w" name="backup_required">Weekly</label>


		<?php echo $settings['cron_w']['expected_backup']; ?>


	</p>

	<p><label for="cron_m">

		<input type="radio" <?php echo ($settings['backup_required']=='cron_m'?'checked="checked"':''); ?> class="tog" id="cron_m" value="cron_m" name="backup_required">Monthly</label>


		<?php echo $settings['cron_m']['expected_backup']; ?>


	</p>    

	<p><label for="cron_y">

		<input type="radio" <?php echo ($settings['backup_required']=='cron_y'?'checked="checked"':''); ?> class="tog" id="cron_y" value="cron_y" name="backup_required">Yearly</label>


		<?php echo $settings['cron_y']['expected_backup']; ?>


	</p> 
	<p>
    <a id="cron_now" title="Click here to backup now">Click here to backup now</a>
    </p>
    
</fieldset></td>

</tr>

<tr valign="top">

<th scope="row"><label for="recpient_email_address">Recipient Email Address</label></th>

<td>

<input type="text" class="medium-text" value="<?php echo is_array($settings['recpient_email_address'])?implode(',', $settings['recpient_email_address']):$settings['recpient_email_address']; ?>" step="1" name="recpient_email_address" id="recpient_email_address">

<p class="description">Default: <?php echo $default_email; ?></p>

</td></tr>

<tr valign="top">

<th scope="row">Maintain Log</th>

<td><fieldset>

	<label for="maintain_log"><input type="checkbox" value="1" <?php echo ($settings['maintain_log']==1?'checked="checked"':''); ?> name="maintain_log" id="maintain_log">

	You will be able to view log with date and time.</label>

	<p class="description">Only log file will be stored on your server.</p>

    

    <p class="description">

    <?php if($settings['log']!=''): ?>

    <div style="height:160px; background-color:#F3F3F3; overflow:auto; width:64%;">

    <?php echo nl2br($settings['log']); ?>

    </div>

    <?php endif; ?>

    </p>

</fieldset></td>

</tr>

<tr valign="top">

<th scope="row">Cron Job Settings <span title="By default we will access cron file placed on your server for your convenience. Because most of the users don't have idea that how to set a cron or conscious about their server performance." style="color:red">(Important)</span></th>

<td><fieldset>

	<p><label for="kbd_cron_default">

		<input <?php echo ($settings['cron_server']=='default'?'checked="checked"':''); ?> type="radio" class="tog" id="kbd_cron_default" value="default" name="cron_server">Default</label>

	</p>

    <p><label for="kbd_cron_custom">

		<input <?php echo ($settings['cron_server']=='custom'?'checked="checked"':''); ?> type="radio" class="tog" id="kbd_cron_custom" value="custom" name="cron_server">Custom <a>(more)</a></label>

	</p>

    <p class="description cron_line" style="display:none">You have to run the following file, write the cron job command which is suitable on your server. <input type="text" class="large-text" value="<?php echo $wpurl; ?>/?kbd_cron_process=1"></p>

</fieldset></td>

</tr>
</tbody></table>

<p class="submit"><input type="submit" value="Save Changes" class="button button-primary" id="submit" name="submit"></p></form>

</div>

<script type="text/javascript" language="javascript">

jQuery('#kbd_cron_custom').parent().find('a').click(function(){

	jQuery('.cron_line').toggle();

});jQuery('#cron_now').click(function(){

	jQuery(this).parent().append('<p class="sending_backup">Sending to '+jQuery('#recpient_email_address').val()+'</p>');
	jQuery(this).remove();
	
	var jqxhr = jQuery.get(jQuery('.cron_line input').val(), function() {
	
	})
	.done(function() { jQuery('.sending_backup').html('Successfully sent.'); })
	.fail(function() { jQuery('.sending_backup').html('Failed.'); })
	.always(function() { jQuery('.sending_backup').html('Please check your inbox.'); });

	
});

</script>