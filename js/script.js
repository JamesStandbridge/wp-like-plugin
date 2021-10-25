

function handleLike(postID) {
    const button = jQuery(".like-button")
    const like_counter = jQuery(`#like-counter-${postID}`)
    const span_like_counter = jQuery(`#span-like-counter-${postID}`)
    const span_like_counter_word = jQuery(`#span-counter-like-word-${postID}`)

    const isLikedMessage = jQuery(button).data("liked")
    const isNotLikedMessage = jQuery(button).data("not-liked")

    const displayIfNull = jQuery(span_like_counter).data("display-0")
    const pluralWord = jQuery(span_like_counter).data("plural-word")
    const word = jQuery(span_like_counter).data("word")

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
        jQuery(button).attr('data-value', liked_state.is_liked == 0 ? false : true)

        console.log(counter)
        if(counter == 0 && !displayIfNull) 
            jQuery(span_like_counter).hide()
        else
            jQuery(span_like_counter).show()

        if(word != null) {
            wordToWrite = counter > 1 ? word+pluralWord : word
            jQuery(span_like_counter_word).html(wordToWrite)
        }
    });
}

jQuery('#like-button').on("click", handleLike);

jQuery(document).ready(function(){
    const button = jQuery(".like-button")
   
    const postID = jQuery(button).data("post")

    const like_counter = jQuery(`#like-counter-${postID}`)
    const span_like_counter = jQuery(`#span-like-counter-${postID}`)
    const span_like_counter_word = jQuery(`#span-counter-like-word-${postID}`)

    const isLikedMessage = jQuery(button).data("liked")
    const isNotLikedMessage = jQuery(button).data("not-liked")

    const displayIfNull = jQuery(span_like_counter).data("display-0")
    const pluralWord = jQuery(span_like_counter).data("plural-word")
    const word = jQuery(span_like_counter).data("word")

    jQuery.ajax({
        url: LikePlugin.ajaxUrl,
        type: "POST",
        data: {
          'action': 'get_post_like_state',
          'post_id': postID
        }
    }).done(function(response) {
        let counter = parseInt(jQuery(like_counter).text())

        jQuery(button).html(response == 0 ? isLikedMessage : isNotLikedMessage)
        jQuery(button).attr('data-value', response == 0 ? false : true)

        if(counter == 0 && !displayIfNull) 
            jQuery(span_like_counter).hide()

        if(word != null) {
            wordToWrite = counter > 1 ? word+pluralWord : word
            jQuery(span_like_counter_word).html(wordToWrite)
        }
    });
})