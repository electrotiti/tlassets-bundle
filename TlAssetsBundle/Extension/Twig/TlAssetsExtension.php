<?php

namespace TlAssetsBundle\Extension\Twig;

class TlAssetsExtension extends \Twig_Extension
{

    const TAG_STYLESHEET = 'style';
    const TAG_JAVASCRIPT = 'js';

    private $manager;

    public function __construct(TlAssetsManager $manager)
    {
        $this->manager = $manager;
    }

    public function getTokenParsers()
    {
        return array(
            new TlAssetsTokenParser($this->manager, self::TAG_STYLESHEET),
            new TlAssetsTokenParser($this->manager, self::TAG_JAVASCRIPT),
        );
    }

    public function getName()
    {
        return 'tlassets';
    }
}