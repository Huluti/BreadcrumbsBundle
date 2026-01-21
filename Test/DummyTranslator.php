<?php declare(strict_types = 1);

namespace Huluti\BreadcrumbsBundle\Test;

use Symfony\Contracts\Translation\TranslatorInterface;

final class DummyTranslator implements TranslatorInterface
{

    public function trans($id, array $parameters = [], $domain = null, $locale = null): string
    {
        $return = $id;
        if (count($parameters) > 0) {
            $params = [];
            foreach ($parameters as $paramKey => $paramValue) {
                $params[] = $paramKey . ':' . $paramValue;
            }
            $return .= '__{' . implode('|', $params) . '}';
        }
        if ($domain !== null) {
            $return .= '__domain:' . $domain;
        }
        if ($locale !== null) {
            $return .= '__locale:' . $locale;
        }

        return $return;
    }

    public function getLocale(): string
    {
        return 'en';
    }

}
