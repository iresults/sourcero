Sourcero.LoginRoute = Ember.Route.extend({
	init: function() {
		this._super();
		window.open("/typo3/", "typo3login",
			"status=1,height=500,width=700,resizable=0"
		);
	}
});