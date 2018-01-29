var http = require('http');
var express = require('express');
var app = express();
var fs = require("fs");
const Console = require('console').Console;
var data = require('./webdata.js');

function isEmpty(obj) {
	for (var key in obj) {
    	return false;
  	}
  	return true;
}

//ограничить выборку по количеству элементов
function getEventsLimitLength(events, offset, limit) {
	if (!events) return null;
	if ((offset >= 0) && (limit > 0))
		return events.slice(offset-1, offset+limit-1);
	return events;
}

//ограничить выборку по пользователю
function getEventsByUser(events, user) {
	if (!events) return null;

	var arr = events.filter(function(item) {

		var authors = item.author.filter(function(author) {
			return author === user;
		});
		return authors[0];
	});
	return arr;
}

//ограничить выборку по категории
function getEventsByCategory(events, category) {
	if (!events) return null;

	var arr = events.filter(function(item) {

		return item.category === category;
	});
	return arr;
}

//http://localhost:8082/api/events?category=A1&user=John%20Smith&offset=1&limit=3
app.get('/api/events', function (req,res){
	
	var arr = data.EVENTS;

	if (!isEmpty(req.query)) {

		var category = req.query.category;
		var user = req.query.user;
		var offset = Number(req.query.offset);
		var limit = Number(req.query.limit);

		console.log('category: ',category);
		console.log('user: ',user);
		console.log('offset: ',offset);
		console.log('limit: ',limit);
	
		if (category){
			console.log('--category');
			arr = getEventsByCategory(arr, category);
		}
		if (user){
			console.log('--user');
			arr = getEventsByUser(arr, user);
		}

		if ((offset > 0) && (limit>0)) {
			console.log('--offset');
			arr = getEventsLimitLength(arr, offset, limit);
		}

	}	

	var str = JSON.stringify(arr);
	
	res.writeHead(200, {
		'Content-Type':'application/json; charset=utf-8',
		'Access-Control-Allow-Origin': '*'
	});
	res.end( str );
});

app.get('/api/category', function (req,res){

    var str = JSON.stringify(data.CATEGORY);

    res.writeHead(200, {
        'Content-Type':'application/json; charset=utf-8',
        'Access-Control-Allow-Origin': '*'
    });
    res.end( str );
});

app.get('/api/user', function (req,res){

    var str = JSON.stringify(data.USER);

    res.writeHead(200, {
        'Content-Type':'application/json; charset=utf-8',
        'Access-Control-Allow-Origin': '*'
    });
    res.end( str );
});

/*
const out = fs.createWriteStream('./stdout.log');
const err = fs.createWriteStream('./stderr.log');
// custom simple logger
const logger = new Console(out, err);
// use it like console
const count = 5;
logger.log('count: %d', count);
// in stdout.log: count 5
*/

app.listen(8082);


