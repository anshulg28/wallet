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

    public function getAvgCheckins($dateStart, $dateEnd, $locations)
    {
        $query = "SELECT DISTINCT (SELECT count(DISTINCT mugId, location) "
                ."FROM  mugcheckinmaster "
                ."WHERE checkinDateTime BETWEEN '$dateStart' AND '$dateEnd' AND location != 0) as overall";

            if(isset($locations))
            {
                $length = count($locations)-1;
                $counter = 0;
                foreach($locations as $key => $row)
                {
                    if(isset($row['id']))
                    {
                        $counter++;
                        if($counter <= $length)
                        {
                            $query .= ",";
                        }
                        $query .= "(SELECT count(DISTINCT mugId, location)"
                            ." FROM  mugcheckinmaster "
                            ."WHERE checkinDateTime BETWEEN '$dateStart' AND '$dateEnd' AND location =". $row['id'].")"
                            ." as '".$row['locUniqueLink']."'";

                    }
                }
            }
        $query .= " FROM mugcheckinmaster";


        $result = $this->db->query($query)->row_array();

        $data['checkInList'] = $result;
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

    public function getRegulars($dateStart, $dateEnd, $locations)
    {
        $query = "SELECT DISTINCT (SELECT count(*) FROM (SELECT m.mugId,homeBase FROM  mugmaster m
                LEFT JOIN mugcheckinmaster mc ON m.mugId = mc.mugId
                Where date(mc.checkinDateTime) BETWEEN '$dateStart' AND '$dateEnd'
				GROUP BY mc.mugId HAVING count(*) > 2) as tbl) as overall";

        if(isset($locations))
        {
            $length = count($locations)-1;
            $counter = 0;
            foreach($locations as $key => $row)
            {
                if(isset($row['id']))
                {
                    $counter++;
                    if($counter <= $length)
                    {
                        $query .= ",";
                    }
                    $query .= "(SELECT count(*) FROM (SELECT m.mugId,homeBase FROM  mugmaster m
                                LEFT JOIN mugcheckinmaster mc ON m.mugId = mc.mugId
                                Where homeBase = ".$row['id']." AND date(mc.checkinDateTime) BETWEEN '$dateStart' AND '$dateEnd'
                                GROUP BY mc.mugId HAVING count(*) > 2) as tbl) as '".$row['locUniqueLink']."'";
                }
            }
        }
        $query .= " FROM mugcheckinmaster";

        $result = $this->db->query($query)->row_array();

        $data['regularCheckins'] = $result;
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

    public function getIrregulars($dateStart, $dateEnd, $locations)
    {
        $query = "SELECT DISTINCT (SELECT count(*) FROM (SELECT m.mugId,homeBase FROM  mugmaster m
                LEFT JOIN mugcheckinmaster mc ON m.mugId = mc.mugId
                Where date(mc.checkinDateTime) BETWEEN '$dateStart' AND '$dateEnd'
				GROUP BY mc.mugId HAVING count(*) <= 1) as tbl) as overall";

        if(isset($locations))
        {
            $length = count($locations)-1;
            $counter = 0;
            foreach($locations as $key => $row)
            {
                if(isset($row['id']))
                {
                    $counter++;
                    if($counter <= $length)
                    {
                        $query .= ",";
                    }
                    $query .= "(SELECT count(*) FROM (SELECT m.mugId,homeBase FROM  mugmaster m
                                LEFT JOIN mugcheckinmaster mc ON m.mugId = mc.mugId
                                Where homeBase = ".$row['id']." AND date(mc.checkinDateTime) BETWEEN '$dateStart' AND '$dateEnd'
                                GROUP BY mc.mugId HAVING count(*) <= 1) as tbl) as '".$row['locUniqueLink']."'";
                }
            }
        }

        $query .= " FROM mugcheckinmaster";

        $result = $this->db->query($query)->row_array();

        $data['irregularCheckins'] = $result;
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

    public function getLapsers($dateStart, $dateEnd, $locations)
    {
        $query = "SELECT DISTINCT (SELECT count(*) FROM mugmaster 
                 WHERE membershipEnd BETWEEN '$dateStart' AND '$dateEnd' AND membershipEnd != '0000-00-00') as overall";

        if(isset($locations))
        {
            $length = count($locations)-1;
            $counter = 0;
            foreach($locations as $key => $row)
            {
                if(isset($row['id']))
                {
                    $counter++;
                    if($counter <= $length)
                    {
                        $query .= ",";
                    }
                    $query .= "(SELECT count(*) FROM mugmaster 
                             WHERE homeBase = ".$row['id']." AND membershipEnd BETWEEN '$dateStart' AND '$dateEnd'
                              AND membershipEnd != '0000-00-00') as '".$row['locUniqueLink']."'";
                }
            }
        }
        $query .= " FROM mugmaster";

        $result = $this->db->query($query)->row_array();

        $data['lapsers'] = $result;
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

    public function saveDashboardRecord($details)
    {
        $details['insertedDate'] = date('Y-m-d');

        $this->db->insert('dashboardmaster', $details);
        return true;
    }
    public function getDashboardRecord()
    {
        $query = "SELECT * "
                ." FROM dashboardmaster WHERE insertedDate = CURRENT_DATE()";

        $result = $this->db->query($query)->result_array();
        $data['todayStat'] = $result;
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
    public function getAllDashboardRecord()
    {
        $query = "SELECT * "
            ." FROM dashboardmaster ORDER BY insertedDate DESC LIMIT 30";

        $result = $this->db->query($query)->result_array();
        $data['dashboardPoints'] = array_reverse($result);
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

    public function saveInstaMojoRecord($details)
    {
        $this->db->insert('instamojomaster', $details);
        return true;
    }
    public function updateInstaMojoRecord($id,$details)
    {
        $this->db->where('id', $id);
        $this->db->update('instamojomaster', $details);
        return true;
    }

    public function getAllInstamojoRecord()
    {
        $query = "SELECT * "
            ." FROM instamojomaster"
            ." WHERE status = 1 AND isApproved = 0";

        $result = $this->db->query($query)->result_array();
        $data['instaRecords'] = $result;
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
    public function getAllFeedbacks($locations)
    {
        $query = "SELECT DISTINCT (SELECT COUNT(overallRating) FROM usersfeedbackmaster 
                 WHERE feedbackLoc != 0) as 'total_overall',
                 (SELECT COUNT(overallRating) FROM usersfeedbackmaster 
                 WHERE feedbackLoc = 1) as 'total_bandra',
                 (SELECT COUNT(overallRating) FROM usersfeedbackmaster 
                 WHERE feedbackLoc = 2) as 'total_andheri',
                 (SELECT COUNT(overallRating) FROM usersfeedbackmaster 
                 WHERE feedbackLoc = 3) as 'total_kemps-corner',
                 (SELECT COUNT(overallRating) FROM usersfeedbackmaster 
                 WHERE feedbackLoc != 0 AND overallRating >= 9) as 'promo_overall',
                 (SELECT COUNT(overallRating) FROM usersfeedbackmaster 
                 WHERE feedbackLoc != 0 AND overallRating < 7) as 'de_overall'";
        if(isset($locations))
        {
            $length = count($locations)-1;
            $counter = 0;
            foreach($locations as $key => $row)
            {
                if(isset($row['id']))
                {
                    $counter++;
                    if($counter <= $length)
                    {
                        $query .= ",";
                    }
                    $query .= "(SELECT COUNT(overallRating) FROM usersfeedbackmaster 
                              WHERE feedbackLoc = ".$row['id']." AND overallRating >= 9) as 'promo_".$row['locUniqueLink']."',";
                    $query .= "(SELECT COUNT(overallRating) FROM usersfeedbackmaster 
                              WHERE feedbackLoc = ".$row['id']." AND overallRating < 7) as 'de_".$row['locUniqueLink']."'";
                }
            }
        }

        $result = $this->db->query($query)->result_array();
        $data['feedbacks'] = $result;
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
    public function getWeeklyFeedBack()
    {
        $query = "SELECT *
                  FROM feedbackweekscore";

        $result = $this->db->query($query)->result_array();
        return $result;
    }

    public function insertFeedBack($details)
    {
        $this->db->insert_batch('usersfeedbackmaster', $details);
        return true;
    }

    public function saveFnbRecord($details)
    {
        $details['updateDateTime'] = date('Y-m-d H:i:s');
        $details['insertedDateTime'] = date('Y-m-d H:i:s');
        $details['ifActive'] = '1';

        $this->db->insert('fnbmaster', $details);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }
    public function saveFnbAttachment($details)
    {
        $details['insertedDateTime'] = date('Y-m-d H:i:s');

        $this->db->insert('fnbattachment', $details);
        return true;
    }

    public function getAllFnB()
    {
        $query = "SELECT fnbId,itemType,itemName,itemHeadline,itemDescription,priceFull,priceHalf,ifActive
                  FROM fnbmaster ORDER BY fnbId DESC";

        $result = $this->db->query($query)->result_array();
        $data['fnbItems'] = $result;
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
    public function getAllActiveFnB()
    {
        $query = "SELECT fm.fnbId,fm.itemType,fm.taggedLoc,fm.itemName,fm.itemHeadline,fm.itemDescription,fm.priceFull,fm.priceHalf,
                  fm.ifActive,fa.id,fa.filename
                  FROM fnbmaster fm
                  LEFT JOIN fnbattachment fa ON fa.fnbId = fm.fnbId
                  WHERE fm.ifActive = 1 
                  GROUP BY fm.fnbId
                  ORDER BY fm.itemType DESC";

        $result = $this->db->query($query)->result_array();
        return $result;
    }
    public function getBeersCount()
    {
        $query = "SELECT count(*) as 'beers' FROM fnbmaster WHERE itemType = 2";

        $result = $this->db->query($query)->row_array();
        return $result;
    }
    public function getFnBById($fnbId)
    {
        $query = "SELECT fnbId,itemType,itemName,itemHeadline,itemDescription,priceFull,priceHalf,ifActive
                  FROM fnbmaster WHERE ifActive = 1 AND fnbId = ".$fnbId;

        $result = $this->db->query($query)->result_array();

        return $result;
    }

    public function getTagLocsFnb($fnbId)
    {
        $query = "SELECT fm.taggedLoc,lm.locName, lm.id
                  FROM fnbmaster fm
                  LEFT JOIN locationmaster lm ON FIND_IN_SET(lm.id,fm.taggedLoc)
                  WHERE fm.taggedLoc IS NOT NULL AND fm.fnbId = ".$fnbId;

        $result = $this->db->query($query)->result_array();

        return $result;
    }
    public function updateBeerLocTag($details, $fnbId)
    {
        $this->db->where('fnbId',$fnbId);
        $this->db->update('fnbmaster', $details);
        return true;
    }

    public function getFnbAttById($id)
    {
        $query = "SELECT id,fnbId,filename,attachmentType
                  FROM fnbattachment WHERE fnbId = ".$id;

        $result = $this->db->query($query)->result_array();

        return $result;
    }

    //Event Related Functions

    public function saveEventRecord($details)
    {
        $details['createdDateTime'] = date('Y-m-d H:i:s');
        $details['ifActive'] = '0';
        $details['ifApproved'] = '0';

        $this->db->insert('eventmaster', $details);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }
    public function updateEventRecord($details, $eventId)
    {
        $this->db->where('eventId',$eventId);
        $this->db->update('eventmaster', $details);
        return true;
    }
    public function saveEventAttachment($details)
    {
        $details['insertedDateTime'] = date('Y-m-d H:i:s');

        $this->db->insert('eventattachment', $details);
        return true;
    }
    public function updateEventAttachment($details, $eventId)
    {
        $this->db->where('eventId',$eventId);
        $this->db->update('eventattachment', $details);
        return true;
    }
    public function getAllEvents()
    {
        $query = "SELECT *
                  FROM eventmaster ORDER BY eventId DESC";

        $result = $this->db->query($query)->result_array();

        return $result;
    }

    public function getEventsByUserId($userId)
    {
        $query = "SELECT em.eventId, em.eventName, em.eventDescription, em.eventType, em.eventDate, em.startTime, em.endTime, em.costType, 
                  em.eventPrice, em.priceFreeStuff, em.eventPlace, em.eventCapacity, em.ifMicRequired, em.ifProjectorRequired, 
                  em.creatorName, em.creatorPhone, em.creatorEmail, em.aboutCreator, em.userId, em.eventShareLink,
                  em.eventPaymentLink, em.ifActive, em.ifApproved, ea.filename, l.locName
                  FROM `eventmaster` em
                  LEFT JOIN eventattachment ea ON ea.eventId = em.eventId
                  LEFT JOIN locationmaster l ON eventPlace = l.id
                  WHERE userId = ".$userId." GROUP BY em.eventId";

        $result = $this->db->query($query)->result_array();

        return $result;
    }
    public function getEventsRegisteredByUser($userId)
    {
        $query = "SELECT erm.bookerId,erm.bookerUserId,erm.eventId,erm.quantity, em.eventId, em.eventName,
                  em.eventDescription, em.eventType, em.eventDate, em.startTime, em.endTime, em.costType, 
                  em.eventPrice, em.priceFreeStuff, em.eventPlace, em.eventCapacity, em.ifMicRequired, em.ifProjectorRequired, 
                  em.creatorName, em.creatorPhone, em.creatorEmail, em.aboutCreator, em.userId, em.eventShareLink,
                  em.eventPaymentLink, em.ifActive, em.ifApproved, ea.filename, l.locName
                  FROM eventregistermaster erm
                  LEFT JOIN eventmaster em ON em.eventId = erm.eventId
                  LEFT JOIN eventattachment ea ON ea.eventId = erm.eventId
                  LEFT JOIN locationmaster l ON l.id = em.eventPlace
                  WHERE erm.eventDone != 1 AND bookerUserId = ".$userId." GROUP BY erm.eventId";

        $result = $this->db->query($query)->result_array();

        return $result;
    }
    public function getEventById($eventId)
    {
        $query = "SELECT *
                  FROM eventmaster WHERE eventId = ".$eventId;

        $result = $this->db->query($query)->result_array();

        return $result;
    }
    public function getWeeklyEvents()
    {
        $query = "SELECT GROUP_CONCAT(eventName SEPARATOR ',') as eventNames,
                  GROUP_CONCAT(eventPlace SEPARATOR ',') as eventPlaces,eventDate FROM eventmaster
                  WHERE eventDate BETWEEN CURRENT_DATE() AND (CURRENT_DATE() + INTERVAL 1 WEEK) 
                  AND ifActive  = ".ACTIVE." AND ifApproved = ".EVENT_APPROVED." GROUP BY eventDate 
                  ORDER BY eventDate ASC";

        $result = $this->db->query($query)->result_array();

        return $result;
    }
    public function getAllApprovedEvents()
    {
        $query = "SELECT em.eventId, em.eventName, em.eventDescription, em.eventType, em.eventDate, em.startTime, em.endTime, em.costType, 
                  em.eventPrice, em.priceFreeStuff, em.eventPlace, em.eventCapacity, em.ifMicRequired, em.ifProjectorRequired, 
                  em.creatorName, em.creatorPhone, em.creatorEmail, em.aboutCreator, em.userId, em.eventShareLink,
                  em.eventPaymentLink, em.ifActive, em.ifApproved, ea.filename, l.locName, l.mapLink
                  FROM `eventmaster` em
                  LEFT JOIN eventattachment ea ON ea.eventId = em.eventId
                  LEFT JOIN locationmaster l ON eventPlace = l.id
                  WHERE em.ifActive = ".ACTIVE." AND em.ifApproved = ".EVENT_APPROVED." AND eventDate >= CURRENT_DATE() GROUP BY em.eventId";

        /*$query = "SELECT * FROM eventmaster where ifActive = ".ACTIVE."
         AND eventDate >= CURRENT_DATE()";*/
        $result = $this->db->query($query)->result_array();

        return $result;
    }
    public function getDashboardEventDetails($eventId)
    {
        $query = "SELECT em.eventId, em.eventName, em.costType,em.eventPrice,em.eventShareLink,
                  em.ifActive, em.ifApproved, SUM(erm.quantity) as 'totalQuant'
                  FROM `eventmaster`em
                  LEFT JOIN eventregistermaster erm ON erm.eventId = em.eventId
                  WHERE em.eventId = ".$eventId;

        $result = $this->db->query($query)->result_array();

        return $result;
    }
    public function getJoinersInfo($eventId)
    {
        $query = "SELECT um.firstName, um.lastName, erm.quantity, erm.createdDT
                  FROM eventregistermaster erm
                  LEFT JOIN doolally_usersmaster um ON um.userId = erm.bookerUserId
                  WHERE erm.eventId = $eventId ORDER BY erm.createdDT DESC";

        $result = $this->db->query($query)->result_array();

        return $result;
    }
    public function ApproveEvent($eventId)
    {
        $data['ifActive'] = 1;
        $data['ifApproved'] = 1;

        $this->db->where('eventId', $eventId);
        $this->db->update('eventmaster', $data);
        return true;
    }
    public function DeclineEvent($eventId)
    {
        $data['ifActive'] = 0;
        $data['ifApproved'] = 2;

        $this->db->where('eventId', $eventId);
        $this->db->update('eventmaster', $data);
        return true;
    }
    public function findCompletedEvents()
    {
        $query = "SELECT em.eventId, em.eventName, em.eventDescription, em.eventType, em.eventDate, em.startTime, em.endTime, em.costType, 
                  em.eventPrice, em.priceFreeStuff, em.eventPlace, em.eventCapacity, em.ifMicRequired, em.ifProjectorRequired, 
                  em.creatorName, em.creatorPhone, em.creatorEmail, em.aboutCreator, em.userId, em.eventShareLink,
                  em.eventPaymentLink, em.ifActive, em.ifApproved, ea.filename, l.locName
                  FROM `eventcompletedmaster` em
                  LEFT JOIN eventattachment ea ON ea.eventId = em.eventId
                  LEFT JOIN locationmaster l ON eventPlace = l.id
                  GROUP BY em.eventId";

        $result = $this->db->query($query)->result_array();
        return $result;
    }
    public function checkUserBooked($userId, $eventId)
    {
        $query = "SELECT * FROM eventregistermaster
                  WHERE bookerUserId = ".$userId." AND eventId = ".$eventId;
        $result = $this->db->query($query)->result_array();

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
    public function checkUserCreated($userId, $eventId)
    {
        $query = "SELECT * FROM eventmaster
                  WHERE userId = ".$userId." AND eventId = ".$eventId;
        $result = $this->db->query($query)->result_array();

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
    public function checkEventSpace($details)
    {
        $query = "SELECT * FROM eventmaster
                  WHERE startTime >= '".$details['startTime']."' AND endTime <= '".$details['endTime']."' AND 
                  eventPlace = '".$details['eventPlace']."' AND eventDate = '".$details['eventDate']."'";
        $result = $this->db->query($query)->result_array();

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
    public function activateEventRecord($eventId)
    {
        $data['ifActive'] = 1;

        $this->db->where('eventId', $eventId);
        $this->db->update('eventmaster', $data);
        return true;
    }
    public function deActivateEventRecord($eventId)
    {
        $data['ifActive'] = 0;

        $this->db->where('eventId', $eventId);
        $this->db->update('eventmaster', $data);
        return true;
    }
    public function eventDelete($eventId)
    {
        $this->db->where('eventId', $eventId);
        $this->db->delete('eventmaster');
        return true;
    }
    public function eventRegisDelete($eventId)
    {
        $this->db->where('eventId', $eventId);
        $this->db->delete('eventregistermaster');
        return true;
    }
    public function eventCompDelete($eventId)
    {
        $this->db->where('eventId', $eventId);
        $this->db->delete('eventcompletedmaster');
        return true;
    }
    public function eventAttDeleteById($eventId)
    {
        $this->db->where('eventId', $eventId);
        $this->db->delete('eventattachment');
        return true;
    }
    public function eventAttDelete($attId)
    {
        $this->db->where('id', $attId);
        $this->db->delete('eventattachment');
        return true;
    }
    public function fnbAttDelete($attId)
    {
        $this->db->where('id', $attId);
        $this->db->delete('fnbattachment');
        return true;
    }
    public function getEventAttById($id)
    {
        $query = "SELECT id, filename
                  FROM eventattachment WHERE eventId = ".$id;

        $result = $this->db->query($query)->result_array();

        return $result;
    }

    public function getFullEventInfoById($eventId)
    {
        $query = "SELECT em.eventId, em.eventName, em.eventDescription, em.eventType, em.eventDate, em.startTime, em.endTime, em.costType, 
                  em.eventPrice, em.priceFreeStuff, em.eventPlace, em.eventCapacity, em.ifMicRequired, em.ifProjectorRequired, 
                  em.creatorName, em.creatorPhone, em.creatorEmail, em.aboutCreator, em.userId, em.eventShareLink,
                  em.eventPaymentLink, em.ifActive, em.ifApproved, ea.filename, l.locName, l.mapLink
                  FROM `eventmaster` em
                  LEFT JOIN eventattachment ea ON ea.eventId = em.eventId
                  LEFT JOIN locationmaster l ON eventPlace = l.id
                  WHERE em.eventId = ".$eventId." GROUP BY em.eventId";

        $result = $this->db->query($query)->result_array();

        return $result;
    }
    public function saveEventRegis($details)
    {
        $details['createdDT'] = date('Y-m-d H:i:s');

        $this->db->insert('eventregistermaster', $details);
        return true;
    }

    //For Fnb
    public function activateFnbRecord($fnbId)
    {
        $data['ifActive'] = 1;

        $this->db->where('fnbId', $fnbId);
        $this->db->update('fnbmaster', $data);
        return true;
    }
    public function DeActivateFnbRecord($fnbId)
    {
        $data['ifActive'] = 0;

        $this->db->where('fnbId', $fnbId);
        $this->db->update('fnbmaster', $data);
        return true;
    }
    public function fnbDelete($fnbId)
    {
        $this->db->where('fnbId', $fnbId);
        $this->db->delete('fnbmaster');
        return true;
    }

    public function updateFnbRecord($details, $fnbId)
    {
        $this->db->where('fnbId',$fnbId);
        $this->db->update('fnbmaster', $details);
        return true;
    }

    public function getTapSongs($tapId)
    {
        $query = 'SELECT * 
                  FROM jukeboxmaster
                  WHERE tapId = '.$tapId;

        $result = $this->db->query($query)->result_array();

        return $result;
    }
}
