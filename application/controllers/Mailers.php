<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Mailers
 * @property Mailers_Model $mailers_model
 * @property Mugclub_Model $mugclub_model
 * @property users_model $users_model
 * @property offers_model $offers_model
*/

class Mailers extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('mailers_model');
        $this->load->model('mugclub_model');
        $this->load->model('users_model');
        $this->load->model('offers_model');
	}
	public function index()
	{
        $data = array();
        if(isSessionVariableSet($this->isUserSession) === false)
        {
            redirect(base_url().'home');
        }
        if($this->userType == GUEST_USER)
        {
            redirect(base_url());
        }

        if($this->userType == EXECUTIVE_USER)
        {
            $userInfo = $this->users_model->getUserDetailsById($this->userId);
            $data['expiredMugs'] = $this->mugclub_model->getExpiredMugsList(true,$userInfo['userData'][0]['assignedLoc']);
            $data['expiringMugs'] = $this->mugclub_model->getExpiringMugsList(1,'week',true,$userInfo['userData'][0]['assignedLoc']);
            $data['birthdayMugs'] = $this->mugclub_model->getBirthdayMugsList(true,$userInfo['userData'][0]['assignedLoc']);
        }
        else
        {
            $data['expiredMugs'] = $this->mugclub_model->getExpiredMugsList();
            $data['expiringMugs'] = $this->mugclub_model->getExpiringMugsList(1,'week');
            $data['birthdayMugs'] = $this->mugclub_model->getBirthdayMugsList();
        }


        $data['globalStyle'] = $this->dataformatinghtml_library->getGlobalStyleHtml($data);
        $data['globalJs'] = $this->dataformatinghtml_library->getGlobalJsHtml($data);
        $data['headerView'] = $this->dataformatinghtml_library->getHeaderHtml($data);
        $data['footerView'] = $this->dataformatinghtml_library->getFooterHtml($data);

       $this->load->view('MailersView',$data);
	}

    public function showMailAdd()
    {
        $data = array();
        if(isSessionVariableSet($this->isUserSession) === false)
        {
            redirect(base_url().'home');
        }
        $data['globalStyle'] = $this->dataformatinghtml_library->getGlobalStyleHtml($data);
        $data['globalJs'] = $this->dataformatinghtml_library->getGlobalJsHtml($data);
        $data['headerView'] = $this->dataformatinghtml_library->getHeaderHtml($data);
        $data['footerView'] = $this->dataformatinghtml_library->getFooterHtml($data);

        $this->load->view('MailAddView',$data);

    }

    public function saveMail()
    {
        $post = $this->input->post();

        $this->mailers_model->saveMailTemplate($post);

    }
    public function sendMail($mailType)
    {
        $data = array();
        if(isSessionVariableSet($this->isUserSession) === false)
        {
            redirect(base_url().'home');
        }
        $data['mailType'] = $mailType;

        $mugData = $this->mugclub_model->getCheckInMugClubList();
        $data['mugData'] = $mugData;
        //fetching mail Templates according to mail type

        $mailResult = $this->mailers_model->getAllTemplatesByType($mailType);

        //check What type of mail it is
        if($mailType == EXPIRED_MAIL)
        {
            if($this->userType == EXECUTIVE_USER)
            {
                $userInfo = $this->users_model->getUserDetailsById($this->userId);
                $expiredMails = $this->mugclub_model->getExpiredMugsList(true,$userInfo['userData'][0]['assignedLoc']);
            }
            else
            {
                $expiredMails = $this->mugclub_model->getExpiredMugsList();
            }
            $data['mailMugs'] = $expiredMails;
        }
        elseif($mailType == EXPIRING_MAIL)
        {
            if($this->userType == EXECUTIVE_USER)
            {
                $userInfo = $this->users_model->getUserDetailsById($this->userId);
                $expiringMails = $this->mugclub_model->getExpiringMugsList(1,'week',true,$userInfo['userData'][0]['assignedLoc']);
            }
            else
            {
                $expiringMails = $this->mugclub_model->getExpiringMugsList(1,'week');
            }
            $data['mailMugs'] = $expiringMails;
        }
        elseif($mailType == BIRTHDAY_MAIL)
        {
            if($this->userType == EXECUTIVE_USER)
            {
                $userInfo = $this->users_model->getUserDetailsById($this->userId);
                $expiringMails = $this->mugclub_model->getBirthdayMugsList(true,$userInfo['userData'][0]['assignedLoc']);
            }
            else
            {
                $expiringMails = $this->mugclub_model->getBirthdayMugsList();
            }

            $data['mailMugs'] = $expiringMails;
        }

        $data['mailList'] = $mailResult;

        $data['globalStyle'] = $this->dataformatinghtml_library->getGlobalStyleHtml($data);
        $data['globalJs'] = $this->dataformatinghtml_library->getGlobalJsHtml($data);
        $data['headerView'] = $this->dataformatinghtml_library->getHeaderHtml($data);
        $data['footerView'] = $this->dataformatinghtml_library->getFooterHtml($data);

        $this->load->view('MailSendView',$data);
    }

    public function sendAllMails($responseType = RESPONSE_RETURN)
    {
        $post = $this->input->post();

        $mugNums = $post['mugNums'];
        if($post['mailType'] == CUSTOM_MAIL)
        {
            $mugNums = explode(',',$post['mugNums']);
        }
        foreach($mugNums as $key)
        {
            $mugInfo = $this->mugclub_model->getMugDataForMailById($key);
            if($post['mailType'] == BIRTHDAY_MAIL)
            {
                $newDate =array("membershipEnd"=> date('Y-m-d', strtotime($mugInfo['mugList'][0]['membershipEnd'].' +3 month')));
                $this->mugclub_model->extendMemberShip($key,$newDate);
                $mugInfo['mugList'][0]['membershipEnd'] = $newDate['membershipEnd'];
            }
            $newSubject = $this->replaceMugTags($post['mailSubject'],$mugInfo);
            $newBody = $this->replaceMugTags($post['mailBody'],$mugInfo);
            if(isset($post['isSimpleMail']) && $post['isSimpleMail'] == '1')
            {
                $mainBody = '<html><body>';
                $body = $newBody;
                $body = wordwrap($body, 70);
                $body = nl2br($body);
                $body = stripslashes($body);
                $mainBody .= $body .'</body></html>';
                $newBody = $mainBody;
            }
            $cc        = 'priyanka@doolally.in,tresha@doolally.in,daksha@doolally.in,shweta@doolally.in';
            $fromName  = 'Doolally';
            if(isset($this->userFirstName))
            {
                $fromName = trim(ucfirst($this->userFirstName));
            }
            $fromEmail = 'priyanka@doolally.in';

            if(isset($this->userEmail))
            {
                $fromEmail = $this->userEmail;
            }

            $this->sendemail_library->sendEmail($mugInfo['mugList'][0]['emailId'],$cc,$fromEmail,$fromName,$newSubject,$newBody);
            $this->mailers_model->setMailSend($key,$post['mailType']);
        }

        if($responseType == RESPONSE_JSON)
        {
            $data['status'] = true;
            echo json_encode($data);
        }
        else
        {
            return true;
        }
    }

    function replaceMugTags($tagStr,$mugInfo)
    {
        $tagStr = str_replace('[sendername]',trim(ucfirst($this->userName)),$tagStr);
        preg_match_all('/\[[brcode]\w+\]/', $tagStr, $output_array);
        if(myIsMultiArray($output_array))
        {
            foreach($output_array as $key => $row)
            {
                foreach($row as $subKey)
                {
                    if($subKey == '[brcode]')
                    {
                        $breakCode = $this->generateBreakfastTwoCode($mugInfo['mugList'][0]['mugId']);
                        $tagStr = str_replace('[brcode]',$breakCode,$tagStr);
                        break;
                    }
                }
                break;
            }
        }
        foreach($mugInfo['mugList'][0] as $key => $row)
        {
            switch($key)
            {
                case 'mugId':
                    $tagStr = str_replace('[mugno]',trim($row),$tagStr);
                    break;
                case 'firstName':
                    $tagStr = str_replace('[firstname]',trim(ucfirst($row)),$tagStr);
                    break;
                case 'lastName':
                    $tagStr = str_replace('[lastname]',trim(ucfirst($row)),$tagStr);
                    break;
                case 'birthDate':
                    $d = date_create($row);
                    $tagStr = str_replace('[birthdate]',date_format($d,DATE_MAIL_FORMAT_UI),$tagStr);
                    break;
                case 'mobileNo':
                    $tagStr = str_replace('[mobno]',trim($row),$tagStr);
                    break;
                case 'membershipEnd':
                    $d = date_create($row);
                    $tagStr = str_replace('[expirydate]',date_format($d,DATE_MAIL_FORMAT_UI),$tagStr);
                    break;
            }
        }
        return $tagStr;
    }

    function replacePressName($tagStr, $pressInfo)
    {
        foreach($pressInfo as $key => $row)
        {
            switch($key)
            {
                case 'pressName':
                    $name = '';
                    if($row != '')
                    {
                        $name = explode(' ',$row)[0];
                    }
                    $tagStr = str_replace('[name]',trim(ucfirst($name)),$tagStr);
                    break;
            }
        }
        return $tagStr;
    }
    public function pressSend()
    {
        $data = array();

        $mailResult = $this->mailers_model->getAllPressEmails();
        if($mailResult['status'] === true)
        {
            $data['pressMails'] = $mailResult['mailData'];
        }
        $data['globalStyle'] = $this->dataformatinghtml_library->getGlobalStyleHtml($data);
        $data['globalJs'] = $this->dataformatinghtml_library->getGlobalJsHtml($data);
        $data['headerView'] = $this->dataformatinghtml_library->getHeaderHtml($data);
        $data['footerView'] = $this->dataformatinghtml_library->getFooterHtml($data);

        $this->load->view('PressMailSendView',$data);
    }

    public function uploadFiles()
    {
        $attchmentArr = '';
        $this->load->library('upload');
        if(isset($_FILES))
        {
            if($_FILES['attachment']['error'] != 1)
            {
                $config = array();
                $config['upload_path'] = './uploads/';
                $config['allowed_types'] = 'gif|jpg|png|jpeg';
                $config['max_size']      = '0';
                $config['overwrite']     = TRUE;

                $this->upload->initialize($config);
                $this->upload->do_upload('attachment');
                $upload_data = $this->upload->data();

                $attchmentArr = $upload_data['full_path'];
            }
            echo $attchmentArr;
        }
    }
    public function sendPressMails($responseType = RESPONSE_RETURN)
    {
        $post = $this->input->post();
        $attchmentArr = array();

        if(isset($post['attachment']))
        {
            $attchmentArr = explode(',',$post['attachment']);
        }
        elseif(isset($post['attachmentUrls']))
        {
            $attchmentArr = explode(',',$post['attachmentUrls']);
        }

        $pressEmails = explode(',',$post['pressEmails']);

        $pressSub = $post['mailSubject'];
        $pressBody = $post['mailBody'];
        $mainBody = '<html><body>';
        $body = $pressBody;
        $mainBody .= $body .'</body></html>';

        foreach($pressEmails as $key)
        {
            $pressInfo = $this->mailers_model->getPressInfoByMail($key);
            $newBody = $this->replacePressName($mainBody,$pressInfo);
            $cc        = 'priyanka@doolally.in,tresha@doolally.in,daksha@doolally.in,shweta@doolally.in';
            $fromName  = 'Doolally';
            if(isset($this->userFirstName))
            {
                $fromName = trim(ucfirst($this->userFirstName));
            }
            $fromEmail = 'priyanka@doolally.in';

            if(isset($this->userEmail))
            {
                $fromEmail = $this->userEmail;
            }

            $this->sendemail_library->sendEmail($key,$cc,$fromEmail,$fromName,$pressSub,$newBody,$attchmentArr);
        }
        if($responseType == RESPONSE_JSON)
        {
            $data['status'] = true;
            echo json_encode($data);
        }
        else
        {
            return true;
        }

    }

    public function generateBreakfastTwoCode($mugId)
    {
        $allCodes = $this->offers_model->getAllCodes();
        $usedCodes = array();
        $toBeInserted = array();
        if($allCodes['status'] === true)
        {
            foreach($allCodes['codes'] as $key => $row)
            {
                $usedCodes[] = $row['offerCode'];
            }
            $newCode = mt_rand(1000,99999);
            while(myInArray($newCode,$usedCodes))
            {
                $newCode = mt_rand(1000,99999);
            }
            $toBeInserted = array(
                'offerCode' => $newCode,
                'offerType' => 'Breakfast2',
                'offerLoc' => null,
                'offerMug' => $mugId,
                'isRedeemed' => 0,
                'ifActive' => 1,
                'createDateTime' => date('Y-m-d H:i:s'),
                'useDateTime' => null
            );
        }
        else
        {
            $newCode = mt_rand(1000,99999);
            $toBeInserted = array(
                'offerCode' => $newCode,
                'offerType' => 'Breakfast2',
                'offerLoc' => null,
                'offerMug' => $mugId,
                'isRedeemed' => 0,
                'ifActive' => 1,
                'createDateTime' => date('Y-m-d H:i:s'),
                'useDateTime' => null
            );
        }

        $this->offers_model->setSingleCode($toBeInserted);
        return 'BR-'.$newCode;
    }
}
