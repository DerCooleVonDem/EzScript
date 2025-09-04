# EZ Script Lexer

The lexer is the first phase of the EZ Script language processing pipeline. It takes raw source code as input and converts it into a stream of tokens that can be processed by the parser.

## Overview

The lexer works by reading the source code character by character and recognizing patterns that correspond to different token types. It uses a set of specialized recognizers to identify different types of tokens, such as keywords, operators, literals, identifiers, and comments.

## Components

### Lexer

The main `Lexer` class orchestrates the lexical analysis process. It:
1. Takes the source code as input
2. Creates a `CharacterReader` to read characters one by one
3. Uses various recognizers to identify tokens
4. Processes the input character by character, recognizing tokens, and adding them to a tokens array
5. Adds an EOF token at the end

### Token

A `Token` represents a meaningful unit of the source code. Each token has:
- A type (defined in `TokenType`)
- A value (the actual text from the source code)
- A line number (for error reporting)

### TokenType

The `TokenType` class defines all the token types used in EZ Script:
- Special tokens (EOF, IDENTIFIER, VARIABLE, STRING, NUMBER, COMMENT, UNKNOWN)
- Keywords (START, STOP, ON, COMMAND, TASK, FUNCTION, CONST, IMPORT, IF, ELSE, etc.)
- Operators (ASSIGN, EQ, NEQ, LT, GT, etc.)
- Delimiters (LPAREN, RPAREN, LBRACE, RBRACE, etc.)

### Recognizers

The lexer uses specialized recognizers to identify different types of tokens:

1. **CommentRecognizer**: Recognizes single-line (`//`) and block (`/* */`) comments
2. **IdentifierRecognizer**: Recognizes identifiers (variable names, function names, etc.) and variables (prefixed with `$`)
3. **KeywordRecognizer**: Recognizes keywords (START, STOP, IF, ELSE, etc.)
4. **LiteralRecognizer**: Recognizes string literals (enclosed in quotes) and number literals
5. **OperatorRecognizer**: Recognizes operators (+, -, *, /, etc.) and delimiters ((, ), {, }, etc.)

## Process Flow

1. The lexer reads the source code character by character
2. For each character, it tries to recognize a token using the appropriate recognizer
3. If a token is recognized, it's added to the tokens array
4. If a character is not recognized, it's added as an UNKNOWN token
5. The lexer continues until it reaches the end of the source code
6. An EOF token is added at the end

## Example

For the source code:
```
start {
    log("Hello, World!");
}
```

The lexer would produce tokens like:
```
START
LBRACE
IDENTIFIER (log)
LPAREN
STRING (Hello, World!)
RPAREN
SEMICOLON
RBRACE
EOF
```

These tokens are then passed to the parser for further processing.