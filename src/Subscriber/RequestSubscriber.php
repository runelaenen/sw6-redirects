<?php declare(strict_types=1);

namespace RuneLaenen\Redirects\Subscriber;

use RuneLaenen\Redirects\Content\Redirect\RedirectEntity;
use Shopware\Core\Framework\Adapter\Cache\CacheCompressor;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\Event\BeforeSendRedirectResponseEvent;
use Shopware\Core\Framework\Event\BeforeSendResponseEvent;
use Shopware\Core\Framework\Util\Json;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RequestSubscriber implements EventSubscriberInterface
{
    private const TAG_KEY = 'rl_redirects_cache';

    private EntityRepository $redirectRepository;

    private EntityRepository $seoUrlRepository;

    private TagAwareAdapterInterface $cache;

    public function __construct(
        EntityRepository $redirectRepository,
        EntityRepository $seoUrlRepository,
        TagAwareAdapterInterface $cache
    ) {
        $this->redirectRepository = $redirectRepository;
        $this->seoUrlRepository = $seoUrlRepository;
        $this->cache = $cache;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeSendResponseEvent::class => 'redirectBeforeSendResponse',
            BeforeSendRedirectResponseEvent::class => 'redirectBeforeRedirectResponse',
        ];
    }

    public function redirectBeforeSendResponse(BeforeSendResponseEvent $event): void
    {
        $response = $this->handleRequestCached($event->getRequest());
        if (!$response) {
            return;
        }

        $event->setResponse($response);
    }

    public function redirectBeforeRedirectResponse(BeforeSendRedirectResponseEvent $event): void
    {
        $requestUri = trim($event->getRequest()->getPathInfo(), '/');
        $response = $this->handleRequestCached($event->getRequest(), $requestUri);

        if (!$response) {
            return;
        }

        $event->setResponse($response);
    }

    private function handleRequestCached(Request $request, ?string $requestUri = null): ?Response
    {
        $cacheKey = md5(Json::encode([
            $requestUri ?? (string) $request->get('resolved-uri'),
            $request->get('sw-storefront-url'),
            $request->getBaseUrl(),
        ]));

        $item = $this->cache->getItem($cacheKey);
        if (!$item->isHit() || $item->get() === null) {
            $response = $this->handleRequest($request, $requestUri);

            $item->expiresAfter(86400);

            $item = CacheCompressor::compress($item, $response);

            $item->tag(self::TAG_KEY);

            $this->cache->save($item);

            return $response;
        }

        return CacheCompressor::uncompress($item);
    }

    private function handleRequest(Request $request, ?string $requestUri = null): ?Response
    {
        $requestBase = $request->getPathInfo();
        if (\strpos($requestBase, '/admin') === 0) {
            return null;
        }
        if (\strpos($requestBase, '/api') === 0) {
            return null;
        }
        if (\strpos($requestBase, '/widgets') === 0) {
            return null;
        }
        if (\strpos($requestBase, '/store-api') === 0) {
            return null;
        }

        $requestUri = $requestUri ?? (string) $request->get('resolved-uri');

        $context = Context::createDefaultContext();
        $redirects = $this->seoUrlRepository->search((new Criteria())
            ->addFilter(new EqualsAnyFilter('pathInfo', [$requestUri])), $context);

        $storefrontUri = $request->get('sw-storefront-url');
        $requestBaseUrl = $request->getBaseUrl();
        $search = [];

        // if found overwrite search term with the seo route
        if ($redirects->count() !== 0) {
            foreach ($redirects as $redirect) {
                $uri = $redirect->getSeoPathInfo();
                // Search for Redirect
                $search[] = [
                    $requestBaseUrl . '/' . $uri, // relative url with shopware 6 in sub folder: /public/Ergonomic-Concrete-Cough-Machine/48314803f1244f609a2ce907bfb48f53
                    $requestBaseUrl . $uri, // relative url with shopware 6 in sub folder url is not shopware seo url: /public/test
                    $storefrontUri . $uri, // absolute url with shopware 6 in sub folder, full url with host: http://shopware-platform.local/public/test1
                    $storefrontUri . '/' . $uri, // absolute url with shopware 6 in sub folder, full url with host and slash at the end: http://shopware-platform.local/public/Freizeit-Elektro/Telefone/
                    $uri, // relative url domain configured in public folder: /Ergonomic-Concrete-Cough-Machine/48314803f1244f609a2ce907bfb48f53 or /test4
                    '/' . $uri, // absolute url domain configured in public folder: http://shopware-platform.local/Shoes-Baby/
                    \substr($uri, 1), // e.g. "test"
                ];
            }
        } else {
            $search[] = [
                $requestBaseUrl . '/' . $requestUri, // relative url with shopware 6 in sub folder: /public/Ergonomic-Concrete-Cough-Machine/48314803f1244f609a2ce907bfb48f53
                $requestBaseUrl . $requestUri, // relative url with shopware 6 in sub folder url is not shopware seo url: /public/test
                $storefrontUri . $requestUri, // absolute url with shopware 6 in sub folder, full url with host: http://shopware-platform.local/public/test1
                $storefrontUri . '/' . $requestUri, // absolute url with shopware 6 in sub folder, full url with host and slash at the end: http://shopware-platform.local/public/Freizeit-Elektro/Telefone/
                $requestUri, // relative url domain configured in public folder: /Ergonomic-Concrete-Cough-Machine/48314803f1244f609a2ce907bfb48f53 or /test4
                '/' . $requestUri, // absolute url domain configured in public folder: http://shopware-platform.local/Shoes-Baby/
                \substr($requestUri, 1), // e.g. "test"
            ];
        }

        $search = array_merge(...$search);

        $search = array_unique($search);

        $criteria = (new Criteria())
            ->addFilter(new EqualsAnyFilter('source', $search))
            ->setLimit(1);

        /** @var RedirectEntity $redirect */
        $redirect = $this->redirectRepository->search($criteria, $context)->first();

        if (!$redirect) {
            return null;
        }

        $targetURL = $redirect->getTarget();

        if (!(\strpos($targetURL, 'http:') === 0 || \strpos($targetURL, 'https:') === 0)) {
            if (\strpos($targetURL, 'www.') === 0) {
                $targetURL = 'http://' . $targetURL;
            } else {
                if (\strpos($targetURL, '/') !== 0) {
                    $targetURL = '/' . $targetURL;
                }
            }
        }

        return new RedirectResponse($targetURL, $redirect->getHttpCode());
    }
}
