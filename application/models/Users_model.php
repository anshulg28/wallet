<?php

/**
 * Class Users_Model
 * @property Mydatafetch_library $mydatafetch_library
 * @property Generalfunction_library $generalfunction_library
 */
class Users_Model extends CI_Model
{
	function __construct()
	{
		parent::__construct();

        $this->load->library('mydatafetch_library');
	}

    public function getAllUsers()
    {
        $query = "SELECT * "
            ."FROM doolally_usersmaster ";

        $result = $this->db->query($query)->result_array();

        $data = $result;
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

    public function getUserDetailsById($userId)
    {
        $query = "SELECT * "
            ."FROM doolally_usersmaster "
            ."WHERE userId = ".$userId;

        $result = $this->db->query($query)->result_array();

        $data['userData'] = $result;
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
    public function searchUserByLoc($locId)
    {
        $query = "SELECT * "
            ."FROM doolally_usersmaster "
            ."WHERE FIND_IN_SET('".$locId."', assignedLoc)";

        $result = $this->db->query($query)->row_array();

        $data['userData'] = $result;
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
    public function getUserDetailsByUsername($userName)
    {
        $query = "SELECT * "
            ."FROM doolally_usersmaster "
            ."WHERE userName = '".$userName."'";

        $result = $this->db->query($query)->result_array();

        $data['userData'] = $result;
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
    public function checkUserDetails($email, $mob)
    {
        $query = "SELECT * "
            ."FROM doolally_usersmaster "
            ."WHERE userType = 4 AND emailId = '".$email."' AND mobNum = '".$mob."'";

        $result = $this->db->query($query)->row_array();

        $data['userData'] = $result;
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
    public function filterUserParameters($post)
    {
        if(myIsArray($post))
        {
            $parameter = array();

            foreach ($post as $key => $row)
            {
                if ($row != '')
                {
                    switch ($key)
                    {
                        case 'pass1':
                            $parameter['password'] = md5($row);
                            break;

                        case 'userLevel':
                            $parameter['userType'] = $row;
                            break;

                        case 'email':
                            $parameter['emailId'] = $row;
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

    public function saveUserRecord($post)
    {
        $post['insertedDate'] = date('Y-m-d H:i:s');
        $post['updateDate'] = date('Y-m-d H:i:s');
        $post['updatedBy'] = $this->userName;
        $post['ifActive'] = '1';

        $this->db->insert('doolally_usersmaster', $post);
        return true;
    }

    public function saveMobUserRecord($post)
    {
        $post['insertedDate'] = date('Y-m-d H:i:s');
        $post['updateDate'] = date('Y-m-d H:i:s');
        $post['updatedBy'] = '';
        $post['ifActive'] = '1';

        $this->db->insert('doolally_usersmaster', $post);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }

    public function savePublicUser($post)
    {
        $this->db->insert('doolally_usersmaster', $post);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }

    public function updateUserRecord($post)
    {
        $post['updateDate'] = date('Y-m-d H:i:s');
        $post['updatedBy'] = $this->userName;

        $this->db->where('userId', $post['userId']);
        $this->db->update('doolally_usersmaster', $post);
        return true;
    }
    public function deleteUserRecord($userId)
    {
        $this->db->where('userId', $userId);
        $this->db->delete('doolally_usersmaster');
        return true;
    }
    public function activateUserRecord($userId)
    {
        $data['ifActive'] = 1;
        $data['updateDate'] = date('Y-m-d H:i:s');
        $data['updatedBy'] = $this->userName;

        $this->db->where('userId', $userId);
        $this->db->update('doolally_usersmaster', $data);
        return true;
    }
    public function deActivateUserRecord($userId)
    {
        $data['ifActive'] = 0;
        $data['updateDate'] = date('Y-m-d H:i:s');
        $data['updatedBy'] = $this->userName;

        $this->db->where('userId', $userId);
        $this->db->update('doolally_usersmaster', $data);
        return true;
    }

}
