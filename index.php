<?php 

/*

Plugin Name: Keep Backup Daily

Plugin URI: http://www.websitedesignwebsitedevelopment.com/plugins/keep_backup_daily.zip

Description: This plugin will backup the mysql tables and email to a specified email address daily, weekly, monthly or even yearly. It will provide you a convenience of settings and having backup in inbox to maintain it on disk as well.

Version: 1.0

Author: Fahad Mahmood 

Author URI: http://www.androidbubbles.com

License: GPL3

*/ 

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	include('functions.php');

	include('kbd_cron.php');





	function kbd_menu()

	{

	

		

		 add_options_page('Keep Backup Daily', 'KBD Settings', 'update_core', 'kbd_settings', 'kbd_settings',   plugin_dir_url(__FILE__).'/database_email.png', 66);

		 

		 

		 

	}

	

	function kbd_settings() 

	{ 

		if ( !current_user_can( 'update_core' ) )  {

			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );

		}

		

		

		 

		

		

		global $wpdb; 

		$salt = date('YmddmY')+date('m');

		$default_email = 'info@'.str_replace('www.','',$_SERVER['HTTP_HOST']);

		$kbd_settings_file = dirname(__FILE__).'/settings.dat';

		$settings = array();

		$settings['recpient_email_address']=array();

		$settings['backup_required'] = 'cron_d';

		$settings['maintain_log'] = 1;

		$settings['cron_server'] = 'default';

		$settings['max_execution_time'] = ini_get('max_execution_time');

		

		$settings['notification'] = '';

		$settings['notification_class'] = '';

		

		$kbd_log_file = dirname(__FILE__).'/log.dat';

		$settings['log'] = file_exists($kbd_log_file)?file_get_contents($kbd_log_file):'';

		 

		if(isValidEmail($_POST['recpient_email_address']))

		{

		

		if(isset($_POST['kbd_key']) && $_SESSION['kbd_key']==$_POST['kbd_key'])

		{

			

			

			$data = array(

			 

			'backup_required'=>$_POST['backup_required'],

			'recpient_email_address'=>$_POST['recpient_email_address'],

			'maintain_log'=>$_POST['maintain_log'],

			'cron_server'=>$_POST['cron_server'],

			'max_execution_time'=>$_POST['max_execution_time']

			

			);

			

			update_kbd_cron($data);

			

			$data = serialize($data);

			

			$handle = fopen($kbd_settings_file,'wb+');

			fwrite($handle, $data);

			fclose($handle);

			

			$settings['notification'] = 'Settings saved.';

			$settings['notification_class'] = 'updated';

			

		}

		else

		{

			$settings['notification'] = 'Access Denied.';

			$settings['notification_class'] = 'error';

		}

		

		}

		elseif(isset($_POST['recpient_email_address']))

		{

			$settings['notification'] = 'Invalid Email Address.';

			$settings['notification_class'] = 'error';

		}

		

		$_SESSION['kbd_key'] = $settings['kbd_key'] = kbd_encrypt($_SERVER['HTTP_HOST'].date('m'), $_SERVER['HTTP_HOST'], $salt);

		



		

		$settings = load_kbd_settings($settings);

		

		

		

		if(count($settings['recpient_email_address'])==0)

		{

			$settings['recpient_email_address'][] = $default_email;

		}		

		

		$settings['notification'] = $settings['notification_class']!=''?'<div class="'.$settings['notification_class'].' settings-error" id="setting-error-settings_updated"> 

<p><strong>'.$settings['notification'].'</strong></p></div>':'';

		

		include('kbd_settings.php');			

	}	

	



	register_activation_hook(__FILE__, 'kbd_start');

	register_deactivation_hook(__FILE__, 'kbd_end' );

	add_action('init', 'init_sessions');	

	add_action( 'admin_menu', 'kbd_menu' );	

		
	if(isset($_REQUEST['kbd_cron_process']) && $_REQUEST['kbd_cron_process']=1)

	{
		add_action('wp_footer', 'kbd_cron_process', 1);
	}