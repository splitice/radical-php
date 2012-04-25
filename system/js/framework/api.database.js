define(['framework/api'],function(API							){
	function DatabaseAPI (table) {
		this.table  = table;
	}
	DatabaseAPI.prototype = new API('Database','*');
	
	DatabaseAPI.prototype._Request = DatabaseAPI.prototype.Request;
	DatabaseAPI.prototype.Request = function(method,args){
		if(typeof args == 'undefined'){
			args = method;
			method = this.method;
		}
		
		this.method = method;
		args['class'] = this.table;
		this._onError = Main.API.onError;
		return this._Request(args);
	}
	DatabaseAPI.prototype.Insert = function(data,callback){
		this.onComplete(callback);
		return this.Request('Insert',{data:data});
		this.onComplete(function(){});
	};
	DatabaseAPI.prototype.Update = function(where,set,callback){
		this.onComplete(callback);
		return this.Request('Update',{where:where,set:set});
		this.onComplete(function(){});
	};
	DatabaseAPI.prototype.Delete = function(where,callback){
		this.onComplete(callback);
		return this.Request('Delete',{where:where});
		this.onComplete(function(){});
	};
	DatabaseAPI.prototype.Select = function(where,callback){
		this.onComplete(callback);
		return this.Request('Select',{where:where});
		this.onComplete(function(){});
	};
	return DatabaseAPI;
});