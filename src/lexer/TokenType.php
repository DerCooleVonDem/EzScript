<?php

namespace JonasWindmann\EzScript\lexer;

class TokenType
{
    // Special
    public const EOF        = "EOF";
    public const IDENTIFIER = "IDENTIFIER";
    public const VARIABLE   = "VARIABLE";
    public const STRING     = "STRING";
    public const NUMBER     = "NUMBER";
    public const COMMENT    = "COMMENT";
    public const UNKNOWN    = "UNKNOWN";

    // Keywords
    public const START      = "START";
    public const STOP       = "STOP";
    public const ON         = "ON";
    public const COMMAND    = "COMMAND";
    public const TASK       = "TASK";
    public const FUNCTION   = "FUNCTION";
    public const CONST      = "CONST";
    public const IMPORT     = "IMPORT";

    public const IF         = "IF";
    public const ELSE       = "ELSE";
    public const ELSEIF     = "ELSEIF";
    public const WHILE      = "WHILE";
    public const FOR        = "FOR";
    public const TRY        = "TRY";
    public const CATCH      = "CATCH";
    public const RETURN     = "RETURN";
    public const BREAK      = "BREAK";
    public const CONTINUE   = "CONTINUE";

    public const AS         = "AS";
    public const ALIAS      = "ALIAS";
    public const PERMISSION = "PERMISSION";
    public const USAGE      = "USAGE";

    // Operators
    public const ASSIGN     = "ASSIGN";    // =
    public const EQ         = "EQ";        // ==
    public const NEQ        = "NEQ";       // !=
    public const LT         = "LT";        // <
    public const GT         = "GT";        // >
    public const LTE        = "LTE";       // <=
    public const GTE        = "GTE";       // >=

    public const PLUS       = "PLUS";      // +
    public const MINUS      = "MINUS";     // -
    public const STAR       = "STAR";      // *
    public const SLASH      = "SLASH";     // /
    public const DOT        = "DOT";       // .
    public const ARROW      = "ARROW";     // ->
    public const INC        = "INC";       // ++
    public const DEC        = "DEC";       // --

    // Delimiters
    public const LPAREN     = "LPAREN";    // (
    public const RPAREN     = "RPAREN";    // )
    public const LBRACE     = "LBRACE";    // {
    public const RBRACE     = "RBRACE";    // }
    public const LBRACKET   = "LBRACKET";  // [
    public const RBRACKET   = "RBRACKET";  // ]
    public const COMMA      = "COMMA";     // ,
    public const SEMICOLON  = "SEMICOLON"; // ;
    public const COLON      = "COLON";     // :
    const AND = "AND";
    const OR = "OR";
    const LE = "LE";
    const GE = "GE";
    const NOT = "NOT";
    const PLUSEQ = "PLUSEQ";
    const MINUSEQ = "MINUSEQ";
    const STAREQ = "STAREQ";
    const SLASHEQ = "SLASHEQ";
    const MODEQ = "MODEQ";
    const MOD = "MOD";
}
