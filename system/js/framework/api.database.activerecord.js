define(['framework/api.database'],function(DatabaseAPI){
	function ActiveRecord(table,data,id){//extends DatabaseAPI
		if (this.constructor != ActiveRecord){
			return new ActiveRecord(table,data,id);
		}
		
		this._table = table;
		this._id = id;
		if(typeof data != 'undefined'){
			//console.log(this);
			//data = {block_id:"2",chemical_id:"1",spray_date:"2012-02-11",spray_id:"31",spray_quantity:"1"};
			$.extend(true,data,data);
			for(var i in data){
				this[i] = data[i];
			}
		}
	}
	ActiveRecord.prototype._getData = function(){
		var ret = {};
		for(var i in this){
			if(typeof this[i] != 'function'){
				if(i.substr(0,1) != '_'){
					ret[i] = this[i];
				}
			}
		}
		return ret;
	}
	ActiveRecord.prototype.getId = function(){
		return this._id;
	};
	ActiveRecord.prototype._ins = function(){
		return new DatabaseAPI(this._table);
	}
	ActiveRecord.prototype.Insert = function(callback){
		this._ins().Insert(this._getData(),callback);
	};
	ActiveRecord.prototype.Update = function(callback){
		this._ins().Update(this.getId(),this._getData(),callback);
	};
	ActiveRecord.prototype.Delete = function(callback){
		this._ins().Delete(this.getId(),callback);
	};
	ActiveRecord.prototype.Select = function(data,callback){
		this._ins().Select(data,function(t){return function(d){
			var ret = [];
			for(var i in d){
				if(typeof d[i] == 'object'){
					ret.push(new ActiveRecord(t._table,d[i].data,d[i].id));
				}
			}
			callback(ret);
		}}(this));	
	};
	ActiveRecord.prototype.bind = function(form){
		$(form).submit(function(){
			var 
				t = $(this),
				data = t.serializeJSON(),
				action = t.attr('data-action');
			
			for(var i in data){
				this[i] = data[i];
			}
			
			this[action]();
		});
	};
	return ActiveRecord;
});