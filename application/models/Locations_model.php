<?php

/**
 * Class Locations_Model
 * @property Mydatafetch_library $mydatafetch_library
 * @property Generalfunction_library $generalfunction_library
 */
class Locations_Model extends CI_Model
{
	function __construct()
	{
		parent::__construct();

        $this->load->library('mydatafetch_library');
	}

    public function getAllLocations()
    {
        $query = "SELECT * "
            ."FROM locationmaster ";

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

    public function getLocationDetailsById($locId)
    {
        $query = "SELECT * "
            ."FROM locationmaster "
            ."WHERE id = ".$locId;

        $result = $this->db->query($query)->result_array();

        $data['locData'] = $result;
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
    public function getLocDetailsByUniqueLink($locUnqiueLink)
    {
        $query = "SELECT * "
            ."FROM locationmaster "
            ."WHERE locUniqueLink = '".$locUnqiueLink."'";

        $result = $this->db->query($query)->result_array();

        $data['locData'] = $result;
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

    public function saveLocationRecord($post)
    {
        $post['insertedDate'] = date('Y-m-d H:i:s');
        $post['lastUpdate'] = date('Y-m-d H:i:s');
        $post['ifActive'] = '1';

        $this->db->insert('locationmaster', $post);
        return true;
    }

    public function updateLocationRecord($post)
    {
        $post['lastUpdate'] = date('Y-m-d H:i:s');

        $this->db->where('id', $post['id']);
        $this->db->update('locationmaster', $post);
        return true;
    }
    public function deleteLocationRecord($locId)
    {
        $this->db->where('id', $locId);
        $this->db->delete('locationmaster');
        return true;
    }

}
