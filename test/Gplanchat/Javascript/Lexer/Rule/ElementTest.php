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
 * Element:
 *     function Identifier ( empty ) { StatementList }
 *     function Identifier ( ParameterList ) { StatementList }
 *     Statement
 */
class ElementTest
    extends AbstractRuleTest
{
    /**
     *
     */
    public function testFunctionWithEmptyParameterList()
    {
        $tokens = [
            [TokenizerInterface::KEYWORD_FUNCTION, 'function', null],
            [TokenizerInterface::TOKEN_END,              null, null]
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
            $this->getGrammarServiceManagerMock($grammarServices));

        $rule->parse($root, $this->getTokenizerMock($tokens));
    }
}
