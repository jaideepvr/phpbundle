/*
 * This is a test javascript file 2.
 * Developer by Jaideep
 * */

var ManagerClass = {
	
	this._managerId;
	this._managerFirstName = "";
	this._astName = "";
	
	this.toString = function() {
		return this._managerId . "~" . this._managerFirstName . "~" . this._managerLastName;
	};
	
	return this;
};

var manager = new ManagerClass();
alert(manager.toString());