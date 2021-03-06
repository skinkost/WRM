<script>
	/* global $ */
	/* global moment */
	
	function Users(id,name) {
		this.id = id;
		this.name = name;
	}
	
	
	var selectedUsers = [];
	var valueArray = [];
	var x = 0;
	
	<?php
	foreach ($gUsers as $gu) {
	?>
	selectedUsers[x] = new Users(<?php echo $gu['ID'] ?>,"<?php echo $gu['name'] ?>");
	x=x+1;
	<?php } ?>
	
	$(document).ready(function() {
		
		//console.log(selectedUsers);
		
		// Create draggable external events with the help of JQ UI
		
		$('.fc-event.external').each(function() {
			$(this).draggable({
				zIndex: 999,
				revert: true,
				revertDuration: 0
			});
		});
		
		// Initialize calendar
		
		$('#calendar').fullCalendar({
			editable: true,
			schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
			refetchResourcesOnNavigate: false,
			resourceLabelText: 'Konsult',
			resourceAreaWidth: '20%',
			slotWidth: '75',
			businessHours: true,
			droppable: true,
			allDayDefault: true,
			defaultView: 'timelineMonth',
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'timelineMonth,timelineWeek'
			},
			views: {
				timelineWeek: {
					slotDuration: '24:00:00'
				}
			},
			resources: function(callback) {
				var res = [];
				var y = 0;
				
				if ($('[name=users]').val() == "") {
					valueArray = [];
				} else {
					valueArray = $('[name=users]').val().split(',');
				}
				
				if (valueArray.length > 0 || valueArray[0] != null) {
					for (var i=0;i<selectedUsers.length;i++) {
						if ($.inArray(selectedUsers[i].id.toString(), valueArray) !== -1) {
							res[y] = {id: selectedUsers[i].id.toString(), title: selectedUsers[i].name};
							y++;
						}
					}
				} else {
					for (var i=0;i<selectedUsers.length;i++) {
						res[i] = {id: selectedUsers[i].id.toString(), title: selectedUsers[i].name};
					}
				}
				callback(res);
			},
			contentHeight: 'auto',
			weekNumbers: true,
			eventLimit: false,
			events: {
				url: 'json-events-feed.php',
				type: 'POST', // Send post data
				data: function() {
					return {users: $('[name=users]').val()};
				},
				error: function(data) {
					//console.log(data.responseText);
					alert('There was an error while fetching events.');
				}
			},
			eventClick: function(calEvent, jsEvent, view) {
				$('#modalmessage').text(calEvent.date + " - " + calEvent.title);
				$('.ui.basic.modal').modal({
					onDeny : function(){
						
					},
					onApprove : function() {
						$.post("save-to-db.php", { 
						type: 'event_remove',
						user: '<?php echo $login_id ?>',
						id: calEvent.id
						}).done(function() {
							$('#calendar').fullCalendar( 'removeEvents' );
							$('#calendar').fullCalendar( 'refetchEvents');
						});
					}
				}).modal('show');
				
				/*
				$('[name=Muser]').val('<?php echo $gUsers[2]['name']; ?>');
				$('[name=Mcustomer]').val('<?php echo $gCustomers[1]['name']; ?>');
				$('[name=Mhours]').val(calEvent.hours);
				$('[name=Mdate]').val(calEvent.date);
				
				$('#eventinfo').modal({
					onDeny : function(){
						
					},
					onApprove : function() {
						$.post("save-to-db.php", { 
						type: 'event_update',
						user: <?php echo $login_id ?>,
						id: calEvent.id
						}).done(function() {
							$('#calendar').fullCalendar( 'removeEvents' );
							$('#calendar').fullCalendar( 'refetchEvents');
						});
					}
				}).modal('show');
				*/
				//console.log($('[name=users]').val());
				
			},
			eventReceive: function(event, date) {
				$.post("save-to-db.php", { 
					type: 'event_add',
					user: event.user,
					date: event.start.format(),
					customer: event.customer,
					hours: event.hours })
					.done(function( data ) {
						$('#calendar').fullCalendar( 'removeEvents' );
						$('#calendar').fullCalendar( 'refetchEvents');
					});
			},
			eventResize: function(event, delta, revertFunc) {
				var nrofDays = (delta/3600/24000); //1
				
				var dateF = moment(event.end);
				
				for (var x=0;x<nrofDays;x++) {
					$.post("save-to-db.php", {type: 'event_add',user: event.user,date: dateF.subtract(1, 'days').format(),customer: event.customer,	hours: event.hours });
				}
				
				$('#calendar').fullCalendar( 'removeEvents' );
				$('#calendar').fullCalendar( 'refetchEvents');
				
			},
			eventDrop: function(event, delta, revertFunc) {
				$.post("save-to-db.php", { 
					type: 'event_move',
					id: event.id,
					user: '<?php echo $login_id ?>',
					date: event.start.format(),
					});	
			},
			loading: function( isLoading, view ) {
				if (isLoading) {
					$(document.body).css('cursor', 'wait');
				} else {
					$(document.body).css('cursor', 'default');
				}
				
			}
		});
		
		$('[name=customer],[name=hours]').change(function () {
			$('.external').text('<?php echo strtoupper($login_session) ?>' + " : " + $('[name=customer] option:selected').text() + " : " + $('[name=hours]').val());
			
			$('.fc-event.external').each(function() {
				$(this).data('event', {
					title: $.trim($(this).text()),
					hours: $('[name=hours]').val(),
					customer: $('[name=customer]').val(),
					user: $('[name=hours]').attr("data-uid"),
					color: "<?php echo $login_color ?>",
					stick: true
				});
			});
		});
		
		
		$('[name=userAdmin]').change(function () {
			$('.external').text($('[name=userAdmin]').val() + " : " + $('[name=customer] option:selected').text() + " : " + $('[name=hours]').val());
			
			$('.fc-event.external').each(function() {
				$(this).data('event', {
					title: $.trim($(this).text()),
					hours: $('[name=hours]').val(),
					customer: $('[name=customer]').val(),
					user: $('[name=userAdmin]').val(),
					color: $('[name=userAdmin]').attr("data-color"),
					stick: true
				});
				$(this).css("background-color", $('[name=userAdmin] option:selected').attr("data-color"));
			});
		});
		
		
		$('[name=users]').change(function () {
			if ($('[name=users]').val() == "") {
				valueArray = [];
			} else {
				valueArray = $('[name=users]').val().split(',');
			}
			
			$('#calendar').fullCalendar( 'refetchEvents' );
			$('#calendar').fullCalendar( 'refetchResources' );
		});
		
	});
</script>

<div class="ui grid">
	<div class="eight wide column">
		<h4>Skapa:</h4>
		
		
		<select class="ui search dropdown" name="customer">
			<option value="">Kund</option>
			<?php
				foreach ($gCustomers as $d) {
					echo "<option value='" . $d['ID'] . "'>" . $d['name'] . "</option>";
				}
			?>
		</select>
		<select class="ui compact selection dropdown" data-uid="<?php echo $login_id; ?>" name="hours">
			<option value="">Timmar</option>
			<option value=1>1h</option>
			<option value=2>2h</option>
			<option value=3>3h</option>
			<option value=4>4h</option>
			<option value=5>5h</option>
			<option value=6>6h</option>
			<option value=7>7h</option>
			<option value=8>8h</option>
		</select>
		<div id='draoslapp' class='ui fc-event external fc-draggable' style='background-color: <?php echo $login_color ?>; border-color: <?php echo $login_color ?>;'><--- Kund och timmar</div>
	</div>
	<div class="eight wide column">
		<h4>Filtrera:</h4>
		<div class="ui multiple selection dropdown">
			<input name="users" type="hidden">
			<i class="dropdown icon"></i>
			<div class="default text" data-value=''>Alla Användare</div>
			<div class="menu">
				<?php
					foreach ($gUsers as $u) {
						echo "<div class='item' data-value='" . $u['ID'] . "'>" . strtoupper($u['username']) . "</div>";
					}
				?>
			</div>
		</div>
	</div>
</div>
<br/>

<div class="sixteen wide column">
	<div id='calendar'></div>
</div>

<div class="ui basic modal">
  <div class="ui icon header">
    <i class="trash icon"></i>
    Ta bort
  </div>
  <div class="content">
    <p>Vill du ta bort dessa timmar?</p>
	<p id="modalmessage"></p>
  </div>
  <div class="actions">
    <div id="no" class="ui red basic cancel inverted button">
      <i class="remove icon"></i>
      Nej
    </div>
    <div id="yes" class="ui green ok inverted button">
      <i class="checkmark icon"></i>
      Ja
    </div>
  </div>
</div>

<div class="ui modal" id="eventinfo">
	<i class="close icon"></i>
	<div class="header">
		Info
	</div>
	<div class="content">
		<div class="ui form">
			<div class="field">
				<label>Användare</label>
				<input type="text" name="Muser">
			</div>
			<div class="field">
				<label>Kund</label>
				<input type="text" name="Mcustomer">
			</div>
			<div class="field">
				<label>Timmar</label>
				<input type="text" name="Mhours">
			</div>
			<div class="field">
				<label>Datum</label>
				<input type="text" name="Mdate">
			</div>
		</div>
	</div>
	<div class="actions">
		<div class="ui black deny button">
			Stäng
		</div>
		<div class="ui positive right labeled icon button">
			Spara
			<i class="checkmark icon"></i>
		</div>
	</div>
</div>