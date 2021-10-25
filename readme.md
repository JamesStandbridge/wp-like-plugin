# Wordpress simple LikePlugin

## usage

Like button :
```php

/**
* This function allows you to generate a button
* allowing you to "like" a post
*
* @param  integer $postID
* @return  string htmlButton
*/
function the_like_button(int $post_id, ?string $isLikedMessage = "Aimer", ?string $isNotLikedMessage = "Ne plus aimer"): string;


/**
* This function retrieves a span tag displaying
* the number of likes of the given post
*
* @param  integer $postID
* @return  string htmlSpan
*/
function the_like_counter(int $postID): string;