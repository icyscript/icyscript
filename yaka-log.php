<?php

	$is_ajax = $_REQUEST['is_ajax'];
	if(isset($is_ajax) && $is_ajax)
	{
		$username = $_REQUEST['username'];
		$password = $_REQUEST['password'];

        $fanlisting->settings['admin_name'];
        $fanlisting->settings['admin_pass'];


        if($username == 'admin_name' && $password == 'admin_pass')
		{
			echo "success";
		}
	}

?>
