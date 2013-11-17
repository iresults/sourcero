Sourcero.FilesController = Ember.ArrayController.extend({
	init: function() {
		console.log('init Sourcero.FilesController');
		this._super();

//		this.set('model', this.get('store').findByIds('file', Sourcero.LocalStorageHelper.get('openFiles')));


	},

	ffinit: function() {
		var _this = this;
		this._super();
//		this.set('model', this.openedFilesFromLocalStorage());

//		this.content = Ember.RSVP.resolve(function() {
//			return _this.openedFilesFromLocalStorage();
//		});
	},

	/**
	 * Returns the files from the local storage
	 */
	openedFilesFromLocalStorage: function() {
		var openFilesIds = Sourcero.LocalStorageHelper.get('openFiles'),
			idsCount = openFilesIds.length,
			openFiles = Ember.A(),
			store = this.get('store'),
			currentId, i;

		for (i = 0; i < idsCount; i++) {
			currentId = openFilesIds[i];
			openFiles.addObject(store.find('file', currentId));
		}



		console.log(
			'Sourcero.LocalStorageHelper',
			openFilesIds,
			openFiles,
			store.findByIds('file', openFilesIds)
		);

		return store.findByIds('file', openFilesIds);
		return openFiles;
	},

	/**
	 * Invoked when the open files changed
	 */
	openFilesChanged: function() {
		console.log('ofc', this.get('content'));

		var openFilesIds = this.get('content').map(function(item, index, enumerable) {
			return item.get('id');
		});
		Sourcero.LocalStorageHelper.set('openFiles', openFilesIds);
	},

	/**
	 * Observes external changes of the open files
	 */
	openFilesChangedObserver: function() {
		Ember.run.once(this, 'openFilesChanged');
	}.observes('@each'),

	actions: {
		closeTab: function(file) {
			this.removeObject(file);
		}
	}
});