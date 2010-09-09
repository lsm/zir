<?php

class IndexController extends Zend_Controller_Action
{

    public function indexAction()
    {
        echo '<a href="index/generate/">宝贝详细生成助手</a>';
        $this->_helper->viewRenderer->setNoRender();
    }

    public function generateAction()
    {
        $this->view->assign('photoNumber', 5);

        if ($this->_getParam('verify') == 1) {
            $pc = '';
            foreach ($_POST['photo'] as $p) {
                if (!empty($p))
                $pc .= '<P align=center><IMG style="BORDER-RIGHT: #000000 0px solid; BORDER-TOP: #000000 0px solid; BORDER-LEFT: #000000 0px solid; BORDER-BOTTOM: #000000 0px solid" src="'. $p .'" align=absMiddle></P>';
            }
            $dc = '';
            $desc = explode("\r\n", $_POST['desc']);
            foreach ($desc as $d) {
                if (!empty($d))
                $dc .= '<P align=center><FONT color=#ff6699>◎' . $d . '</FONT></P>';
            }
            $tpl = file_get_contents('tb-tpl-utf8.html');
            $tpl = str_replace('{$photo}', $pc, $tpl);
            $tpl = str_replace('{$desc}', $dc, $tpl);
            $this->view->assign('preview', $tpl);
        }
    }
}
?>