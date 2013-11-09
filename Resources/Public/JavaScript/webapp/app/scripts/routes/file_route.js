Sourcero.FileRoute = Ember.Route.extend({
	model: function(params) {
		console.log(params)
		return this.get('store').find('file', params.id);
	}
});