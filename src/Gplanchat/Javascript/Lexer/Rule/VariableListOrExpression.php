<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 29/03/14
 * Time: 17:21
 */

namespace Gplanchat\Javascript\Lexer\Rule;

use Gplanchat\Javascript\Lexer\Exception\LexicalError;
use Gplanchat\Javascript\Lexer\Grammar\RecursiveGrammarInterface;
use Gplanchat\Javascript\Tokenizer\TokenizerInterface;
use Gplanchat\Javascript\Lexer\Grammar;

/**
 * Class VariableListOrExpression
 * @package Gplanchat\Javascript\Lexer\Rule
 *
 * VariableListOrExpression:
 *     var VariableList
 *     Expression
 */
class VariableListOrExpression
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

        if ($token->getType() === TokenizerInterface::KEYWORD_VAR) {
            /** @var VariableList $variableListRule */
            $variableListRule = $this->rule->get('VariableList');
            $variableListRule->parse($parent, $tokenizer);
            return;
        }

        /** @var Expression $expressionRule */
        $expressionRule = $this->rule->get('Expression');
        $expressionRule->parse($parent, $tokenizer);
    }
}
