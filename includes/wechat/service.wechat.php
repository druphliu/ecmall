<?php

/**
 * Created by PhpStorm.
 * User: druphliu
 * Date: 14-1-25
 * Time: 上午11:36
 */
class WeChatService
{
    public $tag_to_user = 'touser';
    public $tag_msg_type = 'msgtype';
    public $josn_array;

    function __construct($openid, $msg_type)
    {
        $this->josn_array[$this->tag_to_user] = $openid;
        $this->josn_array[$this->tag_msg_type] = $msg_type;
    }

    function handle_post()
    {
        if (is_array($this->josn_array)) {
            $json = json_encode($this->josn_array);
        }
        $token_obj = new TokenWechat(APPID, APPSECRET);
        $access_token = $token_obj->get_token();
        $url = sprintf('https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=%s', $access_token);
        import("curl.lib");
        $curl = new curl();
        $curl->setOpt(CURLOPT_RETURNTRANSFER, TRUE);
        $curl->setOpt(CURLOPT_SSL_VERIFYPEER, FALSE);
        $result = $curl->post($url, $json);
        return $result;
    }

}

class WeChatTextService extends WeChatService
{
    private $tag_content = 'content';
    private $tag_text = 'text';

    function __construct($openid, $content)
    {
        parent::__construct($openid, "text");
        $this->josn_array[$this->tag_text][$this->tag_content] = $content;
    }
}

class WeChatImageService extends WeChatService
{
    private $tag_media_id = 'media_id';
    private $tag_image = 'image';

    function __construct($openid, $media_id)
    {
        parent::__construct($openid, 'image');
        $this->josn_array[$this->tag_image][$this->tag_media_id] = $media_id;
    }

}

class WeChatVoiceService extends WeChatService
{
    private $tag_media_id = 'media_id';
    private $tag_voice = 'voice';

    function __construct($openid, $media_id)
    {
        parent::__construct($openid, 'voice');
        $this->josn_array[$this->tag_voice][$this->tag_media_id] = $media_id;
    }

}

class WeChatVideoService extends WeChatService
{
    private $tag_media_id = 'media_id';
    private $tag_title = 'title';
    private $tag_description = 'description';
    private $tag_video = 'video';


    function __construct($openid, $media_id, $title, $description)
    {
        parent::__construct($openid, 'video');
        $item[$this->tag_media_id] = $media_id;
        $item[$this->tag_title] = $title;
        $item[$this->tag_description] = $description;
        $this->josn_array[$this->tag_video] = $item;
    }

}

class WeChatMusicService extends WeChatService
{
    private $tag_title = 'title';
    private $tag_description = 'description';
    private $tag_music_url = 'musicurl';
    private $tag_hq_music_url = 'hqmusicurl';
    private $tag_thumb_media_id = 'thumb_media_id';
    private $tag_music = 'music';


    function __construct($openid, $title, $description, $hq_music_url, $thumb_media_id)
    {
        parent::__construct($openid, 'music');
        $item[$this->tag_title] = $title;
        $item[$this->tag_description] = $description;
        $item[$this->tag_music_url] = $hq_music_url;
        $item[$this->tag_hq_music_url] = $hq_music_url;
        $item[$this->tag_thumb_media_id] = $thumb_media_id;
        $this->josn_array[$this->tag_music] = $item;
    }

}

class WeChatNewsService extends WeChatService
{
    private $tag_articles = 'articles';
    private $tag_title = 'title';
    private $tag_description = 'description';
    private $tag_url = 'url';
    private $tag_pic_url = 'picurl';
    private $tag_new = 'news';


    function __construct($openid)
    {
        parent::__construct($openid, 'news');
    }

    function add_article($title, $description, $url, $pic_url)
    {
        $item[$this->tag_title] = $title;
        $item[$this->tag_description] = $description;
        $item[$this->tag_url] = $url;
        $item[$this->tag_pic_url] = $pic_url;
        $this->josn_array[$this->tag_new][$this->tag_articles] = $item;
    }
}
