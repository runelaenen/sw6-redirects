<?php declare(strict_types=1);

namespace RuneLaenen\Redirects\Subscriber;

use RuneLaenen\Redirects\Content\Redirect\RedirectEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\Event\BeforeSendResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class RequestSubscriber implements EventSubscriberInterface
{
    private EntityRepositoryInterface $redirectRepository;

    private EntityRepositoryInterface $seoUrlRepository;

    public function __construct(
        EntityRepositoryInterface $redirectRepository,
        EntityRepositoryInterface $seoUrlRepository
    ) {
        $this->redirectRepository = $redirectRepository;
        $this->seoUrlRepository = $seoUrlRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeSendResponseEvent::class => 'redirectBeforeSendResponse',
        ];
    }

    public function redirectBeforeSendResponse(BeforeSendResponseEvent $event): void
    {
        $requestBase = $event->getRequest()->getPathInfo();
        if (\strpos($requestBase, '/admin') === 0) {
            return;
        }
        if (\strpos($requestBase, '/api') === 0) {
            return;
        }
        if (\strpos($requestBase, '/widgets') === 0) {
            return;
        }
        if (\strpos($requestBase, '/store-api') === 0) {
            return;
        }

        $requestUri = (string) $event->getRequest()->get('resolved-uri');

        $context = Context::createDefaultContext();
        $redirects = $this->seoUrlRepository->search((new Criteria())
            ->addFilter(new EqualsAnyFilter('pathInfo', [$requestUri])), $context);

        $storefrontUri = $event->getRequest()->get('sw-storefront-url');
        $requestBaseUrl = $event->getRequest()->getBaseUrl();
        $search = [];

        // if found overwrite search term with the seo route
        if ($redirects->count() !== 0) {
            foreach ($redirects as $redirect) {
                $requestBase = $redirect->getSeoPathInfo();
                // Search for Redirect
                $search[] = [
                    $requestBaseUrl . '/' . $requestBase, // relative url with shopware 6 in sub folder: /public/Ergonomic-Concrete-Cough-Machine/48314803f1244f609a2ce907bfb48f53
                    $requestBaseUrl . $requestBase, // relative url with shopware 6 in sub folder url is not shopware seo url: /public/test
                    $storefrontUri . $requestBase, // absolute url with shopware 6 in sub folder, full url with host: http://shopware-platform.local/public/test1
                    $storefrontUri . '/' . $requestBase, // absolute url with shopware 6 in sub folder, full url with host and slash at the end: http://shopware-platform.local/public/Freizeit-Elektro/Telefone/
                    $requestBase, // relative url domain configured in public folder: /Ergonomic-Concrete-Cough-Machine/48314803f1244f609a2ce907bfb48f53 or /test4
                    '/' . $requestBase, // absolute url domain configured in public folder: http://shopware-platform.local/Shoes-Baby/
                    \substr($requestBase, 1), // e.g. "test"
                ];
            }
        }
        if (!empty($search)) {
            $search = array_merge(...$search);
        } else {
            $search = [
                $requestBaseUrl . '/' . $requestUri, // relative url with shopware 6 in sub folder: /public/Ergonomic-Concrete-Cough-Machine/48314803f1244f609a2ce907bfb48f53
                $requestBaseUrl . $requestUri, // relative url with shopware 6 in sub folder url is not shopware seo url: /public/test
                $storefrontUri . $requestUri, // absolute url with shopware 6 in sub folder, full url with host: http://shopware-platform.local/public/test1
                $storefrontUri . '/' . $requestUri, // absolute url with shopware 6 in sub folder, full url with host and slash at the end: http://shopware-platform.local/public/Freizeit-Elektro/Telefone/
                $requestUri, // relative url domain configured in public folder: /Ergonomic-Concrete-Cough-Machine/48314803f1244f609a2ce907bfb48f53 or /test4
                '/' . $requestUri, // absolute url domain configured in public folder: http://shopware-platform.local/Shoes-Baby/
                \substr($requestUri, 1), // e.g. "test"
            ];
        }

        $criteria = (new Criteria())
            ->addFilter(new EqualsAnyFilter('source', $search))
            ->setLimit(1);

        /** @var RedirectEntity $redirect */
        $redirect = $this->redirectRepository->search($criteria, $context)->first();

        if (!$redirect) {
            return;
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

        $event->setResponse(new RedirectResponse($targetURL, $redirect->getHttpCode()));
    }
}
