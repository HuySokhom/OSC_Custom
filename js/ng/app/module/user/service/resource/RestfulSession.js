app.factory('module.user.service.resource.RestfulSession',
[
 	'core.service.resource.Restful'
	, function(
		Restful
	){	
		return new Restful({
			url: 'api/Session'
		});
	}
]);
