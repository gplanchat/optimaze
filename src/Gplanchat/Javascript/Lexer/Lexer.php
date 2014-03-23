<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 17/03/14
 * Time: 10:01
 */

namespace Gplanchat\Javascript\Lexer;

use Gplanchat\Javascript\Lexer\Node\Root;
use Gplanchat\Javascript\Lexer\Parser\ParserInterface;
use Gplanchat\Javascript\Tokenizer\Tokenizer;
use Gplanchat\Javascript\Tokenizer\TokenizerInterface;
use Gplanchat\Tokenizer\Token;
use Gplanchat\Javascript\Lexer\Node\NodeInterface;
use Gplanchat\Javascript\Lexer\Node\Node;

class Lexer
{
    /**
     * @var TokenizerInterface
     */
    private $tokenizer;

    /**
     * @var Parser\ParserInterface[]
     */
    private $parserList = [];

    /**
     * @var array
     */
    protected $operatorPrecedence = [
        TokenizerInterface::OP_SEMICOLON => 0,
        TokenizerInterface::OP_COMMA => 1,
        TokenizerInterface::OP_EQ => 2,
        TokenizerInterface::OP_HOOK => 2,
        TokenizerInterface::OP_COLON => 2,
        TokenizerInterface::OP_OR => 4,
        TokenizerInterface::OP_AND => 5,
        TokenizerInterface::OP_BITWISE_OR => 6,
        TokenizerInterface::OP_BITWISE_XOR => 7,
        TokenizerInterface::OP_BITWISE_AND => 8,
        TokenizerInterface::OP_EQ => 9,
        TokenizerInterface::OP_NE => 9,
        TokenizerInterface::OP_STRICT_EQ => 9,
        TokenizerInterface::OP_STRICT_NE => 9,
        TokenizerInterface::OP_LT => 10,
        TokenizerInterface::OP_LE => 10,
        TokenizerInterface::OP_GE => 10,
        TokenizerInterface::OP_GT => 10,
        TokenizerInterface::KEYWORD_IN => 10,
        TokenizerInterface::KEYWORD_INSTANCEOF => 10,
        TokenizerInterface::OP_LSH => 11,
        TokenizerInterface::OP_RSH => 11,
        TokenizerInterface::OP_URSH => 11,
        TokenizerInterface::OP_PLUS => 12,
        TokenizerInterface::OP_MINUS => 12,
        TokenizerInterface::OP_MUL => 13,
        TokenizerInterface::OP_DIV => 13,
        TokenizerInterface::OP_MOD => 13,
        TokenizerInterface::KEYWORD_DELETE => 14,
        TokenizerInterface::KEYWORD_VOID => 14,
        TokenizerInterface::KEYWORD_TYPEOF => 14,
        TokenizerInterface::OP_NOT => 14,
        TokenizerInterface::OP_BITWISE_NOT => 14,
        TokenizerInterface::OP_UNARY_PLUS => 14,
        TokenizerInterface::OP_UNARY_MINUS => 14,
        TokenizerInterface::OP_INCREMENT => 15,
        TokenizerInterface::OP_DECREMENT => 15,
        TokenizerInterface::KEYWORD_NEW => 16,
        TokenizerInterface::OP_DOT => 17,
        TokenizerInterface::JS_NEW_WITH_ARGS => 0,
        TokenizerInterface::JS_INDEX => 0,
        TokenizerInterface::JS_CALL => 0,
        TokenizerInterface::JS_ARRAY_INIT => 0,
        TokenizerInterface::JS_OBJECT_INIT => 0,
        TokenizerInterface::JS_GROUP => 0
    ];

    /**
     * @var array
     */
    protected $operatorArity = [
        TokenizerInterface::OP_COMMA => -2,
        TokenizerInterface::OP_ASSIGN => 2,
        TokenizerInterface::OP_HOOK => 3,
        TokenizerInterface::OP_OR => 2,
        TokenizerInterface::OP_AND => 2,
        TokenizerInterface::OP_BITWISE_OR => 2,
        TokenizerInterface::OP_BITWISE_XOR => 2,
        TokenizerInterface::OP_BITWISE_AND => 2,
        TokenizerInterface::OP_EQ => 2,
        TokenizerInterface::OP_NE => 2,
        TokenizerInterface::OP_STRICT_EQ => 2,
        TokenizerInterface::OP_STRICT_NE => 2,
        TokenizerInterface::OP_LT => 2,
        TokenizerInterface::OP_LE => 2,
        TokenizerInterface::OP_GE => 2,
        TokenizerInterface::OP_GT => 2,
        TokenizerInterface::KEYWORD_IN => 2,
        TokenizerInterface::KEYWORD_INSTANCEOF => 2,
        TokenizerInterface::OP_LSH => 2,
        TokenizerInterface::OP_RSH => 2,
        TokenizerInterface::OP_URSH => 2,
        TokenizerInterface::OP_PLUS => 2,
        TokenizerInterface::OP_MINUS => 2,
        TokenizerInterface::OP_MUL => 2,
        TokenizerInterface::OP_DIV => 2,
        TokenizerInterface::OP_MOD => 2,
        TokenizerInterface::KEYWORD_DELETE => 1,
        TokenizerInterface::KEYWORD_VOID => 1,
        TokenizerInterface::KEYWORD_TYPEOF => 1,
        TokenizerInterface::OP_NOT => 1,
        TokenizerInterface::OP_BITWISE_NOT => 1,
        TokenizerInterface::OP_UNARY_PLUS => 1,
        TokenizerInterface::OP_UNARY_MINUS => 1,
        TokenizerInterface::OP_INCREMENT => 1,
        TokenizerInterface::OP_DECREMENT => 1,
        TokenizerInterface::KEYWORD_NEW => 1,
        TokenizerInterface::OP_DOT => 2,
        TokenizerInterface::JS_NEW_WITH_ARGS => 2,
        TokenizerInterface::JS_INDEX => 2,
        TokenizerInterface::JS_CALL => 2,
        TokenizerInterface::JS_ARRAY_INIT => 1,
        TokenizerInterface::JS_OBJECT_INIT => 1,
        TokenizerInterface::JS_GROUP => 1,
        TokenizerInterface::TOKEN_BLOCK_COMMENT => 1,
        TokenizerInterface::TOKEN_LINE_COMMENT => 1
    ];

    /**
     * @var Node
     */
    protected $rootNode = null;

    protected $ruleList = [];

    /**
     * @param TokenizerInterface $tokenizer
     * @param array $parserList
     */
    public function __construct(TokenizerInterface $tokenizer, array $parserList = [])
    {
        $this->tokenizer = $tokenizer;

//        if (empty($parserList)) {
//            $this->parserList = [
//                'codeBlock'    => new Parser\CodeBlock(),
////                'bracketBlock' => new Parser\BracketBlock()
//            ];
//        } else {
//            $this->parserList = $parserList;
//        }
//
//        foreach ($this->parserList as $context) {
//            /** @var ParserInterface $context */
//            $context->setLexer($this);
//        }

        $this->when(function(Token $token){

            })
            ->then(function(RuleChain $next, NodeInterface $node, TokenizerInterface $tokenizer) {
                $next->process();
            })
        ;
    }

    /**
     * @param callable $callback
     * @return RuleChain
     */
    public function when(callable $callback)
    {
        $rule = new RuleChain($callback);

        $this->ruleList[] = $rule;

        return $rule;
    }

    /**
     * @return array
     */
    public function getOperatorArity()
    {
        return $this->operatorArity;
    }

    /**
     * @return array
     */
    public function getOperatorPrecedence()
    {
        return $this->operatorPrecedence;
    }

    /**
     * @param string $parserName
     * @param NodeInterface $parentNode
     * @param Context $context
     */
    public function switchParser($parserName, NodeInterface $parentNode, Context $context = null)
    {
        if (!isset($this->parserList[$parserName])) {
            return;
        }

        $this->parserList[$parserName]->parse($this->tokenizer, $parentNode, $context ?: new Context());
    }

    /**
     * @return NodeInterface
     * @throws
     */
    public function parse()
    {
        $root = new Root();

        $this->switchParser('codeBlock', $root);

        return $root;
    }





/*










    private function Statements($x)
    {
        $n = new JSNode($this->tokenizer, JS_BLOCK);
        array_push($x->stmtStack, $n);

        while (!$this->tokenizer->isDone() && $this->tokenizer->peek() != TokenizerInterface::TokenizerInterface::OP_RIGHT_CURLY)
            $n->addNode($this->Statement($x));

        array_pop($x->stmtStack);

        return $n;
    }

    private function Block($x)
    {
        $this->tokenizer->mustMatch(TokenizerInterface::OP_LEFT_CURLY);
        $n = $this->Statements($x);
        $this->tokenizer->mustMatch(TokenizerInterface::OP_RIGHT_CURLY);

        return $n;
    }

    private function Statement($x)
    {
        $tt = $this->tokenizer->get();
        $n2 = null;

        // Cases for statements ending in a right curly return early, avoiding the
        // common semicolon insertion magic after this switch.
        switch ($tt) {
            case TokenizerInterface::KEYWORD_FUNCTION:
                return $this->FunctionDefinition(
                    $x,
                    true,
                    count($x->stmtStack) > 1 ? STATEMENT_FORM : DECLARED_FORM
                );
                break;

            case TokenizerInterface::OP_LEFT_CURLY:
                $n = $this->Statements($x);
                $this->tokenizer->mustMatch(TokenizerInterface::OP_RIGHT_CURLY);
                return $n;

            case TokenizerInterface::KEYWORD_IF:
                $n = new JSNode($this->tokenizer);
                $n->condition = $this->ParenExpression($x);
                array_push($x->stmtStack, $n);
                $n->thenPart = $this->Statement($x);
                $n->elsePart = $this->tokenizer->match(TokenizerInterface::KEYWORD_ELSE) ? $this->Statement($x) : null;
                array_pop($x->stmtStack);
                return $n;

            case TokenizerInterface::KEYWORD_SWITCH:
                $n = new JSNode($this->tokenizer);
                $this->tokenizer->mustMatch(TokenizerInterface::OP_LEFT_BRACKET);
                $n->discriminant = $this->Expression($x);
                $this->tokenizer->mustMatch(TokenizerInterface::OP_RIGHT_BRACKET);
                $n->cases = array();
                $n->defaultIndex = -1;

                array_push($x->stmtStack, $n);

                $this->tokenizer->mustMatch(TokenizerInterface::OP_LEFT_CURLY);

                while (($tt = $this->tokenizer->get()) != TokenizerInterface::OP_RIGHT_CURLY) {
                    switch ($tt) {
                        case TokenizerInterface::KEYWORD_DEFAULT:
                            if ($n->defaultIndex >= 0)
                                throw $this->tokenizer->newSyntaxError('More than one switch default');
                        // FALL THROUGH
                        case TokenizerInterface::KEYWORD_CASE:
                            $n2 = new JSNode($this->tokenizer);
                            if ($tt == TokenizerInterface::KEYWORD_DEFAULT)
                                $n->defaultIndex = count($n->cases);
                            else
                                $n2->caseLabel = $this->Expression($x, TokenizerInterface::OP_COLON);
                            break;
                        default:
                            throw $this->tokenizer->newSyntaxError('Invalid switch case');
                    }

                    $this->tokenizer->mustMatch(TokenizerInterface::OP_COLON);
                    $n2->statements = new JSNode($this->tokenizer, JS_BLOCK);
                    while (($tt = $this->tokenizer->peek(
                        )) != TokenizerInterface::KEYWORD_CASE && $tt != TokenizerInterface::KEYWORD_DEFAULT && $tt != TokenizerInterface::OP_RIGHT_CURLY)
                        $n2->statements->addNode($this->Statement($x));

                    array_push($n->cases, $n2);
                }

                array_pop($x->stmtStack);
                return $n;

            case TokenizerInterface::KEYWORD_FOR:
                $n = new JSNode($this->tokenizer);
                $n->isLoop = true;
                $this->tokenizer->mustMatch(TokenizerInterface::OP_LEFT_BRACKET);

                if (($tt = $this->tokenizer->peek()) != TokenizerInterface::OP_SEMICOLON) {
                    $x->inForLoopInit = true;
                    if ($tt == TokenizerInterface::KEYWORD_VAR || $tt == TokenizerInterface::KEYWORD_CONST) {
                        $this->tokenizer->get();
                        $n2 = $this->Variables($x);
                    } else {
                        $n2 = $this->Expression($x);
                    }
                    $x->inForLoopInit = false;
                }

                if ($n2 && $this->tokenizer->match(TokenizerInterface::KEYWORD_IN)) {
                    $n->type = JS_FOR_IN;
                    if ($n2->type == TokenizerInterface::KEYWORD_VAR) {
                        if (count($n2->treeNodes) != 1) {
                            throw $this->tokenizer->SyntaxError(
                                'Invalid for..in left-hand side',
                                $this->tokenizer->filename,
                                $n2->lineno
                            );
                        }

                        // NB: n2[0].type == IDENTIFIER and n2[0].value == n2[0].name.
                        $n->iterator = $n2->treeNodes[0];
                        $n->varDecl = $n2;
                    } else {
                        $n->iterator = $n2;
                        $n->varDecl = null;
                    }

                    $n->object = $this->Expression($x);
                } else {
                    $n->setup = $n2 ? $n2 : null;
                    $this->tokenizer->mustMatch(TokenizerInterface::OP_SEMICOLON);
                    $n->condition = $this->tokenizer->peek(
                    ) == TokenizerInterface::OP_SEMICOLON ? null : $this->Expression($x);
                    $this->tokenizer->mustMatch(TokenizerInterface::OP_SEMICOLON);
                    $n->update = $this->tokenizer->peek(
                    ) == TokenizerInterface::OP_RIGHT_BRACKET ? null : $this->Expression($x);
                }

                $this->tokenizer->mustMatch(TokenizerInterface::OP_RIGHT_BRACKET);
                $n->body = $this->nest($x, $n);
                return $n;

            case TokenizerInterface::KEYWORD_WHILE:
                $n = new JSNode($this->tokenizer);
                $n->isLoop = true;
                $n->condition = $this->ParenExpression($x);
                $n->body = $this->nest($x, $n);
                return $n;

            case TokenizerInterface::KEYWORD_DO:
                $n = new JSNode($this->tokenizer);
                $n->isLoop = true;
                $n->body = $this->nest($x, $n, TokenizerInterface::KEYWORD_WHILE);
                $n->condition = $this->ParenExpression($x);
                if (!$x->ecmaStrictMode) {
                    // <script language="JavaScript"> (without version hints) may need
                    // automatic semicolon insertion without a newline after do-while.
                    // See http://bugzilla.mozilla.org/show_bug.cgi?id=238945.
                    $this->tokenizer->match(TokenizerInterface::OP_SEMICOLON);
                    return $n;
                }
                break;

            case TokenizerInterface::KEYWORD_BREAK:
            case TokenizerInterface::KEYWORD_CONTINUE:
                $n = new JSNode($this->tokenizer);

                if ($this->tokenizer->peekOnSameLine() == TokenizerInterface::TOKEN_IDENTIFIER) {
                    $this->tokenizer->get();
                    $n->label = $this->tokenizer->currentToken()->value;
                }

                $ss = $x->stmtStack;
                $i = count($ss);
                $label = $n->label;
                if ($label) {
                    do {
                        if (--$i < 0)
                            throw $this->tokenizer->newSyntaxError('Label not found');
                    } while ($ss[$i]->label != $label);
                } else {
                    do {
                        if (--$i < 0)
                            throw $this->tokenizer->newSyntaxError('Invalid ' . $tt);
                    } while (!$ss[$i]->isLoop && ($tt != TokenizerInterface::KEYWORD_BREAK || $ss[$i]->type != TokenizerInterface::KEYWORD_SWITCH));
                }

                $n->target = $ss[$i];
                break;

            case TokenizerInterface::KEYWORD_TRY:
                $n = new JSNode($this->tokenizer);
                $n->tryBlock = $this->Block($x);
                $n->catchClauses = array();

                while ($this->tokenizer->match(TokenizerInterface::KEYWORD_CATCH)) {
                    $n2 = new JSNode($this->tokenizer);
                    $this->tokenizer->mustMatch(TokenizerInterface::OP_LEFT_BRACKET);
                    $n2->varName = $this->tokenizer->mustMatch(TokenizerInterface::TOKEN_IDENTIFIER)->value;

                    if ($this->tokenizer->match(TokenizerInterface::KEYWORD_IF)) {
                        if ($x->ecmaStrictMode)
                            throw $this->tokenizer->newSyntaxError('Illegal catch guard');

                        if (count($n->catchClauses) && !end($n->catchClauses)->guard)
                            throw $this->tokenizer->newSyntaxError('Guarded catch after unguarded');

                        $n2->guard = $this->Expression($x);
                    } else {
                        $n2->guard = null;
                    }

                    $this->tokenizer->mustMatch(TokenizerInterface::OP_RIGHT_BRACKET);
                    $n2->block = $this->Block($x);
                    array_push($n->catchClauses, $n2);
                }

                if ($this->tokenizer->match(TokenizerInterface::KEYWORD_FINALLY))
                    $n->finallyBlock = $this->Block($x);

                if (!count($n->catchClauses) && !$n->finallyBlock)
                    throw $this->tokenizer->newSyntaxError('Invalid try statement');
                return $n;

            case TokenizerInterface::KEYWORD_CATCH:
            case TokenizerInterface::KEYWORD_FINALLY:
                throw $this->tokenizer->newSyntaxError($tt + ' without preceding try');

            case TokenizerInterface::KEYWORD_THROW:
                $n = new JSNode($this->tokenizer);
                $n->value = $this->Expression($x);
                break;

            case TokenizerInterface::KEYWORD_RETURN:
                if (!$x->inFunction)
                    throw $this->tokenizer->newSyntaxError('Invalid return');

                $n = new JSNode($this->tokenizer);
                $tt = $this->tokenizer->peekOnSameLine();
                if ($tt != TokenizerInterface::TOKEN_END && $tt != TokenizerInterface::TOKEN_NEWLINE && $tt != TokenizerInterface::OP_SEMICOLON && $tt != TokenizerInterface::OP_RIGHT_CURLY)
                    $n->value = $this->Expression($x);
                else
                    $n->value = null;
                break;

            case TokenizerInterface::KEYWORD_WITH:
                $n = new JSNode($this->tokenizer);
                $n->object = $this->ParenExpression($x);
                $n->body = $this->nest($x, $n);
                return $n;

            case TokenizerInterface::KEYWORD_VAR:
            case TokenizerInterface::KEYWORD_CONST:
                $n = $this->Variables($x);
                break;

            case TokenizerInterface::TOKEN_CONDCOMMENT_START:
            case TokenizerInterface::TOKEN_CONDCOMMENT_END:
                $n = new JSNode($this->tokenizer);
                return $n;

            case TokenizerInterface::KEYWORD_DEBUGGER:
                $n = new JSNode($this->tokenizer);
                break;

            case TokenizerInterface::TOKEN_NEWLINE:
            case TokenizerInterface::OP_SEMICOLON:
                $n = new JSNode($this->tokenizer, TokenizerInterface::OP_SEMICOLON);
                $n->expression = null;
                return $n;

            default:
                if ($tt == TokenizerInterface::TOKEN_IDENTIFIER) {
                    $this->tokenizer->scanOperand = false;
                    $tt = $this->tokenizer->peek();
                    $this->tokenizer->scanOperand = true;
                    if ($tt == TokenizerInterface::OP_COLON) {
                        $label = $this->tokenizer->currentToken()->value;
                        $ss = $x->stmtStack;
                        for ($i = count($ss) - 1; $i >= 0; --$i) {
                            if ($ss[$i]->label == $label)
                                throw $this->tokenizer->newSyntaxError('Duplicate label');
                        }

                        $this->tokenizer->get();
                        $n = new JSNode($this->tokenizer, JS_LABEL);
                        $n->label = $label;
                        $n->statement = $this->nest($x, $n);

                        return $n;
                    }
                }

                $n = new JSNode($this->tokenizer, TokenizerInterface::OP_SEMICOLON);
                $this->tokenizer->unget();
                $n->expression = $this->Expression($x);
                $n->end = $n->expression->end;
                break;
        }

        if ($this->tokenizer->lineno == $this->tokenizer->currentToken()->lineno) {
            $tt = $this->tokenizer->peekOnSameLine();
            if ($tt != TokenizerInterface::TOKEN_END && $tt != TokenizerInterface::TOKEN_NEWLINE && $tt != TokenizerInterface::OP_SEMICOLON && $tt != TokenizerInterface::OP_RIGHT_CURLY)
                throw $this->tokenizer->newSyntaxError('Missing ; before statement');
        }

        $this->tokenizer->match(TokenizerInterface::OP_SEMICOLON);

        return $n;
    }

    private function FunctionDefinition($x, $requireName, $functionForm)
    {
        $f = new JSNode($this->tokenizer);

        if ($f->type != TokenizerInterface::KEYWORD_FUNCTION)
            $f->type = ($f->value == 'get') ? JS_GETTER : JS_SETTER;

        if ($this->tokenizer->match(TokenizerInterface::TOKEN_IDENTIFIER))
            $f->name = $this->tokenizer->currentToken()->value;
        elseif ($requireName)
            throw $this->tokenizer->newSyntaxError('Missing function identifier');

        $this->tokenizer->mustMatch(TokenizerInterface::OP_LEFT_BRACKET);
        $f->params = array();

        while (($tt = $this->tokenizer->get()) != TokenizerInterface::OP_RIGHT_BRACKET) {
            if ($tt != TokenizerInterface::TOKEN_IDENTIFIER)
                throw $this->tokenizer->newSyntaxError('Missing formal parameter');

            array_push($f->params, $this->tokenizer->currentToken()->value);

            if ($this->tokenizer->peek() != TokenizerInterface::OP_RIGHT_BRACKET)
                $this->tokenizer->mustMatch(TokenizerInterface::OP_COMMA);
        }

        $this->tokenizer->mustMatch(TokenizerInterface::OP_LEFT_CURLY);

        $x2 = new JSCompilerContext(true);
        $f->body = $this->Script($x2);

        $this->tokenizer->mustMatch(TokenizerInterface::OP_RIGHT_CURLY);
        $f->end = $this->tokenizer->currentToken()->end;

        $f->functionForm = $functionForm;
        if ($functionForm == DECLARED_FORM)
            array_push($x->funDecls, $f);

        return $f;
    }

    private function Variables($x)
    {
        $n = new JSNode($this->tokenizer);

        do {
            $this->tokenizer->mustMatch(TokenizerInterface::TOKEN_IDENTIFIER);

            $n2 = new JSNode($this->tokenizer);
            $n2->name = $n2->value;

            if ($this->tokenizer->match(TokenizerInterface::OP_ASSIGN)) {
                if ($this->tokenizer->currentToken()->assignOp)
                    throw $this->tokenizer->newSyntaxError('Invalid variable initialization');

                $n2->initializer = $this->Expression($x, TokenizerInterface::OP_COMMA);
            }

            $n2->readOnly = $n->type == TokenizerInterface::KEYWORD_CONST;

            $n->addNode($n2);
            array_push($x->varDecls, $n2);
        } while ($this->tokenizer->match(TokenizerInterface::OP_COMMA));

        return $n;
    }

    private function Expression($x, $stop = false)
    {
        $operators = array();
        $operands = array();
        $n = false;

        $bl = $x->bracketLevel;
        $cl = $x->curlyLevel;
        $pl = $x->parenLevel;
        $hl = $x->hookLevel;

        while (($tt = $this->tokenizer->get()) != TokenizerInterface::TOKEN_END) {
            if ($tt == $stop &&
                $x->bracketLevel == $bl &&
                $x->curlyLevel == $cl &&
                $x->parenLevel == $pl &&
                $x->hookLevel == $hl
            ) {
                // Stop only if tt matches the optional stop parameter, and that
                // token is not quoted by some kind of bracket.
                break;
            }

            switch ($tt) {
                case TokenizerInterface::OP_SEMICOLON:
                    // NB: cannot be empty, Statement handled that.
                    break 2;

                case TokenizerInterface::OP_HOOK:
                    if ($this->tokenizer->scanOperand)
                        break 2;

                    while (!empty($operators) &&
                        $this->opPrecedence[end($operators)->type] > $this->opPrecedence[$tt]
                    )
                        $this->reduce($operators, $operands);

                    array_push($operators, new JSNode($this->tokenizer));

                    ++$x->hookLevel;
                    $this->tokenizer->scanOperand = true;
                    $n = $this->Expression($x);

                    if (!$this->tokenizer->match(TokenizerInterface::OP_COLON))
                        break 2;

                    --$x->hookLevel;
                    array_push($operands, $n);
                    break;

                case TokenizerInterface::OP_COLON:
                    if ($x->hookLevel)
                        break 2;

                    throw $this->tokenizer->newSyntaxError('Invalid label');
                    break;

                case TokenizerInterface::OP_ASSIGN:
                    if ($this->tokenizer->scanOperand)
                        break 2;

                    // Use >, not >=, for right-associative ASSIGN
                    while (!empty($operators) &&
                        $this->opPrecedence[end($operators)->type] > $this->opPrecedence[$tt]
                    )
                        $this->reduce($operators, $operands);

                    array_push($operators, new JSNode($this->tokenizer));
                    end($operands)->assignOp = $this->tokenizer->currentToken()->assignOp;
                    $this->tokenizer->scanOperand = true;
                    break;

                case TokenizerInterface::KEYWORD_IN:
                    // An in operator should not be parsed if we're parsing the head of
                    // a for (...) loop, unless it is in the then part of a conditional
                    // expression, or parenthesized somehow.
                    if ($x->inForLoopInit && !$x->hookLevel &&
                        !$x->bracketLevel && !$x->curlyLevel &&
                        !$x->parenLevel
                    )
                        break 2;
                // FALL THROUGH
                case TokenizerInterface::OP_COMMA:
                    // A comma operator should not be parsed if we're parsing the then part
                    // of a conditional expression unless it's parenthesized somehow.
                    if ($tt == TokenizerInterface::OP_COMMA && $x->hookLevel &&
                        !$x->bracketLevel && !$x->curlyLevel &&
                        !$x->parenLevel
                    )
                        break 2;
                // Treat comma as left-associative so reduce can fold left-heavy
                // COMMA trees into a single array.
                // FALL THROUGH
                case TokenizerInterface::OP_OR:
                case TokenizerInterface::OP_AND:
                case TokenizerInterface::OP_BITWISE_OR:
                case TokenizerInterface::OP_BITWISE_XOR:
                case TokenizerInterface::OP_BITWISE_AND:
                case TokenizerInterface::OP_EQ:
                case TokenizerInterface::OP_NE:
                case TokenizerInterface::OP_STRICT_EQ:
                case TokenizerInterface::OP_STRICT_NE:
                case TokenizerInterface::OP_LT:
                case TokenizerInterface::OP_LE:
                case TokenizerInterface::OP_GE:
                case TokenizerInterface::OP_GT:
                case TokenizerInterface::KEYWORD_INSTANCEOF:
                case TokenizerInterface::OP_LSH:
                case TokenizerInterface::OP_RSH:
                case TokenizerInterface::OP_URSH:
                case TokenizerInterface::OP_PLUS:
                case TokenizerInterface::OP_MINUS:
                case TokenizerInterface::OP_MUL:
                case TokenizerInterface::OP_DIV:
                case TokenizerInterface::OP_MOD:
                case TokenizerInterface::OP_DOT:
                    if ($this->tokenizer->scanOperand)
                        break 2;

                    while (!empty($operators) &&
                        $this->opPrecedence[end($operators)->type] >= $this->opPrecedence[$tt]
                    )
                        $this->reduce($operators, $operands);

                    if ($tt == TokenizerInterface::OP_DOT) {
                        $this->tokenizer->mustMatch(TokenizerInterface::TOKEN_IDENTIFIER);
                        array_push(
                            $operands,
                            new JSNode($this->tokenizer, TokenizerInterface::OP_DOT, array_pop(
                                $operands
                            ), new JSNode($this->tokenizer))
                        );
                    } else {
                        array_push($operators, new JSNode($this->tokenizer));
                        $this->tokenizer->scanOperand = true;
                    }
                    break;

                case TokenizerInterface::KEYWORD_DELETE:
                case TokenizerInterface::KEYWORD_VOID:
                case TokenizerInterface::KEYWORD_TYPEOF:
                case TokenizerInterface::OP_NOT:
                case TokenizerInterface::OP_BITWISE_NOT:
                case TokenizerInterface::OP_UNARY_PLUS:
                case TokenizerInterface::OP_UNARY_MINUS:
                case TokenizerInterface::KEYWORD_NEW:
                    if (!$this->tokenizer->scanOperand)
                        break 2;

                    array_push($operators, new JSNode($this->tokenizer));
                    break;

                case TokenizerInterface::OP_INCREMENT:
                case TokenizerInterface::OP_DECREMENT:
                    if ($this->tokenizer->scanOperand) {
                        array_push($operators, new JSNode($this->tokenizer)); // prefix increment or decrement
                    } else {
                        // Don't cross a line boundary for postfix {in,de}crement.
                        $t = $this->tokenizer->tokens[($this->tokenizer->tokenIndex + $this->tokenizer->lookahead - 1) & 3];
                        if ($t && $t->lineno != $this->tokenizer->lineno)
                            break 2;

                        if (!empty($operators)) {
                            // Use >, not >=, so postfix has higher precedence than prefix.
                            while ($this->opPrecedence[end($operators)->type] > $this->opPrecedence[$tt])
                                $this->reduce($operators, $operands);
                        }

                        $n = new JSNode($this->tokenizer, $tt, array_pop($operands));
                        $n->postfix = true;
                        array_push($operands, $n);
                    }
                    break;

                case TokenizerInterface::KEYWORD_FUNCTION:
                    if (!$this->tokenizer->scanOperand)
                        break 2;

                    array_push($operands, $this->FunctionDefinition($x, false, EXPRESSED_FORM));
                    $this->tokenizer->scanOperand = false;
                    break;

                case TokenizerInterface::KEYWORD_NULL:
                case TokenizerInterface::KEYWORD_THIS:
                case TokenizerInterface::KEYWORD_TRUE:
                case TokenizerInterface::KEYWORD_FALSE:
                case TokenizerInterface::TOKEN_IDENTIFIER:
                case TokenizerInterface::TOKEN_NUMBER:
                case TokenizerInterface::TOKEN_STRING:
                case TokenizerInterface::TOKEN_REGEXP:
                    if (!$this->tokenizer->scanOperand)
                        break 2;

                    array_push($operands, new JSNode($this->tokenizer));
                    $this->tokenizer->scanOperand = false;
                    break;

                case TokenizerInterface::TOKEN_CONDCOMMENT_START:
                case TokenizerInterface::TOKEN_CONDCOMMENT_END:
                    if ($this->tokenizer->scanOperand)
                        array_push($operators, new JSNode($this->tokenizer));
                    else
                        array_push($operands, new JSNode($this->tokenizer));
                    break;

                case TokenizerInterface::OP_LEFT_SQUARE_BRACKET:
                    if ($this->tokenizer->scanOperand) {
                        // Array initialiser.  Parse using recursive descent, as the
                        // sub-grammar here is not an operator grammar.
                        $n = new JSNode($this->tokenizer, JS_ARRAY_INIT);
                        while (($tt = $this->tokenizer->peek()) != TokenizerInterface::OP_RIGHT_SQUARE_BRACKET) {
                            if ($tt == TokenizerInterface::OP_COMMA) {
                                $this->tokenizer->get();
                                $n->addNode(null);
                                continue;
                            }

                            $n->addNode($this->Expression($x, TokenizerInterface::OP_COMMA));
                            if (!$this->tokenizer->match(TokenizerInterface::OP_COMMA))
                                break;
                        }

                        $this->tokenizer->mustMatch(TokenizerInterface::OP_RIGHT_SQUARE_BRACKET);
                        array_push($operands, $n);
                        $this->tokenizer->scanOperand = false;
                    } else {
                        // Property indexing operator.
                        array_push($operators, new JSNode($this->tokenizer, JS_INDEX));
                        $this->tokenizer->scanOperand = true;
                        ++$x->bracketLevel;
                    }
                    break;

                case TokenizerInterface::OP_RIGHT_SQUARE_BRACKET:
                    if ($this->tokenizer->scanOperand || $x->bracketLevel == $bl)
                        break 2;

                    while ($this->reduce($operators, $operands)->type != JS_INDEX)
                        continue;

                    --$x->bracketLevel;
                    break;

                case TokenizerInterface::OP_LEFT_CURLY:
                    if (!$this->tokenizer->scanOperand)
                        break 2;

                    // Object initialiser.  As for array initialisers (see above),
                    // parse using recursive descent.
                    ++$x->curlyLevel;
                    $n = new JSNode($this->tokenizer, JS_OBJECT_INIT);
                    while (!$this->tokenizer->match(TokenizerInterface::OP_RIGHT_CURLY)) {
                        do {
                            $tt = $this->tokenizer->get();
                            $tv = $this->tokenizer->currentToken()->value;
                            if (($tv == 'get' || $tv == 'set') && $this->tokenizer->peek(
                                ) == TokenizerInterface::TOKEN_IDENTIFIER
                            ) {
                                if ($x->ecmaStrictMode)
                                    throw $this->tokenizer->newSyntaxError('Illegal property accessor');

                                $n->addNode($this->FunctionDefinition($x, true, EXPRESSED_FORM));
                            } else {
                                switch ($tt) {
                                    case TokenizerInterface::TOKEN_IDENTIFIER:
                                    case TokenizerInterface::TOKEN_NUMBER:
                                    case TokenizerInterface::TOKEN_STRING:
                                        $id = new JSNode($this->tokenizer);
                                        break;

                                    case TokenizerInterface::OP_RIGHT_CURLY:
                                        if ($x->ecmaStrictMode)
                                            throw $this->tokenizer->newSyntaxError('Illegal trailing ,');
                                        break 3;

                                    default:
                                        throw $this->tokenizer->newSyntaxError('Invalid property name');
                                }

                                $this->tokenizer->mustMatch(TokenizerInterface::OP_COLON);
                                $n->addNode(
                                    new JSNode($this->tokenizer, JS_PROPERTY_INIT, $id, $this->Expression(
                                        $x,
                                        TokenizerInterface::OP_COMMA
                                    ))
                                );
                            }
                        } while ($this->tokenizer->match(TokenizerInterface::OP_COMMA));

                        $this->tokenizer->mustMatch(TokenizerInterface::OP_RIGHT_CURLY);
                        break;
                    }

                    array_push($operands, $n);
                    $this->tokenizer->scanOperand = false;
                    --$x->curlyLevel;
                    break;

                case TokenizerInterface::OP_RIGHT_CURLY:
                    if (!$this->tokenizer->scanOperand && $x->curlyLevel != $cl)
                        throw new Exception('PANIC: right curly botch');
                    break 2;

                case TokenizerInterface::OP_LEFT_BRACKET:
                    if ($this->tokenizer->scanOperand) {
                        array_push($operators, new JSNode($this->tokenizer, JS_GROUP));
                    } else {
                        while (!empty($operators) &&
                            $this->opPrecedence[end(
                                $operators
                            )->type] > $this->opPrecedence[TokenizerInterface::KEYWORD_NEW]
                        )
                            $this->reduce($operators, $operands);

                        // Handle () now, to regularize the n-ary case for n > 0.
                        // We must set scanOperand in case there are arguments and
                        // the first one is a regexp or unary+/-.
                        $n = end($operators);
                        $this->tokenizer->scanOperand = true;
                        if ($this->tokenizer->match(TokenizerInterface::OP_RIGHT_BRACKET)) {
                            if ($n && $n->type == TokenizerInterface::KEYWORD_NEW) {
                                array_pop($operators);
                                $n->addNode(array_pop($operands));
                            } else {
                                $n = new JSNode($this->tokenizer, JS_CALL, array_pop(
                                    $operands
                                ), new JSNode($this->tokenizer, JS_LIST));
                            }

                            array_push($operands, $n);
                            $this->tokenizer->scanOperand = false;
                            break;
                        }

                        if ($n && $n->type == TokenizerInterface::KEYWORD_NEW)
                            $n->type = JS_NEW_WITH_ARGS;
                        else
                            array_push($operators, new JSNode($this->tokenizer, JS_CALL));
                    }

                    ++$x->parenLevel;
                    break;

                case TokenizerInterface::OP_RIGHT_BRACKET:
                    if ($this->tokenizer->scanOperand || $x->parenLevel == $pl)
                        break 2;

                    while (($tt = $this->reduce($operators, $operands)->type) != JS_GROUP &&
                        $tt != JS_CALL && $tt != JS_NEW_WITH_ARGS
                    ) {
                        continue;
                    }

                    if ($tt != JS_GROUP) {
                        $n = end($operands);
                        if ($n->treeNodes[1]->type != TokenizerInterface::OP_COMMA)
                            $n->treeNodes[1] = new JSNode($this->tokenizer, JS_LIST, $n->treeNodes[1]);
                        else
                            $n->treeNodes[1]->type = JS_LIST;
                    }

                    --$x->parenLevel;
                    break;

                // Automatic semicolon insertion means we may scan across a newline
                // and into the beginning of another statement.  If so, break out of
                // the while loop and let the t.scanOperand logic handle errors.
                default:
                    break 2;
            }
        }

        if ($x->hookLevel != $hl)
            throw $this->tokenizer->newSyntaxError('Missing : in conditional expression');

        if ($x->parenLevel != $pl)
            throw $this->tokenizer->newSyntaxError('Missing ) in parenthetical');

        if ($x->bracketLevel != $bl)
            throw $this->tokenizer->newSyntaxError('Missing ] in index expression');

        if ($this->tokenizer->scanOperand)
            throw $this->tokenizer->newSyntaxError('Missing operand');

        // Resume default mode, scanning for operands, not operators.
        $this->tokenizer->scanOperand = true;
        $this->tokenizer->unget();

        while (count($operators))
            $this->reduce($operators, $operands);

        return array_pop($operands);
    }

    private function ParenExpression($x)
    {
        $this->tokenizer->mustMatch(TokenizerInterface::OP_LEFT_BRACKET);
        $n = $this->Expression($x);
        $this->tokenizer->mustMatch(TokenizerInterface::OP_RIGHT_BRACKET);

        return $n;
    }

    // Statement stack and nested statement handler.
    private function nest($x, $node, $end = false)
    {
        array_push($x->stmtStack, $node);
        $n = $this->statement($x);
        array_pop($x->stmtStack);

        if ($end)
            $this->tokenizer->mustMatch($end);

        return $n;
    }

    private function reduce(&$operators, &$operands)
    {
        $n = array_pop($operators);
        $op = $n->type;
        $arity = $this->opArity[$op];
        $c = count($operands);
        if ($arity == -2) {
            // Flatten left-associative trees
            if ($c >= 2) {
                $left = $operands[$c - 2];
                if ($left->type == $op) {
                    $right = array_pop($operands);
                    $left->addNode($right);
                    return $left;
                }
            }
            $arity = 2;
        }

        // Always use push to add operands to n, to update start and end
        $a = array_splice($operands, $c - $arity);
        for ($i = 0; $i < $arity; $i++)
            $n->addNode($a[$i]);

        // Include closing bracket or postfix operator in [start,end]
        $te = $this->tokenizer->currentToken()->end;
        if ($n->end < $te)
            $n->end = $te;

        array_push($operands, $n);

        return $n;
    }*/
}
