var idleTime = 0;
var idleInterval;
var logoutInterval;

$(function(){
	//Increment the idle time counter every minute.
    idleInterval = setInterval(timerIncrement, 60000); // 1 minute
    //Zero the idle timer on mouse movement.
    $(document).mousemove(function (e) {
        idleTime = 0;
    });
    $(document).keypress(function (e) {
        idleTime = 0;
    });

	// Click stay signin button
	$(document).on('click', '#stay-signin', function() {
		 $('#logout-warn').modal('hide');
		// Reset idle time
		idleTime = 0;
		// Start timer
		idleInterval = setInterval(timerIncrement, 60000); // 1 minute
		// Reset logout interval
		clearInterval(logoutInterval);
	});
});

function timerIncrement() {
    idleTime = idleTime + 1;
    if (idleTime > 14) { // 15 minutes
        // $('#logout-warn').modal('show');
        $('#logout-warn').modal({backdrop: 'static', keyboard: false});
        clearInterval(idleInterval);
        startTimer();
    }
}

function startTimer() {
	$("#s_timer").countdowntimer({
		seconds : 30,
        size : "lg"
	});
	logoutInterval = setInterval(auto_logout, 30000); // 1 minute
}

function auto_logout() {
	window.location = projectBaseUrl + 'user/logout';
}