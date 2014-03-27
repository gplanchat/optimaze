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
use Gplanchat\Tokenizer\Token;

/**
 * Class MemberExpression
 * @package Gplanchat\Javascript\Lexer\Rule
 *
 * ArgumentList:
 *     empty
 *     AssignmentExpression
 *     AssignmentExpression , ArgumentList
 */
class ArgumentList
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

        /** @var Grammar\ArgumentList $node */
        $node = $this->grammar->get('ArgumentList');
        $parent->addChild($node);

        /** @var AssignmentExpression $rule */
        $rule = $this->rule->get('AssignmentExpression', [$this->rule, $this->grammar]);
        while (true) {
            $rule->parse($node, $tokenizer);

            $token = $this->currentToken($tokenizer);
            if ($token->getType() !== TokenizerInterface::OP_COMMA) {
                break;
            }

            /** @var Grammar\CommaOperator $commaOperator */
            $commaOperator = $this->grammar
                ->get('CommaOperator')
            ;
            $node->addChild($commaOperator);
            $this->nextToken($tokenizer);
        }
    }
}
