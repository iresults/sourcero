Sourcero.ApplicationRoute = Ember.Route.extend({
	setupController: function(controller) {
		controller.loadFileTree();
	},

	init: function() {
		this._super();

		// Check if files can be fetched
		this.get('store').findAll('file').then(function() {
			Ember.Logger.info('Login ok');
		}, function() {
			Ember.Logger.info('Need to login');
			window.open("/typo3/", "typo3login",
				"status=1,height=500,width=700,resizable=0"
			);
		});
	}
});
