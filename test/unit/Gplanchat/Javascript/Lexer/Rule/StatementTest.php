<?php
/**
 * This file is part of gplanchat/php-javascript-tokenizer
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Grégory Planchat <g.planchat@gmail.com>
 * @licence GNU General Public Licence
 * @package Gplanchat\Javascript\Lexer
 */

namespace Gplanchat\Javascript\Lexer\Rule;

use Gplanchat\Javascript\Lexer\Exception;
use Gplanchat\Lexer\Grammar;
use Gplanchat\Javascript\Lexer\Rule;
use Gplanchat\Javascript\Tokenizer\TokenizerInterface;

/**
 * Statement:
 *     ;
 *     if Condition Statement
 *     if Condition Statement else Statement
 *     while Condition Statement
 *     for ( ; Expression ; Expression ) Statement
 *     for ( VariableListOrExpression ; Expression ; Expression ) Statement
 *     for ( VariableListOrExpression in Expression ) Statement
 *     break ;
 *     continue ;
 *     with ( Expression ) Statement
 *     return Expression ;
 *     { StatementList }
 *     VariableListOrExpression ;
 */
class StatementTest
    extends AbstractRuleTest
{
    /**
     *
     */
    public function testSemicolon()
    {
        $tokens = [
            [TokenizerInterface::OP_SEMICOLON,  ';', null],
            [TokenizerInterface::TOKEN_END,    null, null]
        ];

        $ruleServices = [
        ];

        $grammarServices = [
            ['Statement', Grammar\Statement::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\Statement::class))
        ;

        $rule = new Statement($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }

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
            ['Condition', new Rule\TokenSeeker(TokenizerInterface::OP_RIGHT_BRACKET, ')', true)]
        ];

        $grammarServices = [
            ['Statement',      Grammar\Statement::class],
            ['ConditionChain', Grammar\ConditionChain::class],
            ['IfKeyword',      Grammar\IfKeyword::class],
            ['Statement',      Grammar\Statement::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\Statement::class))
        ;

        $rule = new Statement($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
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
            ['Expression', new Rule\TokenNullSeeker()]
        ];

        $grammarServices = [
            ['Statement',     Grammar\Statement::class],
            ['ConditionChain', Grammar\ConditionChain::class],
            ['IfKeyword',     Grammar\IfKeyword::class],
            ['Statement',     Grammar\Statement::class],
            ['ReturnKeyword', Grammar\ReturnKeyword::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\Statement::class))
        ;

        $rule = new Statement($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testIfControlStructureWithReturnStatementWithMissingSemicolon()
    {
        $this->setExpectedException(Exception\LexicalError::class, RuleInterface::MESSAGE_MISSING_SEMICOLON);

        $tokens = [
            [TokenizerInterface::KEYWORD_IF,               'if', null],
            [TokenizerInterface::OP_LEFT_BRACKET,           '(', null],
            [TokenizerInterface::KEYWORD_TRUE,           'true', null],
            [TokenizerInterface::OP_RIGHT_BRACKET,          ')', null],
            [TokenizerInterface::KEYWORD_RETURN,       'return', null],
            [TokenizerInterface::TOKEN_END,                null, null]
        ];

        $ruleServices = [
            ['Condition', new Rule\TokenSeeker(TokenizerInterface::OP_RIGHT_BRACKET, ')', true)],
            ['Expression', new Rule\TokenNullSeeker()]
        ];

        $grammarServices = [
            ['Statement',     Grammar\Statement::class],
            ['ConditionChain', Grammar\ConditionChain::class],
            ['IfKeyword',     Grammar\IfKeyword::class],
            ['Statement',     Grammar\Statement::class],
            ['ReturnKeyword', Grammar\ReturnKeyword::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\Statement::class))
        ;

        $rule = new Statement($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
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
            ['Condition', new Rule\TokenSeeker(TokenizerInterface::OP_RIGHT_BRACKET, ')', true)]
        ];

        $grammarServices = [
            ['Statement', Grammar\Statement::class],
            ['ConditionChain', Grammar\ConditionChain::class],
            ['IfKeyword', Grammar\IfKeyword::class],
            ['Statement', Grammar\Statement::class],
            ['ElseKeyword', Grammar\ElseKeyword::class],
            ['Statement', Grammar\Statement::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\Statement::class))
        ;

        $rule = new Statement($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
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
            ['Condition',  new Rule\TokenSeeker(TokenizerInterface::OP_RIGHT_BRACKET, ')', true)],
            ['Expression', new Rule\TokenNullSeeker()]
        ];

        $grammarServices = [
            ['Statement', Grammar\Statement::class],
            ['ConditionChain', Grammar\ConditionChain::class],
            ['IfKeyword', Grammar\IfKeyword::class],
            ['Statement', Grammar\Statement::class],
            ['ReturnKeyword', Grammar\ReturnKeyword::class],
            ['ElseKeyword', Grammar\ElseKeyword::class],
            ['Statement', Grammar\Statement::class],
            ['ReturnKeyword', Grammar\ReturnKeyword::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\Statement::class))
        ;

        $rule = new Statement($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
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
            ['Condition', new Rule\TokenSeeker(TokenizerInterface::OP_RIGHT_BRACKET, ')', true)]
        ];

        $grammarServices = [
            ['Statement', Grammar\Statement::class],
            ['ConditionChain', Grammar\ConditionChain::class],
            ['IfKeyword', Grammar\IfKeyword::class],
            ['Statement', Grammar\Statement::class],
            ['IfKeyword', Grammar\IfKeyword::class],
            ['Statement', Grammar\Statement::class],
            ['ElseKeyword', Grammar\ElseKeyword::class],
            ['Statement', Grammar\Statement::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\Statement::class))
        ;

        $rule = new Statement($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
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
            ['Expression', new Rule\TokenNullSeeker()]
        ];

        $grammarServices = [
            ['Statement', Grammar\Statement::class],
            ['ConditionChain', Grammar\ConditionChain::class],
            ['IfKeyword', Grammar\IfKeyword::class],
            ['Statement', Grammar\Statement::class],
            ['ReturnKeyword', Grammar\ReturnKeyword::class],
            ['IfKeyword', Grammar\ElseKeyword::class],
            ['Statement', Grammar\Statement::class],
            ['ReturnKeyword', Grammar\ReturnKeyword::class],
            ['ElseKeyword', Grammar\ElseKeyword::class],
            ['Statement', Grammar\Statement::class],
            ['ReturnKeyword', Grammar\ReturnKeyword::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\Statement::class))
        ;

        $rule = new Statement($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }

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

        $rule = new Statement($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
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

        $rule->parse($root, $this->getTokenizerMock($tokens));
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

        $rule = new Statement($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
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

        $rule = new Statement($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
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

        $rule = new Statement($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }

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
            ['Statement', Grammar\Statement::class],
            ['ForKeyword', Grammar\ForKeyword::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\Statement::class))
        ;

        $rule = new Statement($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
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
            ['Statement', Grammar\Statement::class],
            ['ForKeyword', Grammar\ForKeyword::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\Statement::class))
        ;

        $rule = new Statement($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
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
            ['Statement', Grammar\Statement::class],
            ['ForKeyword', Grammar\ForKeyword::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\Statement::class))
        ;

        $rule = new Statement($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
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
            ['Statement', Grammar\Statement::class],
            ['ForKeyword', Grammar\ForKeyword::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\Statement::class))
        ;

        $rule = new Statement($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
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
            ['Statement', Grammar\Statement::class],
            ['ForKeyword', Grammar\ForKeyword::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\Statement::class))
        ;

        $rule = new Statement($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
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
            ['Statement', Grammar\Statement::class],
            ['ForKeyword', Grammar\ForKeyword::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\Statement::class))
        ;

        $rule = new Statement($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
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
            ['Statement', Grammar\Statement::class],
            ['ForKeyword', Grammar\ForKeyword::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\Statement::class))
        ;

        $rule = new Statement($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
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
            ['Statement', Grammar\Statement::class],
            ['ForKeyword', Grammar\ForKeyword::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\Statement::class))
        ;

        $rule = new Statement($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
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
            ['Statement', Grammar\Statement::class],
            ['ForKeyword', Grammar\ForKeyword::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\Statement::class))
        ;

        $rule = new Statement($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testWithStatement()
    {
        $tokens = [
            [TokenizerInterface::KEYWORD_WITH,           'with', null],
            [TokenizerInterface::OP_LEFT_BRACKET,         '(', null],
            [TokenizerInterface::TOKEN_IDENTIFIER,        'a', null],
            [TokenizerInterface::OP_RIGHT_BRACKET,        ')', null],
            [TokenizerInterface::OP_SEMICOLON,            ';', null],
            [TokenizerInterface::TOKEN_END,              null, null]
        ];

        $ruleServices = [
            ['Expression', new Rule\TokenSeeker(TokenizerInterface::TOKEN_IDENTIFIER, 'a', true)]
        ];

        $grammarServices = [
            ['Statement', Grammar\Statement::class],
            ['WithKeyword', Grammar\WithKeyword::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\Statement::class))
        ;

        $rule = new Statement($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testWithMalformedNotationStatementWithMissingLeftBracket()
    {
        $this->setExpectedException(Exception\LexicalError::class, RuleInterface::MESSAGE_MISSING_LEFT_BRACKET);

        $tokens = [
            [TokenizerInterface::KEYWORD_WITH,           'with', null],
            [TokenizerInterface::TOKEN_IDENTIFIER,        'a', null],
            [TokenizerInterface::OP_RIGHT_BRACKET,        ')', null],
            [TokenizerInterface::OP_SEMICOLON,            ';', null],
            [TokenizerInterface::TOKEN_END,              null, null]
        ];

        $ruleServices = [
        ];

        $grammarServices = [
            ['Statement', Grammar\Statement::class],
            ['WithKeyword', Grammar\WithKeyword::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\Statement::class))
        ;

        $rule = new Statement($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testWithMalformedNotationStatementWithMissingRightBracket()
    {
        $this->setExpectedException(Exception\LexicalError::class, RuleInterface::MESSAGE_MISSING_RIGHT_BRACKET);

        $tokens = [
            [TokenizerInterface::KEYWORD_WITH,           'with', null],
            [TokenizerInterface::OP_LEFT_BRACKET,         '(', null],
            [TokenizerInterface::TOKEN_IDENTIFIER,        'a', null],
            [TokenizerInterface::OP_SEMICOLON,            ';', null],
            [TokenizerInterface::TOKEN_END,              null, null]
        ];

        $ruleServices = [
            ['Expression', new Rule\TokenSeeker(TokenizerInterface::TOKEN_IDENTIFIER, 'a', true)]
        ];

        $grammarServices = [
            ['Statement', Grammar\Statement::class],
            ['WithKeyword', Grammar\WithKeyword::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\Statement::class))
        ;

        $rule = new Statement($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testCoumpoundStatement()
    {
        $tokens = [
            [TokenizerInterface::OP_LEFT_CURLY,           '{', null],
            [TokenizerInterface::TOKEN_IDENTIFIER,        'a', null],
            [TokenizerInterface::OP_RIGHT_CURLY,          '}', null],
            [TokenizerInterface::TOKEN_END,              null, null]
        ];

        $ruleServices = [
            ['StatementListRule', new Rule\TokenSeeker(TokenizerInterface::TOKEN_IDENTIFIER, 'a', true)]
        ];

        $grammarServices = [
            ['Statement', Grammar\Statement::class],
            ['CompoundStatement', Grammar\CompoundStatement::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\Statement::class))
        ;

        $rule = new Statement($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testCoumpoundStatementWithMissingRightCurlyBrace()
    {
        $this->setExpectedException(Exception\LexicalError::class, RuleInterface::MESSAGE_MISSING_RIGHT_CURLY_BRACE);

        $tokens = [
            [TokenizerInterface::OP_LEFT_CURLY,           '{', null],
            [TokenizerInterface::TOKEN_IDENTIFIER,        'a', null],
            [TokenizerInterface::TOKEN_END,              null, null]
        ];

        $ruleServices = [
            ['StatementListRule', new Rule\TokenSeeker(TokenizerInterface::TOKEN_IDENTIFIER, 'a', true)]
        ];

        $grammarServices = [
            ['Statement', Grammar\Statement::class],
            ['CompoundStatement', Grammar\CompoundStatement::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\Statement::class))
        ;

        $rule = new Statement($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testVariableListOrExpressionFallback()
    {
        $tokens = [
            [TokenizerInterface::TOKEN_IDENTIFIER,  'a', null],
            [TokenizerInterface::OP_SEMICOLON,      ';', null],
            [TokenizerInterface::TOKEN_END,        null, null]
        ];

        $ruleServices = [
            ['VariableListOrExpression', new Rule\TokenSeeker(TokenizerInterface::TOKEN_IDENTIFIER, 'a', true)]
        ];

        $grammarServices = [
            ['Statement', Grammar\Statement::class],
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\Statement::class))
        ;

        $rule = new Statement($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testReturnStatementWithoutExpression()
    {
        $tokens = [
            [TokenizerInterface::KEYWORD_RETURN,   'return', null],
            [TokenizerInterface::OP_SEMICOLON,          ';', null],
            [TokenizerInterface::TOKEN_END,            null, null]
        ];

        $ruleServices = [
            ['Expression', new Rule\TokenNullSeeker()]
        ];

        $grammarServices = [
            ['Statement', Grammar\Statement::class],
            ['ReturnKeyword', Grammar\ReturnKeyword::class],
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\Statement::class))
        ;

        $rule = new Statement($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testReturnStatementWithExpression()
    {
        $tokens = [
            [TokenizerInterface::KEYWORD_RETURN,   'return', null],
            [TokenizerInterface::TOKEN_IDENTIFIER,      '$', null],
            [TokenizerInterface::OP_DOT,                '.', null],
            [TokenizerInterface::TOKEN_IDENTIFIER,     'fn', null],
            [TokenizerInterface::OP_SEMICOLON,          ';', null],
            [TokenizerInterface::TOKEN_END,            null, null]
        ];

        $ruleServices = [
            ['Expression', new Rule\TokenSeeker(TokenizerInterface::TOKEN_IDENTIFIER, 'fn', true)]
        ];

        $grammarServices = [
            ['Statement', Grammar\Statement::class],
            ['ReturnKeyword', Grammar\ReturnKeyword::class],
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\Statement::class))
        ;

        $rule = new Statement($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }
}
