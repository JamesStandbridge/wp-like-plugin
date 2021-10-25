# Wordpress simple LikePlugin

## data attributes

### like button

- __data-post__ => post_id
- __data-liked__ => message to display if liked
- __data-not-liked__ => message to display if not liked
- __data-value__ => button state value (true if liked, false if not liked)

### span counter

- __data-display-0__ => Determines if the counter is displayed in case the post has 0 like (false => does not display the counter has 0 like)
- __data-plural-word__ => character to display at the end of the word if more than one like (plural)
- __data-word__ =>  word to display aside the counter
- __data-value__ => counter value



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