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

namespace Gplanchat\Javascript\Tokenizer;

use Gplanchat\PHPUnit\Constraint\TokenList;
use Gplanchat\Tokenizer\TokenizerInterface as BaseTokenizerInterface;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Util_InvalidArgumentHelper as InvalidArgumentHelper;

/**
 * Class AbstractTokenizerTest
 * @package Gplanchat\Javascript\Tokenizer
 */
abstract class AbstractTokenizerTest
    extends TestCase
{
    /**
     * Asserts the token list of a tokenizer.
     *
     * @param array  $expectedTokenList
     * @param mixed  $tokenizer
     * @param string $message
     * @throws
     */
    public function assertTokenList($expectedTokenList, $tokenizer, $message = null)
    {
        if (!is_array($expectedTokenList)) {
            throw InvalidArgumentHelper::factory(1, 'array');
        }

        if (!$tokenizer instanceof BaseTokenizerInterface) {
            throw InvalidArgumentHelper::factory(2, TokenizerInterface::class);
        }

        self::assertThat(
            $tokenizer,
            new TokenList($expectedTokenList),
            $message
        );
    }

    /**
     * @param string $globExpression
     * @return array
     */
    protected function baseDataProvider($globExpression)
    {
        $data = [];

        $oldPath = getcwd();
        chdir(__DIR__);
        foreach (new \GlobIterator($globExpression) as $file) {
            /** @var \SplFileInfo $file */
            $baseName = $file->getBasename('.js');
            if ($baseName === $file->getFilename()) {
                continue;
            }

            $data[] = [
                include $file->getPath() . DIRECTORY_SEPARATOR . $baseName . '.tok',
                file_get_contents($file->getPathname())
            ];
        }
        chdir($oldPath);

        return $data;
    }
}
