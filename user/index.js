var phrases = require('./ru');

/**
 * sdfsdf
 * @param name
 * @constructor
 */
function User(name) {
    this.name = name;
}
/**
 * aaas
 * @param who
 */
User.prototype.hello = function (who) {
    console.log(phrases.Hello + ", " + who.name);
};
console.log("user module plugged");

exports.User = User;
//global.User = User;