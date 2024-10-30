<?php
/**
 * @copyright Copyright (c) 2018 Bjoern Schiessle <bjoern@schiessle.org>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
defined('ABSPATH') or die("No script kiddies please!");
if( !class_exists ( 'Mastodon_api'))
	require_once 'Mastodon_api.php';

class CollectMastodonData {

    /** @var \Mastodon_api */
    private $api;


    /** @var string token to authenticate at the mastodon instance */
    private $bearerToken;

    /** @var int keep cache at least 600 seconds = 10 minutes */
    private $threshold = 600;

    /** @var string uid on the mastodon instance */
    private $uid;

    /** @var array cached comments from previous searches */
    private $commentCache = [];

    private $cacheFile = 'myCommentsCache.json';

    public function __construct($config) {
        $this->mastodonUrl = $config['mastodon-instance'];
        $this->bearerToken = $config['token'];
        $this->uid = $config['user-id'];

        $this->api = new Mastodon_api();

        $this->api->set_url($this->mastodonUrl);
        $this->api->set_token($this->bearerToken, 'bearer');
    }

    private function filterComments($descendants, $root, &$result) {
        foreach ($descendants as $d) {
        	if( $d['visibility'] === "public") {
		        $content = $d['content'];
        		if( !empty($d['emojis'])){
					foreach ($d['emojis'] as $emoji){
						$shortcode = $emoji['shortcode'];
						$url = $emoji['url'];
						$content = str_replace(":" . $shortcode . ":", "<img alt=':". $shortcode .":' width=15 src='" .$url."'/>" , $content);
					}
		        }
		        $result['comments'][ $d['id'] ] = [
			        'author'   => [
				        'display_name' => $d['account']['display_name'],
				        'avatar'       => $d['account']['avatar_static'],
				        'url'          => $d['account']['url']
			        ],
			        'toot'     => $content,
			        'date'     => $d['created_at'],
			        'url'      => $d['uri'],
			        'reply_to' => $d['in_reply_to_id'],
			        'root'     => $root,
		        ];
	        }
        }

        return $result;
    }

    private function filterStats($stats) {
        $result = [
            'reblogs' => (int)$stats['reblogs_count'],
            'favs' => (int)$stats['favourites_count'],
            'replies' => (int)$stats['replies_count'],
            'url' => $stats['url']
        ];
        return $result;
    }

    private function filterSearchResults($searchResult) {
        $result = [];
        if (isset($searchResult['html']['statuses'])) {
            foreach ($searchResult['html']['statuses'] as $status) {
                if ($status['in_reply_to_id'] === null) {
                    $result['id'][] = $status['id'];
	                $result['url'][] = $status['url'];
                }
            }
        }
        return $result;
    }

    /**
     * find all toots for a given blog post and return the corresponding IDs
     *
     * @param string $search
     * @return array
     */
    public function findToots($search) {
        $result = $this->api->search(array('q' => $search));
        return $this->filterSearchResults($result);
    }

    public function getComments($id, &$result) {
        $raw = file_get_contents($this->mastodonUrl . "/api/v1/statuses/$id/context");
        $json = json_decode($raw, true);
        $this->filterComments($json['descendants'], $id, $result);
    }

    public function getStatistics($id, &$result) {
        $raw = file_get_contents($this->mastodonUrl ." /api/v1/statuses/$id");
        $json = json_decode($raw, true);
        $newStats = $this->filterStats($json);
        $result['stats']['reblogs'] += $newStats['reblogs'];
        $result['stats']['favs'] += $newStats['favs'];
        $result['stats']['replies'] += $newStats['replies'];
        if (empty($result['stats']['url'])) {
            $result['stats']['url'] = $newStats['url'];
        }
    }

    public function storeCollection($id, $comments) {
        $timestamp = time();
        $comments['timestamp'] = $timestamp;
        $this->commentCache[$id] = $comments;
        file_put_contents($this->cacheFile, json_encode($this->commentCache));
    }

    public function getCachedCollection($search) {
        if (file_exists($this->cacheFile)) {
            $cachedComments = file_get_contents($this->cacheFile);
            $cachedCommentsArray = json_decode($cachedComments, true);
            if (is_array($cachedCommentsArray)) {
                $this->commentCache = $cachedCommentsArray;
                $currentTimestamp = time();
                if (isset($cachedCommentsArray[$search])) {
                    if ((int)$cachedCommentsArray[$search]['timestamp'] + $this->threshold > $currentTimestamp) {
                        unset($cachedCommentsArray[$search]['timestamp']);
                        return $cachedCommentsArray[$search];
                    }
                }
            }
        }

        return [];
    }
}


