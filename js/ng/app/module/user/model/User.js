app.factory('module.user.model.User', [
	'core.model.Overloadable'
	, function(
		Overloadable	
	){
		var User = function( params ){
			this.properties = {
				name_first: null,
				name_last: null,
				email: null,
			};
		};
		
		User.prototype = new Overloadable();
				
		return User;
	}
]);
