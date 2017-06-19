<?php

require_once( OSMOD_PLUGIN_DIR . 'class.osmod.php' );

class Articles {

    public static function updateArticle($post) {
        $payload = parseForUpdatePayload($post);
        (new Osmod)->updateArticle($payload,$post->ID);
    }

	public static function newArticle($post) {
        (new Osmod)->newArticle(parseForPayload($post));
	}

    public static function newArticleCategory($id) {
        (new Osmod)->newCategory(prepCategoryPayload($id,get_the_category_by_ID($id)));
    }

}

function parseForUpdatePayload($post) {

    // Obtain category. Draw first for now. OSMOD only allows one category per article.
    $category = get_the_category( $post->ID );

    // Prepare id with/without prefix
    $payload = array(
        "data" =>
        array(
            "attributes" =>
            array(
                // Number representing the category inside OSMOD, see below for reading
                // and writing this list.
                "categoryId" => $category[0]->term_id,

                // Title of the article.
                "title" => $post->post_title,

                // Cleaned body content (no non-UTF-8 characters).
                "text" => strip_tags($post->post_content),

                // Live URL of article to give moderators more context.
                "url" => $post->guid
            ),
        ),
    );

    return $payload;

}

function parseForPayload($post) {

    // Obtain category. Draw first for now.
    $category = get_the_category( $post->ID );
    $category = $category[0]->name;

    // Prepare id with/without prefix
    $payload = array(
        "data" =>
        array(
            // Number or string representing the foreign ID of the article in the
            // publishing system.
            "sourceId" => $post->ID,

            // Number representing the category inside OSMOD, see below for reading
            // and writing this list.
            "categoryId" => $category,

            // Date of the article creation ( ISO 8601 format )
            "createdAt" => get_the_date("c", $post->ID),

            // Title of the article.
            "title" => $post->post_title,

            // Cleaned body content (no non-UTF-8 characters).
            "text" => strip_tags($post->post_content),

            // Live URL of article to give moderators more context.
            "url" => $post->guid
        ),
    );

    return $payload;
}


function prepCategoryPayload($id,$name) {

    $payload = array(
        "data" =>
        array(
            "type" => "categories",
            "attributes" =>
            array(
                "id" => $id,
                "label" => $name
            ),
        ),
    );

    return $payload;
}