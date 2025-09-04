# EZ Script Interpreter

The interpreter is the final phase of the EZ Script language processing pipeline. It takes the Abstract Syntax Tree (AST) produced by the parser and executes the code, producing the desired behavior.

## Overview

The interpreter traverses the AST and evaluates each node according to the semantics of the EZ Script language. It maintains an environment that stores variables and functions, and it uses specialized evaluators to handle different types of AST nodes.

## Components

### Interpreter

The main `Interpreter` class orchestrates the interpretation process. It:
1. Creates an `Environment` to store variables and functions
2. Creates a `FunctionRegistry` to register built-in functions
3. Creates specialized evaluators for different AST nodes
4. Creates a main `Evaluator` that delegates to the specialized evaluators
5. Runs the program by evaluating each node in the AST

### Environment

The `Environment` class represents the execution context. It stores:
- Variables and their values
- Function definitions
- Parent environments (for nested scopes)

### FunctionRegistry

The `FunctionRegistry` class manages the built-in functions available to EZ Script programs. It:
- Registers built-in functions (like `log`)
- Provides a way to look up functions by name
- Handles function calls

### Evaluators

The interpreter uses specialized evaluators to handle different types of AST nodes:

1. **LiteralEvaluator**: Evaluates literal values (strings, numbers)
2. **VariableEvaluator**: Evaluates variable references
3. **AssignmentEvaluator**: Evaluates variable assignments
4. **BinaryExpressionEvaluator**: Evaluates binary expressions (arithmetic, comparison)
5. **FunctionCallEvaluator**: Evaluates function calls
6. **BlockEvaluator**: Evaluates blocks of statements

## Process Flow

1. The interpreter starts by evaluating the top-level nodes in the AST
2. For each node, it delegates to the appropriate specialized evaluator
3. The evaluators may recursively call each other to evaluate nested nodes
4. The result of the evaluation is the execution of the program

## Built-in Functions

EZ Script comes with several built-in functions:

- `log(message)`: Outputs a message to the console
- (Other built-in functions would be listed here)

## Example

For the AST representing:
```
start {
    log("Hello, World!");
}
```

The interpreter would:
1. Recognize the `StartBlock` node
2. Evaluate the block's statements
3. Encounter a `FunctionCall` node for the `log` function
4. Evaluate the argument (a string literal)
5. Call the built-in `log` function with the evaluated argument
6. Output "Hello, World!" to the console

## Error Handling

The interpreter includes error handling to provide meaningful error messages when something goes wrong during execution. Errors can include:
- Undefined variables
- Undefined functions
- Type errors
- Runtime errors

When an error occurs, the interpreter will provide information about the error, including the line number where the error occurred.