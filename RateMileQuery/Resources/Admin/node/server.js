/* 
 * Date: 07/18/2016
 * Author: Joe Olliff
 * Program Name: debug-server
 * Description: This Node Server, send various debug information in real time.
 * Parameters Expected: Program Name (this is the name of the program you want debug information about), 
 						Debug Or Prod (this is a boolean, true for debug false for production)

 */

 var express = require('express');
 var app = express();
 var server = require('http').createServer(app);
 var io = require('socket.io')(server);
 var ss = require('socket.io-stream');
 var fs = require('fs')
 var port = 8888;

 app.set('view engine', 'ejs');

 require('/www/websmart/htdocs/wsphp/RateMileQuery/Resources/Admin/node/app/routes.js')(app, io, ss, fs);

 server.listen(port);
