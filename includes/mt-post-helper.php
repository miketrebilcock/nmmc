<?php
require_once dirname( __FILE__ ) . '/wp_post_helper/class-wp_post_helper.php';

class mt_post_helper {


    /**
	* Insert post and postmeta using wp_post_helper.
	*
	* More information: https://gist.github.com/4084471
	*
	* @param array $post
	* @param array $meta
	* @param array $terms
	* @param string $thumbnail The uri or path of thumbnail image.
	* @param bool $is_update
	* @return int|false Saved post id. If failed, return false.
	*/
	public function save_post($post,$meta,$terms,$thumbnail,$is_update) {
		$ph = new wp_post_helper($post);
		
		foreach ($meta as $key => $value) {
			$is_cfs = 0;
			$is_acf = 0;
			$cfs_prefix = 'cfs_';
			if (strpos($key, $cfs_prefix) === 0) {
				$ph->add_cfs_field( substr($key, strlen($cfs_prefix)), $value );
				$is_cfs = 1;
			} else {
				if (function_exists('get_field_object')) {
					if (strpos($key, 'field_') === 0) {
						$fobj = get_field_object($key);
						if (is_array($fobj) && isset($fobj['key']) && $fobj['key'] == $key) {
							$ph->add_field($key,$value);
							$is_acf = 1;
						}
					}
				}
			}
			if (!$is_acf && !$is_cfs)
				$ph->add_meta($key,$value,true);
		}

		foreach ($terms as $key => $value) {
			$ph->add_terms($key, $value);
		}
		
		if ($thumbnail)
		{
			echo " Adding Thumbnail to post";
			$ph->add_media($thumbnail,$post['post_title'],'','',true);
		}
		if ($is_update)
			$result = $ph->update();
		else
			$result = $ph->insert();
		
		unset($ph);
		
		return $result;
	}
}