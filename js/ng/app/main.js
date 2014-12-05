var app = angular.module(
	'main',
	[
	 	'ngRoute'
//	 	, 'associated_products.services'
		, 'ui.bootstrap'
//		, 'ui.bootstrap.tpls'
		, 'ngDropdowns' // "angular-dropdowns"
	]
)

	// load session data
	// - we do this from raw json for performance reasons..
	.service('AppInitSessionData', function(){
		return AppInitSessionData;
	})	
;
