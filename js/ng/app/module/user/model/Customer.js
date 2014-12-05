app.factory('module.user.model.Customer', [
	'module.user.service.resource.RestfulSession'
	, 'module.user.model.User'
	, 'module.place.model.Address'
	, function(
		RestfulSession
		, User
		, Address
	){
		var Customer = function( params ){
			angular.extend(this, User);
			
			angular.extend(this.properties, {
				address_shipping: new Address(),
				address_billing: new Address(),
			});
		};
		
		Customer.prototype = new User();
				
		return Customer;
	}
]);
