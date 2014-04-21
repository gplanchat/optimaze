<?php
/**
 * PHPUnit
 *
 * Copyright (c) 2001-2014, Sebastian Bergmann <sebastian@phpunit.de>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Sebastian Bergmann nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package    PHPUnit
 * @subpackage Framework_Constraint
 * @author     Sebastian Bergmann <sebastian@phpunit.de>
 * @author     Bernhard Schussek <bschussek@2bepublished.at>
 * @copyright  2001-2014 Sebastian Bergmann <sebastian@phpunit.de>
 * @license    http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @link       http://www.phpunit.de/
 * @since      File available since Release 3.0.0
 */

namespace Gplanchat\PHPUnit\Constraint;

use Gplanchat\Tokenizer\Token;
use Gplanchat\Tokenizer\TokenizerInterface;
use Gplanchat\Javascript\Tokenizer\TokenizerInterface as JavascriptTokenizerInterface;
use PHPUnit_Framework_AssertionFailedError as AssertionFailedError;

/**
 * Class TokenList
 * @package Gplanchat\PHPUnit\Constraint
 */
class TokenList extends \PHPUnit_Framework_Constraint
{
    /**
     * @var array
     */
    protected $tokenList = null;

    /**
     * @var int
     */
    protected $tokenIndex = 0;

    /**
     * @var string
     */
    protected $errorMessage = null;

    /**
     * @param array $expectedTokenList
     * @throws
     */
    public function __construct($expectedTokenList)
    {
        parent::__construct();

        $this->tokenList = new \SplQueue();
        foreach ($expectedTokenList as $i => $tokenSpec) {
            if (!is_array($tokenSpec)) {
                throw new AssertionFailedError(sprintf('Invalid expected token list format at index %d, should be array.', $i));
            }

            if (!isset($tokenSpec[0]) || !isset($tokenSpec[1])) {
                throw new AssertionFailedError(sprintf('Invalid expected token list format at index %d.', $i));
            }

            if (isset($tokenSpec[2])) {
                $token = new Token($tokenSpec[0], $tokenSpec[1], 0, 0, 0, 0, $tokenSpec[2]);
            } else {
                $token = new Token($tokenSpec[0], $tokenSpec[1], 0, 0, 0, 0);
            }
            $this->tokenList->enqueue($token);
        }
    }

    /**
     * Evaluates the constraint for parameter $other. Returns true if the
     * constraint is met, false otherwise.
     *
     * @param  mixed $other Value or object to evaluate.
     * @return bool
     * @throws
     */
    protected function matches($other)
    {
        $this->errorMessage = null;

        if (!$other instanceof TokenizerInterface) {
            return false;
        }

        /** @var TokenizerInterface $other */
        $this->tokenList->rewind();
        $other->rewind();
        $i = 0;
        foreach ($other as $token) {
            $i++;
            if (!$this->tokenList->valid()) {
                if ($this->tokenList->count() > $i) {
                    $this->errorMessage = sprintf('Token count does not match.');
                    return false;
                }
                break;
            }

            /** @var Token $expectedToken */
            $expectedToken = $this->tokenList->current();

            /** @var Token $token */
            if ($token->getType() !== $expectedToken->getType()) {
                $re = new \ReflectionClass(JavascriptTokenizerInterface::class);
                $constants = $re->getConstants();

                $this->errorMessage = sprintf(
                    'token type should match at index %d. Was %s, expected %s.',
                    $i, array_search($token->getType(), $constants), array_search($expectedToken->getType(), $constants)
                );
                return false;
            }

            if ($token->getValue() !== $expectedToken->getValue()) {
                $this->errorMessage = sprintf(
                    'Token value does not match at index %d. Was %s, expected %s.',
                    $i, $token->getValue(), $expectedToken->getValue()
                );
                return false;
            }

            if ($token->getAssignOperator() !== $expectedToken->getAssignOperator()) {
                $this->errorMessage = sprintf(
                    'Token assign operator does not match at index %d. Was %s, expected %s.',
                    $i, $token->getAssignOperator(), $expectedToken->getAssignOperator()
                );
                return false;
            }

            $this->tokenList->next();
        }

        return true;
    }

    /**
     * Returns the description of the failure
     *
     * The beginning of failure messages is "Failed asserting that" in most
     * cases. This method should return the second part of that sentence.
     *
     * @param  mixed  $other Evaluated value or object.
     * @return string
     */
    protected function failureDescription($other)
    {
        return $this->errorMessage;
    }

    /**
     * Returns a string representation of the constraint.
     *
     * @return string
     */
    public function toString()
    {
        return implode('', $this->tokenList);
    }
}
