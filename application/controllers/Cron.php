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

class Cron extends MY_Controller {

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

        if(isSessionVariableSet($this->isWUserSession) === true)
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

    function creditBalance()
    {

        $data = array();
        $walletLog = array();
        $totalStaff = $this->dashboard_model->getAllStaffs();

        $mynums = array('8879103942', '9975027683', '9167333659','7045657944','7666192320','8652599420','9987217825','9870553445');
        $smsNums = array();
        $smsBalances = array();

        if($totalStaff['status'] === true)
        {
            foreach($totalStaff['staffList'] as $key => $row)
            {
                if($row['ifActive'] == '1' && $row['mobNum'] != '' && $row['walletBalance'] < 6000)
                {
                    $oldBalance = $row['walletBalance'];
                    $usedAmt = 1500;
                    $finalBal = $oldBalance + $usedAmt;

                    $data = array(
                        'walletBalance' => $finalBal
                    );
                    $this->dashboard_model->updateStaffRecord($row['id'],$data);
                    $smsNums[] = '91'.$row['mobNum'];
                    $smsBalances[] = $finalBal;

                    $walletLog[] = array(
                        'staffId' => $row['id'],
                        'amount' => $usedAmt,
                        'amtAction' => '2',
                        'notes' => 'Monthly Balance Credit',
                        'updatedBy' => 'system'
                    );
                }
            }

            if(isset($data) && myIsMultiArray($data))
            {
                $smsLogs = array();
                //$this->dashboard_model->updateStaffBatch($data);
                $this->dashboard_model->walletLogsBatch($walletLog);

                for($i=0;$i<count($smsNums);$i++)
                {
                    $postDetails = array(
                        'apiKey' => TEXTLOCAL_API,
                        'numbers' => implode(',', array($smsNums[$i])),
                        'sender'=> urlencode('DOLALY'),
                        'message' => rawurlencode('1500 Credited, Total Available Balance: '.$smsBalances[$i])
                    );
                    $smsStatus = $this->curl_library->sendCouponSMS($postDetails);


                    if($smsStatus['status'] == 'failure')
                    {
                        if(isset($smsStatus['warnings']))
                        {
                            $smsLogs[] = array(
                                'staffNum' => $smsNums[$i],
                                'smsStatus' => '2',
                                'smsDescription' => $smsStatus['warnings'][0]['message'],
                                'walletBal' => $smsBalances[$i],
                                'insertedDT' => date('Y-m-d H:i:s')
                            );
                        }
                        else
                        {
                            $smsLogs[] = array(
                                'staffNum' => $smsNums[$i],
                                'smsStatus' => '2',
                                'smsDescription' => $smsStatus['errors'][0]['message'],
                                'walletBal' => $smsBalances[$i],
                                'insertedDT' => date('Y-m-d H:i:s')
                            );
                        }
                    }
                    else
                    {
                        $smsLogs[] = array(
                            'staffNum' => $smsNums[$i],
                            'smsStatus' => '1',
                            'smsDescription' => null,
                            'walletBal' => $smsBalances[$i],
                            'insertedDT' => date('Y-m-d H:i:s')
                        );
                    }
                }

                $this->dashboard_model->smsLogsBatch($smsLogs);
            }
        }

    }

    function smsErrorCodes($code)
    {
        $returnVal = '';
        switch($code)
        {
            case 4:
                $returnVal = 'No recipients specified.';
                break;
            case 5:
                $returnVal = 'No message content.';
                break;
            case 6:
                $returnVal = 'Message too long.';
                break;
            case 7:
                $returnVal = 'Insufficient credits.';
                break;
            case 32:
                $returnVal = 'Invalid number format.';
                break;
            case 33:
                $returnVal = 'You have supplied too many numbers.';
                break;
            case 43:
                $returnVal = 'Invalid sender name.';
                break;
            case 44:
                $returnVal = 'No sender name specified.';
                break;
            case 51:
                $returnVal = 'No valid numbers specified.';
                break;
            case 192:
                $returnVal = 'You cannot send message at this time.';
                break;
        }
        return $returnVal;
    }
}
