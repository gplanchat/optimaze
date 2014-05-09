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
use Gplanchat\ServiceManager\ServiceManagerInterface;
use Gplanchat\Tokenizer\TokenizerInterface;

trait RuleTrait
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
    public function __construct(ServiceManagerInterface $ruleServiceManager, ServiceManagerInterface $grammarServiceManager)
    {
        $this->rule = $ruleServiceManager;
        $this->grammar = $grammarServiceManager;
    }

    /**
     * @param RecursiveGrammarInterface $parent
     * @param TokenizerInterface $tokenizer
     * @return \Generator|null
     */
    abstract public function run(RecursiveGrammarInterface $parent, TokenizerInterface $tokenizer);

    /**
     * @see self::run
     * @param RecursiveGrammarInterface $parent
     * @param TokenizerInterface $tokenizer
     * @return \Generator|null
     */
    public function __invoke(RecursiveGrammarInterface $parent, TokenizerInterface $tokenizer)
    {
        return $this->run($parent, $tokenizer);
    }
}
