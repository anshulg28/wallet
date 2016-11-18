<?php

/**
 * Class Offers_Model
 * @property Mydatafetch_library $mydatafetch_library
 * @property Generalfunction_library $generalfunction_library
 */
class Offers_Model extends CI_Model
{
	function __construct()
	{
		parent::__construct();

        $this->load->library('mydatafetch_library');
	}

    public function getAllCodes()
    {
        $query = "SELECT offerCode "
            ."FROM offersmaster ";

        $result = $this->db->query($query)->result_array();

        $data['codes'] = $result;
        if(myIsArray($result))
        {
            $data['status'] = true;
        }
        else
        {
            $data['status'] = false;
        }

        return $data;
    }
    public function setAllCodes($data)
    {
        $this->db->insert_batch('offersmaster',$data);
        return true;
    }
    public function setSingleCode($data)
    {
        $this->db->insert('offersmaster',$data);
        return true;
    }

    public function getTodayCodes()
    {
        $query = "SELECT offerCode, offerType "
            ."FROM offersmaster"
            ." WHERE date(createDateTime) = CURRENT_DATE()";

        $result = $this->db->query($query)->result_array();

        $data['codes'] = $result;
        if(myIsArray($result))
        {
            $data['status'] = true;
        }
        else
        {
            $data['status'] = false;
        }

        return $data;
    }

    public function getOfferCodes()
    {
        $query = "SELECT o.id, offerCode, offerType, isRedeemed, createDateTime, useDateTime ,l.locName"
                ." FROM offersmaster o "
                ."LEFT JOIN locationmaster l ON l.id = offerLoc ORDER BY useDateTime DESC";

        $result = $this->db->query($query)->result_array();

        $data['codes'] = $result;
        if(myIsArray($result))
        {
            $data['status'] = true;
        }
        else
        {
            $data['status'] = false;
        }

        return $data;
    }
    public function getOldOfferCodes()
    {
        $query = "SELECT o.id, offerCode, offerType, isRedeemed, createDateTime, useDateTime ,l.locName"
            ." FROM oldoffersmaster o "
            ."LEFT JOIN locationmaster l ON l.id = offerLoc ORDER BY useDateTime DESC";

        $result = $this->db->query($query)->result_array();

        $data['codes'] = $result;
        if(myIsArray($result))
        {
            $data['status'] = true;
        }
        else
        {
            $data['status'] = false;
        }

        return $data;
    }

    public function deleteOfferRecord($offerId)
    {
        $this->db->where('id', $offerId);
        $this->db->delete('offersmaster');
        return true;
    }
    public function deleteOldOfferRecord($offerId)
    {
        $this->db->where('id', $offerId);
        $this->db->delete('oldoffersmaster');
        return true;
    }

    public function checkOfferCode($offerCode)
    {
        $query = "SELECT offerType, isRedeemed"
                ." FROM offersmaster "
                ."WHERE offerCode = ".$offerCode;

        $result = $this->db->query($query)->row_array();

        $data['codeCheck'] = $result;
        if(myIsArray($result))
        {
            $data['status'] = true;
        }
        else
        {
            $data['status'] = false;
        }

        return $data;
    }

    public function checkOldOfferCode($offerCode)
    {
        $query = "SELECT offerType, isRedeemed"
            ." FROM oldoffersmaster "
            ."WHERE offerCode = ".$offerCode;

        $result = $this->db->query($query)->row_array();

        $data['codeCheck'] = $result;
        if(myIsArray($result))
        {
            $data['status'] = true;
        }
        else
        {
            $data['status'] = false;
        }

        return $data;
    }

    public function setoldOfferUsed($offerData)
    {
        $this->db->where('offerCode', $offerData['offerCode']);
        $this->db->update('oldoffersmaster', $offerData);
        return true;
    }
    public function setoldOfferUnused($id)
    {
        $data['isRedeemed'] = 0;
        $data['offerLoc'] = null;
        $data['useDateTime'] = null;
        $this->db->where('id', $id);
        $this->db->update('oldoffersmaster', $data);
        return true;
    }

    public function setOfferUsed($offerData)
    {
        $this->db->where('offerCode', $offerData['offerCode']);
        $this->db->update('offersmaster', $offerData);
        return true;
    }
    public function setOfferUnused($id)
    {
        $data['isRedeemed'] = 0;
        $data['offerLoc'] = null;
        $data['useDateTime'] = null;
        $this->db->where('id', $id);
        $this->db->update('offersmaster', $data);
        return true;
    }

    public function getOffersStats()
    {
        $query= "SELECT DISTINCT (SELECT count(*) FROM offersmaster where isRedeemed = 1 AND offerType= 'Beer') AS 'TBeer',
                (SELECT count(*) FROM offersmaster where isRedeemed = 1 AND offerType='Beer' AND date(useDateTime) >= DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH)) AS 'MBeer',
                (SELECT count(*) FROM offersmaster where isRedeemed = 1 AND offerType= 'Breakfast') AS 'TBreakfast',
                (SELECT count(*) FROM offersmaster where isRedeemed = 1 AND offerType='Breakfast' AND date(useDateTime) >= DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH)) AS 'MBreakfast'
                FROM `offersmaster`";

        $result = $this->db->query($query)->row_array();

        $data['offerStat'] = $result;
        if(myIsArray($result))
        {
            $data['status'] = true;
        }
        else
        {
            $data['status'] = false;
        }

        return $data;
    }

    public function getOldOffersStats()
    {
        $query= "SELECT DISTINCT (SELECT count(*) FROM oldoffersmaster where isRedeemed = 1 AND offerType= 'Beer') AS 'TBeer',
                (SELECT count(*) FROM oldoffersmaster where isRedeemed = 1 AND offerType='Beer' AND date(useDateTime) >= DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH)) AS 'MBeer',
                (SELECT (count(*)+59) FROM oldoffersmaster where isRedeemed = 1 AND offerType= 'Breakfast') AS 'TBreakfast',
                (SELECT count(*) FROM oldoffersmaster where isRedeemed = 1 AND offerType='Breakfast' AND date(useDateTime) >= DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH)) AS 'MBreakfast'
                FROM `oldoffersmaster`";

        $result = $this->db->query($query)->row_array();

        $data['offerStat'] = $result;
        if(myIsArray($result))
        {
            $data['status'] = true;
        }
        else
        {
            $data['status'] = false;
        }

        return $data;

    }
}
