<?php

namespace Huluti\BreadcrumbsBundle\Templating\Helper;

use Twig\Environment;
use Huluti\BreadcrumbsBundle\Model\Breadcrumbs;

class BreadcrumbsHelper
{
    /**
     * @var Environment
     */
    protected $twig;

    /**
     * @var Breadcrumbs
     */
    protected $breadcrumbs;

    /**
     * @var array The default options load from config file
     */
    protected $options = array();

    /**
     * @param \Twig\Environment $twig
     * @param \Huluti\BreadcrumbsBundle\Model\Breadcrumbs $breadcrumbs
     * @param array $options The default options load from config file
     */
    public function __construct(Environment $twig, Breadcrumbs $breadcrumbs, array $options)
    {
        $this->twig  = $twig;
        $this->breadcrumbs = $breadcrumbs;
        $this->options = array_merge($options, array(
            'namespace' => Breadcrumbs::DEFAULT_NAMESPACE, // inject default namespace to options
        ));
    }

    /**
     * Returns the HTML for the namespace breadcrumbs
     *
     * @param array $options The user-supplied options from the view
     * @return string A HTML string
     */
    public function breadcrumbs(array $options = array())
    {
        $options = $this->resolveOptions($options);

        // Assign namespace breadcrumbs
        $options["breadcrumbs"] = $this->breadcrumbs->getNamespaceBreadcrumbs($options['namespace']);

        return $this->twig->render(
            $options["viewTemplate"],
            $options
        );
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getName()
    {
        return 'breadcrumbs';
    }

    /**
     * Merges user-supplied options from the view
     * with base config values
     *
     * @param array $options The user-supplied options from the view
     * @return array
     */
    private function resolveOptions(array $options = array())
    {
        return array_merge($this->options, $options);
    }
}
