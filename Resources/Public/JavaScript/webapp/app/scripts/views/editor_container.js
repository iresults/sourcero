Sourcero.EditorContainer = Ember.ContainerView.extend({
	tagName: 'section',
	init: function() {
		this.get('controller').set('editorOutlet', this);
	}
});