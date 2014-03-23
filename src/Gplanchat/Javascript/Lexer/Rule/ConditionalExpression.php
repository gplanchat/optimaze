<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 23/03/14
 * Time: 14:14
 */

namespace Gplanchat\Javascript\Lexer\Rule;

use Gplanchat\Tokenizer\Token;
use Gplanchat\Javascript\Lexer\Grammar;
use Gplanchat\Javascript\Lexer\Grammar\RecursiveGrammarInterface;
use Gplanchat\Javascript\Tokenizer\TokenizerInterface;
use Gplanchat\Javascript\Lexer\Exception\LexicalError;

/**
 * Class ConditionalExpression
 * @package Gplanchat\Javascript\Lexer\Rule
 *
 * ConditionalExpression:
 *     OrExpression
 *     OrExpression ? AssignmentExpression : AssignmentExpression
 */
class ConditionalExpression
    implements RuleInterface
{
    use RuleTrait;

    /**
     * @param Token $token
     * @return bool
     */
    public function match(Token $token)
    {
        return true;
    }

    /**
     * @param RecursiveGrammarInterface $parent
     * @param TokenizerInterface $tokenizer
     * @return void
     * @throws LexicalError
     */
    public function parse(RecursiveGrammarInterface $parent, TokenizerInterface $tokenizer)
    {
        $token = $this->currentToken($tokenizer);
        if (!$this->match($token)) {
            return;
        }

        /** @var Grammar\Expression $node */
        $node = $this->getGrammarServiceManager()->get('ConditionalExpression');
        $parent->addChild($node);

        /** @var OrExpression $orExpressionRule */
        $orExpressionRule = $this->getRuleServiceManager()->get('OrExpression');
        $orExpressionRule->parse($node, $tokenizer);

        $token = $this->currentToken($tokenizer);
        if ($token->getType() !== TokenizerInterface::OP_HOOK) {
            return;
        }

        /** @var AssignmentExpression $assignmentExpressionRule */
        $assignmentExpressionRule = $this->getRuleServiceManager()->get('AssignmentExpression');
        $assignmentExpressionRule->parse($node, $tokenizer);

        $token = $this->currentToken($tokenizer);
        if ($token->getType() !== TokenizerInterface::OP_SEMICOLON) {
            return;
        }

        $assignmentExpressionRule->parse($node, $tokenizer);

        $this->nextToken($tokenizer);
    }
}
