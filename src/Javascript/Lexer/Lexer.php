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

namespace Gplanchat\Javascript\Lexer;

use Gplanchat\Javascript\Lexer\TokenizerNavigationAwareTrait;
use Gplanchat\Lexer\Grammar\GrammarInterface;
use Gplanchat\Javascript\Tokenizer\TokenizerInterface;
use Gplanchat\Lexer\LexerInterface;
use Gplanchat\ServiceManager\ServiceManagerInterface;
use Gplanchat\Tokenizer\TokenizerInterface as BaseTokenizerInterface;
use Gplanchat\Lexer\Grammar;

/**
 * Javascript lexer
 *
 * @package Gplanchat\Javascript\Lexer
 */
class Lexer
    implements LexerInterface
{
    use TokenizerNavigationAwareTrait;

    /**
     * @var ServiceManagerInterface
     */
    protected $rule = null;

    /**
     * @var ServiceManagerInterface
     */
    protected $grammar = null;

    /**
     * @param ServiceManagerInterface $ruleServiceManager
     * @param ServiceManagerInterface $grammarServiceManager
     */
    public function __construct(ServiceManagerInterface $ruleServiceManager = null, ServiceManagerInterface $grammarServiceManager = null)
    {
        if ($grammarServiceManager === null) {
            $this->grammar = new Grammar\ServiceManager();
        } else {
            $this->grammar = $grammarServiceManager;
        }

        if ($ruleServiceManager === null) {
            $this->rule = new Rule\ServiceManager($this->grammar);
        } else {
            $this->rule = $ruleServiceManager;
        }
    }

    /**
     * @param BaseTokenizerInterface $tokenizer
     * @param AccumulatorInterface $accumulator
     * @return GrammarInterface
     * @throws
     *
     * Program:
     *     empty
     *     Element Program
     */
    public function parse(BaseTokenizerInterface $tokenizer, AccumulatorInterface $accumulator = null)
    {
        if ($accumulator === null) {
            /** @var Grammar\Program $program */
            $program = $this->grammar->get('Program');

            /** @var Rule\Element $elementRule */
            $elementRule = $this->rule->get('Element');

            $accumulator = new Accumulator($elementRule, $program);
        }

        return $accumulator($tokenizer);
    }
}
