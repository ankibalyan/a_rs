<?php 
//add_action( $tag, $function_to_add, $priority, $accepted_args );
add_action('wp_ajax_add_user','createUser');
/**
 * createUser function
 * this fucntion do the operation for creating a new user, 
 * it will get the new data in post format, validate and inser into the  database  table name 'rw_users' records,
 * It will also maps the user to the specific brands if selected in users creation entry form, 
 * this function can called by the ajax method from the admin panel and admin login only using action cammand add_user
 * 
 * @return void
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
	function createUser()
	{
		extract($_POST);
		global $wpdb;
		$table = 'rw_users';
		$data = array(
			'user_name' => (isset($user['user_name'])) ? $user['user_name'] : '',
			'user_email' => (isset($user['user_email'])) ? $user['user_email'] : '',
			'user_fullname' => (isset($user['user_fullname'])) ? $user['user_fullname'] : '',

			'user_phone' => (isset($user['user_phone'])) ? $user['user_phone'] : '',
			'user_parent_id' => (isset($user['user_parent_id'])) ? $user['user_parent_id'] : '',
			'user_lvl' => (isset($user['user_lvl'])) ? $user['user_lvl'] : '',

			'source' => (isset($user['source'])) ? $user['source'] : '',
			'user_password' => (isset($user['user_password'])) ?  sha1(trim($user['user_password'])) : sha1('default'),
			'user_actv_ind' => (isset($user['user_actv_ind'])) ? $user['user_actv_ind'] : 1,

			'created_dt' => date('Y-m-d H:i:s'),
			'modified_dt' => date('Y-m-d H:i:s'),
		);
		$errors = validateUserData($data);
		if(!count($errors))
		{
			$format = array('%s','%s','%s', '%s','%d','%d', '%s','%s','%d', '%s','%s');
			$wpdb->insert($table,$data,$format);
			$user_id= $wpdb->insert_id;
			if($user_id)
			{
				if(isset($brand_box) && count($brand_box))
				{
					$table = 'rw_brand_user_map';
					foreach ($brand_box as $key => $value) {
						$brandUserMapData = array(
							'brand_id' => (isset($value)) ? $value : '',
							'user_id' => $user_id,

							'created_dt' => date('Y-m-d H:i:s'),
							'modified_dt' => date('Y-m-d H:i:s'),
						);
						$format = array('%d','%d', '%s','%s');
						$wpdb->insert($table,$brandUserMapData,$format);
					}
				}
				echo "user created Sucessfuly";
			}
		}
		else{
			foreach ($errors as $key => $error) {
				echo $error."<br>";
			}
		}
		die;
	}

add_action('wp_ajax_user_delete','userDelete');
/**
 * userDelete function
 * this fucntion do the operation for Deleting a new user, 
 * it will delete users data from the  database  table name 'rw_users' records,
 * It will not  delete user maping with the specific brands if selected in users creation entry form, 
 * this function can called by the ajax method from the admin panel and admin login only using action cammand user_delete
 * 
 * @return void
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function userDelete()
{
	global $wpdb;
	if($_POST['user_id'])
	{
		$sql = "DELETE FROM rw_users WHERE user_id =".$_POST['user_id'];
		$wpdb->query($sql);
		Echo "User is Deleted";
	}

	die;
}

add_action('wp_ajax_user_actv_state','updateUserStatus');
/**
 * updateUserStatus function
 * this fucntion do the operation for making a user active and inactive, 
 * it will change state of 'actv_ind' coloumn in the table name 'rw_users' records fro a particcular user or for the all users if id is specified or not respectivly,
 * this function can called by the ajax method from the admin panel and admin login only using action cammand user_actv_state and by passing the user_actv_ind as true or false to change the state of user.
 * 
 * @return void
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
	function updateUserStatus()
	{
		extract($_POST);
		global $wpdb;
		$table = 'rw_users';

		$status =  ($status == 'true') ? true : false;
		if(isset($user_id) && $user_id)
		{
			$sql = $wpdb->prepare("
			                         UPDATE {$table}
			                         SET  user_actv_ind = %d, modified_dt = %s
			                         WHERE user_id = $user_id",
			                         $status, date('Y-m-d H:i:s')
			                         );
		}
		else
		{
			$sql = $wpdb->prepare("
			                         UPDATE {$table}
			                         SET  user_actv_ind = %d, modified_dt = %s
			                         WHERE user_lvl != 0 ",
			                         $status, date('Y-m-d H:i:s')
			                         );
		}

		$wpdb->query($sql);		
		die;
	}

add_action('wp_ajax_rw_login','userLogin');
add_action('wp_ajax_nopriv_rw_login','userLogin');
/**
 * userLogin function
 * this fucntion do the operation for Loggin into the site into the frontend, 
 * it will allow the arvind it user, brand user, distribiutors and the dealers only,
 * the key feature of the fucntion is, it will not allow multiple session of the same user, it will make an entry in the database in the sessions tables for the particluar user in front of ip of the user is using.
 * the user login from multiple browsers i.e only from the same i.p address, a user must log out to delete the previous session.
 * this function can called by the ajax method from the admin panel or from the front end as well, using action cammand rw_login
 * 
 * @return void
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
	function userLogin()
	{
		extract($_POST);
		global $wpdb;
		$table = 'rw_users';
		if(isset($user_id) && $user_id !='' && isset($user_password) && $user_password != '' )
		{
			$user_password =  sha1(trim($user_password));
			$sql = "SELECT * FROM $table where user_name = '$user_id' and user_password = '$user_password' and user_actv_ind = 1";
			
			$user = $wpdb->get_row($sql);
			if(count($user))
			{
				$sql = "SELECT * FROM `sessions` where access_id = $user->user_id";
				$session_exist = $wpdb->get_row($sql);
				if($session_exist)
				{
					session_start();
					if($session_exist->session_id == session_id() || $session_exist->ip_addr == getIp())
					{
						session_regenerate_id(true);
						$_SESSION['user_id'] = $user->user_id;
						$sid = session_id();
						if($sid)
							if(saveSession($user->user_id))
						echo "Validate Successfully, Lets go to Home Page";
						die;
					}
					else
					{
						unset($_SESSION);
						session_destroy();
						echo "You are being already logged in from another device";
						die;
					}
					exit;
				}
				elseif(isset($user->user_id) && $user->user_id)
				{
					session_start();
					session_regenerate_id(true);
					$_SESSION['user_id'] = $user->user_id;
					$sid = session_id();
					if($sid)
						if(saveSession())
					echo "Validate Successfully, Lets go to Home Page";
				}
			}
			else{
					sleep(2);
					echo "Either user Id or password is Invalid";
				}
		}
		else
		{
			sleep(2);
			echo "Invalid Credentials Details";
		}

		die;
	}

add_action('wp_ajax_rw_logout','userLogout');
add_action('wp_ajax_nopriv_rw_logout','userLogout');
/**
 * userLogout function
 * this fucntion clears the session and logs out the user from current session
 * it will aslo delete session data recorded in the database with table name 'sessions' in the records,
 * this function can called by the ajax method from the admin panel and admin login only using action cammand rw_logout
 * 
 * @return void
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function userLogout($value='')
{
	global $wpdb;
	$sid = session_start();
		if($sid)
		{
			$sql = "DELETE FROM sessions WHERE session_id = $sid OR access_id =".$_SESSION['user_id'];
			$wpdb->query($sql);
		}
	unset($_SESSION['user_id']);
	session_unset();     // unset $_SESSION variable for the run-time 
	session_destroy();
	die;
}

/**
 * isLogin function
 * this fucntion checks if the user is logged in or not, if user is logged in the it will retruns the current user_id from the current session, or if not then retrun false (0).
 * this function can called by from any where.
 * 
 * @return int
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function isLogin()
{
	global $wpdb;	
	!(session_id())? session_start() : '';
	$sql = "SELECT * FROM sessions where session_id = '".session_id()."'";
	$getLogin = $wpdb->get_row($sql);
	if(count($getLogin))
	{
		if(isset($_SESSION['user_id']) && $_SESSION['user_id'])
		return $_SESSION['user_id'];
	}
		return 0;
}

/**
 * isDistributor function
 * this fucntion checks if the user is Distributor user or not, if user is Distributor the it will retruns the current user all details or if not then retrun false (0).
 * this function can called by from any where.
 * 
 * @return array / FALSE
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function isDistributor($id = null)
{
	global $wpdb;
	if($id)
	{
		$sql = "SELECT parent.* FROM rw_users as parent
				LEFT OUTER JOIN
			    	rw_user_lvl AS level ON parent.user_lvl = level.lvl_id
			    where parent.user_id = $id and parent.user_lvl = 1";
		$user = $wpdb->get_row($sql);
		if(count($user))
		{
			return $user;
		}
	}
	else
	{
		$sql = "SELECT parent.* FROM rw_users as parent
				LEFT OUTER JOIN
			    	rw_user_lvl AS level ON parent.user_lvl = level.lvl_id
			    where parent.user_id =  ".isLogin()." and parent.user_lvl = 1";
		$user = $wpdb->get_results($sql);
		if(count($user))
		{
			return $user;
		}
	}
	return FALSE;
}

/**
 * isDealer function
 * this fucntion checks if the user is Dealer user or not, if user is Dealer the it will retruns the current user all details or if not then retrun false (0).
 * this function can called by from any where.
 * 
 * @return stdObject / FALSE
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function isDealer($id = null)
{
	global $wpdb;
	if($id)
	{
		$sql = "SELECT parent.* FROM rw_users as parent
				LEFT OUTER JOIN
			    	rw_user_lvl AS level ON parent.user_lvl = level.lvl_id
			    where parent.user_id = $id and parent.user_lvl = 2";
		$user = $wpdb->get_row($sql);
		if(count($user))
		{
			return $user;
		}
	}
	else
	{
		$sql = "SELECT parent.* FROM rw_users as parent
				LEFT OUTER JOIN
			    	rw_user_lvl AS level ON parent.user_lvl = level.lvl_id
			    where parent.user_id =  ".isLogin()." and parent.user_lvl = 2";
		$user = $wpdb->get_results($sql);
		if(count($user))
		{
			return $user;
		}
	}

	return FALSE;
}

/**
 * isBrandUser function
 * this fucntion checks if the user is BrandUser or not, if user is BrandUser the it will retruns the current user all details or if not then retrun false (0).
 * this function can called by from any where.
 * 
 * @return stdObject / FALSE
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function isBrandUser($id = null)
{
	global $wpdb;
	if($id)
	{
		$sql = "SELECT parent.* FROM rw_users as parent
				LEFT OUTER JOIN
			    	rw_user_lvl AS level ON parent.user_lvl = level.lvl_id
			    where parent.user_id = $id and parent.user_lvl = 3";
		$user = $wpdb->get_row($sql);
		if(count($user))
		{
			return $user;
		}
	}
	else
	{
		$sql = "SELECT parent.* FROM rw_users as parent
				LEFT OUTER JOIN
			    	rw_user_lvl AS level ON parent.user_lvl = level.lvl_id
			    where parent.user_id =  ".isLogin()." and parent.user_lvl = 3";
		$user = $wpdb->get_results($sql);
		if(count($user))
		{
			return $user;
		}
	}

	return FALSE;
}

/**
 * isArvindUser function
 * this fucntion checks if the user is ArvindUser IT user or not, if user is ArvindUser (IT User) the it will retruns the current user all details or if not then retrun false (0).
 * this function can called by from any where.
 * 
 * @return stdObject / FALSE
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function isArvindUser($id = null)
{
	global $wpdb;
	if($id)
	{
		$sql = "SELECT parent.* FROM rw_users as parent
				LEFT OUTER JOIN
			    	rw_user_lvl AS level ON parent.user_lvl = level.lvl_id
			    where parent.user_id = $id and parent.user_lvl = 4";
		$user = $wpdb->get_row($sql);
		if(count($user))
		{
			return $user;
		}
	}
	else
	{
		$sql = "SELECT parent.* FROM rw_users as parent
				LEFT OUTER JOIN
			    	rw_user_lvl AS level ON parent.user_lvl = level.lvl_id
			    where parent.user_id =  ".isLogin()." and parent.user_lvl = 4";
		$user = $wpdb->get_results($sql);
		if(count($user))
		{
			return $user;
		}
	}

	return FALSE;
}

/**
 * getDealersByDistributor function
 * this fucntion generates an array of all the dealers under a distributor if distributor id is provided,
 * or list all distributors and dealsrs if none of the parametere is provided,  
 * if the users Distributor or Dealers are not available, it will retrun false (0).
 * this function can called by from any where.
 * 
 * @param id it is and distributor's user id
 * @return stdObject / array
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function getDealersByDistributor($id='')
{
	global $wpdb;
	if($id)
	{
		$sql = "SELECT parent.* FROM rw_users as parent
			    where parent.user_parent_id = $id";
		$user = $wpdb->get_results($sql);
		if(count($user))
		{
			return $user;
		}
	}
	else{
		$sql = "SELECT parent.* FROM rw_users as parent
			    where parent.user_lvl = 1 or parent.user_lvl = 2";
		$user = $wpdb->get_results($sql);
		if(count($user))
		{
			return $user;
		}
	}
	return array();
}

/**
 * getRwUsers function
 * this fucntion generates the list of all the users, or under a level, or get the details of a particluar user as well,
 * if id is provided it will returns the details of that particluar user. if level is provided, it will give array of users 
 * belongs to that level, and if none of the parameters are provieded it will retrun the array of all users
 * this function can called by from any where.
 * 
 * @param id  int user id
 * @param level int level id of the user, eg. dealer, distributor
 * @return stdObject / FALSE
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function getRwUsers($id = NULL, $level = NULL)
{
	global $wpdb;
	if ($id) {
		$sql = "SELECT parent.*, child.user_name as parent_user, level.lvl_name
				FROM
			    rw_users AS parent
			        INNER JOIN
			    rw_users AS child ON parent.user_parent_id = child.user_id
			    OR parent.user_parent_id = 0
			    LEFT OUTER JOIN
			    rw_user_lvl AS level ON parent.user_lvl = level.lvl_id  where parent.user_id = $id";
		$user = $wpdb->get_row($sql);
		if(count($user))
		{
			return $user;
		}
		else{
			return FALSE;
		}
	}
	else{
		$sql = "SELECT parent.*, child.user_name as parent_user, level.lvl_name
				FROM
			    rw_users AS parent
			        INNER JOIN
			    rw_users AS child ON parent.user_parent_id = child.user_id
			    OR parent.user_parent_id = 0
			    LEFT OUTER JOIN
			    rw_user_lvl AS level ON parent.user_lvl = level.lvl_id ";
		if($level)
		{
			$sql .= " WHERE parent.user_lvl = $level";
		}
			$sql .= " GROUP BY parent.user_id ";
	}
	return $wpdb->get_results($sql);
}

/**
 * getRwUSersLvl function
 * this fucntion generates the list of available levels in the rw_user_lvl table
 * this function can called by from any where.
 * 
 * @todo param id is users is, and it will return the level of that particular user.
 * @return stdObject / FALSE
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function getRwUsersLvl($id = NULL)
{
	global $wpdb;
	if ($id) {
		return array();
	}
	else{
		$sql = "SELECT *
				FROM rw_user_lvl";
	}
	return $wpdb->get_results($sql);
}

/**
 * get_nested_users function
 * this fucntion generates the array of all Distributors and dealers in a parent child queue.
 * this function can called by from any where.
 * 
 * @return array
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function get_nested_users()
{
    global $wpdb;
    $sql = "SELECT 
                users.*
            FROM
                rw_users AS users
            where users.user_lvl = 1 or users.user_lvl = 2 ";
    $pages = $wpdb->get_results($sql);
    $array = array();
    foreach($pages as $page)
    {
        $page = (array) $page;
        if(!$page['user_parent_id'])
        {
            $array[$page['user_id']] = $page;
        }
        else
        {
            $array[$page['user_parent_id']] ['children'][] = $page;
        }
    }
    return $array;
}

/**
 * validateUserData function
 * this fucntion validates the set of user data passed at the time of user creation by createUSer functioon
 * it will check for the same username or same email is exist or not, if it is returns the errors  in an array
 * this function can called by from any where.
 * 
 * @return array
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function validateUserData($data=array())
{
	global $wpdb;
	$errors = array();
	if(count($data))
	{
		if($data['user_name'])
		{
			$sql = "SELECT * FROM rw_users WHERE user_name = '".$data['user_name']."'";
			$user = $wpdb->get_row($sql);
			if(count($user))
			{
				$errors['user_name'] = "Username ".$data['user_name']. " is already taken, please try another.";
			}
		}
		if($data['user_email'])
		{
			$sql = "SELECT * FROM rw_users WHERE user_email = '".$data['user_email']."'";
			if(count($wpdb->get_row($sql)))
			{
				$errors['user_email'] =  $data['user_email']. " is already taken, please try another.";
			}
		}
	}
	return $errors;
}

/**
 * saveSession function
 * this fucntion saves the cuurent login session to the database tabel 'sesssions',
 * returns true if successfully adde, else false.
 * this function is being called from userLogin function.
 * 
 * @return TRUE / FALSE
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function saveSession($user_id= NULL)
{
	global $wpdb;
	if($user_id)
	{
		$data = array('session_id' => session_id(),
					  'ip_addr' => getIp(),
					  'created_time' => time()
					);

		$format = array('%s','%s','%s');
		$where = array('access_id' => $user_id);
		$where_format = array('%d');		
		$sid = $wpdb->update( 'sessions', $data, $where, $format = null, $where_format = null );
		
		if($sid)
			return true;
	}
	elseif($_SESSION['user_id'])
	{
		$data = array('session_id' => session_id(),
					  'access_id' => $_SESSION['user_id'],
					  'ip_addr' => getIp(),
					  'created_time' => time()
					);
		$wpdb->insert('sessions',$data);
		$sid = $wpdb->insert_id;
		if($sid)
			return true;
	}
	if($_SESSION['user_id'])
	{
		sleep(1 * 60);
		$where = array('access_id' => $_SESSION['user_id']);
		$where_format = array('%d');
		$wpdb->delete('sessions', $where, $where_format = null );
	}
	return FALSE;
}

/**
 * getIp function
 * this fucntion gives the current users ip address.
 * this function can called by from any where.
 * 
 * @return string
 * @author Ankit Balyan - sf.ankit@gmail.com
 **/
function getIp()
{
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		    $ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
		    $ip = $_SERVER['REMOTE_ADDR'];
		}
	return $ip;
}