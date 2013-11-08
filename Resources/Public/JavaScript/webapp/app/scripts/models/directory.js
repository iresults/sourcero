Sourcero.Directory = DS.Model.extend({
	files: DS.hasMany('file'),
	children: DS.hasMany('directory')
});