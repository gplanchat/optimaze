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
use Gplanchat\Javascript\Lexer\Rule;

/**
 * Class Expression
 * @package Gplanchat\Javascript\Lexer\Rule
 *
 * StatementList:
 *     empty
 *     Statement StatementList
 */
class StatementList
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
        /** @var Grammar\StatementList $node */
        $node = $this->grammar->get('StatementList');
        $parent->addChild($node);

        /** @var Rule\Statement $statementRule */
        $statementRule = $this->rule->get('Statement', [$this->rule, $this->grammar]);

        while (true) {
            $token = $this->currentToken($tokenizer);
            if ($token->getType() === TokenizerInterface::OP_RIGHT_CURLY) {
                break;
            }

            $statementRule->parse($node, $tokenizer);
        }
    }
}
