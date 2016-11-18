<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cron extends MY_Controller
{
    /**
     * Class Cron
     * @property Cron_model $cron_model
     * @property Dashboard_Model $dashboard_model
     * @property Locations_Model $locations_model
     */
    function __construct()
    {
        parent::__construct();
        $this->load->model('cron_model');
        $this->load->model('dashboard_model');
        $this->load->model('locations_model');
    }
    public function index()
    {
        $this->load->view('Page404View');
    }

    public function feedsFetch()
    {

        $twitter = $this->getTwitterFeeds();

        $instagram = $this->getInstagramFeeds();

        $facebook =  $this->getFacebookResponse();

        //facebook
        $fbData = $this->cron_model->checkFeedByType("1");

        $fbPost = array(
            'feedText' => json_encode($facebook),
            'feedType' => '1'
        );
        if($fbData['status'] === true)
        {
            $this->cron_model->updateFeedByType($fbPost,"1");
        }
        else
        {
            $this->cron_model->insertFeedByType($fbPost);
        }

        //twitter
        $fbData = $this->cron_model->checkFeedByType("2");

        $fbPost = array(
            'feedText' => json_encode($twitter),
            'feedType' => '2'
        );
        if($fbData['status'] === true)
        {
            $this->cron_model->updateFeedByType($fbPost, "2");
        }
        else
        {
            $this->cron_model->insertFeedByType($fbPost);
        }

        //Instagram
        $fbData = $this->cron_model->checkFeedByType("3");

        $fbPost = array(
            'feedText' => json_encode($instagram),
            'feedType' => '3'
        );
        if($fbData['status'] === true)
        {
            $this->cron_model->updateFeedByType($fbPost, "3");
        }
        else
        {
            $this->cron_model->insertFeedByType($fbPost);
        }

        $this->storeAllFeeds();
    }
    public function getTwitterFeeds()
    {
        $twitterFeeds = '';
        $this->twitter->tmhOAuth->reconfigure();
        $oldparmas = array(
            'count' => '20',
            'exclude_replies' => 'true',
            'screen_name' => 'godoolally'
        );
        $parmas = array(
            'count' => '20',
            'q' => '#doolally OR #animalsofdoolally OR #ontapnow OR doolally OR doolaly
                     OR @godoolally -filter:retweets',
            'geocode' => '20.1885251,64.446117,1000km',
            'lang' => 'en',
            'result_type' => 'recent'
        );
        //$responseCode = $this->twitter->tmhOAuth->request('GET','https://api.twitter.com/1.1/statuses/user_timeline.json',$parmas);
        $responseCode = $this->twitter->tmhOAuth->request('GET','https://api.twitter.com/1.1/search/tweets.json',$parmas);
        if($responseCode == 200)
        {
            $twitterFeeds = $this->twitter->tmhOAuth->response['response'];
            $oldresponseCode = $this->twitter->tmhOAuth->request('GET','https://api.twitter.com/1.1/statuses/user_timeline.json',$oldparmas);

            if($oldresponseCode == 200)
            {
                $oldTwitterFeeds = $this->twitter->tmhOAuth->response['response'];
                $oldTwitterFeeds = json_decode($oldTwitterFeeds,true);
            }
        }
        $twitterFeeds = json_decode($twitterFeeds,true);

        if(isset($oldTwitterFeeds) && myIsMultiArray($oldTwitterFeeds))
        {
            return array_merge($twitterFeeds['statuses'], $oldTwitterFeeds);
        }
        else
        {
            return $twitterFeeds['statuses'];
        }
    }
    public function getInstagramFeeds()
    {
        $instaFeeds = $this->curl_library->getInstagramPosts();
        $moreInsta = $this->curl_library->getMoreInstaFeeds();

        if(!isset($instaFeeds) && !myIsMultiArray($instaFeeds))
        {
            $instaFeeds = null;
        }
        else
        {
            $instaFeeds = $instaFeeds['posts']['items'];
        }

        if(!isset($moreInsta) && !myIsMultiArray($moreInsta))
        {
            $moreInsta = null;
        }
        else
        {
            $moreInsta = $moreInsta['posts']['items'];
        }

        if(myIsMultiArray($instaFeeds) && myIsMultiArray($moreInsta))
        {
            $totalFeeds = array_merge($instaFeeds,$moreInsta);
            shuffle($totalFeeds);
            if(count($totalFeeds) > 90)
            {
                $totalFeeds = array_slice($totalFeeds,0, 85);
            }
        }
        else
        {
            $totalFeeds = (isset($instaFeeds) ? $instaFeeds : $moreInsta);
        }

        return $totalFeeds;
    }

    public function getFacebookResponse()
    {
        $params = array(
            'access_token' => FACEBOOK_TOKEN,
            'limit' => '15',
            'fields' => 'message,permalink_url,id,from,name,picture,source,updated_time'
        );
        $fbFeeds[] = $this->curl_library->getFacebookPosts('godoolallyandheri',$params);
        $fbFeeds[] = $this->curl_library->getFacebookPosts('godoolallybandra',$params);
        //kemps
        $fbFeeds[] = $this->curl_library->getFacebookPosts('1741740822733140',$params);
        $fbFeeds[] = $this->curl_library->getFacebookPosts('godoolally',$params);

        return array_merge($fbFeeds[0]['data'],$fbFeeds[1]['data'],$fbFeeds[2]['data']);
    }

    public function shiftEvents()
    {
        $events = $this->cron_model->findCompletedEvents();

        if(isset($events) && myIsMultiArray($events))
        {
            foreach($events as $key => $row)
            {
                if($row['ifAutoCreated'] == '1')
                {
                    $newDate = date('Y-m-d', strtotime($row['eventDate'].' +1 week'));
                    $this->cron_model->extendAutoEvent($row['eventId'],$newDate);
                }
                else
                {
                    $this->cron_model->updateEventRegis($row['eventId']);
                    $this->cron_model->transferEventRecord($row['eventId']);
                }
            }
        }
    }

    public function weeklyFeedback()
    {
        $locArray = $this->locations_model->getAllLocations();
        $feedbacks = $this->dashboard_model->getAllFeedbacks($locArray);

        foreach($feedbacks['feedbacks'][0] as $key => $row)
        {
            $keySplit = explode('_',$key);
            switch($keySplit[0])
            {
                case 'total':
                    $total[$keySplit[1]] = (int)$row;
                    break;
                case 'promo':
                    $promo[$keySplit[1]] = (int)$row;
                    break;
                case 'de':
                    $de[$keySplit[1]] = (int)$row;
                    break;
            }
        }

        if($total['overall'] != 0)
        {
            $data[] = (int)(($promo['overall']/$total['overall'])*100 - ($de['overall']/$total['overall'])*100);
        }
        if($total['bandra'] != 0)
        {
            $data[] = (int)(($promo['bandra']/$total['bandra'])*100 - ($de['bandra']/$total['bandra'])*100);
        }
        if($total['andheri'] != 0)
        {
            $data[] = (int)(($promo['andheri']/$total['andheri'])*100 - ($de['andheri']/$total['andheri'])*100);
        }
        if($total['kemps-corner'] != 0)
        {
            $data[] = (int)(($promo['kemps-corner']/$total['kemps-corner'])*100 - ($de['kemps-corner']/$total['kemps-corner'])*100);
        }

        $details = array(
            'locs' => implode(',',$data),
            'insertedDate' => date('Y-m-d')
        );
        $this->cron_model->insertWeeklyFeedback($details);
    }

    public function fetchJukeBoxLists()
    {
        $rests = $this->curl_library->getJukeboxTaprooms();
        if(isset($rests) && myIsMultiArray($rests))
        {
            foreach($rests as $key => $row)
            {
                $details = array();
                $resId = $row['id'];
                $details['tapId'] = $resId;
                $details['tapName'] = $row['name'];
                $playlist = $this->curl_library->getTapPlaylist($resId);
                if(isset($playlist) && myIsMultiArray($playlist))
                {
                    $songs = array();
                    foreach($playlist as $playSub => $playKey)
                    {
                        if($playSub == 1)
                            break;
                        $playId = $playKey['id'];
                        $songs[] = $this->curl_library->getTapSongsByPlaylist($resId,$playId);
                    }
                    $details['tapSongs'] = json_encode($songs);
                }

                //save to DB
                $songs = $this->cron_model->checkTapSongs($resId);
                if($songs['status'] === true)
                {
                    $this->cron_model->updateSongs($resId,$details);
                }
                else
                {
                    $this->cron_model->insertSongs($details);
                }

            }
        }
    }

    public function storeAllFeeds()
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
        $fbData = $this->cron_model->checkFeedByType("0");

        $fbPost = array(
            'feedText' => json_encode($allFeeds),
            'feedType' => '0'
        );
        if($fbData['status'] === true)
        {
            $this->cron_model->updateFeedByType($fbPost, "0");
        }
        else
        {
            $this->cron_model->insertFeedByType($fbPost);
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

}
