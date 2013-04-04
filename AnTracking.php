<?php
/*
 * EC-CUBEアフィリナビトラッキングプラグイン
 * Copyright (C) 2013 M-soft All Rights Reserved.
 * http://m-soft.jp/
 * 
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

/**
 * プラグインのメインクラス
 *
 * @package AnTracking
 * @author M-soft
 * @version $Id: $
 */
class AnTracking extends SC_Plugin_Base {
    const SESSION_NAME = 'antracking.trackingdata';

    /**
     * プラグイン設定
     * 
     * @var stdClass
     */
    static $settings;
    
    /**
     * コンストラクタ
     */
    public function __construct(array $arrSelfInfo) {
        parent::__construct($arrSelfInfo);
        
        // オートローダー用にライブラリへのパスを追加。
        ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . PLUGIN_UPLOAD_REALDIR . '/AnTracking/library');
    }
    
    /**
     * プラグインをインストールします。
     *
     * @param array $info プラグイン情報(dtb_plugin)
     * @return void
     */
    public function install($info) {
        // プラグインロゴを配置。
        copy(PLUGIN_UPLOAD_REALDIR . "AnTracking/logo.png", PLUGIN_HTML_REALDIR . "AnTracking/logo.png");
    }
    
    /**
     * プラグインをアンインストールします。
     *
     * @param array $info プラグイン情報
     * @return void
     */
    public function uninstall($info) {
        // プラグイン用のHTMLディレクトリを削除。
        SC_Helper_FileManager_Ex::deleteFile(PLUGIN_HTML_REALDIR . 'AnTracking');
    }
    
    /**
     * プラグインを有効化します。
     *
     * @param array $info プラグイン情報
     * @return void
     */
    public function enable($info) {
    }
    
    /**
     * プラグインを無効化します。
     *
     * @param array $info プラグイン情報
     * @return void
     */
    public function disable($info) {
    }
    
    /**
     * フックを登録します。
     *
     * @param SC_Helper_Plugin $plugin
     * @param int $priority
     */
    public function register(SC_Helper_Plugin $plugin, $priority) {
        parent::register($plugin, $priority);
        
        // 注文完了時に売上トラッキングタグ用のトラッキングデータを用意する。
        $plugin->addAction('LC_Page_Shopping_Confirm_action_confirm', array(&$this, 'hookActionPrepareSellTrackingData'));
        
        // 注文完了時の出力に売上トラッキングタグを挿入する。
        $plugin->addAction('outputfilterTransform', array(&$this, 'hookActionOutputSellTrackingTag'));
    }
    
    /**
     * フックアクション。注文完了時に売上トラッキングタグ用のトラッキングデータを用意します。
     * 
     * @param LC_Page_EX $page
     */
    public function hookActionPrepareSellTrackingData(LC_Page_EX $page) {
        $order_id = $page->arrForm['order_id'];
        
        $context = (object)array(
            'page' => $page,
        );
        $tracker = $this->getTracker();
        $data = $tracker->buildSellTrackingData($context);
        $this->saveTrackingData('sell', $data);

        GC_Utils_Ex::gfPrintLog(var_export($data, true));
        return $data;
    }
    
    /**
     * フックアクション。注文完了時に売上トラッキングタグを出力します。
     * 
     * @param string $source
     * @param LC_Page_EX $page
     * @param string $filename
     */
    public function hookActionOutputSellTrackingTag(&$source, LC_Page_EX $page, $filename) {
        // 注文完了ページ以外は無用。
        if (!($page instanceof LC_Page_Shopping_Complete)) {
            return;
        }
        
        $data = $this->loadTrackingData('sell');
        GC_Utils_Ex::gfPrintLog(var_export($data, true));
        
        // トラッキングデータがなければ何もしない。
        if (!$data) {
            return;
        }

        $tracker = $this->getTracker();
        $tag = $tracker->buildSellTrackingTag($data);
        GC_Utils_Ex::gfPrintLog(var_export($tag, true));
        
        // 売上トラッキングタグを挿入する。
        $transfomer = new SC_Helper_Transform_Ex($source);
        $transfomer->select('body')->appendChild($tag);
        
        $source = $transfomer->getHTML();
        
        // 再表示時に売上トラッキングタグを再度出力しないようにするため。
        $this->deleteTrackingData('sell');
    }
    
    /**
     * @throws LogicException
     * @return AN_Tracker
     */
    public function getTracker() {
        $settings = AnTracking::loadSettings();
        switch ($settings->product) {
            case 'an7':
                $class = 'AN_An7Tracker';
                break;
                
            case 'anpro':
                $class = 'AN_AnproTracker';
                break;
                
            case 'anpro_st':
                $class = 'AN_AnproStTracker';
                break;
                
            default:
                throw new LogicException('Not supported product.'); 
        }

        $tracker = new $class($settings);
        return $tracker;
    }
    
    protected function saveTrackingData($type, $data) {
        $_SESSION[self::SESSION_NAME][$type] = $data;
    }
    
    protected function loadTrackingData($type) {
        return $_SESSION[self::SESSION_NAME][$type];
    }
    
    protected function deleteTrackingData($type = null) {
        if ($type === null) {
            unset($_SESSION[self::SESSION_NAME]);
        } else {
            unset($_SESSION[self::SESSION_NAME][$type]);
        }
    }
    
    /**
     * @return stdClass
     */
    public static function loadSettings($refresh = false) {
        if (!self::$settings || $refresh) {
            $query = SC_Query::getSingletonInstance();
            $row = $query->getCol('free_field1', 'dtb_plugin', 'plugin_code = ?', array(__CLASS__));
            if (empty($row[0])) {
                $settings = new stdClass();
            } else {
                $settings = json_decode($row[0]);
            }
            
            self::$settings = $settings;
        }
        
        return self::$settings;
    }

    /**
     * @param stdClass $settings
     */
    public static function saveSettings($settings) {
        $query = SC_Query::getSingletonInstance();
        $values = array();
        $values['free_field1'] = json_encode($settings);
        $query->update('dtb_plugin', $values, 'plugin_code = ?', array(__CLASS__));
        
        self::$settings = $settings;
    }
}
