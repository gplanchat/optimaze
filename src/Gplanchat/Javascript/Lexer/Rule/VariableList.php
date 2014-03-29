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
 * Variable:
 *     Identifier
 *     Identifier = AssignmentExpression
 */
class VariableList
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
        /** @var Grammar\VariableList $node */
        $node = $this->grammar->get('VariableList');
        $parent->addChild($node);

        while (true) {
            /** @var Grammar\Variable $variable */
            $variable = $this->grammar->get('Variable');
            $node->addChild($variable);

            $token = $this->currentToken($tokenizer);
            if ($token->getType() !== TokenizerInterface::TOKEN_IDENTIFIER) {
                throw new LexicalError('Invalid expression : missing identifier',
                    null, $token->getLine(), $token->getStart());
            }

            /** @var Grammar\Identifier $identifier */
            $identifier = $this->grammar->get('Identifier', [$token->getValue()]);
            $variable->addChild($identifier);

            $token = $this->nextToken($tokenizer);
            if ($token->getType() === TokenizerInterface::OP_EQ) {
                $this->nextToken($tokenizer);

                /** @var AssignmentExpression $assignmentExpressionRule */
                $assignmentExpressionRule = $this->rule->get('AssignmentExpression');
                $assignmentExpressionRule->parse($variable, $tokenizer);
            }

            if ($token->getType() === TokenizerInterface::OP_COMMA) {
                break;
            }
        }
    }
}
