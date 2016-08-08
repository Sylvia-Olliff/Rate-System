$(document).ready(function() {
	const PATH_ 		 = "/wsphp/RateMileQuery/Resources/Admin/";
	const BUILD_DEV_PATH = "/wsphp/RateMileQuery/Dev/TruckRateBuilder_DEV/";
	const QUERY_DEV_PATH = "/wsphp/RateMileQuery/Dev/TruckRateViewer_DEV/";
	const BUILD_PRO_PATH = "/wsphp/RateMileQuery/Prod/TruckRateBuilder/";
	const QUERY_PRO_PATH = "/wsphp/RateMileQuery/Prod/TruckRateViewer/";

	var serverUp = false;

	$.ajaxSetup({
		cache: false
	});


	$('#DEV').hide();
	$('#PRO').hide();
	$('#OUT').hide();
	$('#nodeServer').hide();

	var socket = io.connect("as400.rogers-brown.com:8888/test");

	socket.on('connect', function(){
		$('#nodeServer').html("<h3 class='bg-success'>Debug Server is Running</h3>");
		socket.disconnect();
	});

	socket.io.on('connect_error', function(err){
		$('#nodeServer').html("<h3 class='bg-warning'>Debug Server not Running</h3>");
		socket.disconnect();
	});

	var errorDialog = new BootstrapDialog({
		title : 'Error',
		cssClass : '',
		type: BootstrapDialog.TYPE_WARNING,
		message : ""
	});


	$('#IN').on('click', function(event){
		event.preventDefault();
		var loginData = $('#username').val() + ',' + $('#password').val();
		login(loginData);
	});

	$('#OUT').on('click', function(event){
		event.preventDefault();
		$('#DEV').hide();
		$('#PRO').hide();
		$('#OUT').hide();
		$('#nodeServer').hide();
		$('#interface').html("");
		$('#IN').show();
		$('#username').show();
		$('#password').show();
	});

	$('#dev-env').on('click', function(){
		$('#DEV').hide();
		$('#PRO').hide();
		$('#interface').load(PATH_ + "views/Dev-Main.html", function(){
			$('#btn-back').on('click', function(){
				$('#DEV').show();
				$('#PRO').show();
				$('#interface').html("");
			});

			$('#rateBuilderDev').on('click', function(){
				buildSettings("views/Builder-Control-Dev.html", BUILD_DEV_PATH, errorDialog);
			});

			$('#rateQueryDev').on('click', function(){
				querySettings("views/Query-Control-Dev.html", QUERY_DEV_PATH, errorDialog);
			});
		});
	});

	$('#pro-env').on('click', function(){
		$('#DEV').hide();
		$('#PRO').hide();
		$('#interface').load(PATH_ + "views/Pro-Main.html", function(){
			$('#btn-back').on('click', function(){
				$('#DEV').show();
				$('#PRO').show();
				$('#interface').html("");
			});

			$('#rateBuilderPro').on('click', function(){
				buildSettings("views/Builder-Control-Pro.html", BUILD_PRO_PATH, errorDialog);
			});

			$('#rateQueryPro').on('click', function(){
				querySettings("views/Query-Control-Pro.html", QUERY_PRO_PATH, errorDialog);
			});
		});
	});

	function login(data) {
		$.ajax({
			type: "POST",
			dataType: "HTML",
			url: PATH_ + "php/login.php",
			data: {loginData:data},
			cache: false,
			success: function(response) {
				if (response == "SUCCESS") {
					$('#DEV').show();
					$('#PRO').show();
					$('#OUT').show();
					$('#nodeServer').show();
					$('#IN').hide();
					$('#username').hide();
					$('#password').hide();
				} else if (response == "ACCESS") {
					//display pop up for access denied
					errorDialog.setTitle("ERROR!");
					errorDialog.setType(BootstrapDialog.TYPE_DANGER);
					errorDialog.setMessage("<p>You are not authorized to access this portal...</p>");
					errorDialog.open();
				} else if (response == "INVALID") {
					//display pop up for invalid username or password
					errorDialog.setTitle("ERROR!");
					errorDialog.setType(BootstrapDialog.TYPE_WARNING);
					errorDialog.setMessage("<p>Invalid Username or Password...</p>");
					errorDialog.open();
				} else {
					console.log(response);
				}
			},
			error: function(jqXHR, status, err) {
				console.log("Error");
				console.log(status, err);	
			}
		});
	}

	function parseINIString(data){
	    var regex = {
	        section: /^\s*\[\s*([^\]]*)\s*\]\s*$/,
	        param: /^\s*([\w\.\-\_]+)\s*=\s*(.*?)\s*$/,
	        comment: /^\s*;.*$/
	    };
	    var value = {};
	    var lines = data.split(/\r\n|\r|\n/);
	    var section = null;
	    lines.forEach(function(line){
	        if(regex.comment.test(line)){
	            return;
	        }else if(regex.param.test(line)){
	            var match = line.match(regex.param);
	            if(section){
	                value[section][match[1]] = match[2];
	            }else{
	                value[match[1]] = match[2];
	            }
	        }else if(regex.section.test(line)){
	            var match = line.match(regex.section);
	            value[match[1]] = {};
	            section = match[1];
	        }else if(line.length == 0 && section){
	            section = null;
	        };
	    });
	    return value;
	}


	function buildSettings(page, config, errorDialog) {
		$('#interface').html("");
				$('#interface').load(PATH_ + page, function() {
					$.get(config + "config.ini", function(data){
						var settings = parseINIString(data);
						var d_all = settings.DEBUG_ALL;
						var d_form = settings.DEBUG_FORM;
						var d_proc = settings.DEBUG_PROC;
						var d_input = settings.DEBUG_INPUT;
						var d_res = settings.DEBUG_RESPONSE;
						var d_precs = settings.DEBUG_PRECS;
						var display = settings.DISPLAY;

						$("#d-all").html(d_all);
						$("#d-form").html(d_form);
						$("#d-proc").html(d_proc);
						$("#d-input").html(d_input);
						$("#d-res").html(d_res);
						$("#d-precs").html(d_precs);
						$("#display").html(display);

						//Back Button
						$("#btn-back").on('click', function() {
							$('#DEV').show();
							$('#PRO').show();
							$('#interface').html("");
						});

						$("#d-all").on('click', function(){
							if (d_all == "false") {
								d_all = "true";
								$("#d-all").html(d_all);
							} else {
								d_all = "false";
								$("#d-all").html(d_all);
							}
						});
						$("#d-form").on('click', function(){
							if (d_form == "false") {
								d_form = "true";
								$("#d-form").html(d_form);
							} else {
								d_form = "false";
								$("#d-form").html(d_form);
							}
						});
						$("#d-proc").on('click', function(){
							if (d_proc == "false") {
								d_proc = "true";
								$("#d-proc").html(d_proc);
							} else {
								d_proc = "false";
								$("#d-proc").html(d_proc);
							}
						});
						$("#d-input").on('click', function(){
							if (d_input == "false") {
								d_input = "true";
								$("#d-input").html(d_input);
							} else {
								d_input = "false";
								$("#d-input").html(d_input);
							}
						});
						$("#d-res").on('click', function(){
							if (d_res == "false") {
								d_res = "true";
								$("#d-res").html(d_res);
							} else {
								d_res = "false";
								$("#d-res").html(d_res);
							}
						});
						$("#d-precs").on('click', function(){
							if (d_precs == "false") {
								d_precs = "true";
								$("#d-precs").html(d_precs);
							} else {
								d_precs = "false";
								$("#d-precs").html(d_precs);
							}
						});
						$("#display").on('click', function(){
							if (display == "false") {
								display = "true";
								$("#display").html(display);
							} else {
								display = "false";
								$("#display").html(display);
							}
						});

						$('#update').on('click', function(){
							var configData = {
								PATH : ";" + config,
								DEBUG_ALL: d_all,
								DEBUG_FORM: d_form,
								DEBUG_RESPONSE: d_res,
								DEBUG_INPUT: d_input,
								DEBUG_PRECS: d_precs,
								DEBUG_PROC: d_proc,
								DISPLAY: display
							}

							$.ajax({
					       		type:"POST",
					       		dataType: "HTML", 
					       		url: PATH_ + "php/settingsEditor.php", 
					       		data: {configData:configData}, 
					       		cache: false,
					       		success: function(data) 
					      	 	{
					      	 		errorDialog.setTitle("SUCCESS!");
					      	 		errorDialog.setType(BootstrapDialog.TYPE_SUCCESS);
									errorDialog.setMessage("<p>Config file successfully updated</p>");
									errorDialog.open();
								
								},
								error: function(jqXHR, textStatus, errorThrown)
								{
									console.log("Error");
									console.log(textStatus, errorThrown);	
								}
							});

						});
					});
				});
	}

	function querySettings(page, config, errorDialog) {
		$('#interface').html("");
				$('#interface').load(PATH_ + page, function() {
					$.get(config + "config.ini", function(data){
						var settings = parseINIString(data);
						var d_all = settings.DEBUG_ALL;
						var d_query = settings.DEBUG_QUERY;
						var d_precs = settings.DEBUG_PRECS;
						var d_output = settings.DEBUG_OUTPUT
						var display = settings.DISPLAY;

						$("#d-all").html(d_all);
						$("#d-query").html(d_query);
						$("#d-precs").html(d_precs);
						$("#d-output").html(d_output);
						$("#display").html(display);

						//Back Button
						$("#btn-back").on('click', function() {
							$('#DEV').show();
							$('#PRO').show();
							$('#interface').html("");
						});

						$("#d-all").on('click', function(){
							if (d_all == "false") {
								d_all = "true";
								$("#d-all").html(d_all);
							} else {
								d_all = "false";
								$("#d-all").html(d_all);
							}
						});
						$("#d-query").on('click', function(){
							if (d_query == "false") {
								d_query = "true";
								$("#d-query").html(d_query);
							} else {
								d_query = "false";
								$("#d-query").html(d_query);
							}
						});
						$("#d-precs").on('click', function(){
							if (d_precs == "false") {
								d_precs = "true";
								$("#d-precs").html(d_precs);
							} else {
								d_precs = "false";
								$("#d-precs").html(d_precs);
							}
						});
						$("#d-output").on('click', function(){
							if (d_output == "false") {
								d_output = "true";
								$("#d-output").html(d_output);
							} else {
								d_output = "false";
								$("#d-output").html(d_output);
							}
						});
						$("#display").on('click', function(){
							if (display == "false") {
								display = "true";
								$("#display").html(display);
							} else {
								display = "false";
								$("#display").html(display);
							}
						});

						$('#update').on('click', function(){
							var configData = {
								PATH : ";" + config,
								DEBUG_ALL: d_all,
								DEBUG_QUERY: d_query,
								DEBUG_PRECS: d_precs,
								DEBUG_OUTPUT: d_output,
								DISPLAY: display
							}

							$.ajax({
					       		type:"POST",
					       		dataType: "HTML", 
					       		url: PATH_ + "php/settingsEditor.php", 
					       		data: {configData:configData}, 
					       		cache: false,
					       		success: function(data) 
					      	 	{
					      	 		errorDialog.setTitle("SUCCESS!");
									errorDialog.setType(BootstrapDialog.TYPE_SUCCESS);
									errorDialog.setMessage("<p>Config file successfully updated</p>");
									errorDialog.open();
								
								},
								error: function(jqXHR, textStatus, errorThrown)
								{
									console.log("Error");
									console.log(textStatus, errorThrown);	
								}
							});

						});
					});
				});
	}
	
});
