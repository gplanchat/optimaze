<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 27/03/14
 * Time: 19:39
 */

namespace Gplanchat\Javascript\Lexer\Rule;

use Gplanchat\Javascript\Lexer\Exception;
use Gplanchat\Javascript\Lexer\Grammar;
use Gplanchat\Javascript\Lexer\Rule;
use Gplanchat\Javascript\Tokenizer\TokenizerInterface;

/**
 * ArgumentList:
 *     empty
 *     AssignmentExpression
 *     AssignmentExpression , ArgumentList
 */
class ArgumentListTest
    extends AbstractRuleTest
{
    /**
     *
     */
    public function testEmptyList()
    {
        $tokens = [
            [TokenizerInterface::OP_LEFT_BRACKET,  ')', 0, 1, 1],
            [TokenizerInterface::TOKEN_END,       null, 1, 1, 1]
        ];

        $ruleServices = [
            ['AssignmentExpression', Rule\AssignmentExpression::class]
        ];

        $grammarServices = [
            ['ArgumentList', Grammar\ArgumentList::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\ArgumentList::class))
        ;

        $rule = new ArgumentList($this->getRuleServiceManagerMock($ruleServices),
            $this->getRuleServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testOneArgumentList()
    {
        $tokens = [
            // TokenizerInterface::TOKEN_IDENTIFIER
            [TokenizerInterface::OP_LEFT_BRACKET,  ')', 1, 2, 1],
            [TokenizerInterface::TOKEN_END,       null, 2, 2, 1]
        ];

        $ruleServices = [
            ['AssignmentExpression', Rule\AssignmentExpression::class]
        ];

        $grammarServices = [
            ['ArgumentList',  Grammar\ArgumentList::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\ArgumentList::class))
        ;

        $rule = new ArgumentList($this->getRuleServiceManagerMock($ruleServices),
            $this->getRuleServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }

    /**
     *
     */
    public function testMultipleArgumentList()
    {
        $tokens = [
            // TokenizerInterface::TOKEN_IDENTIFIER
            [TokenizerInterface::OP_COMMA,         ',', 0, 1, 1],
            // TokenizerInterface::TOKEN_IDENTIFIER
            [TokenizerInterface::OP_LEFT_BRACKET,  ')', 1, 2, 1],
            [TokenizerInterface::TOKEN_END,       null, 2, 2, 1]
        ];

        $ruleServices = [
            ['AssignmentExpression', Rule\AssignmentExpression::class]
        ];

        $grammarServices = [
            ['ArgumentList',  Grammar\ArgumentList::class],
            ['CommaOperator', Grammar\CommaOperator::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\ArgumentList::class))
        ;

        $rule = new ArgumentList($this->getRuleServiceManagerMock($ruleServices),
            $this->getRuleServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }
}
