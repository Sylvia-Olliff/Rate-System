function logConnect(program) {
	var config = io.connect("as400.rogers-brown.com:8888/config"); 	

	config.on('connect', function(){
		$("#loggerStart").on('click', function(){
			config.emit('start', {name: program});
		});

		$("#loggerStop").on('click', function(){
			config.emit('stop', {name: program});
		});

		$("#loggerClear").on('click', function(){
			config.emit('clear', {name: program});
		});
				
	});

	config.on('starting', function(){
		//This is only here as a placeholder in case I want to do something once logging is started.
	});

	config.on('log', function(data){
		$('#debugInfo').html("<pre class='scrollable'>" + data.debug + "</pre>");
	});

	$('#OUT').on('click', function(){
		config.disconnect();
	});

	$('#btn-back').on('click', function(){
		config.disconnect();
	});

	config.io.on('connect_error', function(err){
		config.disconnect();
	});
}