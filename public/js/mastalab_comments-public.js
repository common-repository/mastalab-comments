(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */



    jQuery.get(
        params.ajaxurl,
        {
            'action': 'mastalab_comments_getcomment',
            'post_id': params.post_id,
            'search': window.location.href,
            'mastalab_comments_nonce': params.mastalab_comments_nonce
        },
        function(data){
            var targeted_element
            if( params.mastalab_where === 'end_article')
                targeted_element = "#mastalab_comments_end_article";
            else {
                targeted_element = "#comments";
                if (typeof params.selector !== "undefined")
                    targeted_element = params.selector;
            }
            if( typeof data.stats === "undefined" || data.comments.length === 0)
                return;

            var stats = data.stats;
            var root = data.stats.root;
            var element = jQuery('<div/>', {
                id: 'mastalab_comments',
                class: 'mastodon comments-container'
            });
            if( typeof params.mastodon_bg)
                element.css("background-color", params.mastodon_bg);
            if( typeof params.mastodon_text)
                element.css("color", params.mastodon_text);
            var elementCounter = jQuery('<div/>', {
                id: 'mastalab_comments_counters',
                class: 'mastodon_container_counters'
            });
            $("<style type='text/css'> " +
                ".mastodon.comments-container{ background-color:"+params.mastodon_bg +";color:" + params.mastodon_text + "}" +
                ".mastodon.comments-container a{box-shadow: none; color:" + params.mastodon_link + "; text-decoration: none;}" +
                ".mastodon.comments-container a:hover{ box-shadow: none;color:" + params.mastodon_link + "}" +
                ".mastodon_container_counters a{ box-shadow: none; color: black !important}" +
                ".mastodon_container_counters a:hover{ box-shadow: none;color: black !important}" +
                "</style>").appendTo("head");
            elementCounter.append('<div class="mastodon-like-count"><a href="' + stats.url + '"><span title="Likes"><i class="fa fa-star"></i>' + stats.favs + '</span></a></div></div>');
            elementCounter.append('<div class="mastodon-reblog-count"><a href="' + stats.url + '"><span title="Reblogs"><i class="fa fa-retweet"></i>' + stats.reblogs + '</span></a></div></div>');
            elementCounter.append('<div class="mastodon-reply-count"><a href="' + stats.url + '"><span title="Comments"><i class="fa fa-comments"></i>' + stats.replies + '</span></a></div></div>');
            element.prepend(elementCounter);

            var comments = data.comments;
            var array_key = new Array();
            $.each(comments, function(key, value) {
                var timestamp = Date.parse(value.date);
                var date = new Date(timestamp);
                var reply_to = false;
                for( var i = 0 ; i < array_key.length ; i++) {
                    if (value.reply_to === array_key[i]) {
                        reply_to = true;
                        var selector = '#'+value.reply_to;
                        break;
                    }
                }
                var comment;
                if( !reply_to)
                    comment = "<div class='comment' id='" + key + "'>";
                else
                    comment = "<div class='comment mastalab_comments_reply' id='" + key + "'>";
                comment += "<img class='avatar' src='" + value.author.avatar + "' />";
                comment += "<div class='author'><a class='displayName' href='" + value.author.url + "'>" + value.author.display_name + "</a> wrote at ";
                comment += "<a class='date' href='" + value.url + "'>" + date.toDateString() + ', ' + date.toLocaleTimeString() + "</a></div>";
                comment += "<div class='toot'>" + value.toot + "</div>";
                comment += "</div>";
                array_key.push(key);
                if( !reply_to)
                    element.append(comment);
                else
                    $(selector).after($(comment));

                $(targeted_element).prepend(element);
            });
            if (parseInt(root) === 0)
                element.empty();
            $(targeted_element).prepend(element);
        }
    );
    if( params.peertube_comment) {
        $(document).ready(function(){

            $("iframe").each(function() {

                var src= $(this).attr('src');
                var regexPeertube = new RegExp('(https?:\\/\\/[\\da-z\\.-]+\\.[a-z\\.]{2,10})\\/videos\\/embed\\/(\\w{8}-\\w{4}-\\w{4}-\\w{4}-\\w{12})$');
                var result = src.split(regexPeertube);
                if( result.length === 4){
                    var urlPeertube = result[1] + "/api/v1/videos/" + result[2] + "/comment-threads";
                    var peertubeComment = jQuery('<div/>');
                    $(this).after($(peertubeComment));
                    jQuery.get(
                        params.ajaxurl,
                        {
                            'action': 'mastalab_comments_getcomment_peertube',
                            'search': urlPeertube,
                            'source': src,
                            'mastalab_comments_nonce': params.mastalab_comments_nonce
                        },
                        function(data){
                            if( typeof data.stats === "undefined" || data.comments.length === 0)
                                return;
                            var stats = data.stats;
                            var element = jQuery('<div/>', {
                                class: 'peertube  comments-container'
                            });
                            $("<style type='text/css'> " +
                                ".peertube.comments-container{ background-color:"+params.peertube_bg +" !important;color:" + params.peertube_text + " !important;}" +
                                ".peertube.comments-container a{box-shadow: none; color:" + params.peertube_link + " !important; text-decoration: none !important;}" +
                                ".peertube.comments-container a:hover{ box-shadow: none !important;color:" + params.peertube_link + "!important;}" +
                                ".mastodon_container_counters a{ box-shadow: none; color: black !important}" +
                                ".mastodon_container_counters a:hover{ box-shadow: none;color: black !important}" +
                                "</style>").appendTo("head");
                            var elementCounter = jQuery('<div/>', {
                                class: 'mastodon_container_counters',
                                title: 'Mastodon Comments Counters'
                            });
                            elementCounter.append('<div class="mastodon-reply-count"><a href="' + stats.url + '"><span title="Comments"><i class="fa fa-comments"></i>' + stats.replies + '</span></a></div></div>');
                            element.prepend(elementCounter);
                            var comments = data.comments;
                            $.each(comments, function(key, value) {
                                var timestamp = Date.parse(value.date);
                                var date = new Date(timestamp);
                                var comment;
                                comment = "<div class='comment' id='" + key + "'>";
                                comment += "<img class='avatar' src='" + value.author.avatar + "' />";
                                comment += "<div class='author'><a class='displayName' href='" + value.author.url + "'>" + value.author.display_name + "</a> wrote at ";
                                comment += "<a class='date' href='" + value.url + "'>" + date.toDateString() + ', ' + date.toLocaleTimeString() + "</a></div>";
                                comment += "<div class='toot'>" + value.toot + "</div>";
                                comment += "</div>";
                                element.append(comment);
                            });
                            $(peertubeComment).prepend(element);
                        }
                    );
                }
            });
        });
    }

})( jQuery );
