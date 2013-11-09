Sourcero.IndexRoute = Ember.Route.extend({
	init: function() {
		var _this = this;

		this._super();

		this.get('store').findAll('file').then(function() {
			_this.transitionTo('files');
		});
	}
});
