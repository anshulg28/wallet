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
        if ($this->mobile_detect->isMobile())
        {
            $get = $this->input->get();

            if(isset($get['event']) && isStringSet($get['event']) && isset($get['hash']) && isStringSet($get['hash']))
            {
                if(hash_compare(encrypt_data('EV-'.$get['event']),$get['hash']))
                {
                    if(isset($get['status']) && $get['status'] == 'success' && isset($get['payment_id']))
                    {
                        $this->thankYou($get['event'],$get['payment_id']);
                    }
                }
            }
            if(isSessionVariableSet($this->instaMojoStatus) && $this->instaMojoStatus == '1')
            {
                $this->generalfunction_library->setSessionVariable('instaMojoStatus','0');
                $this->instaMojoStatus = '0';
                $data['MojoStatus'] = 1;
            }
            elseif(isSessionVariableSet($this->instaMojoStatus) && $this->instaMojoStatus == '2')
            {
                $this->generalfunction_library->setSessionVariable('instaMojoStatus','0');
                $this->instaMojoStatus = '0';
                $data['MojoStatus'] = 2;
            }
            else
            {
                $data['MojoStatus'] = 0;
            }

            $data['mobileStyle'] = $this->dataformatinghtml_library->getMobileStyleHtml($data);
            $data['mobileJs'] = $this->dataformatinghtml_library->getMobileJsHtml($data);

            $data['myFeeds'] = $this->returnAllFeeds();
            $data['fnbItems'] = $this->dashboard_model->getAllActiveFnB();
            $data['beerCount'] = $this->dashboard_model->getBeersCount();

            $data['mainLocs'] = $this->locations_model->getAllLocations();

            $data['weekEvents'] = $this->dashboard_model->getWeeklyEvents();

            $events = $this->dashboard_model->getAllApprovedEvents();
            usort($events,
                function($a, $b) {
                    $ts_a = strtotime($a['eventDate']);
                    $ts_b = strtotime($b['eventDate']);

                    return $ts_a > $ts_b;
                }
            );

            if(isset($events) && myIsMultiArray($events))
            {
                foreach($events as $key => $row)
                {
                    $shortDWName = $this->googleurlapi->shorten($row['eventShareLink']);
                    if($shortDWName !== false)
                    {
                        $row['eventShareLink'] = $shortDWName;
                    }
                    $data['eventDetails'][$key]['eventData'] = $row;
                }
            }
            if(isStringSet($_SERVER['QUERY_STRING']))
            {
                $query = explode('/',$_SERVER['QUERY_STRING']);
                if(isset($query[1]) && $query[1] == 'events')
                {
                    if(isset($query[2]))
                    {
                        $event = explode('-',$query[2]);
                        $eventData = $this->dashboard_model->getEventById($event[1]);
                        $eventAtt = $this->dashboard_model->getEventAttById($event[1]);
                        $data['meta']['title'] = $eventData[0]['eventName'];
                        $truncated_RestaurantName = (strlen(strip_tags($eventData[0]['eventDescription'])) > 140) ? substr(strip_tags($eventData[0]['eventDescription']), 0, 140) . '..' : strip_tags($eventData[0]['eventDescription']);
                        $data['meta']['description'] = $truncated_RestaurantName;
                        $data['meta']['link'] = $eventData[0]['eventShareLink'];
                        $data['meta']['img'] = $eventAtt[0]['filename'];

                    }
                }
            }
            /*if ($this->mobile_detect->isAndroidOS()) {

                $data['androidStyle'] = $this->dataformatinghtml_library->getAndroidStyleHtml($data);
                $data['androidJs'] = $this->dataformatinghtml_library->getAndroidJsHtml($data);
                $this->load->view('mobile/android/MobileHomeView', $data);
            }
            else
            {
                $data['iosStyle'] = $this->dataformatinghtml_library->getIosStyleHtml($data);
                $data['iosJs'] = $this->dataformatinghtml_library->getIosJsHtml($data);
                $this->load->view('mobile/ios/MobileHomeView', $data);
            }*/
            $data['iosStyle'] = $this->dataformatinghtml_library->getIosStyleHtml($data);
            $data['iosJs'] = $this->dataformatinghtml_library->getIosJsHtml($data);
            $this->load->view('MobileHomeView', $data);
        }
        else
        {
            if(isStringSet($_SERVER['QUERY_STRING']))
            {
                $query = explode('/',$_SERVER['QUERY_STRING']);
                if(isset($query[1]) && $query[1] == 'events')
                {
                    if(isset($query[2]))
                    {
                        $event = explode('-',$query[2]);
                        $eventData = $this->dashboard_model->getEventById($event[1]);
                        $eventAtt = $this->dashboard_model->getEventAttById($event[1]);
                        $data['meta']['title'] = $eventData[0]['eventName'];
                        $truncated_RestaurantName = (strlen(strip_tags($eventData[0]['eventDescription'])) > 140) ? substr(strip_tags($eventData[0]['eventDescription']), 0, 140) . '..' : strip_tags($eventData[0]['eventDescription']);
                        $data['meta']['description'] = $truncated_RestaurantName;
                        $data['meta']['link'] = $eventData[0]['eventShareLink'];
                        $data['meta']['img'] = $eventAtt[0]['filename'];

                    }
                }
            }
            $this->load->view('ComingSoonView', $data);
        }
	}

    public function about()
    {
        $data = array();

        if($this->session->userdata('osType') == 'android')
        {
            $aboutView = $this->load->view('AboutUsView', $data);
        }
        else
        {
            $aboutView = $this->load->view('AboutUsView', $data);
        }
        echo json_encode($aboutView);
    }
    public function eventFetch($eventId, $evenHash)
    {
        $data = array();
        if(hash_compare(encrypt_data($eventId),$evenHash))
        {
            $decodedS = explode('-',$eventId);
            $eventId = $decodedS[count($decodedS)-1];
            $events = $this->dashboard_model->getFullEventInfoById($eventId);
            $shortDWName = $this->googleurlapi->shorten($events[0]['eventShareLink']);
            if($shortDWName !== false)
            {
                $events[0]['eventShareLink'] = $shortDWName;
            }
            $data['meta']['title'] = $events[0]['eventName'];
            $data['eventDetails'] = $events;
            if(isSessionVariableSet($this->userMobId))
            {
                $userCreated = $this->dashboard_model->checkUserCreated($this->userMobId,$eventId);
                if($userCreated['status'] === true)
                {
                    $data['userCreated'] = true;
                }
                else
                {
                    $data['userCreated'] = false;
                }
                $userBooked = $this->dashboard_model->checkUserBooked($this->userMobId,$eventId);
                if($userBooked['status'] === true)
                {
                    $data['userBooked'] = true;
                }
                else
                {
                    $data['userBooked'] = false;
                }
            }

            $aboutView = $this->load->view('EventView', $data);

            echo json_encode($aboutView);
        }
        else
        {
            $pgError = $this->load->view('EventView', $data);
            echo json_encode($pgError);
        }
    }

    public function editEvent($eventId, $evenHash)
    {
        $data = array();

        if(isSessionVariableSet($this->isMobUserSession) === false)
        {
            $data['status'] = false;
        }
        else
        {
            $data['status'] = true;
            if(hash_compare(encrypt_data($eventId),$evenHash))
            {
                $decodedS = explode('-',$eventId);
                $eventId = $decodedS[count($decodedS)-1];
                $data['eventDetails'] = $this->dashboard_model->getFullEventInfoById($eventId);
                $data['eventTc'] = $this->config->item('eventTc');
                $data['locData'] = $this->locations_model->getAllLocations();

            }
            /*else
            {
                $pgError = $this->load->view('mobile/ios/EventEditView', $data);
                echo json_encode($pgError);
            }*/
        }

        $aboutView = $this->load->view('EventEditView', $data);

        echo json_encode($aboutView);

    }

    public function createEvent()
    {
        $data = array();

        $data['eventTc'] = $this->config->item('eventTc');// $this->load->view('mobile/ios/EventTcView', $data);
        $data['locData'] = $this->locations_model->getAllLocations();
        
        $aboutView = $this->load->view('EventAddView', $data);

        echo json_encode($aboutView);
    }

    public function myEvents()
    {
        $data = array();
        if(isSessionVariableSet($this->isMobUserSession) === false)
        {
            $data['status'] = false;
        }
        else
        {
            $data['status'] = true;
            $data['registeredEvents'] = $this->dashboard_model->getEventsRegisteredByUser($this->userMobId);
            $data['userEvents'] = $this->dashboard_model->getEventsByUserId($this->userMobId);
            if(isset($data['registeredEvents']) && myIsMultiArray($data['registeredEvents']))
            {
                $shortDWName = $this->googleurlapi->shorten($data['registeredEvents'][0]['eventShareLink']);
                if($shortDWName !== false)
                {
                    $data['registeredEvents'][0]['eventShareLink'] = $shortDWName;
                }
            }
            if(isset($data['userEvents']) && myIsMultiArray($data['userEvents']))
            {
                $shortDWName1 = $this->googleurlapi->shorten($data['userEvents'][0]['eventShareLink']);
                if($shortDWName1 !== false)
                {
                    $data['userEvents'][0]['eventShareLink'] = $shortDWName1;
                }
            }
        }

        $eventView = $this->load->view('MyEventsView', $data);

        echo json_encode($eventView);

    }

    public function requestSong()
    {
        $data = array();
        if(isSessionVariableSet($this->isMobUserSession) === false)
        {
            $data['status'] = false;
        }
        else
        {
            $data['status'] = true;
        }

        $eventView = $this->load->view('MyEventsView', $data);

        echo json_encode($eventView);

    }

    public function contactUs()
    {
        $data = array();

        $data['locData'] = $this->locations_model->getAllLocations();

        $eventView = $this->load->view('ContactUsView', $data);

        echo json_encode($eventView);

    }
    public function jukeBox()
    {
        $data = array();

        $data['taprooms'] = $this->curl_library->getJukeboxTaprooms();

        $eventView = $this->load->view('JukeboxView', $data);

        echo json_encode($eventView);

    }

    public function taproomInfo($id)
    {
        $data = array();

        $data['taproomId'] = $id;
        $data['taproomInfo'] = $this->curl_library->getTaproomInfo($id);

        $eventView = $this->load->view('TaproomView', $data);

        echo json_encode($eventView);
    }
    public function eventDetails($eventId, $evenHash)
    {
        $data = array();
        if(isSessionVariableSet($this->isMobUserSession) === false)
        {
            $data['status'] = false;
        }
        else
        {
            $data['status'] = true;
            if(hash_compare(encrypt_data($eventId),$evenHash))
            {
                $decodedS = explode('-',$eventId);
                $eventId = $decodedS[count($decodedS)-1];
                $events = $this->dashboard_model->getDashboardEventDetails($eventId);
                $shortDWName = $this->googleurlapi->shorten($events[0]['eventShareLink']);
                if($shortDWName !== false)
                {
                    $events[0]['eventShareLink'] = $shortDWName;
                }
                $data['meta']['title'] = $events[0]['eventName'];
                $data['eventDetails'] = $events;

            }
            /*else
            {
                $pgError = $this->load->view('mobile/ios/EventView', $data);
                echo json_encode($pgError);
            }*/
        }

        $aboutView = $this->load->view('EventSingleView', $data);

        echo json_encode($aboutView);
    }

    public function signupList($eventId, $evenHash)
    {
        $data = array();
        if(isSessionVariableSet($this->isMobUserSession) === false)
        {
            $data['status'] = false;
        }
        else
        {
            $data['status'] = true;
            if(hash_compare(encrypt_data($eventId),$evenHash))
            {
                $decodedS = explode('-',$eventId);
                $eventId = $decodedS[count($decodedS)-1];
                $events = $this->dashboard_model->getJoinersInfo($eventId);

                $data['meta']['title'] = $events[0]['eventName'];
                $data['eventDetails'] = $events;
            }
        }

        $aboutView = $this->load->view('signUpListView', $data);

        echo json_encode($aboutView);
    }

    function thankYou($eventId, $mojoId)
    {
        $sessionDone = false;
        if(isSessionVariableSet($this->instaEventId))
        {
            if($this->instaEventId != $eventId)
            {
                $this->generalfunction_library->setSessionVariable('instaEventId',$eventId);
                $this->instaEventId= $eventId;
                $this->generalfunction_library->setSessionVariable('instaMojoStatus','1');
                $this->instaMojoStatus = '1';
                $sessionDone = true;
                //redirect(base_url().'mobile');
            }
        }
        else
        {
            $this->generalfunction_library->setSessionVariable('instaEventId',$eventId);
            $this->instaEventId= $eventId;
            $this->generalfunction_library->setSessionVariable('instaMojoStatus','1');
            $this->instaMojoStatus = '1';
            $sessionDone = true;
            //redirect(base_url().'mobile');
        }
        if($sessionDone === true)
        {
            $this->load->model('login_model');
            $userId = '';

            $requiredInfo = array();
            $mojoDetails = $this->curl_library->getInstaMojoRecord($mojoId);
            if(isset($mojoDetails) && myIsMultiArray($mojoDetails) && isset($mojoDetails['payment']))
            {
                $userStatus = $this->checkPublicUser($mojoDetails['payment']['buyer_email'],$mojoDetails['payment']['buyer_phone']);
                if($userStatus['status'] === false)
                {
                    $userId = $userStatus['userData']['userId'];
                }
                else
                {
                    $userName = explode(' ',$mojoDetails['payment']['buyer_name']);
                    if(count($userName)< 2)
                    {
                        $userName[1] = '';
                    }

                    $user = array(
                        'userName' => $mojoDetails['payment']['buyer_email'],
                        'firstName' => $userName[0],
                        'lastName' => $userName[1],
                        'password' => md5($mojoDetails['payment']['buyer_phone']),
                        'LoginPin' => null,
                        'isPinChanged' => null,
                        'emailId' => $mojoDetails['payment']['buyer_email'],
                        'mobNum' => $mojoDetails['payment']['buyer_phone'],
                        'userType' => '4',
                        'assignedLoc' => null,
                        'ifActive' => '1',
                        'insertedDate' => date('Y-m-d H:i:s'),
                        'updateDate' => date('Y-m-d H:i:s'),
                        'updatedBy' => $mojoDetails['payment']['buyer_name'],
                        'lastLogin' => date('Y-m-d H:i:s')
                    );

                    $userId = $this->users_model->savePublicUser($user);
                    $mailData= array(
                        'creatorName' => $mojoDetails['payment']['buyer_name'],
                        'creatorEmail' => $mojoDetails['payment']['buyer_email']
                    );
                    $eventData = $this->dashboard_model->getEventById($eventId);
                    $this->sendemail_library->memberWelcomeMail($mailData,$eventData[0]['eventPlace']);
                }

                //Save Booking Details

                $requiredInfo = array(
                    'bookerUserId' => $userId,
                    'eventId' => $eventId,
                    'quantity' => $mojoDetails['payment']['quantity'],
                    'paymentId' => $mojoId
                );

                $this->dashboard_model->saveEventRegis($requiredInfo);
                //$this->sendemail_library->newEventMail($mailEvent);
                if(isSessionVariableSet($this->isMobUserSession) === false)
                {
                    $this->login_model->setLastLogin($userId);
                    $this->generalfunction_library->setMobUserSession($userId);
                }
            }
            else
            {
                $this->generalfunction_library->setSessionVariable('instaMojoStatus','2');
                $this->instaMojoStatus = '2';
            }

        }
        return true;
    }
    public function checkUser()
    {
        $this->load->model('login_model');
        $post = $this->input->post();

        $data = array();
        $userInfo = $this->login_model->checkAppUser($post['username'], md5($post['password']));

        if($userInfo['status'] === true)
        {
            if($userInfo['userData']['ifActive'] == NOT_ACTIVE)
            {
                $data['status'] = false;
                $data['errorMsg'] = 'User Account is Disabled!';
            }
            else
            {
                $loginJuke = $this->getAccessFromJukebox($post['username'], $post['password']);
                if($loginJuke['status'] === false)
                {
                    $data['status'] = false;
                    $data['errorMsg'] = 'Email or Password is wrong!';
                }
                else
                {
                    $data['status'] = true;
                    $this->generalfunction_library->setSessionVariable('jukebox_token',$loginJuke['token']);
                    $this->jukeboxToken = $loginJuke['token'];
                }
                $data['status'] = true;
                $userId = $userInfo['userData']['userId'];
                $this->login_model->setLastLogin($userId);
                $this->generalfunction_library->setMobUserSession($userId);
            }
        }
        else
        {
            $data['status'] = false;
            $data['errorMsg'] = 'Email or Password is wrong!';
        }
        echo json_encode($data);
    }

    public function requestTapSong($id)
    {
        $data = array();

        if(isSessionVariableSet($this->isMobUserSession) === false)
        {
            $data['status'] = false;
        }
        else
        {
            if(isSessionVariableSet($this->jukeboxToken))
            {
                $data['status'] = true;
            }
            else
            {
                $userData = $this->users_model->getUserDetailsById($this->userMobId);
                if(!isset($userData['userData'][0]['plain_pass']))
                {
                    $this->appLogout();
                    $data['status'] = false;
                }
                else
                {
                    $loginCheck = $this->getAccessFromJukebox($userData['userData'][0]['emailId'],$userData['userData'][0]['plain_pass']);
                    if($loginCheck['status'] === false)
                    {
                        $data['status'] = false;
                    }
                    else
                    {
                        $data['status'] = true;
                        $this->generalfunction_library->setSessionVariable('jukebox_token',$loginCheck['token']);
                        $this->jukeboxToken = $loginCheck['token'];
                    }
                }
            }

        }

        if($data['status'] === true && isSessionVariableSet($this->jukeboxToken))
        {
            $data['tapId'] = $id;
            $data['tapSongs'] = $this->dashboard_model->getTapSongs($id);
        }

        $eventView = $this->load->view('RequestSongView', $data);

        echo json_encode($eventView);
    }

    function getAccessFromJukebox($email, $pwd)
    {
        $checkUser = $this->curl_library->checkJukeboxUser($email, $pwd);
        $data = array();

        if(isset($checkUser) && isset($checkUser['email']))
        {
            $logUser = $this->curl_library->loginJukeboxUser($email,$pwd);
            if(isset($logUser['error']))
            {
                $data['status'] = false;
                $data['errorMsg'] = 'Invalid Email or Password!';
            }
            elseif(isset($logUser['access_token']))
            {
                $data['status'] = true;
                $data['token'] = $logUser['access_token'];
            }
        }
        elseif(isset($checkUser['access_token']))
        {
            $data['status'] = true;
            $data['token'] = $checkUser['access_token'];
        }

        return $data;
    }
    public function checkJukeUser()
    {
        $this->load->model('login_model');
        $post = $this->input->post();

        $data = array();
        $userInfo = $this->login_model->checkAppUser($post['username'], md5($post['password']));

        if($userInfo['status'] === true)
        {
            if($userInfo['userData']['ifActive'] == NOT_ACTIVE)
            {
                $data['status'] = false;
                $data['errorMsg'] = 'User Account is Disabled!';
            }
            else
            {
                $token = $this->getAccessFromJukebox($post['username'], $post['password']);
                if($token['status'] === true)
                {
                    $this->generalfunction_library->setSessionVariable('jukebox_token',$token['token']);
                    $this->jukeboxToken = $token['token'];
                    $userId = $userInfo['userData']['userId'];
                    $this->login_model->setLastLogin($userId);
                    $this->generalfunction_library->setMobUserSession($userId);
                    $data['status'] = true;
                }
                else
                {
                    $data['status'] = false;
                    $data['errorMsg'] = 'Email or Password is wrong!';
                }
            }
        }
        else
        {
            $data['status'] = false;
            $data['errorMsg'] = 'Email or Password is wrong!';
        }
        echo json_encode($data);
    }
    public function saveUser()
    {
        $this->load->model('users_model');
        $this->load->model('login_model');
        $post = $this->input->post();

        if(isset($post['hasjukebox']))
        {
            $loginJuke = $this->getAccessFromJukebox($post['username'], $post['jukepass']);
        }
        else
        {
            $loginJuke = $this->getAccessFromJukebox($post['username'], $post['mobNum']);
        }


        if(isset($loginJuke['status']) && $loginJuke['status'] === true)
        {
            $this->generalfunction_library->setSessionVariable('jukebox_token',$loginJuke['token']);
            $this->jukeboxToken = $loginJuke['token'];
            $userInfo = $this->users_model->checkUserDetails($post['username'], md5($post['mobNum']));

            if($userInfo['status'] === true)
            {
                if($userInfo['userData']['ifActive'] == NOT_ACTIVE)
                {
                    $data['status'] = false;
                    $data['errorMsg'] = 'User Account is Disabled!';
                }
                else
                {
                    $data['status'] = true;
                    $userId = $userInfo['userData']['userId'];
                    $this->login_model->setLastLogin($userId);
                    $this->generalfunction_library->setMobUserSession($userId);
                }
            }
            else
            {
                $pss = $post['mobNum'];
                if(isset($post['jukepass']))
                {
                    $pss = $post['jukepass'];
                }
                $details = array(
                    'userName' => $post['username'],
                    'firstName' => '',
                    'lastName' => '',
                    'password' => md5($pss),
                    'plain_pass' => $pss,
                    'LoginPin' => null,
                    'emailId' => $post['username'],
                    'mobNum' => $post['mobNum'],
                    'userType' => '4'
                );
                $userId = $this->users_model->saveMobUserRecord($details);
                if(isset($userId) && isStringSet($userId))
                {
                    $this->login_model->setLastLogin($userId);
                    $this->generalfunction_library->setMobUserSession($userId);
                    $data['status'] = true;
                }
                else
                {
                    $data['status'] = false;
                    $data['errorMsg'] = 'Error Saving User';
                }
            }
        }
        else
        {
            $data['status'] = false;
            $data['errorMsg'] = 'Please use the jukebox password for login!';
        }

        echo json_encode($data);
    }

    public function playTapSong()
    {
        $post = $this->input->post();

        if(isSessionVariableSet($this->jukeboxToken))
        {
            $post['Auth'] = $this->jukeboxToken;
            $songStatus = $this->curl_library->requestTapSong($post);
            if(isset($songStatus['error']))
            {
                $data['status'] = false;
                $data['errorNum'] = 2;
                $data['errorMsg'] = $songStatus['error'];
            }
            elseif(isset($songStatus['detail']))
            {
                $data['status'] = false;
                $data['errorNum'] = 2;
                $data['errorMsg'] = $songStatus['detail'];
            }
            elseif(isset($songStatus['is_requested']) && $songStatus['is_requested'] === true)
            {
                $data['status'] = true;
            }
        }
        else
        {
            $data['status'] = false;
            $data['errorNum'] = 1;
            $data['errorMsg'] = 'Invalid Login/Session';
        }

        echo json_encode($data);
    }
    public function appLogout()
    {
        $this->session->unset_userdata('user_mob_id');
        $this->session->unset_userdata('user_mob_type');
        $this->session->unset_userdata('user_mob_name');
        $this->session->unset_userdata('user_mob_email');
        $this->session->unset_userdata('user_mob_firstname');
        $this->isMobUserSession = '';
        $this->userMobType = '';
        $this->userMobId = '';
        $this->userMobName = '';
        $this->userMobFirstName = '';
        $this->userMobEmail = '';

        $data['status'] = true;
        echo json_encode($data);
    }
    public function checkEventSpace()
    {
        $post = $this->input->post();
        $Edetails = array(
            "startTime" => date('H:i', strtotime($post['startTime'])),
            "endTime" => date('H:i', strtotime($post['endTime'])),
            "eventPlace" => $post['eventPlace'],
            "eventDate" => $post['eventDate']
        );
        $eventSpace = $this->dashboard_model->checkEventSpace($Edetails);
        if($eventSpace['status'] === true)
        {
            $data['status'] = false;
            $data['errorMsg'] = 'Sorry, This time slot is already booked!';
        }
        else
        {
            $data['status'] = true;
        }
        echo json_encode($data);
    }
    public function saveEvent()
    {
        $isUserCreated = false;
        $this->load->model('login_model');
        $post = $this->input->post();
        $userId = '';

        $Edetails = array(
            "startTime" => date('H:i', strtotime($post['startTime'])),
            "endTime" => date('H:i', strtotime($post['endTime'])),
            "eventPlace" => $post['eventPlace'],
            "eventDate" => $post['eventDate']
        );
        $eventSpace = $this->dashboard_model->checkEventSpace($Edetails);

        if($eventSpace['status'] === false)
        {
            if(isset($post['creatorPhone']) && isset($post['creatorEmail']))
            {
                $userStatus = $this->checkPublicUser($post['creatorEmail'],$post['creatorPhone']);

                if($userStatus['status'] === false)
                {
                    $userId = $userStatus['userData']['userId'];
                }
                else
                {
                    $userName = explode(' ',$post['creatorName']);
                    if(count($userName)< 2)
                    {
                        $userName[1] = '';
                    }

                    $user = array(
                        'userName' => $post['creatorEmail'],
                        'firstName' => $userName[0],
                        'lastName' => $userName[1],
                        'password' => md5($post['creatorPhone']),
                        'LoginPin' => null,
                        'isPinChanged' => null,
                        'emailId' => $post['creatorEmail'],
                        'mobNum' => $post['creatorPhone'],
                        'userType' => '4',
                        'assignedLoc' => null,
                        'ifActive' => '1',
                        'insertedDate' => date('Y-m-d H:i:s'),
                        'updateDate' => date('Y-m-d H:i:s'),
                        'updatedBy' => $post['creatorName'],
                        'lastLogin' => date('Y-m-d H:i:s')
                    );

                    $userId = $this->users_model->savePublicUser($user);
                    /*$mailData= array(
                        'creatorName' => $post['creatorName'],
                        'creatorEmail' => $post['creatorEmail']
                    );*/
                    $isUserCreated = true;
                    //$this->sendemail_library->memberWelcomeMail($mailData);
                }

                //Save event
                if(isset($post['attachment']))
                {
                    $attachement = $post['attachment'];
                    unset($post['attachment']);
                }
                $post['userId'] = $userId;
                if(isset($post['ifMicRequired']) && myIsArray($post['ifMicRequired']))
                {
                    $post['ifMicRequired'] = $post['ifMicRequired'][0];
                }
                if(isset($post['ifProjectorRequired']) && myIsArray($post['ifProjectorRequired']))
                {
                    $post['ifProjectorRequired'] = $post['ifProjectorRequired'][0];
                }
                $post['startTime'] = date('H:i', strtotime($post['startTime']));
                $post['endTime'] = date('H:i', strtotime($post['endTime']));
                $eventId = $this->dashboard_model->saveEventRecord($post);

                $eventShareLink = base_url().'?page/events/EV-'.$eventId.'/'.encrypt_data('EV-'.$eventId);

                $details = array(
                    'eventShareLink'=> $eventShareLink
                );
                $this->dashboard_model->updateEventRecord($details,$eventId);

                $img_names = array();
                if(isset($attachement))
                {
                    $img_names = explode(',',$attachement);
                    for($i=0;$i<count($img_names);$i++)
                    {
                        $attArr = array(
                            'eventId' => $eventId,
                            'filename'=> $img_names[$i],
                            'attachmentType' => '1'
                        );
                        $this->dashboard_model->saveEventAttachment($attArr);
                    }
                }
                $mailEvent= array(
                    'creatorName' => $post['creatorName'],
                    'creatorEmail' => $post['creatorEmail'],
                    'eventName' => $post['eventName'],
                    'eventPlace' => $post['eventPlace']
                );
                if($isUserCreated === true)
                {
                    $mailEvent['creatorPhone'] = $post['creatorPhone'];
                }
                $loc = $this->locations_model->getLocationDetailsById($post['eventPlace']);
                $mailVerify = $this->dashboard_model->getEventById($eventId);
                $mailVerify[0]['locData'] = $loc['locData'];
                $mailVerify[0]['attachment'] = $img_names[0];
                $post['locData'] = $loc['locData'];
                $this->sendemail_library->newEventMail($mailEvent);
                $this->sendemail_library->eventVerifyMail($mailVerify);
                $data['status'] = true;
                $this->login_model->setLastLogin($userId);
                $this->generalfunction_library->setMobUserSession($userId);
                echo json_encode($data);
            }
            elseif(isSessionVariableSet($this->userMobId))
            {
                $userId = $this->userMobId;
                $userD = $this->users_model->getUserDetailsById($userId);
                if($userD['status'] === true)
                {
                    if(isStringSet($userD['userData'][0]['firstName']))
                    {
                        $post['creatorName'] = $userD['userData'][0]['firstName'] . ' ' . $userD['userData'][0]['lastName'];
                    }
                    $post['creatorEmail'] = $userD['userData'][0]['emailId'];
                    $post['creatorPhone'] = $userD['userData'][0]['mobNum'];
                }
                else
                {
                    $post['creatorName'] = '';
                    $post['creatorEmail'] = '';
                    $post['creatorPhone'] = '';
                }
                //Save event
                if(isset($post['attachment']))
                {
                    $attachement = $post['attachment'];
                    unset($post['attachment']);
                }
                $post['userId'] = $userId;
                if(isset($post['ifMicRequired']) && myIsArray($post['ifMicRequired']))
                {
                    $post['ifMicRequired'] = $post['ifMicRequired'][0];
                }
                if(isset($post['ifProjectorRequired']) && myIsArray($post['ifProjectorRequired']))
                {
                    $post['ifProjectorRequired'] = $post['ifProjectorRequired'][0];
                }
                $post['startTime'] = date('H:i', strtotime($post['startTime']));
                $post['endTime'] = date('H:i', strtotime($post['endTime']));
                $eventId = $this->dashboard_model->saveEventRecord($post);

                $eventShareLink = base_url().'?page/events/EV-'.$eventId.'/'.encrypt_data('EV-'.$eventId);

                $details = array(
                    'eventShareLink'=> $eventShareLink
                );

                $this->dashboard_model->updateEventRecord($details,$eventId);

                $img_names = array();
                if(isset($attachement))
                {
                    $img_names = explode(',',$attachement);
                    for($i=0;$i<count($img_names);$i++)
                    {
                        $attArr = array(
                            'eventId' => $eventId,
                            'filename'=> $img_names[$i],
                            'attachmentType' => '1'
                        );
                        $this->dashboard_model->saveEventAttachment($attArr);
                    }
                }
                $mailEvent= array(
                    'creatorName' => $post['creatorName'],
                    'creatorEmail' => $post['creatorEmail'],
                    'eventName' => $post['eventName'],
                    'eventPlace' => $post['eventPlace']
                );
                $loc = $this->locations_model->getLocationDetailsById($post['eventPlace']);
                $mailVerify = $this->dashboard_model->getEventById($eventId);
                $mailVerify[0]['locData'] = $loc['locData'];
                $mailVerify[0]['attachment'] = $img_names[0];
                $this->sendemail_library->newEventMail($mailEvent);
                $this->sendemail_library->eventVerifyMail($mailVerify);
                $data['status'] = true;
                $this->login_model->setLastLogin($userId);
                $this->generalfunction_library->setMobUserSession($userId);
                echo json_encode($data);
            }
            else
            {
                $data['status'] = false;
                $data['errorMsg'] = 'Error in Account Creation';
                echo json_encode($data);
            }
        }
        else
        {
            $data['status'] = false;
            $data['errorMsg'] = 'Sorry, This time slot is already booked!';
            echo json_encode($data);
        }

    }
    public function updateEvent()
    {
        $this->load->model('login_model');
        $post = $this->input->post();
        $userId = '';

        if(isSessionVariableSet($this->userMobId))
        {
            $userId = $this->userMobId;
        }
        elseif(isset($post['userId']) && $post['userId'] != '')
        {
            $userId = $post['userId'];
        }
        else
        {
            $data['status'] = false;
            $data['errorMsg'] = 'Error in Account Creation';
            echo json_encode($data);
        }
        if(isStringSet($userId))
        {
            //Save event
            if(isset($post['attachment']))
            {
                $attachement = $post['attachment'];
                unset($post['attachment']);
            }
            if(isset($post['ifMicRequired']) && myIsArray($post['ifMicRequired']))
            {
                $post['ifMicRequired'] = $post['ifMicRequired'][0];
            }
            if(isset($post['ifProjectorRequired']) && myIsArray($post['ifProjectorRequired']))
            {
                $post['ifProjectorRequired'] = $post['ifProjectorRequired'][0];
            }
            $this->dashboard_model->updateEventRecord($post,$post['eventId']);

            $details = array(
                'ifActive' => '0',
                'ifApproved' => '0'
            );

            $this->dashboard_model->updateEventRecord($details,$post['eventId']);

            $img_names = array();
            if(isset($attachement))
            {
                $img_names = explode(',',$attachement);
                for($i=0;$i<count($img_names);$i++)
                {
                    $attArr = array(
                        'filename'=> $img_names[$i],
                    );
                    $this->dashboard_model->updateEventAttachment($attArr,$post['eventId']);
                }
            }

            $loc = $this->locations_model->getLocationDetailsById($post['eventPlace']);
            $mailVerify = $this->dashboard_model->getEventById($post['eventId']);
            $mailVerify[0]['locData'] = $loc['locData'];
            $mailVerify[0]['attachment'] = $img_names[0];
            $this->sendemail_library->eventVerifyMail($mailVerify);
            $data['status'] = true;

            echo json_encode($data);
        }

    }
    public function returnAllFeeds($responseType = RESPONSE_RETURN)
    {
        $feedData = $this->cron_model->getAllFeeds();
        $facebook = array();
        $twitter = array();
        $instagram = array();

        $allFeeds = null;

        if($feedData['status'] === true)
        {
            foreach($feedData['feedData'] as $key => $row)
            {
                switch($row['feedType'])
                {
                    case "1":
                        $facebook = json_decode($row['feedText'],true);
                        break;
                    case "2":
                        $twitter = json_decode($row['feedText'],true);
                        break;
                    case "3":
                        $instagram  = json_decode($row['feedText'],true);
                        break;
                }
            }

            $allFeeds = $this->sortNjoin($twitter,$instagram, $facebook);
        }

        //die();
        if($responseType == RESPONSE_JSON)
        {
            echo json_encode($allFeeds);
        }
        else
        {
            return $allFeeds;
        }
    }

    function sortNjoin($arr1 = array(), $arr2 = array(), $arr3 = array())
    {
        $all = array();
        $arrs[] = $arr1;
        $arrs[] = $arr2;
        $arrs[] = $arr3;
        foreach($arrs as $arr) {
            if(is_array($arr)) {
                $all = array_merge($all, $arr);
            }
        }
        //$all = array_merge($arr1, $arr2,$arr3);

        $sortedArray = array_map(function($fb) {
            $arr = $fb;
            if(isset($arr['updated_time']))
            {
                $arr['socialType'] = 'f';
                $arr['created_at'] = $arr['updated_time'];
                unset($arr['updated_time']);
            }
            elseif (isset($arr['external_created_at']))
            {
                $arr['socialType'] = 'i';
                $arr['created_at'] = $arr['external_created_at'];
                unset($arr['external_created_at']);
            }
            elseif (isset($arr['created_at']))
            {
                $arr['socialType'] = 't';
            }
            return $arr;
        },$all);

        usort($sortedArray,
            function($a, $b) {
                $ts_a = strtotime($a['created_at']);
                $ts_b = strtotime($b['created_at']);

                return $ts_a < $ts_b;
            }
        );
        return $sortedArray;

    }

    public function renderLink()
    {
        $this->load->library('OpenGraph');
        $post = $this->input->post();
        $graph = OpenGraph::fetch($post['url']);
        $array = array();

        foreach($graph as $key => $value) {
            $array[$key] = $value;
        }

        echo json_encode($array);
    }

    public function checkPublicUser($email, $mob)
    {
        $uData = array();
        $userExists = $this->users_model->checkUserDetails($email, $mob);

        if($userExists['status'] === true)
        {
            $uData['status'] = false;
            $uData['userData'] = $userExists['userData'];
        }
        else
        {
            $uData['status'] = true;
        }
        return $uData;
    }

}
