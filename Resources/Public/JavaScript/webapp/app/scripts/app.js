var Sourcero = window.Sourcero = Ember.Application.create(),
	CodeMirror = {};

/* CodeMirror */
require('bower_components/codemirror/lib/codemirror');

CodeMirror.modeURL = CodeMirror.modeURL || 'bower_components/codemirror/mode/%N/%N.js';
require('bower_components/codemirror/addon/mode/loadmode');

require('bower_components/codemirror/addon/comment/comment');
require('bower_components/codemirror/addon/dialog/dialog');

require('bower_components/codemirror/addon/edit/closebrackets');
require('bower_components/codemirror/addon/edit/closetag');
require('bower_components/codemirror/addon/edit/continuecomment');
require('bower_components/codemirror/addon/edit/continuelist');
require('bower_components/codemirror/addon/edit/matchbrackets');
require('bower_components/codemirror/addon/edit/matchtags');
require('bower_components/codemirror/addon/edit/trailingspace');

require('bower_components/codemirror/addon/hint/show-hint');
require('bower_components/codemirror/addon/hint/anyword-hint');
require('bower_components/codemirror/addon/hint/html-hint');
require('bower_components/codemirror/addon/hint/javascript-hint');
// require('bower_components/codemirror/addon/hint/pig-hint');
require('bower_components/codemirror/addon/hint/python-hint');
require('bower_components/codemirror/addon/hint/xml-hint');

require('bower_components/codemirror/addon/search/match-highlighter');
require('bower_components/codemirror/addon/search/search');
require('bower_components/codemirror/addon/search/searchcursor');

require('bower_components/codemirror/addon/selection/active-line');
require('bower_components/codemirror/addon/selection/mark-selection');

require('bower_components/codemirror/addon/tern/tern');

require('bower_components/codemirror/addon/lint/coffeescript-lint');
require('bower_components/codemirror/addon/lint/javascript-lint');
require('bower_components/codemirror/addon/lint/json-lint');
require('bower_components/codemirror/addon/lint/lint');

//require('bower_components/codemirror/mode/*/*');


/* Application */
require('scripts/controllers/*');
require('scripts/components/*');
require('scripts/models/*');
require('scripts/helpers/*');
require('scripts/data/adapter');
require('scripts/store');
require('scripts/routes/*');
require('scripts/views/*');
require('scripts/router');

require('scripts/plugin/tree/*/*');
