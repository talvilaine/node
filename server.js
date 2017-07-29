var userModule = require('./user');
var userPhrases = require('./user/ru');
var _ = require('underscore');

var fUser = new userModule.User("Василий");
var sUser = new userModule.User("Иннокентий");

fUser.hello(sUser);
console.log(userPhrases.Hello);
var stooges = [{name: 'moe', age: 40}, {name: 'larry', age: 50}, {name: 'curly', age: 60}];
var maxAge = _.max(stooges, function(stooge){ return stooge.age; });

console.log(maxAge);