<?php

namespace Knp\Invoice\Generators;

use Knp\Invoice\Generator;
use Knp\Invoice\Model\Invoice;
use RuntimeException;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;
use Twig\TemplateWrapper;

class Twig extends Generator
{
    public function __construct()
    {
        if (!class_exists('\Twig\Environment')) {
            throw new RuntimeException('Twig library is required!');
        }

        $this->generator = new Environment(
            new FilesystemLoader($this->getTheme())
        );
    }

    public function setTemplate($template)
    {
        if (null !== $template && '.' !== substr($template, -5, 1)) {
            $template .= '.html.twig';
        }

        parent::setTemplate($template);
    }

    public function setTheme($themePath)
    {
        $defaultTheme = $this->getTheme();

        parent::setTheme($themePath);

        $this->generator->setLoader(
            new FilesystemLoader(array(
                $themePath,
                $defaultTheme
            ))
        );
    }

    public function generate(Invoice $invoice, $template = null)
    {
        $this->setInvoice($invoice);
        $this->setTemplate($template);

        if (!$template instanceof TemplateWrapper) {
            try {
                $template = $this->generator->load($this->getTemplate());
            } catch (LoaderError | RuntimeError | SyntaxError $e) {
                throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
            }
        }

        ob_start();
        $template->display(array(
            'invoice' => $invoice
        ));
        $this->content = ob_get_clean();

        return $this->content;
    }

    public function generateAndSave(Invoice $invoice, $filename = null, $template = null)
    {
        $this->generate($invoice, $template);
        $this->save($filename);
    }

    public function save($filename)
    {
        file_put_contents($filename ?: $this->getFilename().'.html', $this->content);
    }
}
