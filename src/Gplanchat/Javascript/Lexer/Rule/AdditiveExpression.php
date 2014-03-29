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
 * AdditiveExpression:
 *     MultiplicativeExpression
 *     MultiplicativeExpression + AdditiveExpression
 *     MultiplicativeExpression - AdditiveExpression
 */
class AdditiveExpression
    implements RuleInterface
{
    use RuleTrait;

    protected $additiveOperators = [
        TokenizerInterface::OP_PLUS,
        TokenizerInterface::OP_MINUS
    ];

    /**
     * @param RecursiveGrammarInterface $parent
     * @param TokenizerInterface $tokenizer
     * @return void
     * @throws LexicalError
     */
    public function parse(RecursiveGrammarInterface $parent, TokenizerInterface $tokenizer)
    {
        /** @var Grammar\AdditiveExpression $node */
        $node = $this->grammar->get('AdditiveExpression');
        $parent->addChild($node);

        /** @var MultiplicativeExpression $multiplicativeExpressionRule */
        $multiplicativeExpressionRule = $this->rule->get('MultiplicativeExpression', [$this->rule, $this->grammar]);

        while (true) {
            $multiplicativeExpressionRule->parse($node, $tokenizer);

            $token = $this->currentToken($tokenizer);
            if (!in_array($token->getType(), $this->additiveOperators)) {
                break;
            }

            /** @var Grammar\AdditiveOperator $additiveOperator */
            $additiveOperator = $this->grammar
                ->get('AdditiveOperator', [$token->getValue()])
            ;
            $node->addChild($additiveOperator);
            $this->nextToken($tokenizer);
        }
    }
}
