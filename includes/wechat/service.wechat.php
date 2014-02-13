<?php

/**
 * Created by PhpStorm.
 * User: druphliu
 * Date: 14-1-25
 * Time: 上午11:36
 */
class WeChat
{
    public $tag_to_user = 'touser';
    public $tag_msg_type = 'msgtype';
    public $to_user, $msg_type;
}

abstract class WeChatService extends WeChat
{
    public $josn_array;

    function __construct()
    {
        $this->josn_array[$this->tag_to_user] = $this->to_user;
        $this->josn_array[$this->tag_msg_type] = $this->msg_type;
    }

    abstract function _add_item();

    function to_json()
    {
        $this->_add_item();
        if (is_array($this->josn_array)) {
            $json = json_encode($this->josn_array);
        }
        return $json;
    }

}

class WeChatTextService extends WeChatService
{
    private $tag_content = 'content';
    private $content;

    function __construct($content)
    {
        $this->content = $content;
        $this->msg_type = 'text';
        parent::__construct();
    }

    function _add_item()
    {
        $this->josn_array[$this->msg_type][$this->tag_content] = $this->content;
    }
}

class WeChatImageService extends WeChatService
{
    private $tag_media_id = 'media_id';

    private $media_id;

    function __construct($media_id)
    {
        $this->media_id = $media_id;
        $this->msg_type = 'image';
        parent::__construct();
    }

    function _add_item()
    {
        $this->josn_array[$this->msg_type][$this->tag_media_id] = $this->media_id;
    }
}

class WeChatVoiceService extends WeChatService
{
    private $tag_media_id = 'media_id';

    private $media_id;

    function __construct($media_id)
    {
        $this->msg_type = 'voice';
        $this->media_id = $media_id;
        parent::__construct();
    }

    function _add_item()
    {
        $this->josn_array[$this->msg_type][$this->tag_media_id] = $this->media_id;
    }
}

class WeChatVideoService extends WeChatService
{
    private $tag_media_id = 'media_id';
    private $tag_title = 'title';
    private $tag_description = 'description';

    private $media_id, $title, $description;

    function __construct($media_id, $title, $description)
    {
        $this->msg_type = 'video';
        $this->media_id = $media_id;
        $this->title = $title;
        $this->description = $description;
        parent::__construct();
    }

    function _add_item()
    {
        $item[$this->tag_media_id] = $this->media_id;
        $item[$this->tag_title] = $this->title;
        $item[$this->tag_description] = $this->description;
        $this->josn_array[$this->msg_type] = $item;
    }
}

class WeChatMusicService extends WeChatService
{
    private $tag_title = 'title';
    private $tag_description = 'description';
    private $tag_music_url = 'musicurl';
    private $tag_hq_music_url = 'hqmusicurl';
    private $tag_thumb_media_id = 'thumb_media_id';

    private $title, $description, $music_url, $hq_music_url, $thumb_media_id;

    function __construct($title, $description, $hq_music_url, $thumb_media_id)
    {
        $this->msg_type = 'music';
        $this->title = $title;
        $this->description = $description;
        $this->hq_music_url = $hq_music_url;
        $this->thumb_media_id = $thumb_media_id;
        parent::__construct();
    }

    function _add_item()
    {
        $item[$this->tag_title] = $this->title;
        $item[$this->tag_description] = $this->description;
        $item[$this->tag_music_url] = $this->music_url;
        $item[$this->tag_hq_music_url] = $this->hq_music_url;
        $item[$this->tag_thumb_media_id] = $this->thumb_media_id;
        $this->josn_array[$this->msg_type] = $item;
    }
}

class WeChatNewsService extends WeChatService
{
    private $tag_articles = 'articles';
    private $tag_title = 'title';
    private $tag_description = 'description';
    private $tag_url = 'url';
    private $tag_pic_url = 'picurl';

    private $array;

    function __construct($array)
    {
        $this->array = $array;
        if (count($this->array) > 10) {
            exit("不能大于10条");
        }
        parent::__construct();
    }

    function _add_item()
    {
        foreach ($this->array as $items) {
            $item[$this->tag_title] = $items[$this->tag_title];
            $item[$this->tag_description] = $items[$this->tag_description];
            $item[$this->tag_url] = $items[$this->tag_url];
            $item[$this->tag_pic_url] = $items[$this->tag_pic_url];
            $this->josn_array[$this->msg_type][$this->tag_articles][] = $item;
        }
    }
}

class BuilderWeChat{
    function build($json, $access_token){
        $url = sprintf('https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=%s', $access_token);

    }
}