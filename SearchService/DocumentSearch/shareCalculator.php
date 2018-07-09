<?php 

class shareCalculator {

  private $results;
  private $cache;

  public function __construct()
  {
    $this->results = array();
    $this->cache = new SimpleCache();
    $this->results["total"] = 0;
  }

  function build($url) {
   if($data = $this->cache->get_cache('counter'.sha1($url))){
    $decoded_data = json_decode($data);
    return $decoded_data;
  } 
  else {  
   $results = array();
   $this->twitter($url);
   $this->facebook($url);
   $this->cache->set_cache('counter'.sha1($url), json_encode($this->results));
   return ($this->results);
 }
}

function reddit($url) {
  $score = 0; 
  $reddit_url = 'http://www.reddit.com/api/info.json?url='.$url;
  $json = json_decode(utility::getCURLResult($reddit_url)); 
  if($json) {
   foreach($json->data->children as $child) { 
    $score+= (int) $child->data->score; 
  }
  $this->results["reddit"] = $score;
  $this->results["total"] = $this->results["total"] + $score;
}
}

function buffer($url) { 
  $buffer_url = "https://api.bufferapp.com/1/links/shares.json?url=".urlencode($url);
  $json = json_decode(utility::getCURLResult($buffer_url));
  if ($json) {
    $this->results["buffer"] = $json->shares;
    $this->results["total"] = $this->results["total"] + $json->shares;
  }
}

function delicious($url) { 
  $delicious_url = "http://feeds.delicious.com/v2/json/urlinfo/data?url=".urlencode($url);
  $json = json_decode(utility::getCURLResult($delicious_url));
  if ($json) {
    $this->results["delicious"] = $json[0]->total_posts;
    $this->results["total"] = $this->results["total"] + $json[0]->total_posts;
  }
}

function twitter($url) {     
  $twitter_url = 'http://urls.api.twitter.com/1/urls/count.json?url=' . $url;
  $json = json_decode(utility::getCURLResult($twitter_url));
  if ($json) {
    $this->results["twitter"] = $json->count;
    $this->results["total"] = $this->results["total"] + $json->count;
  }
}

function linkedin($url) {     
  $linkedin_url = "http://www.linkedin.com/countserv/count/share?url=$url&format=json";
  $json = json_decode(utility::getCURLResult($linkedin_url));
  if ($json) {
    $this->results["linkedin"] = $json->count;
    $this->results["total"] = $this->results["total"] + $json->count;
  }
}

function stumbleupon($url) {     
  $linkedin_url = "http://www.stumbleupon.com/services/1.01/badge.getinfo?url=$url";
  $json = json_decode(utility::getCURLResult($linkedin_url));
  if($json && isset($json->result) && isset($json->result->views)) {
    $this->results["stumbleupon"] =  $json->result->views;
    $this->results["total"] = $this->results["total"] + $json->result->views;
  }
}

function pinterest($url) {     
  $pinterest_url = "http://api.pinterest.com/v1/urls/count.json?url=" . $url;
  $resp = str_replace(array('(',')'), "", utility::getCURLResult($pinterest_url));
  $json = json_decode(str_replace("receiveCount","",$resp));
  if($json) {
    $this->results["pinterest"] = $json->count;
    $this->results["total"] = $this->results["total"] + $json->count;
  }
}

function hackernews($url) {     
  $hackernews_url = "http://api.thriftdb.com/api.hnsearch.com/items/_search?q=&filter[fields][url]=$url";
  $json = json_decode(utility::getCURLResult($hackernews_url));
  $c = 0;
  if($json && isset($json->results)) {
    foreach($json->results as $story) {
      $c++;
      if(isset($story->item) && isset($story->item->points)) {
        $c = $c + (int)$story->item->points;
      }
      if(isset($story->item) && isset($story->item->num_comments)) {
        $c = $c + (int)$story->item->num_comments;
      }
    }
  }
  $this->results["hackernews"] = $c;
  $this->results["total"] = $this->results["total"] + $c;
}

function facebook($url) {
  $facebook_url = 'http://api.facebook.com/restserver.php?method=links.getStats&urls='.urlencode($url);
  $json = json_decode(utility::getCURLResult($facebook_url));
  $ch = curl_init($facebook_url);
  curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
  $atom_data = curl_exec($ch);
  if ($atom_data) {
    preg_match('#share_count>(\d+)<#',$atom_data,$share_matches);
    $share_count = $share_matches[1]; 
    preg_match('#like_count>(\d+)<#',$atom_data,$like_matches);
    $like_count = $like_matches[1];  
    preg_match('#comment_count>(\d+)<#',$atom_data,$comment_matches);
    $comment_count = $comment_matches[1]; 
    preg_match('#total_count>(\d+)<#',$atom_data,$total_matches);
    $total_count = $total_matches[1]; 
    preg_match('#click_count>(\d+)<#',$atom_data,$click_matches);
    $click_count = $click_matches[1]; 
  }
  $this->results["facebook"] = array(
    "likes"   => $like_count,
    "shares"  => $share_count,
    "comment" => $comment_count,
    "total"   => $total_count,
    "click"   => $click_count
    );
  $this->results["total"] = $this->results["total"] + $like_count + $share_count + $comment_count + $total_count + $click_count;
} 

function google_plus($url) {

  $params = json_encode(array(
      'method'  => 'pos.plusones.get',
      'id'      => 'p',
      'params'  => array(
      "nolog"   => true,
      "id"      => $url,
      "source"  => "widget",
      "userId"  => "@viewer",
      "groupId" => "@self"
      ),
      "jsonrpc"    => "2.0",
      "key"        => "p",
      "apiVersion" => "v1"
    ));

  $curl = curl_init();
  curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
  curl_setopt($curl, CURLOPT_URL, rtrim("https://clients6.google.com/rpc", '?&'));
  curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

  $phpObj = curl_exec($curl);
  curl_close($curl);
  if(!is_null($phpObj) && !empty($phpObj)){ 
    $item = json_decode($phpObj);   
    $this->results["google"] = $item->result->metadata->globalCounts->count;
  }
}
}
?>