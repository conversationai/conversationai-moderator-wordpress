<?php

require_once( OSMOD_PLUGIN_DIR . 'class.osmod.php' );

class Comments {

	public static function newComment($comment_object) {

        $payload = array(
            "data" =>
            array(
                // Number or string representing the foreign ID of the article in the
                // publishing system.
                "articleId" => $comment_object->comment_post_ID,

                // Number or string representing the foreign ID of the comment in the
                // publishing system.
                "sourceId" => $comment_object->comment_ID,

                // Number or string representing the foreign ID of the comment's author
                // in the publishing system.
                "authorSourceId" => $comment_object->user_id,

                // Date of the post creation ( ISO 8601 format )
                "createdAt" => get_comment_date("c", $comment_object->comment_ID),

                // Cleaned body content (no html or non-UTF-8 characters).
                "text" => $comment_object->comment_content,

                "author" => array(
                    "email" => $comment_object->comment_author_email,
                    "name" => $comment_object->comment_author,
                    "avatar" => get_avatar_url(get_avatar($comment_object->user_id, 150)),
                )

            ),
        );

        (new Osmod)->newComment($payload);

	}

    public static function getDecisions() {
        return (new Osmod)->getDecisions();
    }

    public static function resolveDecisions($ids) {
        return (new Osmod)->resolveDecisions($ids);
    }
}
