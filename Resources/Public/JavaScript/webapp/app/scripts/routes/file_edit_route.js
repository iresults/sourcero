Sourcero.FileEditRoute = Ember.Route.extend({
	model: function(){
		var file = this.modelFor('file'),
			filesController = this.controllerFor('files');

		console.log('edit file', this.modelFor('file'));

		if (!filesController.contains(file)) {
			filesController.pushObject(file);
		}
		return file;
	}
});