# EZ Script Parser

The parser is the second phase of the EZ Script language processing pipeline. It takes the stream of tokens produced by the lexer and constructs an Abstract Syntax Tree (AST) that represents the structure and meaning of the code.

## Overview

The parser analyzes the sequence of tokens according to the grammar rules of the EZ Script language. It builds a hierarchical tree structure (the AST) that represents the syntactic structure of the source code. This tree is then used by the interpreter to execute the code.

## Components

### Parser

The main `Parser` class orchestrates the parsing process. It:
1. Takes the tokens from the lexer as input
2. Creates specialized parser instances for different language constructs
3. Sets up dependencies between the parsers
4. Parses the tokens into an AST
5. Returns the AST for further processing

### Specialized Parsers

The parser uses specialized parsers to handle different language constructs:

1. **DeclarationParser**: Parses declarations such as:
   - Constants (`const`)
   - Functions (`function`)
   - Commands (`command`)
   - Imports (`import`)

2. **ExpressionParser**: Parses expressions such as:
   - Literals (strings, numbers)
   - Variables
   - Binary expressions (arithmetic, comparison)
   - Function calls

3. **LifecycleParser**: Parses lifecycle blocks:
   - Start blocks (`start { ... }`)
   - Stop blocks (`stop { ... }`)

4. **StatementParser**: Parses statements such as:
   - Variable assignments
   - If statements
   - While loops
   - For loops
   - Try-catch blocks
   - Return statements
   - Break and continue statements

## Process Flow

1. The parser starts by parsing top-level constructs (start/stop blocks, declarations)
2. For each top-level construct, it delegates to the appropriate specialized parser
3. The specialized parsers may recursively call each other to parse nested constructs
4. The result is a hierarchical AST that represents the structure of the code

## AST Structure

The AST is a tree of nodes, where each node represents a language construct. Each node has a type and additional properties depending on the type. For example:

- A `Program` node represents the entire program and contains a list of top-level nodes
- A `FunctionDeclaration` node represents a function declaration and contains the function name, parameters, and body
- An `IfStatement` node represents an if statement and contains the condition, then branch, and else branch
- A `BinaryExpression` node represents a binary expression and contains the left operand, operator, and right operand

## Example

For the source code:
```
start {
    log("Hello, World!");
}
```

The parser would produce an AST like:
```json
{
  "type": "Program",
  "body": [
    {
      "type": "StartBlock",
      "body": {
        "type": "Block",
        "statements": [
          {
            "type": "FunctionCall",
            "name": "log",
            "arguments": [
              {
                "type": "StringLiteral",
                "value": "Hello, World!"
              }
            ]
          }
        ]
      }
    }
  ]
}
```

This AST is then passed to the interpreter for execution.