<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Offers
 * @property Offers_Model $offers_model
*/

class Offers extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('offers_model');
	}
	public function index()
	{

        $data = array();
        $data['globalStyle'] = $this->dataformatinghtml_library->getGlobalStyleHtml($data);
        $data['globalJs'] = $this->dataformatinghtml_library->getGlobalJsHtml($data);
        $data['headerView'] = $this->dataformatinghtml_library->getHeaderHtml($data);
        $data['footerView'] = $this->dataformatinghtml_library->getFooterHtml($data);

        if(isSessionVariableSet($this->isUserSession) === true)
        {
            $data['title'] = 'Offers :: Doolally';
        }
        else
        {
            $data['title'] = 'Login :: Doolally';
        }
        $this->load->view('OfferView', $data);
	}

    public function check()
    {
        $this->session->set_userdata('page_url', base_url(uri_string()));
        if(!isset($this->currentLocation) || isSessionVariableSet($this->currentLocation) === false)
        {
            redirect(base_url().'location-select');
        }

        $data = array();
        $data['globalStyle'] = $this->dataformatinghtml_library->getGlobalStyleHtml($data);
        $data['globalJs'] = $this->dataformatinghtml_library->getGlobalJsHtml($data);
        $data['headerView'] = $this->dataformatinghtml_library->getHeaderHtml($data);
        $data['footerView'] = $this->dataformatinghtml_library->getFooterHtml($data);

        $this->load->view('OfferCheckView', $data);
    }


    public function generate()
    {
        $data = array();
        $data['todayCount'] = $this->offers_model->getTodayCodes();
        $data['globalStyle'] = $this->dataformatinghtml_library->getGlobalStyleHtml($data);
        $data['globalJs'] = $this->dataformatinghtml_library->getGlobalJsHtml($data);
        $data['headerView'] = $this->dataformatinghtml_library->getHeaderHtml($data);
        $data['footerView'] = $this->dataformatinghtml_library->getFooterHtml($data);

        $this->load->view('OfferGenView', $data);
    }

    public function createCodes($responseType = RESPONSE_JSON)
    {
        $post = $this->input->post();
        $usedCodes = array();
        $unUsedCodes = array();
        $toBeInserted = array();
        $allCodes = $this->offers_model->getAllCodes();
        if($allCodes['status'] === true)
        {
            foreach($allCodes['codes'] as $key => $row)
            {
                $usedCodes[] = $row['offerCode'];
            }

            if(isset($post['beerNums']) && $post['beerNums'] != 0)
            {
                for($i=0;$i<$post['beerNums'];$i++)
                {
                    $newCode = mt_rand(1000,99999);
                    while(myInArray($newCode,$usedCodes))
                    {
                        $newCode = mt_rand(1000,99999);
                    }
                    $unUsedCodes[] = array(
                        'code' => $newCode,
                        'type' => 'Beer'
                    );

                    $toBeInserted[] = array(
                        'offerCode' => $newCode,
                        'offerType' => 'Beer',
                        'offerLoc' => null,
                        'isRedeemed' => 0,
                        'ifActive' => 1,
                        'createDateTime' => date('Y-m-d H:i:s'),
                        'useDateTime' => null
                    );

                }
            }
            if(isset($post['breakNums']) && $post['breakNums'] != 0)
            {
                for($i=0;$i<$post['breakNums'];$i++)
                {
                    $newCode = mt_rand(1000,99999);
                    while(myInArray($newCode,$usedCodes))
                    {
                        $newCode = mt_rand(1000,99999);
                    }
                    $unUsedCodes[] = array(
                        'code' => $newCode,
                        'type' => 'Breakfast2'
                    );
                    $toBeInserted[] = array(
                        'offerCode' => $newCode,
                        'offerType' => 'Breakfast2',
                        'offerLoc' => null,
                        'isRedeemed' => 0,
                        'ifActive' => 1,
                        'createDateTime' => date('Y-m-d H:i:s'),
                        'useDateTime' => null
                    );

                }
            }
            if(isset($post['customCode']) && $post['customNums'] != 0)
            {
                for($i=0;$i<$post['customNums'];$i++)
                {
                    $newCode = mt_rand(1000,99999);
                    while(myInArray($newCode,$usedCodes))
                    {
                        $newCode = mt_rand(1000,99999);
                    }
                    $unUsedCodes[] = array(
                        'code' => $newCode,
                        'type' => $post['customName']
                    );
                    $toBeInserted[] = array(
                        'offerCode' => $newCode,
                        'offerType' => $post['customName'],
                        'offerLoc' => null,
                        'isRedeemed' => 0,
                        'ifActive' => 1,
                        'createDateTime' => date('Y-m-d H:i:s'),
                        'useDateTime' => null
                    );

                }
            }
        }
        else
        {
            if(isset($post['beerNums']) && $post['beerNums'] != 0)
            {
                for($i=0;$i<$post['beerNums'];$i++)
                {
                    $newCode = mt_rand(1000,99999);

                    $unUsedCodes[] = array(
                        'code' => $newCode,
                        'type' => 'Beer'
                    );
                    $toBeInserted[] = array(
                        'offerCode' => $newCode,
                        'offerType' => 'Beer',
                        'offerLoc' => null,
                        'isRedeemed' => 0,
                        'ifActive' => 1,
                        'createDateTime' => date('Y-m-d H:i:s'),
                        'useDateTime' => null
                    );

                }
            }
            if(isset($post['breakNums']) && $post['breakNums'] != 0)
            {
                for($i=0;$i<$post['breakNums'];$i++)
                {
                    $newCode = mt_rand(1000,99999);

                    $unUsedCodes[] = array(
                        'code' => $newCode,
                        'type' => 'Breakfast2'
                    );
                    $toBeInserted[] = array(
                        'offerCode' => $newCode,
                        'offerType' => 'Breakfast2',
                        'offerLoc' => null,
                        'isRedeemed' => 0,
                        'ifActive' => 1,
                        'createDateTime' => date('Y-m-d H:i:s'),
                        'useDateTime' => null
                    );

                }
            }
            if(isset($post['customCode']) && $post['customNums'] != 0)
            {
                for($i=0;$i<$post['customNums'];$i++)
                {
                    $newCode = mt_rand(1000,99999);

                    $unUsedCodes[] = array(
                        'code' => $newCode,
                        'type' => $post['customName']
                    );
                    $toBeInserted[] = array(
                        'offerCode' => $newCode,
                        'offerType' => $post['customName'],
                        'offerLoc' => null,
                        'isRedeemed' => 0,
                        'ifActive' => 1,
                        'createDateTime' => date('Y-m-d H:i:s'),
                        'useDateTime' => null
                    );

                }
            }
        }

        $this->offers_model->setAllCodes($toBeInserted);
        if($responseType == RESPONSE_JSON)
        {
            echo json_encode($unUsedCodes);
        }
        else
        {
            return $unUsedCodes;
        }
    }

    public function stats()
    {
        $data = array();
        $data['offerCodes'] = $this->offers_model->getOfferCodes();
        $data['oldOffersCodes'] = $this->offers_model->getOldOfferCodes();

        //Getting All Offers Stats
        $data['newOfferStats'] = $this->offers_model->getOffersStats();
        $data['oldOfferStats'] = $this->offers_model->getOldOffersStats();

        $data['globalStyle'] = $this->dataformatinghtml_library->getGlobalStyleHtml($data);
        $data['globalJs'] = $this->dataformatinghtml_library->getGlobalJsHtml($data);
        $data['headerView'] = $this->dataformatinghtml_library->getHeaderHtml($data);
        $data['footerView'] = $this->dataformatinghtml_library->getFooterHtml($data);

        $this->load->view('OfferStatsView', $data);
    }
    public function delete($offerId, $offerAge)
    {
        if(isset($offerId))
        {
            if($offerAge == 'old')
            {
                $this->offers_model->deleteOldOfferRecord($offerId);
            }
            else
            {
                $this->offers_model->deleteOfferRecord($offerId);
            }
        }
        redirect(base_url().'offers/stats');
    }

    public function offerCheck($offerCode)
    {
        $data = array();
        $offerStatus = $this->offers_model->checkOfferCode($offerCode);

        if($offerStatus['status'] === false)
        {
            $data['status'] = false;
            $data['errorMsg'] = 'Invalid Code!';
        }
        else
        {
            if($offerStatus['codeCheck']['isRedeemed'] == 1)
            {
                $data['status'] = false;
                $data['errorMsg'] = 'Sorry, this code has been redeemed before.';
            }
            else
            {
                $offerData = array();
                $offerData['offerCode'] = $offerCode;
                if(isset($this->currentLocation) || isSessionVariableSet($this->currentLocation) === true)
                {
                    $offerData['offerLoc'] = $this->currentLocation;
                }
                $offerData['isRedeemed'] = 1;
                $offerData['useDateTime'] = date('Y-m-d H:i:s');
                $this->offers_model->setOfferUsed($offerData);
                $data['status'] = true;
                $data['offerType'] = $offerStatus['codeCheck']['offerType'];
            }
        }

        echo json_encode($data);
    }

    public function oldOfferCheck($offerCode)
    {
        $data = array();
        $offerStatus = $this->offers_model->checkOldOfferCode($offerCode);

        if($offerStatus['status'] === false)
        {
            $data['status'] = false;
            $data['errorMsg'] = 'Invalid Code!';
        }
        else
        {
            if($offerStatus['codeCheck']['isRedeemed'] == "1")
            {
                $data['status'] = false;
                $data['errorMsg'] = 'Sorry, this code has been redeemed before.';
            }
            else
            {
                $offerData = array();
                $offerData['offerCode'] = $offerCode;
                if(isset($this->currentLocation) || isSessionVariableSet($this->currentLocation) === true)
                {
                    $offerData['offerLoc'] = $this->currentLocation;
                }
                $offerData['isRedeemed'] = 1;
                $offerData['useDateTime'] = date('Y-m-d H:i:s');
                $this->offers_model->setoldOfferUsed($offerData);
                $data['status'] = true;
                $data['offerType'] = $offerStatus['codeCheck']['offerType'];
            }
        }

        echo json_encode($data);
    }
    
    public function offerUnused($id)
    {
        $this->offers_model->setOfferUnused($id);

        redirect($this->pageUrl);
    }
    public function oldOfferUnused($id)
    {
        $this->offers_model->setoldOfferUnused($id);

        redirect($this->pageUrl);
    }
}
