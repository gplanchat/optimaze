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
 * RelationalExpression:
 *     ShiftExpression
 *     RelationalExpression RelationalOperator ShiftExpression
 */
class RelationalExpression
    implements RuleInterface
{
    use RuleTrait;

    protected $relationalOperators = [
        TokenizerInterface::OP_GT,
        TokenizerInterface::OP_GE,
        TokenizerInterface::OP_LE,
        TokenizerInterface::OP_LT,
        TokenizerInterface::KEYWORD_INSTANCEOF
    ];

    /**
     * @param RecursiveGrammarInterface $parent
     * @param TokenizerInterface $tokenizer
     * @return void
     * @throws LexicalError
     */
    public function parse(RecursiveGrammarInterface $parent, TokenizerInterface $tokenizer)
    {
        /** @var Grammar\RelationalExpression $node */
        $node = $this->grammar->get('RelationalExpression');
        $parent->addChild($node);

        /** @var ShiftExpression $shiftExpressionRule */
        $shiftExpressionRule = $this->rule->get('ShiftExpression', [$this->rule, $this->grammar]);

        while (true) {
            $shiftExpressionRule->parse($node, $tokenizer);

            $token = $this->currentToken($tokenizer);
            if (!in_array($token->getType(), $this->relationalOperators)) {
                break;
            }

            /** @var Grammar\RelationalOperator $relationalOperator */
            $relationalOperator = $this->grammar
                ->get('RelationalOperator', [$token->getValue()])
            ;
            $node->addChild($relationalOperator);
            $this->nextToken($tokenizer);
        }
    }
}
