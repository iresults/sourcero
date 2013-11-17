Sourcero.FileEditRoute = Ember.Route.extend({
	model: function(){
		var file = this.modelFor('file'),
			filesController = this.controllerFor('files');
		filesController.addObject(file);
		return file;
	}
});