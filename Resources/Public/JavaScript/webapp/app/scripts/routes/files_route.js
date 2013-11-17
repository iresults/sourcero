Sourcero.FilesRoute = Ember.Route.extend({
	setupController: function(controller) {
		var openFilesIds = Sourcero.LocalStorageHelper.get('openFiles'),
			store;
		if (openFilesIds) {
			store = this.get('store');
			controller.set('model', store.findByIds('file', openFilesIds));
		}
	}
});