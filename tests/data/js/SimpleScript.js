/*
 * This is a test javascript file.
 * Developer by Jaideep
 * */
function alertUser(var mode) {
	var message = "No Parameter passed";
	
	// Check the mode parameter and prepare the message to alert
	switch (mode) {
		case 1:
			message = "You have passed 1 parameter";
			break;
		case 2:
			message = "You have passed 2 parameters";
			break;
	}
	alert(mode);
	alert(message); // alert the user with the updated message
};

var EmployeeClass = {
	
	this._employeeId;
	this._firstName = "";
	this._lastName = "";
	
	this.toString = function() {
		return this._employeeId . "~" . this._firstName . "~" . this._lastName;
	};
	
	return this;
};