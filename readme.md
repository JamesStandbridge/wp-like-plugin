# Wordpress simple LikePlugin

## usage

```php

/**
 * This function allows you to generate a button 
 * allowing you to "like" a post
 *
 * @param integer $postID
 * @param string $isLikedMessage default="Aimer"
 * @param string $isNotLikedMessage default="Ne plus aimer"
 * @param string $class default = "like-button
 * @return string htmlButton
 */
function the_like_button(
	int $postID, 
	?string $isLikedMessage = "Aimer", 
	?string $isNotLikedMessage = "Ne plus aimer",
	string $class = "like-button"
) : string;

/**
 * This function retrieves a span tag displaying 
 * the number of likes of the given post
 *
 * @param integer $postID
 * @param bool    $displayIf0
 * @param string  $word default=null
 * @param string  $pluralWord default="s"
 * @param string  $class
 * @return string htmlSpan
 */
function the_like_counter(
	int $postID, 
	bool $displayIf0 = true, 
	?string $word = null,
	?string $pluralWord = "s",
	?string $class = "like-counter"
) : ?string;


/**
 * Returns the number of likes on a given post 
 * (This counter does not update in ajax)
 *
 * @param integer $postID
 * @return int
 */
function get_count_likes(int $postID) : int;