<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Mydatafetch_library
 */
class Mydatafetch_library
{
    private $CI;

    function __construct()
    {
        $this->CI = &get_instance();
    }

    /*
    * user related function
    */
    public function getUserDetailsByUserId($userId)
    {
        $this->CI->db->select('userName, firstName, userId, userType, emailId');
        $this->CI->db->from('doolally_usersmaster');
        $this->CI->db->where('userId', $userId);

        $result = $this->CI->db->get()->row_array();

        return $result;
    }

    public function getBaseLocations()
    {
        $this->CI->db->select('id, locName, locUniqueLink');
        $this->CI->db->from('locationmaster');
        $this->CI->db->where('ifActive', '1');

        $result = $this->CI->db->get()->result_array();

        return $result;
    }

    public function getBaseLocationsById($id)
    {
        $this->CI->db->select('locName');
        $this->CI->db->from('locationmaster');
        $this->CI->db->where('ifActive', '1');
        $this->CI->db->where('id', $id);

        $result = $this->CI->db->get()->row_array();

        return $result;
    }
}