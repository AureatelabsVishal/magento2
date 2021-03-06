<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Ui\TemplateEngine\Xhtml;

/**
 * Class Template
 */
class Template
{
    const XML_VERSION = '1.0';

    const XML_ENCODING = 'UTF-8';

    /**
     * @var \DOMElement
     */
    protected $templateNode;

    /**
     * Constructor
     *
     * @param string $content
     */
    public function __construct($content)
    {
        $document = new \DOMDocument(static::XML_VERSION, static::XML_ENCODING);
        $document->loadXML($content);
        $this->templateNode = $document->documentElement;
    }

    /**
     * Get template root element
     *
     * @return \DOMElement
     */
    public function getDocumentElement()
    {
        return $this->templateNode;
    }

    /**
     * Append
     *
     * @param string $content
     * @return void
     */
    public function append($content)
    {
        $newFragment = $this->templateNode->ownerDocument->createDocumentFragment();
        $newFragment->appendXML($content);
        $this->templateNode->appendChild($newFragment);
    }

    /**
     * Returns the string representation
     *
     * @return string
     */
    public function __toString()
    {
        try {
            $this->templateNode->ownerDocument->normalizeDocument();
            $result = $this->templateNode->ownerDocument->saveHTML();
        } catch (\Exception $e) {
            $result = '';
        }
        return $result;
    }
}
