<?php

namespace App\Http\Middleware;

use Closure;
use DB; 

use App\customLib\cls_session;



class InitMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */


    /*----------------------------------------------------------------
     | 模擬ecshop 初始化動作  
     |----------------------------------------------------------------
     |
     */
    public function handle($request, Closure $next)
    {   



        if( (isset($_GET['utm_source']) && $_GET['utm_source'] =='affiliates') && ( !empty($_GET['vtm_stat_id']) ) ){
            
            $fromAffiliates = trim($_GET['vtm_stat_id']);
             
            //setcookie('fromAffiliates', $fromAffiliates , time() + (86400 * 30), "/"); 
            //$coores=cookie('fromAffiliates', $fromAffiliates , (1440 * 30));
            setcookie("fromAffiliates",$fromAffiliates,time() + (86400 * 30) , "/");
            
        }
        // 初始化客製srssion類別
        
        //$sess = new cls_session( 'sessions' , 'sessions_data' );
        
        if(isset($_SESSION['user_id'])){  
        
            //如果存在會員登錄  
            if( $_SESSION['user_id']>0 ){  
            /*
                //取得對應user_id的session MD5碼， 
                $user_session=md5($_SESSION['user_id'].'srdesign');   
            
                //取得之前的session_id
                $old_session=$sess->get_session_id();  
            
            //如果會員的session_id和原先的session_id不同（則為新登錄情況），則將購物車內原session_id的商品，更新為會員下的商品！  
            if($user_session != $old_session){  
                $sql="update ".$GLOBALS['ecs']->table('cart')."set session_id='".$user_session."',user_id='".$_SESSION['user_id']."' where session_id='".$old_session."' ";  
                $GLOBALS['db']->query($sql);  
            }  
            
            //定義新的會員唯一session_id  
             define('SESS_ID',$user_session); */
                //echo '1';
            }else{  
                
                // 不存在會員，繼續用原有的session_id  
                //define('SESS_ID', $sess->get_session_id());  
            }  

        }else{  

            // 不存在會員，繼續用原有的session_id    
            //define('SESS_ID', $sess->get_session_id());  
            
        }        
        //$request->attributes->add(['myAttribute' => 'myValue']);
        return $next($request);
    }
    

}
