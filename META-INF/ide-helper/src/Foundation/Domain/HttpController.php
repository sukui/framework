<?php

namespace Zan\Framework\Foundation\Domain;

use Zan\Framework\Foundation\Core\Config;
use Zan\Framework\Network\Http\Response\RedirectResponse;
use Zan\Framework\Network\Http\Response\Response;
use Zan\Framework\Network\Http\Response\JsonResponse;
use Zan\Framework\Foundation\View\View;
use Zan\Framework\Contract\Network\Request;
use Zan\Framework\Utilities\DesignPattern\Context;
use Zan\Framework\Foundation\View\JsVar;
use ZanPHP\HttpFoundation\Response\FileResponse;

class HttpController extends Controller
{
    protected $viewData = [];
    protected $jsVar = null;

    public function __construct(Request $request, Context $context)
    {
        parent::__construct($request, $context);
        $this->jsVar = new JsVar();
    }

    public function setJsVar($key, $value)
    {
        // 剔除全局模版js变量值包含标签
        if (is_string($value)) {
            $value = strip_tags($value);
        }
        $this->jsVar->setBusiness($key, $value);
    }

    public function setShare($cover, $title, $desc)
    {
        $this->jsVar->setShare('cover', trim($cover));
        $this->jsVar->setShare('title', trim($title));
        $this->jsVar->setShare('desc', trim($desc));
    }

    public function setShareData(array $shareItems){
        foreach($shareItems as $key=>$item){
            $this->jsVar->setShare($key, $item);
        }
    }

    public function setDomains(array $domains)
    {
        $this->jsVar->setDomain($domains);
    }

    public function getJsVars()
    {
        return $this->jsVar->get();
    }

    public function output($content)
    {
        return new Response($content);
    }

    private function setupQiniu()
    {
        $this->jsVar->setConfig('qn_public', Config::get('qiniu.scope.public', null));
        $this->jsVar->setConfig('qn_private', Config::get('qiniu.scope.private', null));
    }

    public function display($tpl)
    {
        $this->setupQiniu();
        $csrfToken = $this->context->get('csrf_token', '');
        $this->jsVar->setCsrfToken($csrfToken);
        $this->viewData['_js_var'] = $this->getJsVars();
        $content = View::display($tpl, $this->viewData);
        return $this->output($content);
    }

    public function render($tpl)
    {
        $this->setupQiniu();
        $csrfToken = $this->context->get('csrf_token', '');
        $this->jsVar->setCsrfToken($csrfToken);
        $this->viewData['_js_var'] = $this->getJsVars();
        return View::display($tpl, $this->viewData);
    }

    public function sendFile($filepath, array $headers = [])
    {
        return new FileResponse($filepath, 200, $headers);
    }

    public function assign($key, $value)
    {
        $this->viewData[$key] = $value;
    }

    public function r($code, $msg, $data)
    {
        $data = [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data,
        ];
        return new JsonResponse($data);
    }

    public function redirect($url, $code = 302)
    {
        return RedirectResponse::create($url, $code);
    }
}
