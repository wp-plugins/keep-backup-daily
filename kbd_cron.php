<?php ini_set('max_execution_time', 60*60);
		/* backup the db OR just a table */

		function backup_tables($name, $backup_file, $zip_file, $tables = '*'){ 

		$ret = false;

		if(!file_exists($backup_file)){


			//get all of the tables

			if($tables == '*'){

			$tables = array();

			$result = mysql_query('SHOW TABLES');

			while($row = mysql_fetch_row($result)){

			  $tables[] = $row[0];

			}

			}
			else{

			$tables = is_array($tables) ? $tables : explode(',',$tables);

			}
			//cycle through

			foreach($tables as $table) {

			$result = mysql_query('SELECT * FROM '.$table);

			$num_fields = mysql_num_fields($result);

			$return.= 'DROP TABLE IF EXISTS '.$table.';';

			$row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$table));

			$return.= "\n\n".$row2[1].";\n\n";

			for ($i = 0; $i < $num_fields; $i++) {

			  while($row = mysql_fetch_row($result))

			  {

				$return.= 'INSERT INTO '.$table.' VALUES(';

				for($j=0; $j<$num_fields; $j++) 

				{

				  $row[$j] = addslashes($row[$j]);

				  $row[$j] = ereg_replace("\n","\\n",$row[$j]);

				  if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }

				  if ($j<($num_fields-1)) { $return.= ','; }

				}

				$return.= ");\n";

			  }

			}

			$return.="\n\n\n";

			}	

			$backup_file = $backup_file;

			$handle = fopen($backup_file,'wb+');

			fwrite($handle,$return);

			fclose($handle);
			
			$ret = true;

		}

		if($kbd_rc['ZipArchive']){
			$ret = kbd_zip_it($zip_file, $backup_file);
		}

		return $ret;

		}


		if(!function_exists('kbd_zip_it')){


		function kbd_zip_it($zip_file, $backup_file){		
		 
				$ret = false;
				if(!file_exists($zip_file) && file_exists($backup_file)){
		
			  
		
					$zip = new ZipArchive;
		
					if ($zip->open($zip_file, ZIPARCHIVE::CREATE ) === TRUE) {
		
						$bkup_file = DB_NAME.'.sql';
						
						$zip->addFile($backup_file, $bkup_file);	
							
		
						$zip->close(); 
		
						$ret = true;
		
					} 
		
				}
		
				else
		
				{
		
					$ret = true;
		
				}
		
				if(file_exists($backup_file) && file_exists($zip_file)){
		
					unlink($backup_file);
		
				}	 
				
				return $ret;

			}
		}
		
		
		function kbd_cron_process(){


				$settings = load_kbd_settings();	


				$configEmail = $settings['recpient_email_address'];

				$backup_file = dirname(__FILE__).'/'.DB_NAME.'.sql';
				
				

				$attach_file = $zip_file = dirname(__FILE__).'/'.DB_NAME.'_'.date('d_m_Y').'.zip';

				$zip_created = 0;

				$zip_created = backup_tables(DB_NAME, $backup_file, $zip_file);	
				
				if(!$kbd_rc['ZipArchive']){
					$attach_file = $backup_file;
				}	
					
					
					
				if(wp_mail( $configEmail, str_replace('.zip', '', basename($zip_file)), $_SERVER['HTTP_HOST'].' - Database Backup by Wordpress Plugin Keep Backup Daily', '', array($attach_file) ))

				{
				
					$db_size = formatSizeUnits(filesize($zip_file));
					unlink($zip_file);

					if($settings['maintain_log'])

					{	

						$string = 'File size: '.$db_size.',  Sent to <a mailto="'.$configEmail.'">'.$configEmail.'</a> at '.date('d M, Y h:i:s a');

						log_kbd($string);

					}					
					
					echo strtolower($_SERVER['HTTP_HOST']);

				}else{
					echo strtoupper($_SERVER['HTTP_HOST']);
				}
			exit;
		}

				

		function kbd_force_download()

		{

				$file_info = new finfo(FILEINFO_MIME);  

				$type = $file_info->buffer(file_get_contents($zip_file));

				ob_clean();						

				header("Pragma: public");

				header("Expires: 0");

				header("Pragma: no-cache");

				header("Charset: null");

				header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");

				header("Content-Type: application/force-download");

				header("Content-Type: application/octet-stream");

				header("Content-Type: application/download");

				header('Content-disposition: attachment; filename=' . str_replace(' ','-', file_parts($zip_file,'name')));

				header("Content-Type: $type");

				header("Content-Transfer-Encoding: binary");

				header('Content-Length: ' . filesize($zip_file));

				@readfile($zip_file);

				exit(0);	

		}

			//exit;
?>