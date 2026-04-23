<?php

/**
 * @file classes/BlogEntry.php
 *
 * @package plugins.generic.blog
 * @class BlogEntry
 * Data object representing a blog entry.
 */

namespace APP\plugins\generic\blog\classes;

use PKP\core\DataObject;

class BlogEntry extends DataObject {

    public function getContextId(): ?int {
        return $this->getData('contextId');
    }

    public function setContextId(int $contextId): void {
        $this->setData('contextId', $contextId);
    }

    public function getTitle(): ?string {
        return $this->getData('title');
    }

    public function setTitle(string $title): void {
        $this->setData('title', $title);
    }

    public function getByline(): ?string {
        return $this->getData('byline');
    }

    public function setByline(string $byline): void {
        $this->setData('byline', $byline);
    }

    public function getContent(): ?string {
        return $this->getData('content');
    }

    public function setContent(string $content): void {
        $this->setData('content', $content);
    }

    public function getAbbreviatedContent(): string {
        return implode(' ', array_slice(explode(' ', $this->getData('content')), 0, 25));
    }

    public function getKeywords(?string $locale = null): mixed {
        $keywords = $this->getData('keywords');
        if ($locale) {
            return $keywords[$locale] ?? null;
        }
        return $keywords;
    }

    public function setKeywords(array $keywords): void {
        $this->setData('keywords', $keywords);
    }

    public function getDatePosted(): ?string {
        $date = $this->getData('datePosted');
        return $date ? date('Y-m-d', strtotime($date)) : null;
    }

    public function setDatePosted(string $datePosted): void {
        $this->setData('datePosted', $datePosted);
    }
}