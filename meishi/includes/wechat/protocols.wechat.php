<?php

/**
 * Created by PhpStorm.
 * User: druphliu
 * Date: 14-1-22
 * Time: 下午4:36
 */
class WeChat
{
    public static $tag_create_time = 'CreateTime';
    public static $tag_from_user_name = 'FromUserName';
    public static $tag_to_user_name = 'ToUserName';
    public static $tag_msg_type = 'MsgType';
    public $create_time, $from_user_name, $to_user_name, $msg_type;
}

abstract class WeChatRequest extends WeChat
{
    abstract function _handle_node($param);

    function parse($root)
    {
        foreach ($root->childNodes as $param) {
            $tagName = $param->nodeName;
            $nodeValue = $param->nodeValue;
            switch ($tagName) {
                case parent::$tag_from_user_name:
                    $this->from_user_name = $nodeValue;
                    break;
                case parent::$tag_to_user_name:
                    $this->to_user_name = $nodeValue;
                    break;
                case parent::$tag_create_time:
                    $this->create_time = $nodeValue;
                    break;
                case parent::$tag_msg_type:
                    $this->msg_type = $nodeValue;
                    break;
                default:
                    $this->_handle_node($param);
                    break;
            }
        }
        return $this;
    }
}

class WeChatEventRequest extends WeChatRequest
{
    private $tag_event = 'Event';
    private $tag_ticket = 'Ticket';
    private $tag_eventkey = 'EventKey';
    private $tag_latitude = 'Latitude';
    private $tag_longitude = 'Longitude';
    private $tag_precision = 'Precision';

    private $event_value_subscribe = 'subscribe';
    private $event_value_unsubscribe = 'unsubscribe';
    private $event_value_scan = 'scan';
    private $event_value_location = 'LOCATION';
    private $event_value_click = 'CLICK';

    static public $type_none = 0;
    static public $type_subscribe = 1;
    static public $type_menu = 2;
    static public $type_location = 3;
    static public $type_scan = 4;

    public $event_type;
    public $ticket;
    public $event_key;
    public $latitude;
    public $longitude;
    public $precision;
    public $is_subscribe;

    function _handle_node($param)
    {
        $value = $param->nodeValue;
        $name = $param->nodeName;
        switch ($name) {
            case $this->tag_event:
                switch ($value) {
                    case $this->event_value_subscribe:
                        $this->event_type = WeChatEventRequest::$type_subscribe;
                        $this->is_subscribe = true;
                        break;
                    case $this->event_value_unsubscribe:
                        $this->event_type = WeChatEventRequest::$type_subscribe;
                        $this->is_subscribe = false;
                        break;
                    case $this->event_value_scan:
                        $this->event_type = WeChatEventRequest::$type_scan;
                        break;
                    case $this->event_value_click:
                        $this->event_type = WeChatEventRequest::$type_menu;
                        break;
                    case $this->event_value_location:
                        $this->event_type = WeChatEventRequest::$type_location;
                        break;
                    default:
                        $this->event_type = WeChatEventRequest::$type_none;
                        break;
                }
                break;
            case $this->tag_ticket:
                $this->ticket = $value;
                break;
            case $this->tag_eventkey:
                $this->event_key = $value;
                break;
            case $this->tag_longitude:
                $this->longitude = $value;
                break;
            case $this->tag_latitude:
                $this->latitude = $value;
                break;
            case $this->tag_precision:
                $this->precision = $value;
                break;
        }
    }
}

class WeChatCommonRequest extends WeChatRequest
{
    private $tag_msgid = 'MsgId';

    private $tag_content = 'Content';

    private $tag_picurl = 'PicUrl';
    private $tag_mediaid = 'MediaId';

    private $tag_format = 'Format';
    private $tag_recognition = 'Recognition'; //开通语音识别后会返回语音识别结果

    private $tag_thumbmediaid = 'ThumbMediaId';

    private $tag_location_x = 'Location_X';
    private $tag_location_y = 'Location_Y';
    private $tag_scale = 'Scale';
    private $tag_label = 'Label';

    private $tag_title = 'Title';
    private $tag_description = 'Description';
    private $tag_url = 'Url';

    private $type_value_text = 'text';
    private $type_value_image = 'image';
    private $type_value_voice = 'voice';
    private $type_value_video = 'video';
    private $type_value_location = 'location';
    private $type_value_link = 'link';

    public static $type_none = 0;
    public static $type_text = 1;
    public static $type_image = 2;
    public static $type_voice = 3;
    public static $type_video = 4;
    public static $type_location = 5;
    public static $type_link = 6;

    public $text_type;

    public $msg_id;

    public $content;

    public $pic_url;
    public $media_id;

    public $format;
    public $recognition;

    public $thumb_media_id;

    public $location_x;
    public $location_y;
    public $scale;
    public $label;

    public $title;
    public $description;
    public $url;

    function _handle_node($param)
    {
        $value = $param->nodeValue;
        $name = $param->nodeName;
        switch ($name) {
            case $this->tag_msgid:
                $this->msg_id = $value;
                break;
            case $this->tag_content:
                $this->content = $value;
                break;
            case $this->tag_picurl:
                $this->pic_url = $value;
                break;
            case $this->tag_mediaid:
                $this->media_id = $value;
                break;
            case $this->tag_format:
                $this->format = $value;
                break;
            case $this->tag_recognition:
                $this->recognition = $value;
                break;
            case $this->tag_thumbmediaid:
                $this->thumb_media_id = $value;
                break;
            case $this->tag_location_x:
                $this->location_x = $value;
                break;
            case $this->tag_location_y:
                $this->location_y = $value;
                break;
            case $this->tag_scale:
                $this->scale = $value;
                break;
            case $this->tag_label:
                $this->label = $value;
                break;
            case $this->tag_title:
                $this->title = $value;
                break;
            case $this->tag_description:
                $this->description = $value;
                break;
            case $this->tag_url:
                $this->url = $value;
                break;
        }
        switch ($this->msg_type) {
            case $this->type_value_text:
                $this->text_type = WeChatCommonRequest::$type_text;
                break;
            case $this->type_value_image:
                $this->text_type = WeChatCommonRequest::$type_image;
                break;
            case $this->type_value_voice:
                $this->text_type = WeChatCommonRequest::$type_voice;
                break;
            case $this->type_value_video:
                $this->text_type = WeChatCommonRequest::$type_video;
                break;
            case $this->type_value_location:
                $this->text_type = WeChatCommonRequest::$type_location;
                break;
            case $this->type_value_link:
                $this->text_type = WeChatCommonRequest::$type_link;
                break;
        }
    }
}

class WeChatRequestBuilder
{
    private $request_text = 'text';
    private $request_event = 'event';

    function builder($xml)
    {
        $dom = new DOMDocument();
        $dom->loadXML($xml);
        $root = $dom->documentElement;
        $msg_type = $root->getElementsByTagName(WeChat::$tag_msg_type)->item(0)->nodeValue;
        switch ($msg_type) {
            case $this->request_text:
                $object = new WeChatCommonRequest();
                break;
            case $this->request_event:
                $object = new WeChatEventRequest();
                break;
        }
        return $object->parse($root);
    }
}

abstract class WebChatResponse extends WeChat
{
    public $dom, $root;
    function __construct()
    {
        $this->dom = new DOMDocument("1.0");
        $this->root = $this->dom->createElement("xml");
        $this->dom->appendChild($this->root);
        $this->_to_cdata_data($this->root, $this->dom, parent::$tag_msg_type, $this->msg_type);
    }

    abstract function _to_element();

    function _to_xml($request)
    {
        $this->_to_cdata_data($this->root, $this->dom, parent::$tag_from_user_name, $request->to_user_name);
        $this->_to_cdata_data($this->root, $this->dom, parent::$tag_to_user_name, $request->from_user_name);
        $this->_to_text_data($this->root, $this->dom, parent::$tag_create_time, $request->create_time);
        $this->_to_element();
        return $this->dom->saveXML();
    }

    function _to_text_data($root, $dom, $tag, $value)
    {
        $item = $dom->createElement($tag);
        $root->appendChild($item);
        $text = $dom->createTextNode($value);
        $item->appendChild($text);
    }

    function _to_cdata_data($root, $dom, $tag, $value)
    {
        $item = $dom->createElement($tag);
        $root->appendChild($item);
        $text = $dom->createCDATASection($value);
        $item->appendChild($text);
    }

}

class WeChatTextResponse extends WebChatResponse
{
    private $tag_content = "Content";
    private $content;

    function __construct($content)
    {
        $this->msg_type = 'text';
        parent::__construct();
        $this->content = $content;
    }

    function _to_element()
    {
        $this->_to_cdata_data($this->root, $this->dom, $this->tag_content, $this->content);
    }
}

class WeChatImageResponse extends WebChatResponse
{
    private $tag_image = 'Image';
    private $tag_media_id = 'MediaId';
    private $media_id;

    function __construct($media_id)
    {
        $this->msg_type = 'image';
        parent::__construct();
        $this->media_id = $media_id;
    }

    function _to_element()
    {
        $image_item = $this->dom->createElement($this->tag_image);
        $this->root->appendChild($image_item);
        $this->_to_cdata_data($image_item, $this->dom, $this->tag_media_id, $this->media_id);
    }
}

class WeChatVoiceResponse extends WebChatResponse
{
    private $tag_voice = 'Voice';
    private $tag_media_id = 'MediaId';
    private $media_id;

    function __construct($media_id)
    {
        $this->msg_type = 'voice';
        parent::__construct();
        $this->media_id = $media_id;
    }

    function _to_element()
    {
        $voice_element = parent::$dom->createElement($this->tag_voice);
        parent::$dom->appendChild($voice_element);
        $this->_to_cdata_data($voice_element, parent::$dom, $this->tag_media_id, $this->media_id);
    }
}

class WeChatVideoResponse extends WebChatResponse
{
    private $tag_video = 'Video';
    private $tag_media_id = 'MediaId';
    private $tag_title = 'Title';
    private $tag_description = 'Description';
    private $media_id;
    private $title;
    private $description;

    function __construct($media_id, $title, $description)
    {
        $this->msg_type = 'video';
        parent::__construct();
        $this->media_id = $media_id;
        $this->title = $title;
        $this->description = $description;
    }

    function _to_element()
    {
        $video_element = parent::$dom->createElement($this->tag_video);
        parent::$dom->appendChild($video_element);
        $this->_to_cdata_data($video_element, parent::$dom, $this->tag_media_id, $this->media_id);
        $this->_to_cdata_data($video_element, parent::$dom, $this->tag_title, $this->title);
        $this->_to_cdata_data($video_element, parent::$dom, $this->tag_description, $this->description);
    }
}

class WeChatMusicResponse extends WebChatResponse
{
    private $tag_music = 'Music';
    private $tag_title = 'Title';
    private $tag_description = 'Description';
    private $tag_music_url = 'MusicUrl';
    private $tag_HQ_music_url = 'HQMusicUrl';
    private $tag_thumb_media_id = 'ThumbMediaId';
    private $title;
    private $description;
    private $music_url;
    private $hq_music_url;
    private $thumb_media_id;

    function __construct($title, $description, $music_url, $hq_music_url, $thumb_media_id)
    {
        $this->msg_type = 'music';
        parent::__construct();
        $this->title = $title;
        $this->description = $description;
        $this->music_url = $music_url;
        $this->hq_music_url = $hq_music_url;
        $this->thumb_media_id = $thumb_media_id;
    }

    function _to_element()
    {
        $music_element = parent::$dom->createElement($this->tag_music);
        parent::$dom->appendChild($music_element);
        $this->_to_cdata_data($music_element, parent::$dom, $this->tag_title, $this->title);
        $this->_to_cdata_data($music_element, parent::$dom, $this->tag_description, $this->description);
        $this->_to_cdata_data($music_element, parent::$dom, $this->tag_music_url, $this->music_url);
        $this->_to_cdata_data($music_element, parent::$dom, $this->tag_HQ_music_url, $this->hq_music_url);
        $this->_to_cdata_data($music_element, parent::$dom, $this->tag_thumb_media_id, $this->thumb_media_id);
    }
}

class WeChatArticleResponse extends WebChatResponse
{
    private $tag_article_count = 'ArticleCount';
    private $tag_articles = 'Articles';
    private $tag_item = 'item';
    private $tag_title = 'Title';
    private $tag_description = 'Description';
    private $tag_pic_url = 'PicUrl';
    private $tag_url = 'Url';

    private $count;
    private $array = array();

    function __construct($array)
    {
        $this->msg_type = 'news';
        parent::__construct();
        $this->count = count($array);
        if ($this->count > 10)
            exit("不能超过10条");
        $this->array = $array;
    }

    function _to_element()
    {
        $this->_to_text_data($this->root, $this->dom, $this->tag_article_count, $this->count);
        $articles_item = $this->dom->createElement($this->tag_articles);
        $this->root->appendChild($articles_item);
        foreach ($this->array as $item) {
            $item_item = $this->dom->createElement($this->tag_item);
            $articles_item->appendChild($item_item);
            $this->_to_cdata_data($item_item, $this->dom, $this->tag_title, $item['title']);
            $this->_to_cdata_data($item_item, $this->dom, $this->tag_description, $item['description']);
            $this->_to_cdata_data($item_item, $this->dom, $this->tag_pic_url, $item['pic_url']);
            $this->_to_cdata_data($item_item, $this->dom, $this->tag_url, $item['url']);
        }
    }
}


