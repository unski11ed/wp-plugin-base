<?php

class callendar_email_confirmation extends Shortcode{
    private $_reservations;
    
    public function __construct($urlData, $modelData) {
        parent::__construct($urlData, $modelData);
        
        $this->_reservations = new ReservedHours();
    }
    
    public function Execute(){
        if(!isset($this->UrlParams['token'])){
            $this->UrlParams['token'] = "";
        }
        
        $reservation = $this->_reservations->FindByToken(
                    urldecode($this->UrlParams['token'])
                );
        
        if(!$reservation){
            $output = array(
                'status' => 'error',
                'message' => 'Nie znaleziono rezerwacji, spróbuj złożyć rezerwację jeszcze raz'
            );
        }else{
            if(!$this->_reservations->CheckIfExists(array(
                    'iday' => intval($reservation['day']),
                    'imonth' => intval($reservation['month']),
                    'iyear' => intval($reservation['year']),
                    'hour' => $reservation['hour'],
					'roomId' => $reservation['roomId']
                ))){
                $this->_reservations->SetStatus($reservation['id'], 'active');
                
                $output = array(
                    'status' => 'success',
                    'message' => 'Pomyślnie potwierdzono rezerwację na dzień '
                                        .sprintf("%02d-%02d-%d o godz. %s", 
                                            $reservation['day'],
                                            $reservation['month'],
                                            $reservation['year'],
                                            $reservation['hour'])
                );
            }  else {
                $output = array(
                    'status' => 'error',
                    'message' => 'Inny użytkownik, wcześniej potwierdził rezerwację na ten czas. Zarezerwuj inny termin'
                );
            }
        }
        
        return $this->View("/Shortcodes/email_confirmation", array('state' => $output));
    }
}

