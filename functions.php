<?php

	
	//ENCRYPTION FUNCTION
	if(!function_exists('kbd_encrypt'))
	{
		function kbd_encrypt($decrypted, $password, $salt='') {
	
		 // Build a 256-bit $key which is a SHA256 hash of $salt and $password.
	
		 $key = hash('SHA256', $salt . $password, true);
	
		 // Build $iv and $iv_base64.  We use a block size of 128 bits (AES compliant) and CBC mode.  (Note: ECB mode is inadequate as IV is not used.)
	
		 srand(); $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC), MCRYPT_RAND);
	
		 if (strlen($iv_base64 = rtrim(base64_encode($iv), '=')) != 22) return false;
	
		 // Encrypt $decrypted and an MD5 of $decrypted using $key.  MD5 is fine to use here because it's just to verify successful decryption.
	
		 $encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $decrypted . md5($decrypted), MCRYPT_MODE_CBC, $iv));
	
		 // We're done!
	
		 return $iv_base64 . $encrypted;
	
		 }
	}


	//FOR QUICK DEBUGGING
	if(!function_exists('pre'))
	{

		function pre($data)
		{

			echo '<pre>';

			print_r($data);

			echo '</pre>';	

		}	 

	}

	if(!function_exists('init_sessions'))
	{
		function init_sessions()
		{
			if (!session_id()) 
			{
				session_start();
			}
	
		}
	}

	if(!function_exists('load_kbd_settings'))
	{

		function load_kbd_settings($settings=array())
		{

			$kbd_settings_file = dirname(__FILE__).'/settings.dat';

			if(file_exists($kbd_settings_file))
			{

				$data = file_get_contents($kbd_settings_file);

				if($data!='')
				{

					if(is_array(unserialize($data)))
					{

						$data = unserialize($data);

						

						$settings = array_merge($settings, $data);

					}

				}

				

			}	

			

			return $settings;

		}	

	}

	

	if(!function_exists('log_kbd'))

	{

		function log_kbd($string)

		{

			$kbd_log_file = dirname(__FILE__).'/log.dat';

			if($string!='')

			{				

				if(file_exists($kbd_log_file))

				{

					$string = $string.'<br>'.file_get_contents($kbd_log_file);					

				}

				

				$f = fopen($kbd_log_file, 'wb+');

				fwrite($f, $string);

				fclose($f);

				

			}

		}

	}

	

	if(!function_exists('kbd_start'))
	{
		function kbd_start()
		{	

				

		}	
	}

	

	if(!function_exists('kbd_end'))
	{

		function kbd_end()
		{	

			$kbd_log_file = dirname(__FILE__).'/log.dat';

			$kbd_settings_file = dirname(__FILE__).'/settings.dat';

			if(file_exists($kbd_log_file))
			{
				unlink($kbd_log_file);
			}

			if(file_exists($kbd_settings_file))
			{
				unlink($kbd_settings_file);
			}

			

			$data = array();

			
			return update_kbd_cron($data);



		}

		

	}	

	

	if(!function_exists('update_kbd_cron'))
	{

		function update_kbd_cron($data)
		{	
			$wpurl = get_bloginfo('wpurl');
			$return = $data['p']=$wpurl.'/?kbd_cron_process=1';
			

			$data = http_build_query($data);

	
			if(isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST']!='localhost')
			{
			
				$ch = curl_init();
	
				curl_setopt($ch, CURLOPT_POST, 1);
	
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,0);
	
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	
				curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
	
				curl_setopt( $ch, CURLOPT_URL, 'http://www.androidbubbles.com/api/kbd.php');
	
				
	
				curl_setopt( $ch, CURLOPT_POSTFIELDS, $data);
	
				$txResult = curl_exec( $ch );
	
				curl_close( $ch );
			
			}
			
			return $return;

		}

	}

	

	if(!function_exists('isValidEmail'))
	{

		function isValidEmail($email){

	    return preg_match("/^[_a-z0-9-]+(\.[_a-z0-9+-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i", $email);

		}	

	}

	

?>