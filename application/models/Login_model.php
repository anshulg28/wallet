<?php

/**
 * Class Login_Model
 * @property Mydatafetch_library $mydatafetch_library
 * @property Generalfunction_library $generalfunction_library
 */
class Login_Model extends CI_Model
{
	function __construct()
	{
		parent::__construct();

        $this->load->library('mydatafetch_library');
	}

    public function checkUser($userName, $userPassword)
    {
        $query = "SELECT userId,ifActive "
            ."FROM doolally_usersmaster "
            ."where userName = '".$userName."' "
            ."AND password = '".$userPassword."' ";

        $result = $this->db->query($query)->row_array();

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

    public function checkAppUser($userEmail, $userPassword)
    {
        $query = "SELECT userId,ifActive "
            ."FROM doolally_usersmaster "
            ."where emailId = '".$userEmail."' "
            ."AND password = '".$userPassword."' ";

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

    public function checkUserByPin($loginPin)
    {
        $query = "SELECT userId, isPinChanged, ifActive "
            ."FROM doolally_usersmaster "
            ."where LoginPin = '".$loginPin."' ";

        $result = $this->db->query($query)->row_array();

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

    public function setLastLogin($userId)
    {
        $data = array(
          'lastLogin'=> date('Y-m-d H:i:s')
        );

        $this->db->where('userId', $userId);
        $this->db->update('doolally_usersmaster', $data);
        return true;
    }

    public function updateUserPass($post)
    {
        $post['updateDate'] = date('Y-m-d H:i:s');
        $post['updatedBy'] = $this->userName;
        $post['password'] = md5($post['password']);

        $this->db->where('userId', $post['userId']);
        $this->db->update('doolally_usersmaster', $post);
        return true;
    }
    public function updateUserPin($post)
    {
        $post['updateDate'] = date('Y-m-d H:i:s');
        $post['updatedBy'] = $this->userName;
        $post['LoginPin'] = md5($post['LoginPin']);


        $this->db->where('userId', $post['userId']);
        $this->db->update('doolally_usersmaster', $post);
        return true;
    }
}
