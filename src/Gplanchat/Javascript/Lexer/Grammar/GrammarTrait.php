<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 17/03/14
 * Time: 11:04
 */

namespace Gplanchat\Javascript\Lexer\Grammar;

use Gplanchat\Tokenizer\Token;

trait GrammarTrait
{
    /** @var int */
    protected $type = null;

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     * @return $this
     */
    protected function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @param int $level
     * @return string
     */
    public function dump($level = 0)
    {
        $separatorPosition = strrpos(static::class, '\\');
        $namespace = substr(static::class, 0, $separatorPosition);
        $class = substr(static::class, $separatorPosition + 1);
        $padding = str_pad('', $level * 2, ' ');

        return sprintf("\n%1\$s%2\$s [%3\$s]", $padding, $class, $namespace);
    }
}
