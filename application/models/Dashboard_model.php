<?php

/**
 * Class Dashboard_Model
 * @property Mydatafetch_library $mydatafetch_library
 * @property Generalfunction_library $generalfunction_library
 */
class Dashboard_Model extends CI_Model
{
	function __construct()
	{
		parent::__construct();

        $this->load->library('mydatafetch_library');
	}

    public function getAllStaffs()
    {
        $query = "SELECT sm.id, sm.empId, sm.firstName, sm.middleName, sm.lastName, sm.staffPlace,
                   sm.walletBalance, sm.staffDept, sm.staffDesignation, sm.staffDoj, sm.staffDob, sm.mobNum,
                    sm.insertedDT, sm.ifActive, lm.locName"
            ." FROM staffmaster sm"
            ." LEFT JOIN locationmaster lm ON lm.id = sm.staffPlace";

        $result = $this->db->query($query)->result_array();
        $data['staffList'] = $result;
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

    public function getStaffById($id)
    {
        $query = "SELECT *"
            ." FROM staffmaster"
            ." WHERE id = ".$id;

        $result = $this->db->query($query)->result_array();
        $data['staffDetails'] = $result;
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

    public function updateWalletLog($details)
    {
        $details['loggedDT'] = date('Y-m-d H:i:s');

        $this->db->insert('walletlogmaster', $details);
        return true;
    }

    public function updateStaffRecord($id,$details)
    {
        $this->db->where('id', $id);
        $this->db->update('staffmaster', $details);
        return true;
    }

    public function blockStaffRecord($id)
    {
        $details = array(
            'ifActive'=> '0'
        );
        $this->db->where('id', $id);
        $this->db->update('staffmaster', $details);
        return true;
    }

    public function freeStaffRecord($id)
    {
        $details = array(
            'ifActive'=> '1'
        );
        $this->db->where('id', $id);
        $this->db->update('staffmaster', $details);
        return true;
    }

    public function saveStaffRecord($details)
    {
        $details['insertedDT'] = date('Y-m-d H:i:s');
        $details['ifActive'] = '1';

        $this->db->insert('staffmaster', $details);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }

    public function getWalletBalance($id)
    {
        $query = "SELECT sm.firstName, sm.middleName, sm.lastName, sm.walletBalance"
            ." FROM staffmaster sm"
            ." WHERE sm.id = ".$id;

        $result = $this->db->query($query)->result_array();

        return $result;
    }

    public function getWalletTrans($id)
    {
        $query = "SELECT wlm.amount, wlm.amtAction, wlm.loggedDT, wlm.updatedBy"
                ." FROM walletlogmaster wlm"
                ." WHERE wlm.staffId = ".$id;

        $result = $this->db->query($query)->result_array();
        $data['walletDetails'] = $result;
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
    public function getBalanceByMob($mobnum)
    {
        $query = "SELECT sm.empId, sm.firstName, sm.middleName, sm.lastName, sm.walletBalance"
            ." FROM staffmaster sm"
            ." WHERE sm.mobNum = '".$mobnum."'";

        $result = $this->db->query($query)->row_array();

        return $result;
    }

    public function getBalanceByEmp($empId)
    {
        $query = "SELECT sm.empId, sm.firstName, sm.middleName, sm.lastName, sm.walletBalance"
            ." FROM staffmaster sm"
            ." WHERE sm.empId = '".$empId."'";

        $result = $this->db->query($query)->row_array();

        return $result;
    }
    public function checkStaffChecked($empId)
    {
        $query = "SELECT *"
            ." FROM staffcheckinmaster"
            ." WHERE empId = '".$empId."' AND staffStatus = 1";

        $result = $this->db->query($query)->result_array();
        $data['checkin'] = $result;
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

    public function getAllCheckins()
    {
        $query = "SELECT *"
            ." FROM staffcheckinmaster"
            ." WHERE staffStatus = 1";

        $result = $this->db->query($query)->result_array();

        return $result;
    }

    public function saveCheckinLog($details)
    {
        $details['updateDT'] = date('Y-m-d H:i:s');
        $details['staffStatus'] = '1';

        $this->db->insert('staffcheckinmaster', $details);
        return true;
    }
}