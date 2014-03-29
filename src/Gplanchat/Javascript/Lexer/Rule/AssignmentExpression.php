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
 * Class AssignmentExpression
 * @package Gplanchat\Javascript\Lexer\Rule
 *
 * AssignmentExpression:
 *     ConditionalExpression
 *     ConditionalExpression AssignmentOperator AssignmentExpression
 */
class AssignmentExpression
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
        $token = $this->currentToken($tokenizer);
        if ($token->getType() === TokenizerInterface::OP_LEFT_BRACKET) {
            return;
        }

        /** @var Grammar\AssignmentExpression $node */
        $node = $this->grammar->get('AssignmentExpression');
        $parent->addChild($node);

        /** @var AssignmentExpression $conditionalExpressionRule */
        $conditionalExpressionRule = $this->rule->get('ConditionalExpression', [$this->rule, $this->grammar]);

        while (true) {
            $conditionalExpressionRule->parse($node, $tokenizer);

            $token = $this->currentToken($tokenizer);
            if ($token->getType() !== TokenizerInterface::OP_ASSIGN) {
                break;
            }

            /** @var Grammar\AssignmentOperator $assignmentOperator */
            $assignmentOperator = $this->grammar
                ->get('AssignmentOperator', [$token->getAssignOperator()])
            ;
            $node->addChild($assignmentOperator);
        }
    }
}
