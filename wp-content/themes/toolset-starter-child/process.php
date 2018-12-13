<?php
$arrContextOptions=array( "ssl" => array( "verify_peer" => false,"verify_peer_name" => false ) );
$youtube_key = 'AIzaSyA7dxlViSTWdJGzgq-EhRcdiRKTU-FS2xA';

if( isset( $_POST['url'] ) ){    
    $url = $_POST['url'];
    if (strpos($url,'/channel/') == true) {
        $pos = strrpos($url, '/');
        $id = $pos === false ? $url : substr($url, $pos + 1);
        $posturl = 'https://www.googleapis.com/youtube/v3/search?key='.$youtube_key.'&channelId='.$id.'&part=snippet,id&order=date&maxResults=50';
        $data = file_get_contents( $posturl, false, stream_context_create($arrContextOptions));
        $response = json_decode($data);
        $count = count($response->items);
        $videoid = $response->items[rand(0,$count)]->id->videoId;
        $posturl = 'https://www.googleapis.com/youtube/v3/channels?part=snippet&id='.$id.'&key='.$youtube_key;
    }elseif(strpos($url,'/user/') == true) {
        $pos = strrpos($url, '/');
        $id = $pos === false ? $url : substr($url, $pos + 1);
        $posturl = 'https://www.googleapis.com/youtube/v3/channels?part=snippet&forUsername='.$id.'&key='.$youtube_key;
        $data = file_get_contents( $posturl, false, stream_context_create($arrContextOptions));
        $response = json_decode($data);
        $cid = $response->items[0]->id;
        $posturl = 'https://www.googleapis.com/youtube/v3/search?key='.$youtube_key.'&channelId='.$cid.'&part=snippet,id&order=date&maxResults=50';
        $data = file_get_contents( $posturl, false, stream_context_create($arrContextOptions));
        $response = json_decode($data);
        $count = count($response->items);
        $videoid = $response->items[rand(0,$count)]->id->videoId;
        $posturl = 'https://www.googleapis.com/youtube/v3/channels?part=snippet&forUsername='.$id.'&key='.$youtube_key;
    }elseif(strpos($url,'/watch?') == true){
        $url = parse_url($url);
        parse_str($url['query'], $query);
        $youtubeid = $query['v'];
        $posturl = 'https://www.googleapis.com/youtube/v3/videos?part=id%2C+snippet&id='.$youtubeid.'&key='.$youtube_key;
        $data = file_get_contents( $posturl, false, stream_context_create($arrContextOptions));
        $response = json_decode($data);
        $channelid = $response->items[0]->snippet->channelId;
        $videoid = $response->items[0]->id;
        $posturl = 'https://www.googleapis.com/youtube/v3/channels?part=snippet&id='.$channelid.'&key='.$youtube_key;
    }elseif(strpos($url,'/youtu.be/') == true){
        $pos = strrpos($url, '/');
        $youtubeid = $pos === false ? $url : substr($url, $pos + 1);
        $posturl = 'https://www.googleapis.com/youtube/v3/videos?part=id%2C+snippet&id='.$youtubeid.'&key='.$youtube_key;
        $data = file_get_contents( $posturl, false, stream_context_create($arrContextOptions));
        $response = json_decode($data);
        $channelid = $response->items[0]->snippet->channelId;
        $videoid = $response->items[0]->id;
        $posturl = 'https://www.googleapis.com/youtube/v3/channels?part=snippet&id='.$channelid.'&key='.$youtube_key;
    }else{
        echo 'https://image.flaticon.com/icons/svg/812/812892.svg';        
    }
    $data = file_get_contents( $posturl, false, stream_context_create($arrContextOptions));
    $response = json_decode($data);
    if(count($response->items)>0){
        $data = array(
            'url'=>$response->items[0]->snippet->thumbnails->high->url,
            'channelId'=>$response->items[0]->id,
            'videoid'=> isset($videoid)?$videoid:'',
            'count'=>$count
        );
        echo json_encode($data);
    }else{
        echo 'https://image.flaticon.com/icons/svg/812/812892.svg';
    }
	exit;
} elseif( isset( $_POST['action'] ) && $_POST['action'] == 'collab_form_channel_data' && isset( $_POST['channelid'] ) && !empty( $_POST['channelid'] ) ) {
	//$pos = strrpos($url, '/');
	$id = $_POST['channelid'];
	$posturl_search = 'https://www.googleapis.com/youtube/v3/search?key='.$youtube_key.'&channelId='.$id.'&part=snippet,id&order=date&maxResults=1';
	$data_search = file_get_contents( $posturl_search, false, stream_context_create($arrContextOptions));
	$response_search = json_decode($data_search);
	$count = count($response_search->items);
	$videoid = $response_search->items[0]->id->videoId;
	$posturl = 'https://www.googleapis.com/youtube/v3/channels?part=snippet&id='.$id.'&key='.$youtube_key;
	$data = file_get_contents( $posturl, false, stream_context_create($arrContextOptions));
    $response = json_decode($data);
    if( count( $response->items ) > 0 ){
        $data = array(
            'url'=>$response->items[0]->snippet->thumbnails->high->url,
            'channelId'=>$response->items[0]->id,
            'videoid'=> isset( $videoid ) ? $videoid:'',
            'count'=>$count
        );
        echo json_encode($data);
    }else{
        echo 'https://image.flaticon.com/icons/svg/812/812892.svg';
    }
	exit;	
}