(function () {
  function forEach(arr, f) {
    for (var i = 0, e = arr.length; i < e; ++i) f(arr[i]);
  }

  function arrayContains(arr, item) {
    if (!Array.prototype.indexOf) {
      var i = arr.length;
      while (i--) {
        if (arr[i] === item) {
          return true;
        }
      }
      return false;
    }
    return arr.indexOf(item) != -1;
  }

  function scriptHint(editor, _keywords, getToken) {
    // Find the token at the cursor
    var cur = editor.getCursor(), token = getToken(editor, cur), tprop = token;
    // If it's not a 'word-style' token, ignore the token.

    if (!/^[\w$_]*$/.test(token.string)) {
        token = tprop = {start: cur.ch, end: cur.ch, string: "", state: token.state,
                         className: token.string == ":" ? "php-type" : null};
    }

    if (!context) var context = [];
    context.push(tprop);

    var completionList = getCompletions(token, context, editor);
    completionList = completionList.sort();
    //prevent autocomplete for last word, instead show dropdown with one word
    if(completionList.length == 1) {
      completionList.push(" ");
    }

    return {list: completionList,
            from: CodeMirror.Pos(cur.line, token.start),
            to: CodeMirror.Pos(cur.line, token.end)};
  }

  CodeMirror.phpHint = function(editor) {
    return scriptHint(editor, phpKeywordsU, function (e, cur) {return e.getTokenAt(cur);});
  };

  var phpKeywords = "and or && || break continue class for foreach while do as extends implements "
+ "interface try catch __FILE__ __CLASS__ __DIR__ __METHOD__ __FUNCTION__";
  var phpKeywordsL = phpKeywords.split(" ");
  var phpKeywordsU = phpKeywords.toUpperCase().split(" ");

  var phpBuiltins = "instanceof pow str_replace "
+ "any eval isinstance pow sum basestring execfile issubclass print super"
+ "bin file iter property tuple bool filter len range type"
+ "bytearray float list raw_input unichr callable format locals reduce unicode"
+ "chr frozenset long reload vars classmethod getattr map repr xrange"
+ "cmp globals max reversed zip compile hasattr memoryview round __import__"
+ "complex hash min set apply delattr help next setattr buffer"
+ "dict hex object slice coerce dir id oct sorted intern ";
  var phpBuiltinsL = phpBuiltins.split(" ").join("() ").split(" ");
  var phpBuiltinsU = phpBuiltins.toUpperCase().split(" ").join("() ").split(" ");

  function getCompletions(token, context, editor) {
    var found = [], start = token.string;

    console.log(token, context, start, editor.getValue());

    // Find variables
    if (start.substring(0, 1) === '$') {

      return findVariables(editor).filter(
        function (element, index, array) {
          return element.indexOf(start) === 0;
        }
      );
    }


    function maybeAdd(str) {
      if (str.indexOf(start) == 0 && !arrayContains(found, str)) found.push(str);
    }

    function gatherCompletions(_obj) {
        forEach(phpBuiltinsL, maybeAdd);
        forEach(phpBuiltinsU, maybeAdd);
        forEach(phpKeywordsL, maybeAdd);
        forEach(phpKeywordsU, maybeAdd);
    }

    function findVariables(editor) {
      var contents = editor.getValue(),
        editorLines = contents.length,
        matches;

        matches = contents.match(/\$[a-z0-9\-_]*[a-z0-9_]/ig);
        // Use prototype's method uniq()
        return matches.uniq();
    }

    if (context) {
      // If this is a property, see if it belongs to some object we can
      // find in the current environment.
      var obj = context.pop(), base;

      if (obj.type == "variable")
          base = obj.string;
      else if(obj.type == "variable-3")
          base = ":" + obj.string;

      while (base != null && context.length)
        base = base[context.pop().string];
      if (base != null) gatherCompletions(base);
    }
    //return ['hello', 'world']
    return found;
  }
})();
