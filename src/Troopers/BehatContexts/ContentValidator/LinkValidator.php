<?php

namespace Troopers\BehatContexts\ContentValidator;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class LinkValidator.
 */
class LinkValidator implements ContentValidatorInterface, ContainerAwareInterface
{
    /** @var Router $router */
    private $router;

    /**
     * Sets the container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->router = $container->get('router');
    }

    /**
     * @param mixed $value
     *
     * @throws \Troopers\BehatContexts\ContentValidator\ContentValidatorException
     */
    public function supports($value)
    {
        if (!is_array($value)) {
            throw new ContentValidatorException(sprintf('To define a link you need to use an array, value given (%s)', json_encode($value)));
        } else {
            if (!isset($value['link']) && !$value['link']) {
                throw new ContentValidatorException(sprintf('To define a link you need to set "link", value given (%s)', json_encode($value)));
            }
            if (!isset($value['url']) && (!isset($value['route']) && $value['route'] && !isset($value['parameters']) && !is_array($value['parameters']))) {
                throw new ContentValidatorException(sprintf('To define a link you need to set "url" or "route" and "parameters" array, value given (%s)', json_encode($value)));
            }
        }
    }

    /**
     * @param array  $value
     * @param string $content
     *
     * @throws \InvalidArgumentException
     *
     * @return mixed
     */
    public function valid($value = [], $content = '')
    {
        $link = $value['link'];
        if (isset($value['url'])) {
            $url = $value['url'];
        } else {
            $referenceType = isset($value['referenceType']) ? $value['referenceType']*1 : 1;
            $url = $this->router->generate($value['route'], $value['parameters'], $referenceType);
        }
        $crawler = new Crawler($content);
        $mailLink = $crawler->selectLink($value['link']);
        if (!$mailLink->html()) {
            throw new \InvalidArgumentException(sprintf('Unable to find a link for "%s" link ', $link));
        }
        if (false === strpos($url, $mailLink->attr('href'))) {
            throw new \InvalidArgumentException(sprintf('Unable to match link "%s" with "%s"', $url, $mailLink->attr('href')));
        }
    }
}
