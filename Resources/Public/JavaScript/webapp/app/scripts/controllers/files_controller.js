Sourcero.FilesController = Ember.ArrayController.extend({
	model: function() {
		var _this = this;

		console.log(
			'Sourcero.LocalStorageController',
			Sourcero.LocalStorageController.get('openFiles', function() {})
		);

		return Ember.RSVP.resolve(
			Sourcero.LocalStorageController.get('openFiles', function() {
				return _this.get('store').findAll('file');
			})
		);
	},

	/**
	 * Invoked when the open files changed
	 */
	openFilesChanged: function() {
		console.log('ofc', this.get('content').slice())
		Sourcero.LocalStorageController.set('openFiles', this.get('content'));
	},

	/**
	 * Observes external changes of the open files
	 */
	openFilesChangedObserver: function() {
		Ember.run.once(this, 'openFilesChanged');
	}.observes('@each')
});