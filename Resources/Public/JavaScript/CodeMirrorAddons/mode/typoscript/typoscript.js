(function() {
  function keywords(str) {
    var obj = {}, words = str.split(" ");
    for (var i = 0; i < words.length; ++i) obj[words[i]] = true;
    return obj;
  }
  function heredoc(delim) {
    return function(stream, state) {
      if (stream.match(delim)) state.tokenize = null;
      else stream.skipToEnd();
      return "string";
    };
  }
  var typoscriptConfig = {
    name: "clike",
    // keywords: keywords("abstract and array as break case catch class clone const continue declare default " +
    //                    "do else elseif enddeclare endfor endforeach endif endswitch endwhile extends final " +
    //                    "for foreach function global goto if implements interface instanceof namespace " +
    //                    "new or private protected public static switch throw trait try use var while xor " +
    //                    "die echo empty exit eval include include_once isset list require require_once return " +
    //                    "print unset __halt_compiler self static parent"),
    keywords: keywords("global globalVars globalString COA cObj RESOURCE CONTENT FILE CASE TEXT HTML USER USER_INT"),
    blockKeywords: keywords("catch do else elseif for foreach if switch try while"),
    atoms: keywords("true false null TRUE FALSE NULL __CLASS__ __DIR__ __FILE__ __LINE__ __METHOD__ __FUNCTION__ __NAMESPACE__"),
    builtin: keywords("wrap innerWrap outerWrap require if equals value"),
    multiLineStrings: true,
    hooks: {
      "$": function(stream) {
        stream.eatWhile(/[\w\$_]/);
        return "variable-2";
      },
      "<": function(stream, state) {
        if (stream.match(/<</)) {
          stream.eatWhile(/[\w\.]/);
          state.tokenize = heredoc(stream.current().slice(3));
          return state.tokenize(stream, state);
        }
        return false;
      },
      "#": function(stream) {
        while (!stream.eol() && !stream.match("?>", false)) stream.next();
        return "comment";
      },
      "/": function(stream) {
        if (stream.eat("/")) {
          while (!stream.eol() && !stream.match("?>", false)) stream.next();
          return "comment";
        }
        return false;
      }
    }
  };

  CodeMirror.defineMode("typoscript", function(config, parserConfig) {
    var htmlMode = CodeMirror.getMode(config, "text/html");
    var typoscriptMode = CodeMirror.getMode(config, typoscriptConfig);

    function dispatch(stream, state) {
      var isTyposcript = state.curMode == typoscriptMode;
      if (stream.sol() && state.pending != '"') state.pending = null;
      if (!isTyposcript) {
        if (stream.match(/^<\?\w*/)) {
          state.curMode = typoscriptMode;
          state.curState = state.typoscript;
          return "meta";
        }
        if (state.pending == '"') {
          while (!stream.eol() && stream.next() != '"') {}
          var style = "string";
        } else if (state.pending && stream.pos < state.pending.end) {
          stream.pos = state.pending.end;
          var style = state.pending.style;
        } else {
          var style = htmlMode.token(stream, state.curState);
        }
        state.pending = null;
        var cur = stream.current(), openTyposcript = cur.search(/<\?/);
        if (openTyposcript != -1) {
          if (style == "string" && /\"$/.test(cur) && !/\?>/.test(cur)) state.pending = '"';
          else state.pending = {end: stream.pos, style: style};
          stream.backUp(cur.length - openTyposcript);
        }
        return style;
      } else if (isTyposcript && state.typoscript.tokenize == null && stream.match("?>")) {
        state.curMode = htmlMode;
        state.curState = state.html;
        return "meta";
      } else {
        return typoscriptMode.token(stream, state.curState);
      }
    }

    return {
      startState: function() {
        var html = CodeMirror.startState(htmlMode), typoscript = CodeMirror.startState(typoscriptMode);
        return {html: html,
                typoscript: typoscript,
                curMode: parserConfig.startOpen ? typoscriptMode : htmlMode,
                curState: parserConfig.startOpen ? typoscript : html,
                pending: null};
      },

      copyState: function(state) {
        var html = state.html, htmlNew = CodeMirror.copyState(htmlMode, html),
            typoscript = state.typoscript, typoscriptNew = CodeMirror.copyState(typoscriptMode, typoscript), cur;
        if (state.curMode == htmlMode) cur = htmlNew;
        else cur = typoscriptNew;
        return {html: htmlNew, typoscript: typoscriptNew, curMode: state.curMode, curState: cur,
                pending: state.pending};
      },

      token: dispatch,

      indent: function(state, textAfter) {
        if ((state.curMode != typoscriptMode && /^\s*<\//.test(textAfter)) ||
            (state.curMode == typoscriptMode && /^\?>/.test(textAfter)))
          return htmlMode.indent(state.html, textAfter);
        return state.curMode.indent(state.curState, textAfter);
      },

      electricChars: "/{}:",
      blockCommentStart: "/*",
      blockCommentEnd: "*/",
      lineComment: "#",

      innerMode: function(state) { return {state: state.curState, mode: state.curMode}; }
    };
  }, "htmlmixed", "clike");

  CodeMirror.defineMIME("text/x-typoscript", typoscriptConfig);
})();