<?php
class Callendar extends Controller{
    private $_reservations;
    public function __construct($urlData, $modelData){
        parent::__construct($urlData, $modelData);
        //Model
        $this->_reservations = new ReservedHours();
    }

    public function action__Manage(){
		$availableRooms = CalendarSettings::AvailableRooms();

		if(!isset($this->UrlParams['roomId'])){
			$room = $availableRooms[0];
		}else{
			$roomId = intval($this->UrlParams['roomId']);
			$room = array_filter($availableRooms, function($availableRoom) use($roomId){
				if($availableRoom['id'] == $roomId)
					return true;
			});
			$room = reset($room);
		}

        return $this->View("CallendarController/Manage",
			array(
				'plugin_main' => $this->Url['Main'],
				'availableRooms' => $availableRooms,
				'selectedRoom' => $room
			)
		);
    }

    public function ajax__public__GetUser(){
        $currentDate = new DateTime();

        $month = intval(isset($this->ViewModel['month']) ? $this->ViewModel['month']
                    : $currentDate->format('n'));
        $year = intval(isset($this->ViewModel['year']) ? $this->ViewModel['year']
                    : $currentDate->format('Y'));
        $roomId = intval($this->ViewModel['roomId']);
        $room = reset(array_filter(CalendarSettings::AvailableRooms(), function($element) use ($roomId) {
          return $element['id'] === $roomId;
        }));
        $output = array(
            'imonth' => $month,
            'iyear' => $year,
            'availableHours' => $room['hours'],
            'reservedHours' => $this->_reservations->GetAllInMonth($month, $year, $roomId)
        );

        return json_encode(
                    array(
						'prices' => CalendarSettings::Prices(),
                        'month' => $output
                    )
                );
    }

    public function ajax__public__GetDetails(){
        $reservationDetails = $this->_reservations
                ->GetReservationDetails($this->ViewModel['reservationId']);

        return json_encode(array(
			'prices' => CalendarSettings::Prices(),
            'reservationDetails' => $reservationDetails[0]
        ));
    }

    public function ajax__private__CreateAdmin(){
        if($this->_reservations->CheckIfExists($this->ViewModel)){
            return json_encode(array(
                'status' => 'error',
                'message' => 'Wybrana godzina została już zarezerwowana i potwierdzona, wybierz inny termin.'
            ));
        }

        if($this->_reservations->Create($this->ViewModel, "", true)){
            return json_encode(array(
                'status' => 'success',
                'message' => 'Złożono rezerwację.'
            ));
        }

        return json_encode(array(
            'status' => 'error',
            'message' => 'Błąd bazy danych, spróbuj jeszcze raz. Jeśli problem będzie się powtarzał, skontaktuj się z administratorem.'
        ));
    }

    public function ajax__public__Create(){
        if($this->_reservations->CheckIfExists($this->ViewModel)){
            return json_encode(array(
                'status' => 'error',
                'message' => 'Wybrana godzina została już zarezerwowana i potwierdzona, wybierz inny termin.'
            ));
        }

        $token = $this->createUniqueToken();

        if($this->_reservations->Create($this->ViewModel, $token)){
			//Get registered room name
			$targetRoomId = $this->ViewModel['roomId'];
			$availableRooms = CalendarSettings::AvailableRooms();
			$room = array_filter($availableRooms, function($availableRoom) use($targetRoomId){
				if($availableRoom['id'] == $targetRoomId)
					return true;
			});
			$room = reset($room);
			//Build registration date
            $dateString = sprintf("%02d-%02d-%d o godz. %s",
                                            $this->ViewModel['iday'],
                                            $this->ViewModel['imonth'],
                                            $this->ViewModel['iyear'],
                                            $this->ViewModel['hour']);

            //Send confirmation mail to user
            send_email($this->Path['View'] . "/Emails/confirmation.html",
                   CalendarSettings::FromEmail(),
                   $this->ViewModel['email'],
                   array(
                       'date' => $dateString,
                       'name' => $this->ViewModel['name'],
                       'url' => CalendarSettings::ConfirmationUrlBase() . urlencode($token),
					   'roomName' => $room['name']
				   )
			   );

            //Send admin notification
            send_email($this->Path['View'] . "/Emails/info.html",
                   CalendarSettings::FromEmail(),
                   CalendarSettings::AdminEmail(),
                   array(
                       'date' => $dateString,
                       'name' => $this->ViewModel['name'],
                       'email' => $this->ViewModel['email'],
                       'phone' => $this->ViewModel['phone'],
                       'count' => $this->ViewModel['count'],
                       'isVoucher' => $this->ViewModel['isVoucherPayment'] ? 'Tak' : 'Nie',
                       'isVat' => $this->ViewModel['isVAT'] ? 'Tak' : 'Nie',
					   'roomName' => $room['name']
                ));

            //Return result
            return json_encode(array(
                'status' => 'success',
                'message' => 'Złożono rezerwację, odbierz email i postępuj według dalszych kroków, aby potwierdzić zamówienie.'
            ));
        }

        return json_encode(array(
            'status' => 'error',
            'message' => 'Błąd bazy danych, spróbuj jeszcze raz. Jeśli problem będzie się powtarzał, skontaktuj się z administratorem.'
        ));
    }

    public function ajax__public__Delete(){
        if($this->_reservations->Delete($this->ViewModel['reservationId'])){
            return json_encode(array(
                'status' => 'success',
                'message' => 'Usunięto rezerwację'
            ));
        }
        return json_encode(array(
            'status' => 'error',
            'message' => 'Błąd bazy danych, spróbuj jeszcze raz'
        ));
    }

    public function ajax__public__SetStatus(){
        if($this->_reservations->SetStatus(
                $this->ViewModel['reservationId'],
                $this->ViewModel['status']
                )
           ){
            return json_encode(array(
                'currentStatus' => $this->ViewModel['status']
            ));
        }

        throw new Exception("Błąd bazy danych");
    }

	private function createUniqueToken(){
		do{
			$token = md5(uniqid(mt_rand(), true));
			$found = $this->_reservations->FindByToken($token);
		}while($found !== false);

		return $token;
	}
}
