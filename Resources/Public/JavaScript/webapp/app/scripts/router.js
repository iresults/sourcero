//Sourcero.Router.map(function () {
//	this.resource('file', {path: '/file/:path'});
//});
Sourcero.Router.map(function(){
	this.resource('files', function(){
		this.resource('file', { path:'/:path' }, function(){
			this.route('edit');
		});
//		this.route('create');
	});
	this.route('login');
});