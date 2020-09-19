<?php
/**
 * @file
 * Contains the Page class.
 */

namespace Atlassian\Confluence;

// http://www.zestudio.net/actualites/confluence-send-api-rest-getpostput-requests-in-json-format-php/
// http://stackoverflow.com/questions/30907337/using-confluence-rest-api-to-post-generated-html-table-php-without-getting-htt
// http://stackoverflow.com/questions/31878032/using-php-to-create-confluence-wiki-pages
// https://developer.atlassian.com/confdev/confluence-rest-api/confluence-rest-api-examples
// https://confluence.atlassian.com/confkb/how-to-use-php-inside-confluence-pages-317197666.html

use Atlassian\Configuration\Config;
use Atlassian\RestApiClient\ConfluenceClient;

interface PageInterface
{
    public static function init($space_key, $title);

    public static function getQuery();

    public static function getConfluence();

    public static function get();

    /**
     * Get the page id with $title in the $space.
     *
     * @return bool|mixed
     */
    public static function getId();

    /**
     * Returns de page version in space $space_key with title $title.
     *
     * @return bool
     */
    public static function getVersion();

    /**
     * Checks if a page with title $title exist in space $space_key.
     *
     * @return bool
     *   TRUE is exists otherwise FALSE.
     */
    public static function exists();

    /**
     * Updates a page.
     *
     * @param string $ancestor_id
     *   The ancestor page id.
     * @param string $page_id
     *   The page id.
     * @param string $body
     *   The body you want to replace with.
     *
     * @return mixed|string
     */
    public static function update($ancestor_id, $body);
    /**
     * Creates a new page.
     *
     * @param string $ancestor_id
     *   The ancestor page id.
     * @param string $body
     *   The body you want to replace with.
     */
    public static function add($ancestor_id, $body = '');

    /**
     * Attach image to a page.
     *
     * @param string $filename
     * @param string $comment
     *
     * @return mixed|string
     */
    public static function addImage($filename, $comment = '');

    /**
     * Delete a page with given page id.
     *
     * @param string $id
     *
     * @return mixed|string
     */
    public static function delete();
}
