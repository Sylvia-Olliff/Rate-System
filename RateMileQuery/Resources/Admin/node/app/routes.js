//debug-server routes.js

module.exports = function(app, io, ss, fs) {

	var programs = require("./models/programs");
	var previousRead = "";

	var testResponse = io
		.of('/test')
		.on('connection', function(socket){
			console.log("Test received");
			testResponse.emit('running');
		});

	var debugInfo = io
		.of('/config')
		.on('connection', function(socket){
			console.log("User connected");

			socket.on('start', function(pgm){
				console.log("Received start command");
				debugInfo.emit('starting');

				if (programs.list[pgm.name].active) {
					console.log("Program is active");
					checkDebug();
					socket.on('stop', function(){
						console.log("Stop command sent");
						clearInterval(checkDebugInterval);
						previousRead = "";
					});

					socket.on('disconnect', function(){
						console.log("User Disconnected");
						clearInterval(checkDebugInterval);
						previousRead = "";
					});

					socket.on('clear', function(){
						console.log("clear file message received");
						fs.writeFile(programs.directory + programs.list[pgm.name].debug, '', 'utf8', function(){});
					});
					
					var checkDebugInterval = setInterval(checkDebug, 5000);
					

				} else {
					debugInfo.emit('inactive');
				}

				function checkDebug(){
					console.log("Reading file");
					fs.readFile(programs.directory + programs.list[pgm.name].debug, 'utf8', function(err, data){
						if (err) throw err;
										
						if (previousRead != data) {
							previousRead = data;
							debugInfo.emit('log', {debug: data});
						}
					});
				}
			});
		});

		
}