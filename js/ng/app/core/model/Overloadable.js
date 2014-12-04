app.factory('core.model.Overloadable', [
	function(){
		var Overloadable = function( params ){
			this.properties = {};
		};
		
		Overloadable.prototype.setProperties = function( params ){
			if( 
				typeof(params) !== 'undefined' 
					&&
				typeof(params.properties !== 'undefined')
			){
				angular.foreach(params.properties,function(v,k){
					this.properties[k] = v;
				});
			}
		};
				
		return Overloadable;
	}
]);
