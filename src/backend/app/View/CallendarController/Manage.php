<script>
    var CALLENDAR_MAIN_URL = '<?php echo $VIEWBAG['plugin_main']; ?>',
		CALLENDAR_RESERVATION_ROOM_ID = <?php echo $VIEWBAG['selectedRoom']['id']; ?>,
		CALLENDAR_RESERVATION_ROOM_NAME = '<?php echo $VIEWBAG['selectedRoom']['name']; ?>';
</script>

<ul class="callendar-selected-room">
	<?php foreach($VIEWBAG['availableRooms'] as $availableRoom): ?>
		<li>
			<a 
				href="admin.php?page=kalendarz&roomId=<?php echo $availableRoom['id'] ?>" 
				<?php if($availableRoom['id'] == $VIEWBAG['selectedRoom']['id']) echo "class='active'" ?>
			>
				<?php echo $availableRoom['name'] ?>
			</a>
		</li>
	<?php endforeach; ?>
</ul>

<div class="callendar-main" ng-app="callendarApp" ng-controller="mainController as mainCtrl">
    <div class="control-panel">
        <span type="button" ng-click="prevMonth()">< {{control.prevMonth}}</span><span>{{control.currentMonth}}</span><span type="button" ng-click="nextMonth()">{{control.nextMonth}} ></span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <td>Poniedziałek</td>
                    <td>Wtorek</td>
                    <td>Środa</td>
                    <td>Czwartek</td>
                    <td>Piątek</td>
                    <td>Sobota</td>
                    <td>Niedziela</td>
                </tr>
            </thead>
            <tbody>
                <tr ng-repeat="week in weeks">
                    <td ng-class="{notactive : day.day < 1}" ng-repeat="day in week">
                        <div ng-show="day.day >= 1">
                            <h3>{{day.day}}</h3>
                            <div class="hours" ng-controller="actionController">
                                <span ng-repeat="hour in day.hours" 
                                      ng-class="{unavailable: !hour.available, admin: hour.isAdmin}"
                                      ng-click="(!hour.available && viewDetails(hour.reservationId))
                                                || (hour.available && reservation(year, month, day.day, hour, prices, true))">
                                    {{hour.value}}
                                </span>
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="loading" ng-show="status === 'loading'">
            <div class="loader"></div>
        </div>
    </div>
</div>
