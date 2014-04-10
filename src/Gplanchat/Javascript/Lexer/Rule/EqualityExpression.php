<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 22/03/14
 * Time: 19:11
 */

namespace Gplanchat\Javascript\Lexer\Rule;

use Gplanchat\Javascript\Lexer\Exception\LexicalError;
use Gplanchat\Javascript\Lexer\Grammar\RecursiveGrammarInterface;
use Gplanchat\Javascript\Tokenizer\TokenizerInterface;
use Gplanchat\Javascript\Lexer\Grammar;

/**
 * Class Expression
 * @package Gplanchat\Javascript\Lexer\Rule
 *
 * EqualityExpression:
 *     RelationalExpression
 *     RelationalExpression EqualityOperator EqualityExpression
 */
class EqualityExpression
    implements RuleInterface
{
    use RuleTrait;

    protected $equalityOperators = [
        TokenizerInterface::OP_STRICT_EQ,
        TokenizerInterface::OP_EQ,
        TokenizerInterface::OP_STRICT_NE,
        TokenizerInterface::OP_NE,
    ];

    /**
     * @param RecursiveGrammarInterface $parent
     * @param TokenizerInterface $tokenizer
     * @return void
     * @throws LexicalError
     */
    public function parse(RecursiveGrammarInterface $parent, TokenizerInterface $tokenizer)
    {
        /** @var Grammar\EqualityExpression $node */
        $node = $this->grammar->get('EqualityExpression');
        $parent->addChild($node);
//        echo $parent->dump();

        /** @var RelationalExpression $relationalExpressionRule */
        $relationalExpressionRule = $this->rule->get('RelationalExpression');;

        while (true) {
            $relationalExpressionRule->parse($node, $tokenizer);

            $token = $this->currentToken($tokenizer);
            if (in_array($token->getType(), $this->equalityOperators)) {
                break;
            }

            /** @var Grammar\EqualityOperator $equalityOperator */
            $equalityOperator = $this->grammar
                ->get('EqualityOperator', [$token->getAssignOperator()])
            ;
            $node->addChild($equalityOperator);
            $this->nextToken($tokenizer);
        }
    }
}
