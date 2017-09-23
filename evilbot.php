<?php
error_reporting(0);

class Pic {
    public $url;
    protected $resourceDomain;

    protected function getPicUrl() {
        $picUrl = $this->resourceDomain . '/gallery/images/preview/2017/09/19/2529195.jpg';
        $this->url = $picUrl;
        return $picUrl;
    }

    /**
     * @param TelegramBot $to
     * @param string $picUrl
     * @return string
     */
    protected function getResponseTelegramUrl($to, $picUrl) {
        return $to->telegramUrl . '/sendPhoto?chat_id=' . $to->chatID . '&photo='
        . $picUrl;
    }

    public function send($to) {
        $picUrl = $this->getPicUrl();
        $responseTelegramUrl = $this->getResponseTelegramUrl($to, $picUrl);
        file_get_contents($responseTelegramUrl);
    }


}

class CommonPic extends Pic {
    protected $resourceDomain = 'https://club.foto.ru';

    protected function getPicUrl() {
        $doc = new DOMDocument();
        /**
         * @var DOMElement|null $picElement
         */
        $picElement = null;
        while ($picElement === null) {
            $picElement = $this->getPicElement($doc);
        }
        $picUrl = $picElement->getAttribute('src');
        if (empty($picUrl)) {
            $picUrl = '/gallery/images/preview/2017/09/19/2529195.jpg';
        }

        return $this->resourceDomain . $picUrl;
    }

    /**
     * @param DOMDocument $doc
     * @return DOMElement
     */
    private function getPicElement($doc) {
        $pageId = rand(100, 2289664);
        $loadPicUrl = $this->resourceDomain . "/gallery/35/photos/$pageId/";
        $doc->loadHTMLFile($loadPicUrl);

        return $doc->getElementById('mainpic');
    }

}

class NaturePic extends CommonPic {

    protected function getPicUrl() {
        $doc = new DOMDocument();
        $pageId = rand(1, 500);
        $loadPicUrl = $this->resourceDomain . "/gallery/photos/top100.php?cat_id=35&sort=date&page=$pageId/";
        $doc->loadHTMLFile($loadPicUrl);
        $elements = $doc->getElementsByTagName('img');

        $imagesArray = [];
        /**
         * @var DOMElement $el
         */
        foreach ($elements as $el) {
            $imgUrl = $el->getAttribute('src');
            if (strpos($imgUrl, '/gallery/images/') !== false) {
                $imagesArray[] = $imgUrl;
            }
        }
        $picPreviewUrl = $imagesArray[array_rand($imagesArray)];
        if (empty($picPreviewUrl)) {
            $picPreviewUrl = '/gallery/images/small/2014/05/23/2289772.jpg';
        }

        return $this->resourceDomain . str_replace('small', 'photo', $picPreviewUrl);
    }

}

class BoobsPic extends Pic {
    protected $resourceDomain = 'http://oboobs.ru/';

    protected function getPicUrl() {
        $doc = new DOMDocument();
        $pageId = rand(1, 555);
        $loadPicUrl = $this->resourceDomain . "$pageId/";
        $doc->loadHTMLFile($loadPicUrl);
        $elements = $doc->getElementsByTagName('img');

        $imagesArray = [];
        /**
         * @var DOMElement $el
         */
        foreach ($elements as $el) {
            $imgUrl = $el->getAttribute('src');
            if (strpos($imgUrl, 'boobs_preview') !== false) {
                $imagesArray[] = $imgUrl;
            }
        }
        $picUrl = $imagesArray[array_rand($imagesArray)];
        if (empty($picUrl)) {
            $picUrl = 'http://media.oboobs.ru/boobs_preview/10040.jpg';
        }

        return $picUrl;
    }

    public function send($to) {
        parent::send($to);
        //mail('imply91@gmail.com', 'from bot', print_r($to->updates, true) . print_r($this->url, true));
    }
}

class TelegramBot {
    public $updates;
    public $chatID;
    private $token = "418750412:AAFWHRgJIWX6-A6ku6tlje49b67FhBOaRMo";
    public $telegramUrl;

    public function __construct() {
        $this->telegramUrl = "https://api.telegram.org/bot{$this->token}";
    }

    public function getUpdates() {
        $updates = file_get_contents('php://input');
        $updates = json_decode($updates, true);
        $this->updates = $updates;
    }

    public function sendResponse() {
        $command = $this->getCommand();
        switch ($command) {
            case 'pic':
                $pic = new CommonPic();
                $pic->send($this);
                break;
            case 'nature_pic':
                $pic = new NaturePic();
                $pic->send($this);
                break;
            case 'куку':
                $this->sendMessage('Сам ты куку %)');
                break;
            case 'boobs':
                $pic = new BoobsPic();
                $pic->send($this);
                break;
            default:
                return false;
        }

        return false;
    }

    public function getCommand() {
        $command = '';
        $text = $this->updates['message']['text'];
        if (substr($text, 0, 1) === '/') {
            $this->chatID = $this->updates['message']['chat']['id'];
            if (strpos($text, '@streshyag_bot') !== false) {
                $command = substr($text, 1, strpos($text, '@') - 1);
            } else {
                $command = substr($text, 1);
            }
        }

        return $command;
    }

    function sendMessage($message) {
        $url = $this->telegramUrl . '/sendMessage?chat_id=' . $this->chatID . '&text='
            . urlencode($message);
        file_get_contents($url);
    }

}
$telegramBot = new TelegramBot();
$telegramBot->getUpdates();
$telegramBot->sendResponse();