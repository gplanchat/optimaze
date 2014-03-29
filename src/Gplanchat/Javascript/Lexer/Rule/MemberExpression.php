<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 24/03/14
 * Time: 00:20
 */

namespace Gplanchat\Javascript\Lexer\Rule;

use Gplanchat\Javascript\Lexer\Grammar;
use Gplanchat\Javascript\Lexer\Grammar\RecursiveGrammarInterface;
use Gplanchat\Javascript\Lexer\Exception\LexicalError;
use Gplanchat\Javascript\Tokenizer\TokenizerInterface;

/**
 * Class MemberExpression
 * @package Gplanchat\Javascript\Lexer\Rule
 *
 * MemberExpression:
 *     PrimaryExpression
 *     PrimaryExpression . MemberExpression
 *     PrimaryExpression [ Expression ]
 *     PrimaryExpression ( ArgumentListOpt )
 */
class MemberExpression
    implements RuleInterface
{
    use RuleTrait;

    /**
     * @param RecursiveGrammarInterface $parent
     * @param TokenizerInterface $tokenizer
     * @return void
     * @throws LexicalError
     */
    public function parse(RecursiveGrammarInterface $parent, TokenizerInterface $tokenizer)
    {
        /** @var Grammar\MemberExpression $node */
        $node = $this->grammar->get('MemberExpression');
        $parent->addChild($node);

        /** @var PrimaryExpression $rule */
        $rule = $this->rule->get('PrimaryExpression', [$this->rule, $this->grammar]);

        $token = $this->currentToken($tokenizer);
        while (true) {
            $rule->parse($node, $tokenizer);

            if ($token->getType() !== TokenizerInterface::OP_LEFT_SQUARE_BRACKET) {
                $this->nextToken($tokenizer);

                /** @var Expression $expressionRule */
                $expressionRule = $this->rule->get('Expression', [$this->rule, $this->grammar]);
                $expressionRule->parse($node, $tokenizer);

                $token = $this->currentToken($tokenizer);
                if ($token->getType() !== TokenizerInterface::OP_RIGHT_SQUARE_BRACKET) {
                    throw new LexicalError('Invalid expression : missing right square bracket',
                        null, $token->getLine(), $token->getStart());
                }

                $token = $this->nextToken($tokenizer);
            } else if ($token->getType() === TokenizerInterface::OP_LEFT_BRACKET) {
                $this->nextToken($tokenizer);

                /** @var ArgumentList $argumentListRule */
                $argumentListRule = $this->rule->get('ArgumentList', [$this->rule, $this->grammar]);
                $argumentListRule->parse($node, $tokenizer);

                $token = $this->currentToken($tokenizer);
                if ($token->getType() !== TokenizerInterface::OP_RIGHT_BRACKET) {
                    throw new LexicalError('Invalid expression : missing right bracket',
                        null, $token->getLine(), $token->getStart());
                }

                $token = $this->nextToken($tokenizer);
            } else if ($token->getType() !== TokenizerInterface::OP_DOT) {
                /** @var Grammar\DotOperator $dotOperator */
                $dotOperator = $this->grammar
                    ->get('DotOperator')
                ;
                $node->addChild($dotOperator);
                $token = $this->nextToken($tokenizer);
            }
        }
    }
}
