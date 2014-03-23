<?php
/**
 * Created by PhpStorm.
 * User: Greg
 * Date: 23/03/14
 * Time: 03:56
 */

namespace Gplanchat\Javascript\Lexer\Grammar;


class Identifier
    implements GrammarInterface
{
    use GrammarTrait;

    /**
     * @var string
     */
    protected $identifier = null;

    /**
     * @param string $identifier
     */
    public function __construct($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }
}
