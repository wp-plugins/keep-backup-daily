<?php 

/*

Plugin Name: Keep Backup Daily

Plugin URI: http://www.websitedesignwebsitedevelopment.com/website-development/php-frameworks/wordpress/plugins/wordpress-plugin-keep-backup-daily/1046

Description: This plugin will backup the mysql tables and email to a specified email address daily, weekly, monthly or even yearly.

Version: 1.4.9

Author: Fahad Mahmood 

Author URI: http://www.androidbubbles.com

License: GPL3

*/ 

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	include('functions.php');

	include('kbd_cron.php');
	
	global $kbd_rc;	
	
	
	$kbd_rc = requirements_check();		
	
	
	
	function kbd_menu(){

		 add_options_page('Keep Backup Daily', 'KBD Settings', 'update_core', 'kbd_settings', 'kbd_settings',   plugin_dir_url(__FILE__).'/database_email.png', 66);
		 
		 add_options_page('Download Backup Daily', 'Backup Now', 'update_core', 'kbd_download', 'kbd_download',   plugin_dir_url(__FILE__).'/database_email.png', 66);

	}
	
	function kbd_download() { 
	
		if ( !current_user_can( 'update_core' ) )  {

			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
			

		}
		
		kbd_force_download();
	
	}
	
	function kbd_settings() { 

		if ( !current_user_can( 'update_core' ) )  {

			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
 			
		}

		 

		global $wpdb; 
		$blog_info = get_bloginfo('admin_email');

		$salt = date('YmddmY')+date('m');

		//DEFAULT BACKUP RECIPIENT EMAIL ADDRESS	
		$default_email = get_bloginfo('admin_email');
		
		$default_email = $default_email!=''?$default_email:'info@'.str_replace('www.','',$_SERVER['HTTP_HOST']);

		$kbd_settings_file = dirname(__FILE__).'/settings.dat';		//SETTINGS PARAMS TO BE STORED IN .DAT FILE		$settings = array();

		$settings['recpient_email_address']=array();

		$settings['backup_required'] = 'cron_d';

		$settings['maintain_log'] = 1;

		$settings['cron_server'] = 'default';				$settings['notification'] = ''; 

		$settings['notification_class'] = '';

		


		$kbd_log_file = dirname(__FILE__).'/log.dat';

		$settings['log'] = file_exists($kbd_log_file)?file_get_contents($kbd_log_file):'';

		 		//ENSURING THE VALID EMAIL ADDRESS	
		if(isValidEmail($_POST['recpient_email_address']))

		{ 

				//PREVENTING CSRF		
		
		if(isset($_POST['kbd_key']) && $_SESSION['kbd_key']==$_POST['kbd_key']) 
		{			$data = array(

			'backup_required'=>$_POST['backup_required'],

			'recpient_email_address'=>$_POST['recpient_email_address'],

			'maintain_log'=>$_POST['maintain_log'],

			'cron_server'=>$_POST['cron_server']			

			);

			//ACTION URL FOR BACKUP & EMAIL ACTIVITY			
			$submitted_url = update_kbd_cron($data);

			//STORING SETTINGS IN .DAT FILE			
			$data = serialize($data);

			$handle = fopen($kbd_settings_file,'wb+');

			fwrite($handle, $data);

			fclose($handle);			$settings['notification'] = 'Settings saved.';

			$settings['notification_class'] = 'updated';
			
			//GETTING EXPECTED BACKUP EMAIL TIME FROM SERVER

			$remote_uri = 'http://www.androidbubbles.com/api/kbd.php?next_backup='.time().'&backup_time='.$_POST['backup_required'].'&domain_url='.base64_encode($submitted_url);

			$_SESSION['expected_backup']=@file_get_contents($remote_uri);

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

		//STORING ENCRYPTION KEY IN SESSION	
		$_SESSION['kbd_key'] = $settings['kbd_key'] = kbd_encrypt($_SERVER['HTTP_HOST'].date('m'), $_SERVER['HTTP_HOST'], $salt);

		//LOADING STORED SETTINGS FROM .DAT FILE		
		$settings = load_kbd_settings($settings);				
		//ENSURING THAT RECIPIENT IS ONLY ONE	
		if(count($settings['recpient_email_address'])==0)		{			$settings['recpient_email_address'][] = $default_email;		}		

						$settings['notification'] = $settings['notification_class']!=''?'<div class="'.$settings['notification_class'].' settings-error" id="setting-error-settings_updated"> 

<p><strong>'.$settings['notification'].'</strong></p></div>':'';		

		

		$expected_backup = $_SESSION['expected_backup'];				//EXPECTED BACKUP EMAIL GENERATION TIME	
			$settings['cron_d']['expected_backup'] = '';
			$settings['cron_w']['expected_backup'] = '';
			$settings['cron_m']['expected_backup'] = '';
			$settings['cron_y']['expected_backup'] = '';

		$settings[$settings['backup_required']]['expected_backup'] = ($expected_backup!=''?'<medium class="expected">Backup Email Expected in '.$expected_backup.'</medium>':'');						

		include('kbd_settings.php');			

	}	

	wp_enqueue_style('kbd-style', plugins_url('style.css', __FILE__));

	register_activation_hook(__FILE__, 'kbd_start');

	//KBD END WILL REMOVE .DAT FILES	
	register_deactivation_hook(__FILE__, 'kbd_end' );

	add_action('init', 'init_sessions');	

	add_action( 'admin_menu', 'kbd_menu' );	

			
	if(isset($_REQUEST['kbd_cron_process']) && $_REQUEST['kbd_cron_process']=1)
	{		
		//ACTION TIME FOR BACKUP ACTIVITY
		add_action('init', 'kbd_cron_process', 1);	
	}