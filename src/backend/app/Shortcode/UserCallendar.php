<?php

class user_callendar extends Shortcode{
	private $roomId;
	
    public function __construct($urlData, $modelData) {
        parent::__construct($urlData, $modelData);   
		
		$this->roomId = $modelData["room_id"];
    }
    
    public function Execute(){
		$availableRooms = CalendarSettings::AvailableRooms();
		$targetRoomId = $this->roomId;

		$room = array_filter($availableRooms, function($availableRoom) use($targetRoomId){
			if($availableRoom['id'] == $targetRoomId)
				return true;
		});
		
		if(empty($room)){
			throw "Fatal: Nie znaleziono pokoju o określonym id. Czy shortcode posiada właściwe id pokoju?";
		}else{
			$room = reset($room);
		}
		
		
        return $this->View("/Shortcodes/user_callendar", array('plugin_main' => $this->Url['Main'], 'roomId' => $room['id'], 'roomName' => $room['name']));
    }
}

