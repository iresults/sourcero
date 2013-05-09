/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Andreas Thurnheer-Meier <tma@iresults.li>, iresults
 *  Daniel Corn <cod@iresults.li>, iresults
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/



(function() {
    var root = this,
        codeFolding;

//    require(['ace/lib/ace/ace'], function (ace) {
    require(['lib/ace'], function (ace) {
        root.delegate = {
            /**
             * Editor instance
             */
            editor: null,

            /**
             * Textarea
             */
            codeTextarea: document.getElementById("codeTextarea"),

            /**
             * Initialize
             */
            init: function () {
                var editor = ace.edit("code");
                editor.setTheme("ace/theme/monokai");
                editor.getSession().setMode("ace/mode/javascript");


                return;
                this.editor = ace.edit("code");
                this.codeTextarea.hide();
                this.editor.getSession().setValue(this.codeTextarea.getValue());
                this.editor.getSession().on('change', function(){
                    this.codeTextarea.val(editor.getSession().getValue());
                });
                this.editor.setTheme("ace/theme/monokai");
    //            this.editor.getSession().setMode("ace/mode/javascript");
                return;



                this.editor = CodeMirror.fromTextArea(this.codeTextarea, {
                    // Add a comment, so this is not treated as Fluid variable
                    lineNumbers: true,
                    matchBrackets: true,
                    theme: "monokai",
                    mode: codeMirrorMode,
                    indentUnit: 4,
                    indentWithTabs: true,
                    enterMode: "keep",
                    tabMode: "shift",
                    autoCloseTags: true,
                    autoCloseBrackets: true,
                    lineWrapping: true,
                    styleActiveLine: true,
                    highlightSelectionMatches: true,
                    extraKeys: {
                        "Cmd-.": "autocomplete",
                        "Ctrl-.": "autocomplete",
                        "Cmd-S": "saveFile",
                        "Ctrl-S": "saveFile"
                    }
                });
                this.editor.on("gutterClick", codeFolding);
                this.restoreCursorPosition();
            },

            /**
             * Tries to restore the cursor position from the localStorage
             */
            restoreCursorPosition: function () {
                var cursorPosition = root.localStorage.getItem('editor.cursorPosition');
                if (cursorPosition) {
                    cursorPosition = JSON.parse(cursorPosition);
                    this.editor.setCursor(cursorPosition);
                }
            },

            /**
             * Save the file
             */
            saveFile: function () {
                // Save the cursor position
                var serializedCursorPosition = JSON.stringify(this.editor.getCursor());
                root.localStorage.setItem('editor.cursorPosition', serializedCursorPosition);

                // Save the file
                this.codeTextarea.form.submit();
            },

            /**
             * Get the completion list
             * @param Object editor Code mirror editor
             */
            getCompletionList: function (editor) {
                var completionObject,
                    completionCallback,
                    currentPosition,
                    token;

                if (false && CodeMirror[codeMirrorModeHint]) {
                    completionCallback = CodeMirror[codeMirrorModeHint];
                    completionObject = completionCallback(editor);
                } else {
                    currentPosition = editor.getCursor();
                    token = editor.getTokenAt(currentPosition);
                    completionObject = {
                        from: CodeMirror.Pos(currentPosition.line, token.start),
                        to: CodeMirror.Pos(currentPosition.line, token.end)
                    }
                }
                completionObject.list = root.delegate.getGeneralCompletionList(editor, token);
                return completionObject;
            },

            /**
             * Get the general completion list
             * @param Object editor Code mirror editor
             */
            getGeneralCompletionList: function (editor, token) {
                var contents = this.editor.getValue(),
                    contentsArray,
                    start = token.string;

                console.log(contents.split(/\s+/), token);

                contentsArray = contents.split(/\s+/);
                return contentsArray.filter(function (element, index, array) {
                    console.log(element, start);
                    return element.indexOf(start) === 0 && element !== start;
                });
            }
        }

//    ace = require("ace-builds-master/src-min-noconflict/ace")
//    root.delegate.init();

//    require(['ace-builds-master/src-min-noconflict/ace'], function (ace) {
//    require(['./ace/lib/ace/ace.js'], function (ace) {
//    require([ACEInstallPath], function (ace) {

        //foo is now loaded.
        root.delegate.init();
    });

//    CodeMirror.commands.autocomplete = function(cm) {
//        CodeMirror.showHint(cm, root.delegate.getCompletionList);
//    }
//    CodeMirror.commands.saveFile = function(cm) {
//        root.delegate.saveFile();
//    }
//    codeFolding = CodeMirror.newFoldFunction(CodeMirror.braceRangeFinder);


})(this);