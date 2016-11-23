<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Main
 * @property cron_model $cron_model
 * @property dashboard_model $dashboard_model
 * @property locations_model $locations_model
 * @property login_model $login_model
 * @property users_model $users_model
*/

class Main extends MY_Controller {

	function __construct()
	{
		parent::__construct();
        $this->load->model('cron_model');
        $this->load->model('dashboard_model');
        $this->load->model('locations_model');
        $this->load->model('users_model');
	}
	public function index()
	{
        $data = array();

        if(isSessionVariableSet($this->isUserSession) === true)
        {
            $staff = $this->dashboard_model->getAllStaffs();
            if(isset($staff['status']) &&$staff['status'] === true)
            {
                $data['staffList'] = $staff['staffList'];
            }
        }
        $data['globalStyle'] = $this->dataformatinghtml_library->getGlobalStyleHtml($data);
        $data['globalJs'] = $this->dataformatinghtml_library->getGlobalJsHtml($data);
        $data['headerView'] = $this->dataformatinghtml_library->getHeaderHtml($data);

        $this->load->view('HomeView', $data);

	}
	public function checkUser($responseType = RESPONSE_RETURN)
    {
        $this->load->model('login_model');
        $post = $this->input->post();

        $userResult = $this->login_model->checkAdminUser($post['userName'],md5($post['password']));

        if($userResult['status'] === true && $userResult['userId'] != 0)
        {
            if($userResult['ifActive'] == NOT_ACTIVE)
            {
                $data['errorMsg'] = 'User Account is Disabled!';
                $data['status'] = false;
            }
            else
            {
                $this->login_model->setLastLogin($userResult['userId']);
                $this->generalfunction_library->setUserSession($userResult['userId']);
                $data['status'] = true;
                $data['isUserSession'] = $this->isUserSession;
                $data['userName'] = $this->userName;
            }
        }
        else
        {
            $data['status'] = false;
            $data['errorMsg'] = 'Username and password does not match.';
        }

        if($responseType == RESPONSE_JSON)
        {
            $data['pageUrl'] = $this->pageUrl;
            echo json_encode($data);
        }
        else
        {
            redirect($this->pageUrl);
        }
    }

    function logout()
    {
        $this->session->unset_userdata('user_id');
        $this->session->unset_userdata('user_type');
        $this->session->unset_userdata('user_name');
        $this->session->unset_userdata('user_email');
        $this->session->unset_userdata('user_firstname');

        redirect(base_url());
    }

    function addStaff()
    {
        $this->checkUserLogin();
        $data = array();

        $locArray = $this->locations_model->getAllLocations();
        $data['locations'] = $locArray;

        $data['globalStyle'] = $this->dataformatinghtml_library->getGlobalStyleHtml($data);
        $data['globalJs'] = $this->dataformatinghtml_library->getGlobalJsHtml($data);
        $data['headerView'] = $this->dataformatinghtml_library->getHeaderHtml($data);

        $this->load->view('StaffAddView', $data);

    }

    function blockStaff($id)
    {
        $this->checkUserLogin();
        $this->dashboard_model->blockStaffRecord($id);
        redirect(base_url());
    }
    function freeStaff($id)
    {
        $this->checkUserLogin();
        $this->dashboard_model->freeStaffRecord($id);
        redirect(base_url());
    }

    function staffEdit($id)
    {
        $this->checkUserLogin();
        $data = array();
        $staff = $this->dashboard_model->getStaffById($id);

        if(isset($staff['status']) && $staff['status'] === true)
        {
            $data['staffDetails'] = $staff['staffDetails'];
        }
        $locArray = $this->locations_model->getAllLocations();
        $data['locations'] = $locArray;

        $data['globalStyle'] = $this->dataformatinghtml_library->getGlobalStyleHtml($data);
        $data['globalJs'] = $this->dataformatinghtml_library->getGlobalJsHtml($data);
        $data['headerView'] = $this->dataformatinghtml_library->getHeaderHtml($data);

        $this->load->view('StaffEditView', $data);

    }

    function saveStaff()
    {
        $this->checkUserLogin();
        $post = $this->input->post();

        $id = $this->dashboard_model->saveStaffRecord($post);

        $walletRecord = array(
            'staffId' => $id,
            'amount' => '1500',
            'amtAction' => '2',
            'updatedBy' => $this->userName
        );
        $this->dashboard_model->updateWalletLog($walletRecord);

        redirect(base_url());
    }
    function updateStaff()
    {
        $this->checkUserLogin();
        $post = $this->input->post();
        $oldBal = $post['oldBalance'];
        unset($post['oldBalance']);

        $walletDiff = 0;
        $dorc = 1;
        if( $oldBal > $post['walletBalance'])
        {
            $walletDiff = $oldBal - $post['walletBalance'];
            $dorc = 1;
        }
        elseif($oldBal < $post['walletBalance'])
        {
            $walletDiff = $post['walletBalance'] - $oldBal;
            $dorc = 2;
        }
        $this->dashboard_model->updateStaffRecord($post['id'],$post);
        if($walletDiff != 0)
        {
            $walletRecord = array(
                'staffId' => $post['id'],
                'amount' => $walletDiff,
                'amtAction' => $dorc,
                'updatedBy' => $this->userName
            );
            $this->dashboard_model->updateWalletLog($walletRecord);
        }
        redirect(base_url());
    }

    function walletManage($id)
    {
        $this->checkUserLogin();
        $wallet = $this->dashboard_model->getWalletTrans($id);
        $data = array();
        if(isset($wallet['status']) && $wallet['status'] === true)
        {
            $data['walletDetails'] = $wallet['walletDetails'];
        }

        $data['walletBalance'] = $this->dashboard_model->getWalletBalance($id);
        $data['walletId'] = $id;
        $data['globalStyle'] = $this->dataformatinghtml_library->getGlobalStyleHtml($data);
        $data['globalJs'] = $this->dataformatinghtml_library->getGlobalJsHtml($data);
        $data['headerView'] = $this->dataformatinghtml_library->getHeaderHtml($data);
        $data['footerView'] = $this->dataformatinghtml_library->getFooterHtml($data);

        $this->load->view('WalletView', $data);
    }
    function checkWallet()
    {
        $data = array();

        $data['checkins'] = $this->dashboard_model->getAllCheckins();

        $data['globalStyle'] = $this->dataformatinghtml_library->getGlobalStyleHtml($data);
        $data['globalJs'] = $this->dataformatinghtml_library->getGlobalJsHtml($data);
        $data['headerView'] = $this->dataformatinghtml_library->getHeaderHtml($data);
        $data['footerView'] = $this->dataformatinghtml_library->getFooterHtml($data);

        $this->load->view('WalletCheckView', $data);
    }
    function checkUserLogin()
    {
        if(isSessionVariableSet($this->isUserSession) === false)
        {
            redirect(base_url());
        }
    }

    function updateWallet($id)
    {
        $this->checkUserLogin();
        $post = $this->input->post();
        $oldBal = $post['oldBalance'];
        $finalBal = 0;
        $dorc = 1;
        $gotAmount = 0;
        if(isset($post['addAmt']) && $post['addAmt'] != '')
        {
            $addBal = $post['addAmt'];
            $gotAmount = $post['addAmt'];
            $finalBal = $oldBal + $addBal;
            $dorc = 2;
        }
        elseif(isset($post['subAmt']) && $post['subAmt'] != '')
        {
            $subBal = $post['subAmt'];
            $gotAmount = $post['subAmt'];
            $finalBal = $oldBal - $subBal;
        }

        $walletRecord = array(
            'staffId' => $id,
            'amount' => $gotAmount,
            'amtAction' => $dorc,
            'updatedBy' => $this->userName
        );
        $this->dashboard_model->updateWalletLog($walletRecord);

        $details = array(
            'walletBalance' => $finalBal
        );
        $this->dashboard_model->updateStaffRecord($id,$details);
        $data['status'] = true;
        echo json_encode($data);
    }

    function getWallet()
    {
        $post = $this->input->post();
        $data = array();

        if(isset($post['userInput']) && is_numeric($post['userInput']))
        {
            $walletBal = $this->dashboard_model->getBalanceByMob($post['userInput']);
        }
        else
        {
            $walletBal = $this->dashboard_model->getBalanceByEmp($post['userInput']);
        }
        if(isset($walletBal))
        {
            $data['status'] = true;
            $data['balance'] = $walletBal;
        }
        else
        {
            $data['status'] = false;
        }
        echo json_encode($data);
    }

    function checkinStaff()
    {
        $post = $this->input->post();
        $data = array();

        $checkin = $this->dashboard_model->checkStaffChecked($post['empId']);
        if($checkin['status'] === true)
        {
            $data['status'] = false;
            $data['errorMsg'] = 'Employee Already Checked In';
        }
        else
        {
            $details = array(
                'staffName'=> $post['staffName'],
                'walletBalance'=> $post['walletBalance'],
                'empId'=> $post['empId']
            );
            $this->dashboard_model->saveCheckinLog($details);
            $data['status'] = true;
        }
        echo  json_encode($data);
    }
}
