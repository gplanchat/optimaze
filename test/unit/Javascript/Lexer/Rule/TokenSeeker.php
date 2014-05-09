<?php
/**
 * This file is part of gplanchat/php-javascript-tokenizer
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Grégory Planchat <g.planchat@gmail.com>
 * @licence GNU General Public Licence
 * @package Gplanchat\Javascript\Lexer
 */

namespace Gplanchat\Javascript\Lexer\Rule;

use Gplanchat\Javascript\Lexer\TokenizerNavigationAwareTrait;
use Gplanchat\Lexer\Grammar\RecursiveGrammarInterface;
use Gplanchat\Javascript\Tokenizer\TokenizerInterface;
use Gplanchat\Tokenizer\Token;
use Gplanchat\Tokenizer\TokenizerInterface as BaseTokenizerInterface;

class TokenSeeker
    implements RuleInterface
{
    use RuleTrait;

    /**
     * @var int|string
     */
    protected $tokenType = null;

    /**
     * @var string
     */
    protected $expectedValue = null;

    /**
     * @var bool
     */
    protected $isGreedy = false;

    /**
     * @param int|string $tokenType
     * @param string $expectedValue
     * @param bool $isGreedy
     */
    public function __construct($tokenType, $expectedValue, $isGreedy = false)
    {
        $this->tokenType = $tokenType;
        $this->expectedValue = $expectedValue;
        $this->isGreedy = $isGreedy;
    }

    /**
     * @return string
     */
    public function getExpectedValue()
    {
        return $this->expectedValue;
    }

    /**
     * @return int|string
     */
    public function getTokenType()
    {
        return $this->tokenType;
    }

    /**
     * @return bool
     */
    public function getIsGreedy()
    {
        return $this->isGreedy;
    }

    /**
     * @param RecursiveGrammarInterface $parent
     * @param BaseTokenizerInterface $tokenizer
     * @return \Generator|null
     */
    public function run(RecursiveGrammarInterface $parent, BaseTokenizerInterface $tokenizer)
    {
        /** @var Token $token */
        $token = $this->currentToken($tokenizer);

        while (true) {
            if ($token->getType() === $this->getTokenType() &&
                $token->getValue() === $this->getExpectedValue()) {
                break;
            }

            $token = $this->nextToken($tokenizer);
        }

        if ($this->getIsGreedy() === true) {
            $this->nextToken($tokenizer);
        }
    }
}
