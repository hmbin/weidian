<?php
if (!defined("IN_IA")) {
    exit("Access Denied");
}
class Plugin extends Core
{
    public $pluginname;
    public $model;
    public function __construct($val2 = '')
    {
        parent::__construct();
        $this->modulename = "ewei_shop";
        $this->pluginname = $val2;
        $this->loadModel();
        if (strexists($_SERVER["REQUEST_URI"], "/web/")) {
            cpa($this->pluginname);
        } else {
            if (strexists($_SERVER["REQUEST_URI"], "/app/")) {
                $this->setFooter();
            }
        }
        $this->module["title"] = pdo_fetchcolumn("select title from " . tablename("modules") . " where name='ewei_shop' limit 1");
    }
    private function loadModel()
    {
        $val12 = IA_ROOT . "/addons/" . $this->modulename . "/plugin/" . $this->pluginname . "/model.php";
        if (is_file($val12)) {
            $val16 = ucfirst($this->pluginname) . "Model";
            require $val12;
            $this->model = new $val16($this->pluginname);
        }
    }
    public function getSet()
    {
        return $this->model->getSet();
    }
    public function updateSet($val22 = array())
    {
        $this->model->updateSet($val22);
    }
    public function template($val24, $val25 = TEMPLATE_INCLUDEPATH)
    {
        global $_W;
        $val27 = IA_ROOT . "/addons/ewei_shop/";
        if (defined("IN_SYS")) {
            $val28 = IA_ROOT . "/addons/ewei_shop/plugin/" . $this->pluginname . "/template/{$val24}.html";
            $val31 = IA_ROOT . "/data/tpl/web/{$_W['template']}/ewei_shop/plugin/" . $this->pluginname . "/{$val24}.tpl.php";
            if (!is_file($val28)) {
                $val28 = IA_ROOT . "/addons/ewei_shop/template/{$val24}.html";
                $val31 = IA_ROOT . "/data/tpl/web/{$_W['template']}/ewei_shop/{$val24}.tpl.php";
            }
            if (!is_file($val28)) {
                $val28 = IA_ROOT . "/web/themes/{$_W['template']}/{$val24}.html";
                $val31 = IA_ROOT . "/data/tpl/web/{$_W['template']}/{$val24}.tpl.php";
            }
            if (!is_file($val28)) {
                $val28 = IA_ROOT . "/web/themes/default/{$val24}.html";
                $val31 = IA_ROOT . "/data/tpl/web/default/{$val24}.tpl.php";
            }
        } else {
            $val53 = m("cache")->getString("template_shop");
            if (empty($val53)) {
                $val53 = "default";
            }
            if (!is_dir(IA_ROOT . "/addons/ewei_shop/template/mobile/" . $val53)) {
                $val53 = "default";
            }
            $val58 = m("cache")->getString("template_" . $this->pluginname);
            if (empty($val58)) {
                $val58 = "default";
            }
            if (!is_dir(IA_ROOT . "/addons/ewei_shop/plugin/" . $this->pluginname . "/template/mobile/" . $val58)) {
                $val58 = "default";
            }
            $val31 = IA_ROOT . "/data/app/ewei_shop/plugin/" . $this->pluginname . "/{$val58}/mobile/{$val24}.tpl.php";
            $val28 = $val27 . "/plugin/" . $this->pluginname . "/template/mobile/{$val58}/{$val24}.html";
            if (!is_file($val28)) {
                $val28 = $val27 . "/plugin/" . $this->pluginname . "/template/mobile/default/{$val24}.html";
                $val31 = IA_ROOT . "/data/app/ewei_shop/plugin/" . $this->pluginname . "/default/mobile/{$val24}.tpl.php";
            }
            if (!is_file($val28)) {
                $val28 = $val27 . "/template/mobile/{$val53}/{$val24}.html";
                $val31 = IA_ROOT . "/data/app/ewei_shop/{$val53}/{$val24}.tpl.php";
            }
            if (!is_file($val28)) {
                $val28 = $val27 . "/template/mobile/default/{$val24}.html";
                $val31 = IA_ROOT . "/data/app/ewei_shop/default/{$val24}.tpl.php";
            }
            if (!is_file($val28)) {
                $val28 = $val27 . "/template/mobile/{$val24}.html";
                $val31 = IA_ROOT . "/data/app/ewei_shop/{$val24}.tpl.php";
            }
            if (!is_file($val28)) {
                $val99 = explode("/", $val24);
                $val101 = $val99[0];
                $val103 = m("cache")->getString("template_" . $val101);
                if (empty($val103)) {
                    $val103 = "default";
                }
                if (!is_dir(IA_ROOT . "/addons/ewei_shop/plugin/" . $val101 . "/template/mobile/" . $val103)) {
                    $val103 = "default";
                }
                $val110 = $val99[1];
                $val28 = IA_ROOT . "/addons/ewei_shop/plugin/" . $val101 . "/template/mobile/" . $val103 . "/{$val110}.html";
            }
        }
        if (!is_file($val28)) {
            exit("Error: template source '{$val24}' is not exist!");
        }
        if (DEVELOPMENT || !is_file($val31) || filemtime($val28) > filemtime($val31)) {
            shop_template_compile($val28, $val31, true);
        }
        return $val31;
    }
    public function _exec_plugin($val124, $val125 = true)
    {
        global $_GPC;
        if ($val125) {
            $val128 = IA_ROOT . "/addons/ewei_shop/plugin/" . $this->pluginname . "/core/web/" . $val124 . ".php";
        } else {
            $val128 = IA_ROOT . "/addons/ewei_shop/plugin/" . $this->pluginname . "/core/mobile/" . $val124 . ".php";
        }
        if (!is_file($val128)) {
            message("未找到控制器文件 : {$val128}");
        }
        include $val128;
        exit;
    }
}