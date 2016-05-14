app.factory('core.service.resource.Restful', [
 	'$rootScope'
	, function(
		$rootScope
	){		
		var Restful = function( params ){
			this.url = params.url;
		};
		
		Restful.prototype.request = function( request_type, params ){
			var self = this;
			
			params.data = (
				(
					typeof(params.data) === 'object'
						||
					typeof(params.data) === 'string'
				)
					?
				params.data
					:
				{}
			);
			
			return $.ajax({
				method: request_type,
				url: self.url + (
					typeof(params.id) === 'number'
						?
					'/' + params.id
						:
					''
				),
				data: params.data
			})
				.success(function(data){
					$rootScope.$apply(function(){
						if( typeof(params.success) === 'function' ){
							params.success(data);
						}
					});
				})
				.error(function(xhr){
					console.log(xhr);
				})
			;
		};
		
		// convenience methods
		Restful.prototype.get = function( params ){
 			return this.request('GET', params);
 		};
 		
 		Restful.prototype.post = function( params ){
 			return this.request('POST', params);
 		};
 		
 		Restful.prototype.patch = function( params ){
 			// need to stringify for proper parsing..
 			if( 
 				typeof(params) === 'object'
 					&&
 				typeof(params.data) === 'object'
 			){
 				params.data = JSON.stringify(params.data);
 			}	
 			
 			return this.request('PATCH', params);
 		};
 		
 		Restful.prototype.put = function( params ){
 			// need to stringify for proper parsing..
 			if( 
 				typeof(params) === 'object'
 					&&
 				typeof(params.data) === 'object'
 			){
 				params.data = JSON.stringify(params.data);
 			}		
 			
 			return this.request('PUT', params);
 		};
 		
 		// reserved keyword..
 		Restful.prototype['delete'] = function( params ){
 			// need to stringify for proper parsing..
 			if( 
 				typeof(params) === 'object'
 					&&
 				typeof(params.data) === 'object'
 			){
 				params.data = JSON.stringify(params.data);
 			}	
 			
 			return this.request('DELETE', params);
 		};
 		
 			
 		return Restful;
	}
]);
