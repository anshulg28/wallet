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


	public $isUserSession = '';
	public $userType = '';
	public $userId = '';
	public $userName = '';
    public $userFirstName = '';
	public $userEmail = '';
	public $currentLocation = '';

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


		if (isSession($this->session->user_type) !== false)
		{
			$this->isUserSession = ACTIVE;
			$this->userType = $this->session->user_type;
			$this->userName = $this->session->user_name;
			$this->userId = $this->session->user_id;
			if(isset($this->session->user_email))
			{
				$this->userEmail = $this->session->user_email;
			}
            $this->userFirstName = $this->session->user_firstname;
		}

		//get location from session
		if(isSessionVariableSet($this->session->currentLocation) === true)
        {
            $this->currentLocation = $this->session->currentLocation;
        }

		//
		$this->currentUrl = str_replace('/index.php', '', current_url());
	}
}
