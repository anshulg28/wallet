<?php

/**
 * Class Login_Model
 * @property Mydatafetch_library $mydatafetch_library
 * @property Generalfunction_library $generalfunction_library
 */
class Mugclub_Model extends CI_Model
{
	function __construct()
	{
		parent::__construct();

        $this->load->library('mydatafetch_library');
	}

    public function getAllMugClubList()
    {
        $query = "SELECT mugId,mugTag,homeBase,l.locName,firstName,lastName,mobileNo, emailId, birthDate, membershipStart, membershipEnd,notes "
            ."FROM mugmaster m "
            ."LEFT JOIN locationmaster l ON id = m.homeBase";

        $result = $this->db->query($query)->result_array();

        $data['mugList'] = $result;
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

    public function getCheckInMugClubList()
    {
        $query = "SELECT mugId,mugTag,homeBase,l.locName,firstName,lastName,mobileNo, emailId, birthDate, membershipEnd "
                ."FROM mugmaster m "
                ."LEFT JOIN locationmaster l ON id = m.homeBase";

        $result = $this->db->query($query)->result_array();

        $data['mugList'] = $result;
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

    public function getAllMugsCount()
    {
        $query = "SELECT DISTINCT (SELECT count(*) FROM mugmaster WHERE membershipStart <= (CURRENT_DATE() - INTERVAL 1 MONTH)) as oldOverall,
                  (SELECT count(*) FROM mugmaster WHERE homeBase = 2 AND membershipStart <= (CURRENT_DATE() - INTERVAL 1 MONTH)) as oldAndheri,
                  (SELECT count(*) FROM mugmaster WHERE homeBase = 1 AND membershipStart <= (CURRENT_DATE() - INTERVAL 1 MONTH)) as oldBandra,
                  (SELECT count(*) FROM mugmaster WHERE homeBase = 3 AND membershipStart <= (CURRENT_DATE() - INTERVAL 1 MONTH)) as oldKemps,
                  (SELECT count(*) FROM mugmaster WHERE membershipStart <= CURRENT_DATE()) as newOverall,
                  (SELECT count(*) FROM mugmaster WHERE homeBase = 2 AND membershipStart <= CURRENT_DATE()) as newAndheri,
                  (SELECT count(*) FROM mugmaster WHERE homeBase = 3 AND membershipStart <= CURRENT_DATE()) as newKemps,
                  (SELECT count(*) FROM mugmaster WHERE homeBase = 1 AND membershipStart <= CURRENT_DATE()) as newBandra
                  FROM mugmaster";
        $result = $this->db->query($query)->row_array();

        $avgMugs['overall'] = ((int)$result['newOverall']+(int)$result['oldOverall'])/2;
        $avgMugs['bandra'] = ((int)$result['newBandra']+(int)$result['oldBandra'])/2;
        $avgMugs['andheri'] = ((int)$result['newAndheri']+(int)$result['oldAndheri'])/2;
        $avgMugs['kemps'] = ((int)$result['newKemps']+(int)$result['oldKemps'])/2;

        $data = $avgMugs;
        return $data;

    }
    public function getMugDataById($mugId)
    {
        $query = "SELECT * "
            ."FROM mugmaster "
            ."where mugId = ".$mugId;

        $result = $this->db->query($query)->result_array();

        $data['mugList'] = $result;
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

    public function getMugHoldById($mugId)
    {
        $query = "SELECT * "
            ."FROM holdmugmaster "
            ."where mugId = ".$mugId;

        $result = $this->db->query($query)->result_array();

        $data['mugList'] = $result;
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
    public function getAllMugHolds()
    {
        $query = "SELECT mugId "
            ."FROM holdmugmaster ";

        $result = $this->db->query($query)->result_array();

        $data['mugList'] = $result;
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

    public function getMugIdForRenew($mugId)
    {
        $query = "SELECT emailId, firstName, invoiceDate, invoiceNo, membershipStart, membershipEnd "
            ."FROM mugmaster "
            ."where mugId = ".$mugId;

        $result = $this->db->query($query)->row_array();
        $data = $result;

        return $data;
    }

    public function getMugEndDateById($mugId)
    {
        $query = "SELECT membershipEnd "
            ."FROM mugmaster "
            ."where mugId = ".$mugId;

        $result = $this->db->query($query)->result_array();

        $data['mugList'] = $result;
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
    public function setMugRenew($post)
    {
        $this->db->where('mugId', $post['mugId']);
        $this->db->update('mugmaster', $post);
        return true;
    }
    public function getMugDataForMailById($mugId)
    {
        $query = "SELECT mugId, firstName, lastName, mobileNo, emailId, birthDate, membershipEnd "
            ."FROM mugmaster "
            ."where mugId = ".$mugId;

        $result = $this->db->query($query)->result_array();

        $data['mugList'] = $result;
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

    public function getMugRange($mugId, $rangeEnd)
    {

        if($mugId < $rangeEnd)
        {
            $query = "SELECT mugId FROM mugmaster"
                ." WHERE mugId BETWEEN ".$mugId." AND ".$rangeEnd;
        }
        else
        {
            $query = "SELECT mugId FROM mugmaster"
                ." WHERE mugId BETWEEN ".$rangeEnd." AND ".$mugId;
        }

        $result = $this->db->query($query)->result_array();

        if(myIsArray($result))
        {
            $numArray = array();
            foreach($result as $key)
            {
                $numArray[] = $key['mugId'];
            }

            return $numArray;
        }
        else
        {
            return $result;
        }

    }

    public function verifyMobileNo($mobNo)
    {
        $query = "SELECT * "
            ."FROM mugmaster "
            ."where mobileNo = '".$mobNo."'";

        $result = $this->db->query($query)->row_array();

        $data['mugList'] = $result;
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
    public function filterMugParameters($post)
    {
        if(myIsArray($post))
        {
            $parameter = array();

            foreach ($post as $key => $row)
            {
                if ($row != '' && $key != 'ifMail')
                {
                    switch ($key)
                    {
                        case 'mugNum':
                            $parameter['mugId'] = $row;
                            break;

                        case 'baseLocation':
                            $parameter['homeBase'] = $row;
                            break;

                        case 'mobNum':
                            $parameter['mobileNo'] = $row;
                            break;

                        case 'birthdate':
                            $parameter['birthDate'] = $row;
                            break;

                        case 'memberS':
                            $parameter['membershipStart'] = $row;
                            break;

                        case 'memberE':
                            $parameter['membershipEnd'] = $row;
                            break;

                        case 'mugNotes':
                            $parameter['notes'] = $row;
                            break;

                        default:
                            $parameter[$key] = $row;
                            break;
                    }
                }
            }

            return $parameter;
        }
        else
        {
            return false;
        }
    }

    public function saveMugRecord($post)
    {
        if(isset($post['birthDate']))
        {
            $post['birthDate'] = date('Y-m-d', strtotime($post['birthDate']));
        }

        if(isset($post['invoiceDate']))
        {
            $post['invoiceDate'] = date('Y-m-d', strtotime($post['invoiceDate']));
        }

        if(isset($post['membershipStart']))
        {
            $post['membershipStart'] = date('Y-m-d', strtotime($post['membershipStart']));
        }

        if(isset($post['membershipEnd']))
        {
            $post['membershipEnd'] = date('Y-m-d', strtotime($post['membershipEnd']));
        }

        if(isset($post['notes']))
        {
            $post['notes'] = trim($post['notes']);
        }
        $post['ifActive'] = '1';

        $this->db->insert('mugmaster', $post);
        return true;
    }

    public function updateMugRecord($post, $mugNum = '')
    {
        if(isset($post['birthDate']))
        {
            $post['birthDate'] = date('Y-m-d', strtotime($post['birthDate']));
        }

        if(isset($post['invoiceDate']))
        {
            $post['invoiceDate'] = date('Y-m-d', strtotime($post['invoiceDate']));
        }

        if(isset($post['membershipStart']))
        {
            $post['membershipStart'] = date('Y-m-d', strtotime($post['membershipStart']));
        }

        if(isset($post['membershipEnd']))
        {
            $post['membershipEnd'] = date('Y-m-d', strtotime($post['membershipEnd']));
        }

        if(isset($post['notes']))
        {
            $post['notes'] = trim($post['notes']);
        }

        $post['ifActive'] = '1';

        if(isset($mugNum) && isStringSet($mugNum))
        {
            $this->db->where('mugId', $mugNum);
        }
        else
        {
            $this->db->where('mugId', $post['mugId']);
        }
        $this->db->update('mugmaster', $post);
        return true;
    }
    public function deleteMugRecord($mugId)
    {
        $query = "INSERT INTO deletedmugmaster "
            ."SELECT * FROM mugmaster "
            ."where mugId = ".$mugId;

        $this->db->query($query);

        $this->db->where('mugId', $mugId);
        $this->db->delete('mugmaster');
        return true;
    }
    public function holdMugRecord($mugId)
    {
        $query = "INSERT INTO holdmugmaster "
            ."SELECT * FROM mugmaster "
            ."where mugId = ".$mugId;

        $this->db->query($query);

        $this->db->where('mugId', $mugId);
        $this->db->delete('mugmaster');
        return true;
    }
    public function extendMemberShip($mugId, $newDate)
    {
        $this->db->where('mugId', $mugId);
        $this->db->update('mugmaster',$newDate);
        return true;
    }

    public function getExpiringMugsList($intervalNum, $intervalSpan,$locSort = false, $locArray = '')
    {

        $timeInterval = 'DAY';

        switch(strtolower($intervalSpan))
        {
            case 'day':
                $timeInterval = 'DAY';
                break;
            case 'week':
                $timeInterval = 'WEEK';
                break;
            case 'month':
                $timeInterval = 'MONTH';
                break;
            case 'year':
                $timeInterval = 'YEAR';
                break;
        }

        $query = "SELECT mugId, firstName, emailId"
                ." FROM mugmaster "
                ."WHERE membershipEnd IS NOT NULL AND membershipEnd != '0000-00-00' AND mailStatus = 0 "
                ."AND membershipEnd = (CURRENT_DATE() + INTERVAL ".$intervalNum." ".$timeInterval.")";

        if($locSort === true)
        {
            $query .= ' AND homeBase IN('.$locArray.')';
        }

        $result = $this->db->query($query)->result_array();

        $data['expiryMugList'] = $result;
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

    public function getExpiredMugsList($locSort = false, $locArray = '')
    {
        $query = "SELECT mugId, firstName, emailId"
            ." FROM mugmaster "
            ."WHERE membershipEnd IS NOT NULL AND membershipEnd != '0000-00-00' "
            ."AND membershipEnd <= CURRENT_DATE() AND mailStatus = 0";

        if($locSort === true)
        {
            $query .= ' AND homeBase IN('.$locArray.')';
        }

        $result = $this->db->query($query)->result_array();

        $data['expiryMugList'] = $result;
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

    public function getBirthdayMugsList($locSort = false, $locArray = '')
    {
        $query = "SELECT mugId, firstName, emailId "
            ." FROM mugmaster "
            ."WHERE birthDate IS NOT NULL AND birthDate != '0000-00-00' AND membershipEnd >= CURRENT_DATE() "
            ."AND DATE_FORMAT(birthDate,'%m-%d') = DATE_FORMAT(NOW(),'%m-%d') AND birthdayMailStatus = 0";

        if($locSort === true)
        {
            $query .= ' AND homeBase IN('.$locArray.')';
        }

        $result = $this->db->query($query)->result_array();

        $data['expiryMugList'] = $result;
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

    public function saveRenewRecord($post)
    {
        $this->db->insert('mugrenewmaster', $post);
        return true;
    }

}
