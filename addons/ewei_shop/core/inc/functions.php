<?php
if (!defined("IN_IA")) {
    exit("Access Denied");
}
function m($val0 = '') {
    static $val1 = array();
    if (isset($val1[$val0])) {
        return $val1[$val0];
    }
    $val6 = EWEI_SHOP_CORE . "model/" . strtolower($val0) . ".php";
    if (!is_file($val6)) {
        die(" Model " . $val0 . " Not Found!");
    }
    require $val6;
    $val11 = "Ewei_Dshop_" . ucfirst($val0);
    $val1[$val0] = new $val11();
    return $val1[$val0];
}
function p($val0 = '') {
    if ($val0 != "perm" && !IN_MOBILE) {
        static $val21;
        if (!$val21) {
            $val22 = EWEI_SHOP_PLUGIN . "perm/model.php";
            if (is_file($val22)) {
                require $val22;
                $val25 = "PermModel";
                $val21 = new $val25("perm");
            }
        }
        if ($val21) {
            if (!$val21->check_plugin($val0)) {
                return false;
            }
        }
    }
    static $val30 = array();
    if (isset($val30[$val0])) {
        return $val30[$val0];
    }
    $val6 = EWEI_SHOP_PLUGIN . strtolower($val0) . "/model.php";
    if (!is_file($val6)) {
        return false;
    }
    require $val6;
    $val11 = ucfirst($val0) . "Model";
    $val30[$val0] = new $val11($val0);
    return $val30[$val0];
}
function byte_format($val46, $val47 = 0) {
    $val48 = array(
        " B",
        "K",
        "M",
        "G",
        "T"
    );
    $val49 = round($val46, $val47);
    $val52 = 0;
    while ($val49 > 1024) {
        $val49/= 1024;
        $val52++;
    }
    $val56 = round($val49, $val47) . $val48[$val52];
    return $val56;
}
function save_media($val62) {
    load()->func("file");
    $val63 = array(
        "qiniu" => false
    );
    $val64 = p("qiniu");
    if ($val64) {
        $val63 = $val64->getConfig();
        if ($val63) {
            if (strexists($val62, $val63["url"])) {
                return $val62;
            }
            $val72 = $val64->save(tomedia($val62) , $val63);
            if (empty($val72)) {
                return $val62;
            }
            return $val72;
        }
        return $val62;
    }
    return $val62;
}
function save_remote($val62) {
}
function is_array2($val81) {
    if (is_array($val81)) {
        foreach ($val81 as $val84 => $val85) {
            return is_array($val85);
        }
        return false;
    }
    return false;
}
function set_medias($val87 = array() , $val88 = null) {
    if (empty($val88)) {
        foreach ($val87 as & $val91) {
            $val91 = tomedia($val91);
        }
        return $val87;
    }
    if (!is_array($val88)) {
        $val88 = explode(",", $val88);
    }
    if (is_array2($val87)) {
        foreach ($val87 as $val100 => & $val49) {
            foreach ($val88 as $val103) {
                if (isset($val87[$val103])) {
                    $val87[$val103] = tomedia($val87[$val103]);
                }
                if (is_array($val49) && isset($val49[$val103])) {
                    $val49[$val103] = tomedia($val49[$val103]);
                }
            }
        }
        return $val87;
    } else {
        foreach ($val88 as $val103) {
            if (isset($val87[$val103])) {
                $val87[$val103] = tomedia($val87[$val103]);
            }
        }
        return $val87;
    }
}
function get_last_day($val127, $val128) {
    return date("t", strtotime("{$val127}-{$val128} -1"));
}
function show_message($val130 = '', $val62 = '', $val132 = 'success') {
    $val133 = "<script language='javascript'>require(['core'],function(core){ core.message('" . $val130 . "','" . $val62 . "','" . $val132 . "')})</script>";
    die($val133);
}
function show_json($val138 = 1, $val139 = null) {
    $val140 = array(
        "status" => $val138
    );
    if ($val139) {
        $val140["result"] = $val139;
    }
    die(json_encode($val140));
}
function is_weixin() {
    if (empty($_SERVER["HTTP_USER_AGENT"]) || strpos($_SERVER["HTTP_USER_AGENT"], "MicroMessenger") === false && strpos($_SERVER["HTTP_USER_AGENT"], "Windows Phone") === false) {
        return false;
    }
    return true;
}
function b64_encode($val149) {
    if (is_array($val149)) {
        return urlencode(base64_encode(json_encode($val149)));
    }
    return urlencode(base64_encode($val149));
}
function b64_decode($val153, $val154 = true) {
    $val153 = base64_decode(urldecode($val153));
    if ($val154) {
        return json_decode($val153, true);
    }
    return $val153;
}
function create_image($val160) {
    $val161 = strtolower(substr($val160, strrpos($val160, ".")));
    if ($val161 == ".png") {
        $val165 = imagecreatefrompng($val160);
    } else if ($val161 == ".gif") {
        $val165 = imagecreatefromgif($val160);
    } else {
        $val165 = imagecreatefromjpeg($val160);
    }
    return $val165;
}
function get_authcode() {
    $val173 = get_auth();
    return empty($val173["code"]) ? '' : $val173["code"];
}
function get_auth() {
    global $_W;
    $val177 = pdo_fetch("select sets from " . tablename("ewei_shop_sysset") . " order by id asc limit 1");
    $val178 = iunserializer($val177["sets"]);
    if (is_array($val178)) {
        return is_array($val178["auth"]) ? $val178["auth"] : array();
    }
    return array();
}
function check_shop_auth($val62 = '', $val132 = 's') {
    global $_W, $_GPC;
    if ($_W["ispost"] && $_GPC["do"] != "auth") {
        $val173 = get_auth();
        load()->func("communication");
        $val190 = $_SERVER["HTTP_HOST"];
        $val192 = gethostbyname($val190);
        $val194 = setting_load("site");
        $val195 = isset($val194["site"]["key"]) ? $val194["site"]["key"] : '0';
        if (empty($val132) || $val132 == "s") {
            $val200 = array(
                "type" => $val132,
                "ip" => $val192,
                "id" => $val195,
                "code" => $val173["code"],
                "domain" => $val190
            );
        } else {
            $val200 = array(
                "type" => "m",
                "m" => $val132,
                "ip" => $val192,
                "id" => $val195,
                "code" => $val173["code"],
                "domain" => $val190
            );
        }
        $val212 = ihttp_post($val62, $val200);
        $val138 = $val212["content"];
        if ($val138 != "1") {
            message(base64_decode("57O757uf5q2j5Zyo57u05oqk77yM6K+35oKo56iN5ZCO5YaN6K+V77yM5pyJ55aR6Zeu6K+36IGU57O757O757uf566h55CG5ZGYIQ==") , '', "error");
        }
    }
}
$my_scenfiles = array();
function my_scandir($val219) {
    global $my_scenfiles;
    if ($val221 = opendir($val219)) {
        while (($val223 = readdir($val221)) !== false) {
            if ($val223 != ".." && $val223 != ".") {
                if (is_dir($val219 . "/" . $val223)) {
                    my_scandir($val219 . "/" . $val223);
                } else {
                    $my_scenfiles[] = $val219 . "/" . $val223;
                }
            }
        }
        closedir($val221);
    }
}
function shop_template_compile($val235, $val236, $val237 = false) {
    $val238 = dirname($val236);
    if (!is_dir($val238)) {
        load()->func("file");
        mkdirs($val238);
    }
    $val242 = shop_template_parse(file_get_contents($val235) , $val237);
    if (IMS_FAMILY == "x" && !preg_match("/(footer|header|account\/welcome|login|register)+/", $val235)) {
        $val242 = str_replace("微擎", "系统", $val242);
    }
    file_put_contents($val236, $val242);
}
function shop_template_parse($val153, $val237 = false) {
    $val153 = template_parse($val153, $val237);
    $val153 = preg_replace("/{ifp\s+(.+?)}/", "<?php if(cv($1)) { ?>", $val153);
    $val153 = preg_replace("/{ifpp\s+(.+?)}/", "<?php if(cp($1)) { ?>", $val153);
    $val153 = preg_replace("/{ife\s+(\S+)\s+(\S+)}/", "<?php if( ce($1 ,$2) ) { ?>", $val153);
    return $val153;
}
function ce($val266 = '', $val267 = null) {
    $val268 = p("perm");
    if ($val268) {
        return $val268->check_edit($val266, $val267);
    }
    return true;
}
function cv($val272 = '') {
    $val268 = p("perm");
    if ($val268) {
        return $val268->check_perm($val272);
    }
    return true;
}
function ca($val272 = '') {
    if (!cv($val272)) {
        message("您没有权限操作，请联系管理员!", '', "error");
    }
}
function cp($val278 = '') {
    $val268 = p("perm");
    if ($val268) {
        return $val268->check_plugin($val278);
    }
    return true;
}
function cpa($val278 = '') {
    if (!cp($val278)) {
        message("您没有权限操作，请联系管理员!", '', "error");
    }
}
function plog($val132 = '', $val285 = '') {
    $val268 = p("perm");
    if ($val268) {
        $val268->log($val132, $val285);
    }
}
function tpl_form_field_category_3level($val0, $val291, $val292, $val293, $val294, $val295) {
    $val296 = '
<script type="text/javascript">
	window._' . $val0 . ' = ' . json_encode($val292) . ';
</script>';
    if (!defined("TPL_INIT_CATEGORY_THIRD")) {
        $val296.= '	
<script type="text/javascript">
	function renderCategoryThird(obj, name){
		var index = obj.options[obj.selectedIndex].value;
		require([\'jquery\', \'util\'], function($, u){
			$selectChild = $(\'#\'+name+\'_child\');
                                                      $selectThird = $(\'#\'+name+\'_third\');
			var html = \'<option value="0">请选择二级分类</option>\';
                                                      var html1 = \'<option value="0">请选择三级分类</option>\';
			if (!window[\'_\'+name] || !window[\'_\'+name][index]) {
				$selectChild.html(html); 
                                                                        $selectThird.html(html1);
				return false;
			}
			for(var i=0; i< window[\'_\'+name][index].length; i++){
				html += \'<option value="\'+window[\'_\'+name][index][i][\'id\']+\'">\'+window[\'_\'+name][index][i][\'name\']+\'</option>\';
			}
			$selectChild.html(html);
                                                    $selectThird.html(html1);
		});
	}
        function renderCategoryThird1(obj, name){
		var index = obj.options[obj.selectedIndex].value;
		require([\'jquery\', \'util\'], function($, u){
			$selectChild = $(\'#\'+name+\'_third\');
			var html = \'<option value="0">请选择三级分类</option>\';
			if (!window[\'_\'+name] || !window[\'_\'+name][index]) {
				$selectChild.html(html);
				return false;
			}
			for(var i=0; i< window[\'_\'+name][index].length; i++){
				html += \'<option value="\'+window[\'_\'+name][index][i][\'id\']+\'">\'+window[\'_\'+name][index][i][\'name\']+\'</option>\';
			}
			$selectChild.html(html);
		});
	}
</script>
			';
        define("TPL_INIT_CATEGORY_THIRD", true);
    }
    $val296.= '<div class="rowrow-fix tpl-category-container">
	<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
		<select class="form-control tpl-category-parent" id="' . $val0 . '_parent" name="' . $val0 . '[parentid]" onchange="renderCategoryThird(this,\'' . $val0 . '\') ">
			<option value="0">请选择一级分类</option>';
    $val318 = '';
    foreach ($val291 as $val91) {
        $val296.= '
			<option value="' . $val91['id'] . '" ' . (($val91["id"] == $val293) ? 'selected="selected"' : '') . '>' . $val91["name"] . '</option>';
    }
    $val296.= '
		</select>
	</div>
	<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
		<select class="form-control tpl-category-child" id="' . $val0 . '_child" name="' . $val0 . '[childid]" onchange="renderCategoryThird1(this,\'' . $val0 . '\') ">
			<option value="0">请选择二级分类</option>';
    if (!empty($val293) && !empty($val292[$val293])) {
        foreach ($val292[$val293] as $val91) {
            $val296.= '
			<option value="' . $val91["id"] . '"' . (($val91["id"] == $val294) ? 'selected="selected"' : '') . '>' . $val91["name"] . '</option>';
        }
    }
    $val296.= '
		</select> 
	</div> 
                  <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
		<select class="form-control tpl-category-child" id="' . $val0 . '_third" name="' . $val0 . '[thirdid]">
			<option value="0">请选择三级分类</option>';
    if (!empty($val294) && !empty($val292[$val294])) {
        foreach ($val292[$val294] as $val91) {
            $val296.= '
			<option value="' . $val91["id"] . '"' . (($val91["id"] == $val295) ? 'selected="selected"' : '') . '>' . $val91["name"] . '</option>';
        }
    }
    $val296.= '</select>
	</div>
</div>';
    return $val296;
} 
?>
