<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 17/03/14
 * Time: 10:01
 */

namespace Gplanchat\Javascript\Tokenizer;

use Gplanchat\Tokenizer\TokenizerInterface as BaseTokenizerInterface;

interface TokenizerInterface
    extends BaseTokenizerInterface
{
    const TOKEN_END                   = 0x1;
    const TOKEN_NUMBER_INTEGER        = 0x2;
    const TOKEN_NUMBER_FLOATING_POINT = 0x3;
    const TOKEN_IDENTIFIER            = 0x4;
    const TOKEN_STRING                = 0x5;
    const TOKEN_REGEXP                = 0x6;
    const TOKEN_NEWLINE               = 0x7;
    const TOKEN_LINE_COMMENT          = 0x8;
    const TOKEN_BLOCK_COMMENT         = 0x9;

    const JS_SCRIPT        = 0x100;
    const JS_BLOCK         = 0x101;
    const JS_LABEL         = 0x102;
    const JS_FOR_IN        = 0x103;
    const JS_CALL          = 0x104;
    const JS_NEW_WITH_ARGS = 0x105;
    const JS_INDEX         = 0x106;
    const JS_ARRAY_INIT    = 0x107;
    const JS_OBJECT_INIT   = 0x108;
    const JS_PROPERTY_INIT = 0x109;
    const JS_GETTER        = 0x110;
    const JS_SETTER        = 0x111;
    const JS_GROUP         = 0x112;
    const JS_LIST          = 0x113;
    const JS_MINIFIED      = 0x1FF;

    /* Operators */
    const OP_SEMICOLON            = ';';
    const OP_COMMA                = ',';
    const OP_HOOK                 = '?';
    const OP_COLON                = ':';
    const OP_OR                   = '||';
    const OP_AND                  = '&&';
    const OP_BITWISE_OR           = '|';
    const OP_BITWISE_XOR          = '^';
    const OP_BITWISE_AND          = '&';
    const OP_STRICT_EQ            = '===';
    const OP_EQ                   = '==';
    const OP_ASSIGN               = '=';
    const OP_STRICT_NE            = '!==';
    const OP_NE                   = '!=';
    const OP_LSH                  = '<<';
    const OP_LE                   = '<=';
    const OP_LT                   = '<';
    const OP_URSH                 = '>>>';
    const OP_RSH                  = '>>';
    const OP_GE                   = '>=';
    const OP_GT                   = '>';
    const OP_INCREMENT            = '++';
    const OP_DECREMENT            = '--';
    const OP_PLUS                 = '+';
    const OP_MINUS                = '-';
    const OP_MUL                  = '*';
    const OP_DIV                  = '/';
    const OP_MOD                  = '%';
    const OP_NOT                  = '!';
    const OP_BITWISE_NOT          = '~';
    const OP_DOT                  = '.';
    const OP_LEFT_SQUARE_BRACKET  = '[';
    const OP_RIGHT_SQUARE_BRACKET = ']';
    const OP_LEFT_CURLY           = '{';
    const OP_RIGHT_CURLY          = '}';
    const OP_LEFT_BRACKET         = '(';
    const OP_RIGHT_BRACKET        = ')';
    const OP_CONDCOMMENT_END      = '@*/';

    const OP_UNARY_PLUS = 'U+';
    const OP_UNARY_MINUS = 'U-';

    /* Keywords {{{*/
    const KEYWORD_BREAK      = 'break';
    const KEYWORD_CASE       = 'case';
    const KEYWORD_CATCH      = 'catch';
    const KEYWORD_CONST      = 'const';
    const KEYWORD_CONTINUE   = 'continue';
    const KEYWORD_DEBUGGER   = 'debugger';
    const KEYWORD_DEFAULT    = 'default';
    const KEYWORD_DELETE     = 'delete';
    const KEYWORD_DO         = 'do';
    const KEYWORD_ELSE       = 'else';
    const KEYWORD_ENUM       = 'enum';
    const KEYWORD_FALSE      = 'false';
    const KEYWORD_FINALLY    = 'finally';
    const KEYWORD_FOR        = 'for';
    const KEYWORD_FUNCTION   = 'function';
    const KEYWORD_IF         = 'if';
    const KEYWORD_IN         = 'in';
    const KEYWORD_INSTANCEOF = 'instanceof';
    const KEYWORD_NEW        = 'new';
    const KEYWORD_NULL       = 'null';
    const KEYWORD_RETURN     = 'return';
    const KEYWORD_SWITCH     = 'switch';
    const KEYWORD_THIS       = 'this';
    const KEYWORD_THROW      = 'throw';
    const KEYWORD_TRUE       = 'true';
    const KEYWORD_TRY        = 'try';
    const KEYWORD_TYPEOF     = 'typeof';
    const KEYWORD_VAR        = 'var';
    const KEYWORD_VOID       = 'void';
    const KEYWORD_WHILE      = 'while';
    const KEYWORD_WITH       = 'with';
    /*}}}*/
}
