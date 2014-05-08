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
 * IfExpression:
 *     if Condition Statement
 *     if Condition Statement else Statement
 */
class IfExpressionTest
    extends AbstractRuleTest
{
    /**
     *
     */
    public function testIfControlStructure()
    {
        $tokens = [
            [TokenizerInterface::KEYWORD_IF,           'if', null],
            [TokenizerInterface::OP_LEFT_BRACKET,       '(', null],
            [TokenizerInterface::KEYWORD_TRUE,       'true', null],
            [TokenizerInterface::OP_RIGHT_BRACKET,      ')', null],
            [TokenizerInterface::OP_SEMICOLON,          ';', null],
            [TokenizerInterface::TOKEN_END,            null, null]
        ];

        $ruleServices = [
            ['Condition', new Rule\TokenSeeker(TokenizerInterface::OP_RIGHT_BRACKET, ')', true)],
            ['Statement', new Rule\TokenSeeker(TokenizerInterface::OP_SEMICOLON, ';', true)]
        ];

        $grammarServices = [
            ['ConditionChain', Grammar\ConditionChain::class],
            ['IfKeyword',      Grammar\IfKeyword::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\ConditionChain::class))
        ;

        $rule = new IfExpression($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $accumulator = new Accumulator($rule, $root);
        $accumulator($this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testIfControlStructureWithReturnStatement()
    {
        $tokens = [
            [TokenizerInterface::KEYWORD_IF,           'if', null],
            [TokenizerInterface::OP_LEFT_BRACKET,       '(', null],
            [TokenizerInterface::KEYWORD_TRUE,       'true', null],
            [TokenizerInterface::OP_RIGHT_BRACKET,      ')', null],
            [TokenizerInterface::KEYWORD_RETURN,   'return', null],
            [TokenizerInterface::OP_SEMICOLON,          ';', null],
            [TokenizerInterface::TOKEN_END,            null, null]
        ];

        $ruleServices = [
            ['Condition', new Rule\TokenSeeker(TokenizerInterface::OP_RIGHT_BRACKET, ')', true)],
            ['Statement', new Rule\TokenSeeker(TokenizerInterface::OP_SEMICOLON, ';', true)]
        ];

        $grammarServices = [
            ['ConditionChain', Grammar\ConditionChain::class],
            ['IfKeyword',      Grammar\IfKeyword::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\ConditionChain::class))
        ;

        $rule = new IfExpression($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $accumulator = new Accumulator($rule, $root);
        $accumulator($this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testIfElseControlStructure()
    {
        $tokens = [
            [TokenizerInterface::KEYWORD_IF,           'if', null],
            [TokenizerInterface::OP_LEFT_BRACKET,       '(', null],
            [TokenizerInterface::KEYWORD_TRUE,       'true', null],
            [TokenizerInterface::OP_RIGHT_BRACKET,      ')', null],
            [TokenizerInterface::OP_SEMICOLON,          ';', null],
            [TokenizerInterface::KEYWORD_ELSE,       'else', null],
            [TokenizerInterface::OP_SEMICOLON,          ';', null],
            [TokenizerInterface::TOKEN_END,            null, null]
        ];

        $ruleServices = [
            ['Condition', new Rule\TokenSeeker(TokenizerInterface::OP_RIGHT_BRACKET, ')', true)],
            ['Statement', new Rule\TokenSeeker(TokenizerInterface::OP_SEMICOLON, ';', true)]
        ];

        $grammarServices = [
            ['ConditionChain', Grammar\ConditionChain::class],
            ['IfKeyword',      Grammar\IfKeyword::class],
            ['ElseKeyword',    Grammar\ElseKeyword::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\ConditionChain::class))
        ;

        $rule = new IfExpression($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $accumulator = new Accumulator($rule, $root);
        $accumulator($this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testIfElseControlStructureWithReturnStatement()
    {
        $tokens = [
            [TokenizerInterface::KEYWORD_IF,           'if', null],
            [TokenizerInterface::OP_LEFT_BRACKET,       '(', null],
            [TokenizerInterface::KEYWORD_TRUE,       'true', null],
            [TokenizerInterface::OP_RIGHT_BRACKET,      ')', null],
            [TokenizerInterface::KEYWORD_RETURN,   'return', null],
            [TokenizerInterface::OP_SEMICOLON,          ';', null],
            [TokenizerInterface::KEYWORD_ELSE,       'else', null],
            [TokenizerInterface::KEYWORD_RETURN,   'return', null],
            [TokenizerInterface::OP_SEMICOLON,          ';', null],
            [TokenizerInterface::TOKEN_END,            null, null]
        ];

        $ruleServices = [
            ['Condition', new Rule\TokenSeeker(TokenizerInterface::OP_RIGHT_BRACKET, ')', true)],
            ['Statement', new Rule\TokenSeeker(TokenizerInterface::OP_SEMICOLON, ';', true)]
        ];

        $grammarServices = [
            ['ConditionChain', Grammar\ConditionChain::class],
            ['IfKeyword', Grammar\IfKeyword::class],
            ['ElseKeyword', Grammar\ElseKeyword::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\ConditionChain::class))
        ;

        $rule = new IfExpression($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $accumulator = new Accumulator($rule, $root);
        $accumulator($this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testChainedIfElseControlStructure()
    {
        $tokens = [
            [TokenizerInterface::KEYWORD_IF,           'if', null],
            [TokenizerInterface::OP_LEFT_BRACKET,       '(', null],
            [TokenizerInterface::KEYWORD_TRUE,       'true', null],
            [TokenizerInterface::OP_RIGHT_BRACKET,      ')', null],
            [TokenizerInterface::OP_SEMICOLON,          ';', null],
            [TokenizerInterface::KEYWORD_ELSE,       'else', null],
            [TokenizerInterface::KEYWORD_IF,           'if', null],
            [TokenizerInterface::OP_LEFT_BRACKET,       '(', null],
            [TokenizerInterface::KEYWORD_TRUE,       'false', null],
            [TokenizerInterface::OP_RIGHT_BRACKET,      ')', null],
            [TokenizerInterface::OP_SEMICOLON,          ';', null],
            [TokenizerInterface::KEYWORD_ELSE,       'else', null],
            [TokenizerInterface::OP_SEMICOLON,          ';', null],
            [TokenizerInterface::TOKEN_END,            null, null]
        ];

        $ruleServices = [
            ['Condition', new Rule\TokenSeeker(TokenizerInterface::OP_RIGHT_BRACKET, ')', true)],
            ['Statement', new Rule\TokenSeeker(TokenizerInterface::OP_SEMICOLON, ';', true)]
        ];

        $grammarServices = [
            ['ConditionChain', Grammar\ConditionChain::class],
            ['IfKeyword', Grammar\IfKeyword::class],
            ['IfKeyword', Grammar\IfKeyword::class],
            ['ElseKeyword', Grammar\ElseKeyword::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\ConditionChain::class))
        ;

        $rule = new IfExpression($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $accumulator = new Accumulator($rule, $root);
        $accumulator($this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testChainedIfElseControlStructureWithReturnStatement()
    {
        $tokens = [
            [TokenizerInterface::KEYWORD_IF,           'if', null],
            [TokenizerInterface::OP_LEFT_BRACKET,       '(', null],
            [TokenizerInterface::KEYWORD_TRUE,       'true', null],
            [TokenizerInterface::OP_RIGHT_BRACKET,      ')', null],
            [TokenizerInterface::KEYWORD_RETURN,   'return', null],
            [TokenizerInterface::OP_SEMICOLON,          ';', null],
            [TokenizerInterface::KEYWORD_ELSE,       'else', null],
            [TokenizerInterface::KEYWORD_IF,           'if', null],
            [TokenizerInterface::OP_LEFT_BRACKET,       '(', null],
            [TokenizerInterface::KEYWORD_TRUE,       'false', null],
            [TokenizerInterface::OP_RIGHT_BRACKET,      ')', null],
            [TokenizerInterface::KEYWORD_RETURN,   'return', null],
            [TokenizerInterface::OP_SEMICOLON,          ';', null],
            [TokenizerInterface::KEYWORD_ELSE,       'else', null],
            [TokenizerInterface::KEYWORD_RETURN,   'return', null],
            [TokenizerInterface::OP_SEMICOLON,          ';', null],
            [TokenizerInterface::TOKEN_END,            null, null]
        ];

        $ruleServices = [
            ['Condition',  new Rule\TokenSeeker(TokenizerInterface::OP_RIGHT_BRACKET, ')', true)],
            ['Statement', new Rule\TokenSeeker(TokenizerInterface::OP_SEMICOLON, ';', true)]
        ];

        $grammarServices = [
            ['ConditionChain', Grammar\ConditionChain::class],
            ['IfKeyword', Grammar\IfKeyword::class],
            ['IfKeyword', Grammar\ElseKeyword::class],
            ['ElseKeyword', Grammar\ElseKeyword::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\ConditionChain::class))
        ;

        $rule = new IfExpression($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $accumulator = new Accumulator($rule, $root);
        $accumulator($this->getTokenizerMock($tokens));
    }
}
