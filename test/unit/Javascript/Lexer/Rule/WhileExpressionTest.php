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
class WhileExpressionTest
    extends AbstractRuleTest
{
    /**
     *
     */
    public function testWhileStatement()
    {
        $tokens = [
            [TokenizerInterface::KEYWORD_WHILE,     'while', null],
            [TokenizerInterface::OP_LEFT_BRACKET,       '(', null],
            [TokenizerInterface::KEYWORD_TRUE,       'true', null],
            [TokenizerInterface::OP_RIGHT_BRACKET,      ')', null],
            [TokenizerInterface::OP_SEMICOLON,          ';', null],
            [TokenizerInterface::TOKEN_END,            null, null]
        ];

        $ruleServices = [
            ['Condition',  new Rule\TokenSeeker(TokenizerInterface::OP_RIGHT_BRACKET, ')', true)]
        ];

        $grammarServices = [
            ['Statement', Grammar\Statement::class],
            ['WhileKeyword', Grammar\WhileKeyword::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\Statement::class))
        ;

        $rule = new WhileExpression($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $accumulator = new Accumulator($rule, $root);
        $accumulator($this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testWhileWithBreakStatement()
    {
        $tokens = [
            [TokenizerInterface::KEYWORD_WHILE,     'while', null],
            [TokenizerInterface::OP_LEFT_BRACKET,       '(', null],
            [TokenizerInterface::KEYWORD_TRUE,       'true', null],
            [TokenizerInterface::OP_RIGHT_BRACKET,      ')', null],
            [TokenizerInterface::KEYWORD_BREAK,     'break', null],
            [TokenizerInterface::OP_SEMICOLON,          ';', null],
            [TokenizerInterface::TOKEN_END,            null, null]
        ];

        $ruleServices = [
            ['Condition',  new Rule\TokenSeeker(TokenizerInterface::OP_RIGHT_BRACKET, ')', true)]
        ];

        $grammarServices = [
            ['Statement', Grammar\Statement::class],
            ['WhileKeyword', Grammar\WhileKeyword::class],
            ['BreakKeyword', Grammar\BreakKeyword::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\Statement::class))
        ;

        $rule = new Statement($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $accumulator = new Accumulator($rule, $root);
        $accumulator($this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testWhileWithContinueStatement()
    {
        $tokens = [
            [TokenizerInterface::KEYWORD_WHILE,       'while', null],
            [TokenizerInterface::OP_LEFT_BRACKET,         '(', null],
            [TokenizerInterface::KEYWORD_TRUE,         'true', null],
            [TokenizerInterface::OP_RIGHT_BRACKET,        ')', null],
            [TokenizerInterface::KEYWORD_CONTINUE, 'continue', null],
            [TokenizerInterface::OP_SEMICOLON,            ';', null],
            [TokenizerInterface::TOKEN_END,              null, null]
        ];

        $ruleServices = [
            ['Condition',  new Rule\TokenSeeker(TokenizerInterface::OP_RIGHT_BRACKET, ')', true)]
        ];

        $grammarServices = [
            ['Statement', Grammar\Statement::class],
            ['WhileKeyword', Grammar\WhileKeyword::class],
            ['ContinueKeyword', Grammar\ContinueKeyword::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\Statement::class))
        ;

        $rule = new WhileExpression($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $accumulator = new Accumulator($rule, $root);
        $accumulator($this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testWhileWithBreakWithMissingSemicolonStatement()
    {
        $this->setExpectedException(Exception\LexicalError::class, RuleInterface::MESSAGE_MISSING_SEMICOLON);

        $tokens = [
            [TokenizerInterface::KEYWORD_WHILE,     'while', null],
            [TokenizerInterface::OP_LEFT_BRACKET,       '(', null],
            [TokenizerInterface::KEYWORD_TRUE,       'true', null],
            [TokenizerInterface::OP_RIGHT_BRACKET,      ')', null],
            [TokenizerInterface::KEYWORD_BREAK,     'break', null],
            [TokenizerInterface::TOKEN_END,            null, null]
        ];

        $ruleServices = [
            ['Condition',  new Rule\TokenSeeker(TokenizerInterface::OP_RIGHT_BRACKET, ')', true)]
        ];

        $grammarServices = [
            ['Statement', Grammar\Statement::class],
            ['WhileKeyword', Grammar\WhileKeyword::class],
            ['BreakKeyword', Grammar\BreakKeyword::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\Statement::class))
        ;

        $rule = new WhileExpression($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $accumulator = new Accumulator($rule, $root);
        $accumulator($this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testWhileWithContinueWithMissingSemicolonStatement()
    {
        $this->setExpectedException(Exception\LexicalError::class, RuleInterface::MESSAGE_MISSING_SEMICOLON);

        $tokens = [
            [TokenizerInterface::KEYWORD_WHILE,       'while', null],
            [TokenizerInterface::OP_LEFT_BRACKET,         '(', null],
            [TokenizerInterface::KEYWORD_TRUE,         'true', null],
            [TokenizerInterface::OP_RIGHT_BRACKET,        ')', null],
            [TokenizerInterface::KEYWORD_CONTINUE, 'continue', null],
            [TokenizerInterface::TOKEN_END,              null, null]
        ];

        $ruleServices = [
            ['Condition',  new Rule\TokenSeeker(TokenizerInterface::OP_RIGHT_BRACKET, ')', true)]
        ];

        $grammarServices = [
            ['Statement', Grammar\Statement::class],
            ['WhileKeyword', Grammar\WhileKeyword::class],
            ['ContinueKeyword', Grammar\ContinueKeyword::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\Statement::class))
        ;

        $rule = new WhileExpression($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $accumulator = new Accumulator($rule, $root);
        $accumulator($this->getTokenizerMock($tokens));
    }
}
