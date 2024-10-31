<?php
/*
Plugin Name: Protection Grid
Module Name: Backup Monitor
Description: WP Site Protection and Monitoring
Version: 1.5.1
Author:  Business Technology Group, LLC
Author URI: https://mibiztec.com
Text Domain: mibiztec.com
*/
$protection_grid_session = null;
$protection_grid_trafficRecorded = false;
$protection_grid_backup_file= null;
$protection_grid_backup_cache= [];

if ( ! wp_next_scheduled( 'protection_grid_backup_manager' ) ) {
    wp_schedule_event( time(), 'hourly', 'protection_grid_backup_manager' );
}
function protection_grid_backup_cache($file,$last_mod){
    global $protection_grid_backup_file, $protection_grid_backup_cache;
    if(!$protection_grid_backup_file){
        $sep = DIRECTORY_SEPARATOR;
        $protection_grid_backup_file = $sep . trim( sys_get_temp_dir(), $sep ) . $sep . parse_url( get_site_url()."_pg_cache", PHP_URL_HOST );
        if(file_exists($protection_grid_backup_file) && !isset($_GET['pg_clear'])){
            // load cached backup data
            $lines = file($protection_grid_backup_file);
            foreach($lines as $line)
            {
                $d = explode(":|:",trim($line));
                if(count($d)>1)
                    $protection_grid_backup_cache[$d[0]]=['last_modified'=>$d[1]??'',
                                                          'seen'=>0
                                                          ];
            }
        }else{
            //create the file
            file_put_contents( $protection_grid_backup_file, "" );
        }
		
		//print_r($protection_grid_backup_cache);exit();
    }
    if(!isset($protection_grid_backup_cache[$file]) || $protection_grid_backup_cache[$file]['last_modified'] != $last_mod){
        //store file record
		file_put_contents($protection_grid_backup_file, $file.":|:".$last_mod."\n".PHP_EOL , FILE_APPEND | LOCK_EX);
        $protection_grid_backup_cache[$file] = ['last_modified'=>$last_mod,
                                                'seen'=>1
                                              ];
        return false;
    }
    //print_r($protection_grid_backup_cache);
    $protection_grid_backup_cache[$file]['seen']=1;
    return true;
}
function protection_grid_backup_cache_update(){
    global $protection_grid_backup_cache,$protection_grid_backup_file;
    file_put_contents( $protection_grid_backup_file, "" );// clear cache
	foreach($protection_grid_backup_cache as $file =>$d){
		if($d['seen']==1){
			file_put_contents($protection_grid_backup_file, $file.":|:".$d['last_modified']."\n".PHP_EOL , FILE_APPEND | LOCK_EX);
		}
	}
}
function protection_grid_backup_manager() {
	//return true;
	//file_put_contents( $file, $content );
	set_time_limit(120);
	$files = dircrawl(ABSPATH,"");	
    //print_r($files);
    $results = protection_grid_put("files",$files);
    //print_r($results);
	
	//reset array for removal
    $files = array();
    // look for remnoved files
    global $protection_grid_backup_cache;
	foreach($protection_grid_backup_cache as $file =>$d){
		if($d['seen']==0){
			$files[] = array('file'=>$file
							 );
			//print $file ."<br/>\n";
		}
	}
	/*
	if(count($files)>0)
		$results = protection_grid_delete("files",$files);
	*/
	protection_grid_backup_cache_update();
	
}

function dircrawl($root,$dir,$return=array()){
	$fileList = array_diff(scandir($root.$dir), array('.', '..'));
	//print_r($fileList);
    $return=[];
	foreach($fileList as $filename){
		$fpath = $dir."/".$filename;
		$fullpath = $root."/".$fpath;
		if(is_file($fullpath)){
            $last_mod = date("F d Y H:i:s.", filemtime($fullpath));
            //only send the file if the contents have changed
            if(!protection_grid_backup_cache($fpath,$last_mod)){
                $chksum = md5_file($fullpath);
                $contents = implode("\n",file($fullpath));
               // print "updating : " . $fpath . "\n<br/>";
        
                //only send new files
                $return[] = array('file'=>$fpath,
                                  'size'=>filesize($fullpath),
                                  'chksum'=>$chksum,
                                  'content' => $contents,
                                  'last modified'=>$last_mod
                                 );
            }
            
		    //echo $fpath . " " . $return[count($return)-1]['file'] . " " . $return[count($return)-1]['size'] . " " . $return[count($return)-1]['chksum'] . "\n"; 
		}else if(is_dir($fullpath)){
			$return = array_merge($return,dircrawl($root,$fpath,$return));
			
		}
		//print_r($return);
		if(count($return)>10){
			//print "Submitting 10...<br/>\n";
			$results = protection_grid_put("files",$return);
			//print_r($results);
			$return = array();
		}
	}
    //exit();
	return $return;
}