var attr = DS.attr;

Sourcero.File = DS.Model.extend({
	path: attr('string'),

	contents: attr('string'),
	type: attr('string'),

	size: attr('number'),
	lastModifiedDate: attr('date'),

	name: function() {
		var path = this.get('path');
		if (!path) return '';
		return path.replace(/^.*[\\\/]/, '');
	}.property('path'),

	directory: function() {
		var path = this.get('path');
		if (!path) return '';
		return path.replace(/\\/g, '/').replace(/\/[^\/]*\/?$/, '');
	}.property('path')
});