<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @property Mydatafetch_library $mydatafetch_library
 * @property Generalfunction_library $generalfunction_library
 * @property Dataformatinghtml_library $dataformatinghtml_library
 * @property Sendemail_library $sendemail_library
 * @property curl_library $curl_library
 * @property CI_User_agent $agent
 * @property CI_Session $session
 * @property CI_Config $config
 * @property CI_Loader $load
 * @property CI_Input $input
 */
class MY_Controller extends CI_Controller
{
	public $pageUrl = '';


	public $isWUserSession = '';
	public $WuserType = '';
	public $WuserId = '';
	public $WuserName = '';
    public $WuserFirstName = '';
	public $WuserEmail = '';
	//public $WcurrentLocation = '';

	public $currentUrl = '';


	function __construct()
	{
		parent::__construct();

        $this->load->library('mydatafetch_library');
        $this->load->library('generalfunction_library');
		$this->load->library('dataformatinghtml_library');
        $this->load->library('user_agent');
		$this->load->library('sendemail_library');
		$this->load->library('curl_library');

		//
		if($this->agent->is_referral() == false)
		{
			$this->pageUrl = str_replace('/index.php', '', $this->agent->referrer());
		}
		else
		{
			$this->pageUrl = base_url();
		}


		if (isSession($this->session->Wuser_type) !== false)
		{
			$this->isWUserSession = ACTIVE;
			$this->wuserType = $this->session->Wuser_type;
			$this->WuserName = $this->session->Wuser_name;
			$this->WuserId = $this->session->Wuser_id;
			if(isset($this->session->Wuser_email))
			{
				$this->WuserEmail = $this->session->Wuser_email;
			}
            $this->WuserFirstName = $this->session->Wuser_firstname;
		}

		//get location from session
		/*if(isSessionVariableSet($this->session->currentLocation) === true)
        {
            $this->currentLocation = $this->session->currentLocation;
        }*/

		//
		$this->currentUrl = str_replace('/index.php', '', current_url());
	}
}
