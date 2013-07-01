<?php

require_once(APPPATH . 'third_party/facebook/facebook.php');

class auth_connect
{

    var $CI;
    var $connection;

    function __construct()
    {

        $this->CI =& get_instance();
        $this->CI->load->config('yovoca');
        $this->CI->load->model('App_model');

        $this->connection = new Facebook(array(
            'appId'  => $this->CI->config->item('facebook_app_id'),
            'secret' => $this->CI->config->item('facebook_api_secret'),
        ));

        unset($uid, $user_profile);

        $uid = $this->connection->getUser();
        $user_profile = null;

        // IF USER IS LOGGED IN
        if($uid)
        {

            // We have a user ID, so probably a logged in user.
            // If not, we'll get an exception, which we handle below.
            try {

                // SAVED SESSION USER PROFILE
                // $session_user = $this->CI->session->userdata('user_info');

                $user_profile = $this->connection->api("/me"); //  ,'GET'

                // An active access token must be used to query information about the current user.

            } catch(FacebookApiException $e) {

                // If the user is logged out, you can have a 
                // user ID even though the access token is invalid.
                // In this case, we'll get an exception, so we'll
                // just ask the user to login again here.

                error_log($e->getType());
                error_log($e->getMessage());
                error_log(json_encode($e->getResult()));

                $this->CI->session->unset_userdata('user_info');
                $uid = null;
            }
        }

        if(($uid) && ($user_profile))
        {

            // CHECK IF IS USER IN USER DATABASE
            $row = $this->CI->App_model->_check_user_exist($uid, 'facebook');

            if(!$row)
            {
                // NEW USER
                $row['ID'] = $this->CI->App_model->addUser('facebook', $user_profile);
            }

            // USER IN DATABASE
            $this->CI->session->set_userdata('user_id', $row['ID']);

            // SAVE USER FB PROFILE TO SESSION
            $this->CI->session->set_userdata('user_info', $user_profile);

        }
        else
        {

            $this->CI->session->sess_destroy();
            $this->CI->session->unset_userdata('user_info');
        }
    }


    function is_logged_in()
    {

        if($this->CI->session->userdata('user_info')) {
            return TRUE;
        }

        return FALSE;
    }


    function get_login_button()
    {
        return '<fb:login-button></fb:login-button>';
    }


    function get_logout_button()
    {
        return '<fb:login-button autologoutlink="true" size="medium" background="white" length="short"></fb:login-button>';
    }
}

