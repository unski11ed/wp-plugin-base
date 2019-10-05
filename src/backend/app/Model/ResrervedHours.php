<?php
class ReservedHours{
    private $_db;
    public function __construct(){
        global $wpdb;
        $this->_db = $wpdb;
    }
    
    public function GetAllInMonth($month, $year, $roomId){
        $query = $this->_db->prepare("
            SELECT id, hour, day, month, year, isAdmin
            FROM cal_reservations
            WHERE month = %d AND year = %d AND status='active' AND roomId=%d
        ", $month, $year, $roomId);
        
        return $this->_db->get_results($query, ARRAY_A);
    }
    
    public function CheckIfExists($reservation){
        $query = $this->_db->prepare(
                    "SELECT COUNT(*) FROM cal_reservations "
                    . "WHERE status='active' "
                    . "AND day = %d AND month = %d "
                    . "AND year = %d AND hour = %s"
					. "AND roomId = %d"
                , $reservation['iday'], $reservation['imonth']
                , $reservation['iyear'], $reservation['hour'], $reservation['roomId']);
        
        $count = $this->_db->get_var($query);
        
        return $count != 0;
    }
    
    public function Create($reservation, $confirmation_token, $isAdmin = false){
        $result = $this->_db->insert(
            'cal_reservations', 
            array(
				'roomId' => $reservation['roomId'],
                'status' => $isAdmin ? 'active' : 'notconfirmed',
                'confirmation_token' => $confirmation_token,
                
                'day' => $reservation['iday'],
                'month' => $reservation['imonth'],
                'year' => $reservation['iyear'],
                'hour' => $reservation['hour'],
                
                'name' => $reservation['name'],
                'email' => $reservation['email'],
                'phone' => $reservation['phone'],
                
                'isVoucher' => $reservation['isVoucherPayment'],
                'isVAT' => $reservation['isVAT'],
                
                'count' => $reservation['count'],
                
                'isAdmin' => $isAdmin ? '1' : '0'
            )
        );
        return $result;
    }
    
    public function GetReservationDetails($reservationId){
        $query = $this->_db->prepare(
                "SELECT * FROM cal_reservations "
                . "WHERE id = %d", intval($reservationId));
        return $this->_db->get_results($query, ARRAY_A);
    }
    
    public function SetStatus($reservationId, $status){
        $result = $this->_db->update(
                'cal_reservations',
                array(
                    'status' => $status
                ),
                array(
                    'id' => intval($reservationId)
                ),
                array(
                    '%s'
                ),
                array(
                    '%d'
                )
            );
        
        return $result;
    }
    
    public function Delete($reservationId){
        return $this->_db->delete(
                'cal_reservations',
                array(
                    'id' => $reservationId
                ),
                array(
                    '%d'
                )
            );  
    }
    
    public function FindByToken($token){
        $query = $this->_db->prepare(
                "SELECT * FROM cal_reservations "
            .   "WHERE confirmation_token = %s", $token);
        $result =  $this->_db->get_results($query, ARRAY_A);
        
        return empty($result) ? false : $result[0];
    }
}
