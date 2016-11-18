<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Generalfunction_library
 */
class Generalfunction_library
{
    private $CI;

    function __construct()
    {
        $this->CI = &get_instance();
    }
    public function setUserSession($id)
    {
        $data = $this->CI->mydatafetch_library->getUserDetailsByUserId($id);
        
        $this->CI->session->set_userdata('user_id', $data['userId']);
        $this->CI->userId = $data['userId'];
        $this->CI->session->set_userdata('user_type', $data['userType']);
        $this->CI->userType = $data['userType'];
        $this->CI->session->set_userdata('user_name', $data['userName']);
        $this->CI->userName = $data['userName'];
        if(isset($data['emailId']))
        {
            $this->CI->session->set_userdata('user_email', $data['emailId']);
            $this->CI->userEmail = $data['emailId'];
        }
        $this->CI->session->set_userdata('user_firstname', $data['firstName']);
        $this->CI->userFirstName = $data['firstName'];
    }
    public function setMobUserSession($id)
    {
        $data = $this->CI->mydatafetch_library->getUserDetailsByUserId($id);

        $this->CI->session->set_userdata('user_mob_id', $data['userId']);
        $this->CI->userMobId = $data['userId'];
        $this->CI->session->set_userdata('user_mob_type', $data['userType']);
        $this->CI->userMobType = $data['userType'];
        $this->CI->session->set_userdata('user_mob_name', $data['userName']);
        $this->CI->userMobName = $data['userName'];
        if(isset($data['emailId']))
        {
            $this->CI->session->set_userdata('user_mob_email', $data['emailId']);
            $this->CI->userMobEmail = $data['emailId'];
        }
        $this->CI->session->set_userdata('user_mob_firstname', $data['firstName']);
        $this->CI->userMobFirstName = $data['firstName'];
    }

    public function setSessionVariable($key, $value)
    {
        $this->CI->session->set_userdata($key, $value);
    }

}
/* End of file */