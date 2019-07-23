<?php
namespace App\customLib;

use DB;


/*----------------------------------------------------------------------------------------------------
 | 客製session 類別
 |----------------------------------------------------------------------------------------------------
 | 此類別參考ecshop中的 cls_session.php , 以session判斷使用者是否為會員 , 並且記錄當下會員所購買的
 | 商品品項以及商品數量
 |
 */
class cls_session
{   
    var $db             = NULL;
    var $session_table  = '';

    var $max_life_time  = 43200; // SESSION 杩囨湡鏃堕棿

    var $session_name   = '';
    var $session_id     = '';

    var $session_expiry = '';
    var $session_md5    = '';

    var $session_cookie_path   = '/';
    var $session_cookie_domain = '';
    var $session_cookie_secure = false;

    var $_ip   = '';
    var $_time = 0;


    function __construct( $session_table, $session_data_table, $session_name = 'ECS_ID', $session_id = '')
    {   

        $GLOBALS['_SESSION'] = array();
        

        if (!empty($GLOBALS['cookie_path']))
        {
            $this->session_cookie_path = $GLOBALS['cookie_path'];
        }
        else
        {
            $this->session_cookie_path = '/';
        }
        

        if (!empty($GLOBALS['cookie_domain']))
        {
            $this->session_cookie_domain = $GLOBALS['cookie_domain'];
        }
        else
        {
            $this->session_cookie_domain = '';
        }

        if (!empty($GLOBALS['cookie_secure']))
        {
            $this->session_cookie_secure = $GLOBALS['cookie_secure'];
        }
        else
        {
            $this->session_cookie_secure = false;
        }

        $this->session_name       = $session_name;
        $this->session_table      = $session_table;
        $this->session_data_table = $session_data_table;

        //$this->db  = &$db;
		
        //$this->_ip = real_ip();

		if(isset($_COOKIE['real_ipd']) && !empty($_COOKIE['real_ipd']))
		{
			$this->_ip = $_COOKIE['real_ipd'];
		}else{

			$this->_ip = $this->real_ip();
			//setcookie("real_ipd", $this->_ip, time()+864000, "/");
			setcookie("real_ipd", $this->_ip, time()+864000, $this->session_cookie_path); 
		}

        if ($session_id == '' && !empty($_COOKIE[$this->session_name]))
        {
            $this->session_id = $_COOKIE[$this->session_name];
        }
        else
        {
            $this->session_id = $session_id;
        }
        


        if ($this->session_id)
        {
            $tmp_session_id = substr($this->session_id, 0, 32);
            if ($this->gen_session_key($tmp_session_id) == substr($this->session_id, 32))
            {
                $this->session_id = $tmp_session_id;
            }
            else
            {
                $this->session_id = '';
            }
        }

        $this->_time = time();

        if ($this->session_id)
        {
            $this->load_session();
        }
        else
        {           
            $this->gen_session_id();

            setcookie($this->session_name, $this->session_id . $this->gen_session_key($this->session_id), 0, $this->session_cookie_path, $this->session_cookie_domain, $this->session_cookie_secure);
        }
        
        register_shutdown_function(array(&$this, 'close_session'));
    }

    function gen_session_id()
    {
        $this->session_id = md5(uniqid(mt_rand(), true));

        return $this->insert_session();
    }

    function gen_session_key($session_id)
    {
        static $ip = '';

        if ($ip == '')
        {
            $ip = substr($this->_ip, 0, strrpos($this->_ip, '.'));
        }

        return sprintf('%08x', crc32( $ip . $session_id));
    }

    function insert_session()
    {
        //return $this->db->query('INSERT INTO ' . $this->session_table . " (sesskey, expiry, ip, data) VALUES ('" . $this->session_id . "', '". $this->_time ."', '". $this->_ip ."', 'a:0:{}')");
        
        /*return DB::table( $this->session_table )->insertGetId(
            [ 'sesskey' => $this->session_id, 
              'expiry'  => $this->_time,
              'ip'      => $this->_ip,
              'data'    => 'a:0:{}'
            ]
        );*/

        return DB::table( $this->session_table  )->insert(
            [ 'sesskey' => $this->session_id, 
              'expiry'  => $this->_time,
              'ip'      => $this->_ip,
              'data'    => 'a:0:{}',
              'user_name' => '0',
              'user_rank' => '0',
              'discount'  => '0',
              'email'     => '0'

            ]
        );        
    }

    function load_session()
    {
        //$session = $this->db->getRow('SELECT userid, adminid, user_name, user_rank, discount, email, data, expiry FROM ' . $this->session_table . " WHERE sesskey = '" . $this->session_id . "'");
        
        $session_tmp = DB::table($this->session_table)->where('sesskey', $this->session_id )->first();
        
        if( $session_tmp ){
            
            $session_tmp = (Array)$session_tmp;
        }

        $session = $session_tmp;

        if (empty($session))
        {
            $this->insert_session();

            $this->session_expiry = 0;
            $this->session_md5    = '40cd750bba9870f18aada2478b24840a';
            $GLOBALS['_SESSION']  = array();
        }
        else
        {
            if (!empty($session['data']) && $this->_time - $session['expiry'] <= $this->max_life_time)
            {
                $this->session_expiry = $session['expiry'];
                $this->session_md5    = md5($session['data']);
                $GLOBALS['_SESSION']  = unserialize($session['data']);
                $GLOBALS['_SESSION']['user_id'] = $session['userid'];
                $GLOBALS['_SESSION']['admin_id'] = $session['adminid'];
                $GLOBALS['_SESSION']['user_name'] = $session['user_name'];
                $GLOBALS['_SESSION']['user_rank'] = $session['user_rank'];
                $GLOBALS['_SESSION']['discount'] = $session['discount'];
                $GLOBALS['_SESSION']['email'] = $session['email'];
            }
            else
            {
                $session_data = $this->db->getRow('SELECT data, expiry FROM ' . $this->session_data_table . " WHERE sesskey = '" . $this->session_id . "'");
                if (!empty($session_data['data']) && $this->_time - $session_data['expiry'] <= $this->max_life_time)
                {
                    $this->session_expiry = $session_data['expiry'];
                    $this->session_md5    = md5($session_data['data']);
                    
                    if($session_data['data'])
                    $GLOBALS['_SESSION']  = @unserialize($session_data['data']);
                    $GLOBALS['_SESSION']['user_id'] = $session['userid'];
                    $GLOBALS['_SESSION']['admin_id'] = $session['adminid'];
                    $GLOBALS['_SESSION']['user_name'] = $session['user_name'];
                    $GLOBALS['_SESSION']['user_rank'] = $session['user_rank'];
                    $GLOBALS['_SESSION']['discount'] = $session['discount'];
                    $GLOBALS['_SESSION']['email'] = $session['email'];
                }
                else
                {
                    $this->session_expiry = 0;
                    $this->session_md5    = '40cd750bba9870f18aada2478b24840a';
                    $GLOBALS['_SESSION']  = array();
                }
            }
        }
    }

    function update_session()
    {
        static $i = 0;
        
        if($i !== 0)
            return false;
        $i = 1;
        
        $adminid = !empty($GLOBALS['_SESSION']['admin_id']) ? intval($GLOBALS['_SESSION']['admin_id']) : 0;
        $userid  = !empty($GLOBALS['_SESSION']['user_id'])  ? intval($GLOBALS['_SESSION']['user_id'])  : 0;
        $user_name  = !empty($GLOBALS['_SESSION']['user_name'])  ? trim($GLOBALS['_SESSION']['user_name'])  : 0;
        $user_rank  = !empty($GLOBALS['_SESSION']['user_rank'])  ? intval($GLOBALS['_SESSION']['user_rank'])  : 0;
        $discount  = !empty($GLOBALS['_SESSION']['discount'])  ? round($GLOBALS['_SESSION']['discount'], 2)  : 0;
        $email  = !empty($GLOBALS['_SESSION']['email'])  ? trim($GLOBALS['_SESSION']['email'])  : 0;
        unset($GLOBALS['_SESSION']['admin_id']);
        unset($GLOBALS['_SESSION']['user_id']);
        unset($GLOBALS['_SESSION']['user_name']);
        unset($GLOBALS['_SESSION']['user_rank']);
        unset($GLOBALS['_SESSION']['discount']);
        unset($GLOBALS['_SESSION']['email']);
        
        if($GLOBALS['_SESSION'])
        $data        = serialize($GLOBALS['_SESSION']);
        $this->_time = time();

        if ($this->session_md5 == md5($data) && $this->_time < $this->session_expiry + 10)
        {
            return true;
        }

        $data = addslashes($data);

        if (isset($data{255}))
        {
            $this->db->autoReplace($this->session_data_table, array('sesskey' => $this->session_id, 'expiry' => $this->_time, 'data' => $data), array('expiry' => $this->_time,'data' => $data));

            $data = '';
        }

        return $this->db->query('UPDATE ' . $this->session_table . " SET expiry = '" . $this->_time . "', ip = '" . $this->_ip . "', userid = '" . $userid . "', adminid = '" . $adminid . "', user_name='" . $user_name . "', user_rank='" . $user_rank . "', discount='" . $discount . "', email='" . $email . "', data = '$data' WHERE sesskey = '" . $this->session_id . "' LIMIT 1");
    }

    function close_session()
    {
        $this->update_session();

        /* 闅忔満瀵 sessions_data 鐨勫簱杩涜?鍒犻櫎鎿嶄綔 */
        if (mt_rand(0, 2) == 2)
        {
            $this->db->query('DELETE FROM ' . $this->session_data_table . ' WHERE expiry < ' . ($this->_time - $this->max_life_time));
        }

        if ((time() % 2) == 0)
        {
            return $this->db->query('DELETE FROM ' . $this->session_table . ' WHERE expiry < ' . ($this->_time - $this->max_life_time));
        }

        return true;
    }

    function delete_spec_admin_session($adminid)
    {
        if (!empty($GLOBALS['_SESSION']['admin_id']) && $adminid)
        {
            return $this->db->query('DELETE FROM ' . $this->session_table . " WHERE adminid = '$adminid'");
        }
        else
        {
            return false;
        }
    }

    function destroy_session()
    {
        $GLOBALS['_SESSION'] = array();

        /* 
		setcookie($this->session_name, $this->session_id, 1, $this->session_cookie_path, $this->session_cookie_domain, $this->session_cookie_secure);

        ECSHOP 鑷?畾涔夋墽琛岄儴鍒 
        if (!empty($GLOBALS['ecs']))
        {
            $this->db->query('DELETE FROM ' . $GLOBALS['ecs']->table('cart') . " WHERE session_id = '$this->session_id' AND user_id = '' ");
			
        }
		*/
		
		setcookie($this->session_name, $this->session_id, 1, $this->session_cookie_path, $this->session_cookie_domain, $this->session_cookie_secure);
		
		
		if (!empty($GLOBALS['ecs']))  
		{  
			$this->db->query('DELETE FROM ' . $GLOBALS['ecs']->table('cart') . " WHERE session_id = '$this->session_id' AND user_id = 0");
			//只清空匿名購買 
		}  
		
        /* ECSHOP 鑷?畾涔夋墽琛岄儴鍒 */

        $this->db->query('DELETE FROM ' . $this->session_data_table . " WHERE sesskey = '" . $this->session_id . "' LIMIT 1");

        return $this->db->query('DELETE FROM ' . $this->session_table . " WHERE sesskey = '" . $this->session_id . "' LIMIT 1");
    }

    function get_session_id()
    {   
        return $this->session_id;
    }

    function get_users_count()
    {
        return $this->db->getOne('SELECT count(*) FROM ' . $this->session_table);
    }



    /*----------------------------------------------------------------
     | 取得ip位置
     |----------------------------------------------------------------
     |
     */
    function real_ip(){
    
        static $realip = NULL;

        if ($realip !== NULL)
        {
            return $realip;
        }

        if(isset($_COOKIE['real_ipd']) && !empty($_COOKIE['real_ipd'])){
            $realip = $_COOKIE['real_ipd'];  
            return $realip;
        }
    
        if(isset($_SERVER)){
            
            if( isset($_SERVER['HTTP_X_FORWARDED_FOR']) ){
                
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

                /* 取X-Forwarded-For中第一个非unknown的有效IP字符串 */
                foreach ($arr AS $ip){
                    
                    $ip = trim($ip);

                    if ($ip != 'unknown'){

                        $realip = $ip;

                        break;
                    }
                }
            }elseif( isset($_SERVER['HTTP_CLIENT_IP']) ){
                
                $realip = $_SERVER['HTTP_CLIENT_IP'];
            }else{
                
                if (isset($_SERVER['REMOTE_ADDR'])){
                    
                    $realip = $_SERVER['REMOTE_ADDR'];
                
                }else{
                    
                    $realip = '0.0.0.0';
                }
            }
        
        }else{
            
            if(getenv('HTTP_X_FORWARDED_FOR')){
                
                $realip = getenv('HTTP_X_FORWARDED_FOR');
            
            }elseif(getenv('HTTP_CLIENT_IP')){
            
                $realip = getenv('HTTP_CLIENT_IP');
            
            }else{

                $realip = getenv('REMOTE_ADDR');
            }
        
        }

    preg_match("/[\d\.]{7,15}/", $realip, $onlineip);
    $realip = !empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0';
    setcookie("real_ipd", $realip, time()+36000, "/");  /*添加*/
    return $realip;
}     
}

?>