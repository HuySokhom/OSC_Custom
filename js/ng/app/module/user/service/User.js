app.factory('module.user.service.User', [
	'AppInitSessionData'
	, 'module.user.model.Customer'
	, function(
		AppInitSessionData
		, Customer
	){
		this.model = new Customer({
			properties: AppInitSessionData.user
		});
		
		this.properties = this.model.properties;
				
		return this;
	}
]);
