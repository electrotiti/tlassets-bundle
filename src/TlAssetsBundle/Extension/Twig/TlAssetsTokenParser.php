<?php

namespace TlAssetsBundle\Extension\Twig;
use TlAssetsBundle\Extension\Twig\TlAssetsManager;

class TlAssetsTokenParser extends \Twig_TokenParser
{
    private $tag;
    private $manager;

    public function __construct(TlAssetsManager $manager, $tag)
    {
        $this->manager = $manager;
        $this->tag = $tag;
    }

    public function parse(\Twig_Token $token)
    {
        $inputs = array();
        $attrs = array('filters'=>array());

        $stream = $this->parser->getStream();
        while (!$stream->test(\Twig_Token::BLOCK_END_TYPE)) {
            if ($stream->test(\Twig_Token::STRING_TYPE)) {
                $inputs[] = $stream->next()->getValue();
            } elseif ($stream->test(\Twig_Token::NAME_TYPE, 'filter')) {
                $stream->next();
                $stream->expect(\Twig_Token::OPERATOR_TYPE, '=');
                $attrs['filters'] = array_merge($attrs['filters'], array_filter(array_map('trim', explode(',', $stream->expect(\Twig_Token::STRING_TYPE)->getValue()))));
            } elseif ($stream->test(\Twig_Token::NAME_TYPE, 'output')) {
                $stream->next();
                $stream->expect(\Twig_Token::OPERATOR_TYPE, '=');
                $attributes['output'] = $stream->expect(\Twig_Token::STRING_TYPE)->getValue();
            }
            else {
                $token = $stream->getCurrent();
                throw new \Twig_Error_Syntax(sprintf('Unexpected token "%s" of value "%s"', \Twig_Token::typeToEnglish($token->getType(), $token->getLine()), $token->getValue()), $token->getLine());
            }
        }

        $stream->expect(\Twig_Token::BLOCK_END_TYPE);
        $content = $this->parser->subparse(array($this, 'testEndTag'), true);
        $stream->expect(\Twig_Token::BLOCK_END_TYPE);

        $this->manager->load($inputs, $attrs, $this->getTag());
        $params['assets'] = $this->manager->getAssetsPath();
        return new TlAssetsNode(array('content'=>$content), $params, $token->getLine(), $this->getTag());
    }

    public function testEndTag(\Twig_Token $token)
    {
        return $token->test(array('end'.$this->getTag()));
    }

    public function getTag()
    {
        return $this->tag;
    }
}