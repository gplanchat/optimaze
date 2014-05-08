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
            ['WhileKeyword', Grammar\WhileKeyword::class]
        ];

        $root = $this->getRootGrammarMock();
        $root->expects($this->at(0))
            ->method('addChild')
            ->with($this->isInstanceOf(Grammar\WhileKeyword::class))
        ;

        $rule = new WhileExpression($this->getRuleServiceManagerMock($ruleServices),
            $this->getGrammarServiceManagerMock($grammarServices));

        $accumulator = new Accumulator($rule, $root);
        $accumulator($this->getTokenizerMock($tokens));
    }
}
