

function handleLike(postID) {
    const button = jQuery(".like-button")
    const like_counter = jQuery(`.like-counter-${postID}`)

    const isLikedMessage = jQuery(button).data("liked")
    const isNotLikedMessage = jQuery(button).data("not-liked")

    jQuery.ajax({
        url: LikePlugin.ajaxUrl,
        type: "POST",
        data: {
          'action': 'like_post',
          'post_id': postID
        }
    }).done(function(response) {
        liked_state = JSON.parse(response)
        let counter = parseInt(jQuery(like_counter).text())

        counter = liked_state.is_liked == 0 ? counter - 1 : counter + 1

        jQuery(like_counter).html(counter)
        jQuery(button).html(liked_state.is_liked == 0 ? isLikedMessage : isNotLikedMessage)
    });
}

jQuery('#like-button').on("click", handleLike);

jQuery(document).ready(function(){
    const button = jQuery(".like-button")
    const postID = jQuery(button).data("post")
    
    const isLikedMessage = jQuery(button).data("liked")
    const isNotLikedMessage = jQuery(button).data("not-liked")

    jQuery.ajax({
        url: LikePlugin.ajaxUrl,
        type: "POST",
        data: {
          'action': 'get_post_like_state',
          'post_id': postID
        }
    }).done(function(response) {
        console.log(response)
        jQuery(button).html(response == 0 ? isLikedMessage : isNotLikedMessage)
    });
})