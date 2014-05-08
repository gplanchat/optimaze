<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 02/05/14
 * Time: 23:38
 */

namespace Gplanchat\Javascript\Lexer\Rule;

use Gplanchat\Javascript\Lexer\Accumulator;
use Gplanchat\Javascript\Lexer\Exception;
use Gplanchat\Lexer\Grammar;
use Gplanchat\Javascript\Lexer\Rule;
use Gplanchat\Javascript\Tokenizer\TokenizerInterface;

/**
 * WhileExpression:
 *     while Condition Statement
 */
class ForExpressionTest
    extends AbstractRuleTest
{
    /**
     *
     */
    public function testForTernaryNotationStatementWithInitializer()
    {
        $tokens = [
            [TokenizerInterface::KEYWORD_FOR,           'for', null],
            [TokenizerInterface::OP_LEFT_BRACKET,         '(', null],
            [TokenizerInterface::KEYWORD_VAR,           'var', null],
            [TokenizerInterface::TOKEN_IDENTIFIER,        'i', null],
            [TokenizerInterface::OP_ASSIGN,               '=', null],
            [TokenizerInterface::TOKEN_NUMBER_INTEGER,    '0', null],
            [TokenizerInterface::OP_SEMICOLON,            ';', null],
            [TokenizerInterface::KEYWORD_TRUE,         'true', null],
            [TokenizerInterface::OP_SEMICOLON,            ';', null],
            [TokenizerInterface::KEYWORD_TRUE,         'true', null],
            [TokenizerInterface::OP_RIGHT_BRACKET,        ')', null],
            [TokenizerInterface::OP_SEMICOLON,            ';', null],
            [TokenizerInterface::TOKEN_END,              null, null]
        ];

        $ruleServices = [
            ['VariableListOrExpression', new Rule\TokenSeeker(TokenizerInterface::TOKEN_NUMBER_INTEGER, '0', true)],
            ['Expression', new Rule\TokenSeeker(TokenizerInterface::KEYWORD_TRUE, 'true', true)]
        ];

        $grammarServices = [
            ['ForKeyword', Grammar\ForKeyword::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\ForKeyword::class))
        ;

        $rule = new ForExpression($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $accumulator = new Accumulator($rule, $root);
        $accumulator($this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testForTernaryNotationStatementWithoutInitializer()
    {
        $tokens = [
            [TokenizerInterface::KEYWORD_FOR,       'for', null],
            [TokenizerInterface::OP_LEFT_BRACKET,     '(', null],
            [TokenizerInterface::OP_SEMICOLON,        ';', null],
            [TokenizerInterface::KEYWORD_TRUE,     'true', null],
            [TokenizerInterface::OP_SEMICOLON,        ';', null],
            [TokenizerInterface::KEYWORD_TRUE,     'true', null],
            [TokenizerInterface::OP_RIGHT_BRACKET,    ')', null],
            [TokenizerInterface::OP_SEMICOLON,        ';', null],
            [TokenizerInterface::TOKEN_END,          null, null]
        ];

        $ruleServices = [
            ['Expression', new Rule\TokenSeekerIterator([
                new Rule\TokenSeeker(TokenizerInterface::OP_SEMICOLON, ';'),
                new Rule\TokenSeeker(TokenizerInterface::OP_RIGHT_BRACKET, ')')
            ])
            ]
        ];

        $grammarServices = [
            ['ForKeyword', Grammar\ForKeyword::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\ForKeyword::class))
        ;

        $rule = new ForExpression($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $accumulator = new Accumulator($rule, $root);
        $accumulator($this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testForTernaryNotationStatementWithoutInitializerWithoutCondition()
    {
        $tokens = [
            [TokenizerInterface::KEYWORD_FOR,       'for', null],
            [TokenizerInterface::OP_LEFT_BRACKET,     '(', null],
            [TokenizerInterface::OP_SEMICOLON,        ';', null],
            [TokenizerInterface::OP_SEMICOLON,        ';', null],
            [TokenizerInterface::KEYWORD_TRUE,     'true', null],
            [TokenizerInterface::OP_RIGHT_BRACKET,    ')', null],
            [TokenizerInterface::OP_SEMICOLON,        ';', null],
            [TokenizerInterface::TOKEN_END,          null, null]
        ];

        $ruleServices = [
            ['Expression', new Rule\TokenSeekerIterator([
                new Rule\TokenNullSeeker(),
                new Rule\TokenSeeker(TokenizerInterface::OP_RIGHT_BRACKET, ')')
            ])
            ]
        ];

        $grammarServices = [
            ['ForKeyword', Grammar\ForKeyword::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\ForKeyword::class))
        ;

        $rule = new ForExpression($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $accumulator = new Accumulator($rule, $root);
        $accumulator($this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testForTernaryNotationStatementWithoutInitializerWithoutConditionWithoutStep()
    {
        $tokens = [
            [TokenizerInterface::KEYWORD_FOR,       'for', null],
            [TokenizerInterface::OP_LEFT_BRACKET,     '(', null],
            [TokenizerInterface::OP_SEMICOLON,        ';', null],
            [TokenizerInterface::OP_SEMICOLON,        ';', null],
            [TokenizerInterface::OP_RIGHT_BRACKET,    ')', null],
            [TokenizerInterface::OP_SEMICOLON,        ';', null],
            [TokenizerInterface::TOKEN_END,          null, null]
        ];

        $ruleServices = [
            ['Expression', new Rule\TokenSeekerIterator([
                new Rule\TokenNullSeeker(),
                new Rule\TokenSeeker(TokenizerInterface::OP_RIGHT_BRACKET, ')')
            ])
            ]
        ];

        $grammarServices = [
            ['ForKeyword', Grammar\ForKeyword::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\ForKeyword::class))
        ;

        $rule = new ForExpression($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $accumulator = new Accumulator($rule, $root);
        $accumulator($this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testForUnaryNotationStatement()
    {
        $tokens = [
            [TokenizerInterface::KEYWORD_FOR,       'for', null],
            [TokenizerInterface::OP_LEFT_BRACKET,     '(', null],
            [TokenizerInterface::TOKEN_IDENTIFIER,    'a', null],
            [TokenizerInterface::KEYWORD_IN,         'in', null],
            [TokenizerInterface::TOKEN_IDENTIFIER,    'b', null],
            [TokenizerInterface::OP_SEMICOLON,        ';', null],
            [TokenizerInterface::OP_RIGHT_BRACKET,    ')', null],
            [TokenizerInterface::OP_SEMICOLON,        ';', null],
            [TokenizerInterface::TOKEN_END,          null, null]
        ];

        $ruleServices = [
            ['VariableListOrExpression', new Rule\TokenSeeker(TokenizerInterface::KEYWORD_IN, 'in')],
            ['Expression', new Rule\TokenSeeker(TokenizerInterface::OP_RIGHT_BRACKET, ')')]
        ];

        $grammarServices = [
            ['ForKeyword', Grammar\ForKeyword::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\ForKeyword::class))
        ;

        $rule = new ForExpression($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $accumulator = new Accumulator($rule, $root);
        $accumulator($this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testForTernaryNotationStatementWithMissingLeftBracket()
    {
        $this->setExpectedException(Exception\LexicalError::class, RuleInterface::MESSAGE_MISSING_LEFT_BRACKET);

        $tokens = [
            [TokenizerInterface::KEYWORD_FOR,           'for', null],
            [TokenizerInterface::KEYWORD_VAR,           'var', null],
            [TokenizerInterface::TOKEN_END,              null, null]
        ];

        $ruleServices = [
        ];

        $grammarServices = [
            ['ForKeyword', Grammar\ForKeyword::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\ForKeyword::class))
        ;

        $rule = new ForExpression($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $accumulator = new Accumulator($rule, $root);
        $accumulator($this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testForTernaryNotationStatementWithMissingRightBracket()
    {
        $this->setExpectedException(Exception\LexicalError::class, RuleInterface::MESSAGE_MISSING_RIGHT_BRACKET);

        $tokens = [
            [TokenizerInterface::KEYWORD_FOR,       'for', null],
            [TokenizerInterface::OP_LEFT_BRACKET,     '(', null],
            [TokenizerInterface::OP_SEMICOLON,        ';', null],
            [TokenizerInterface::OP_SEMICOLON,        ';', null],
            [TokenizerInterface::OP_SEMICOLON,        ';', null],
            [TokenizerInterface::TOKEN_END,          null, null]
        ];

        $ruleServices = [
            ['Expression', new Rule\TokenNullSeeker()]
        ];

        $grammarServices = [
            ['ForKeyword', Grammar\ForKeyword::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\ForKeyword::class))
        ;

        $rule = new ForExpression($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $accumulator = new Accumulator($rule, $root);
        $accumulator($this->getTokenizerMock($tokens));
    }
    /**
     *
     */
    public function testForTernaryNotationStatementWithMissingSemicolon()
    {
        $this->setExpectedException(Exception\LexicalError::class, RuleInterface::MESSAGE_MISSING_SEMICOLON);

        $tokens = [
            [TokenizerInterface::KEYWORD_FOR,       'for', null],
            [TokenizerInterface::OP_LEFT_BRACKET,     '(', null],
            [TokenizerInterface::OP_SEMICOLON,        ';', null],
            [TokenizerInterface::OP_RIGHT_BRACKET,    ')', null],
            [TokenizerInterface::OP_SEMICOLON,        ';', null],
            [TokenizerInterface::TOKEN_END,          null, null]
        ];

        $ruleServices = [
            ['Expression', new Rule\TokenSeekerIterator([
                new Rule\TokenNullSeeker(),
                new Rule\TokenSeeker(TokenizerInterface::OP_RIGHT_BRACKET, ')')
            ])
            ]
        ];

        $grammarServices = [
            ['ForKeyword', Grammar\ForKeyword::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\ForKeyword::class))
        ;

        $rule = new ForExpression($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $accumulator = new Accumulator($rule, $root);
        $accumulator($this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testForMalformedNotationStatementWithInitializer()
    {
        $this->setExpectedException(Exception\LexicalError::class, RuleInterface::MESSAGE_MISSING_SEMICOLON_OR_IN_KEYWORD);

        $tokens = [
            [TokenizerInterface::KEYWORD_FOR,           'for', null],
            [TokenizerInterface::OP_LEFT_BRACKET,         '(', null],
            [TokenizerInterface::TOKEN_IDENTIFIER,        'a', null],
            [TokenizerInterface::TOKEN_IDENTIFIER,        'b', null],
            [TokenizerInterface::OP_RIGHT_BRACKET,        ')', null],
            [TokenizerInterface::OP_SEMICOLON,            ';', null],
            [TokenizerInterface::TOKEN_END,              null, null]
        ];

        $ruleServices = [
//            ['Expression', new Rule\TokenSeeker(TokenizerInterface::TOKEN_IDENTIFIER, 'a')],
            ['VariableListOrExpression', new Rule\TokenSeeker(TokenizerInterface::TOKEN_IDENTIFIER, 'a')]
        ];

        $grammarServices = [
            ['ForKeyword', Grammar\ForKeyword::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\ForKeyword::class))
        ;

        $rule = new ForExpression($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $accumulator = new Accumulator($rule, $root);
        $accumulator($this->getTokenizerMock($tokens));
    }
}
