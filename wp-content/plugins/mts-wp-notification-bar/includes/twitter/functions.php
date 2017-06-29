<?php
function mtsnb_get_twitter_content( $args ) {

	extract( $args );

	\Codebird\Codebird::setConsumerKey($api_key, $api_secret);

	$cb = \Codebird\Codebird::getInstance();
	$cb->setToken($token, $token_secret);
	$cb->setReturnFormat(CODEBIRD_RETURNFORMAT_ARRAY);

	$count = 0;
	$target = 'target="_blank"';

	$twitter_data = get_transient('mtsnb_twitter_feed');

	if (false === $twitter_data) {

		try {
			$twitter_data = (array) $cb->statuses_userTimeline();
		} catch (Exception $e) {
			_e('An Error has occurred and we were unable to get your tweets.');
		}

		set_transient('mtsnb_twitter_feed', $twitter_data, 300);
	}

	if ($twitts_number == 1 || (isset($twitter_data) && is_array($twitter_data) && count($twitter_data) <= 1)) {
		$out = '<div class="mtsnb-twitter-feed">';
	} else {
		$out = '<div class="mtsnb-slider-container loading"><div class="mtsnb-slider">';
	}

	if (isset($twitter_data) && is_array($twitter_data)) {
		foreach($twitter_data as $message) {

			if ($count >= $twitts_number) {
				break;
			}

			if (!isset($message['text'])) {
				continue;
			}

			$msg = $message['text'];

			if ($twitts_number != 1 || (isset($twitter_data) && is_array($twitter_data) && count($twitter_data) <= 1)) {
				$out .= '<div>';
			}

			$hashtag_link_pattern = '<a href="http://twitter.com/search?q=%%23%s&src=hash" rel="nofollow" target="_blank">#%s</a>';
			$url_link_pattern = '<a href="%s" rel="nofollow" target="_blank" title="%s">%s</a>';
			$user_mention_link_pattern = '<a href="http://twitter.com/%s" rel="nofollow" target="_blank" title="%s">@%s</a>';
			$media_link_pattern = '<div class="mtsnb-twitter-image-link"><a href="%s" rel="nofollow" target="_blank" title="%s">%s</a></div>';

			$text = $message['text'];
			$entity_holder = array();

			foreach ($message['entities']['hashtags'] as $hashtag) {

				$entity = new stdclass();
				$entity->start = $hashtag['indices'][0];
				$entity->end = $hashtag['indices'][1];
				$entity->length = $hashtag['indices'][1] - $hashtag['indices'][0];
				$entity->replace = sprintf($hashtag_link_pattern, strtolower($hashtag['text']), $hashtag['text']);

				$entity_holder[$entity->start] = $entity;
			}

			foreach ($message['entities']['urls'] as $url) {
				$entity = new stdclass();
				$entity->start = $url['indices'][0];
				$entity->end = $url['indices'][1];
				$entity->length = $url['indices'][1] - $url['indices'][0];
				$entity->replace = sprintf($url_link_pattern, $url['url'], $url['expanded_url'], $url['url']);

				$entity_holder[$entity->start] = $entity;
			}

			foreach ($message['entities']['user_mentions'] as $user_mention) {

				$entity = new stdclass();
				$entity->start = $user_mention['indices'][0];
				$entity->end = $user_mention['indices'][1];
				$entity->length = $user_mention['indices'][1] - $user_mention['indices'][0];
				$entity->replace = sprintf($user_mention_link_pattern, strtolower($user_mention['screen_name']), $user_mention['name'], $user_mention['screen_name']);

				$entity_holder[$entity->start] = $entity;

			}

			krsort($entity_holder);

			foreach($entity_holder as $entity) {
				$text = substr_replace($text, $entity->replace, $entity->start, $entity->length);
			}

			$out .= $text;

			if ($twitts_number != 1 || (isset($twitter_data) && is_array($twitter_data) && count($twitter_data) <= 1)) {
				$out .= '</div>';
			}

			$count++;
		}
	}

	if ($twitts_number == 1 || (isset($twitter_data) && is_array($twitter_data) && count($twitter_data) <= 1)) {
		$out .= '</div>';
	} else {
		$out .= '</div></div>';
	}

	return $out;
}