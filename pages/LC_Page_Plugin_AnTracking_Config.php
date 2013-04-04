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

require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';
require_once PLUGIN_UPLOAD_REALDIR . 'AnTracking/AnTracking.php';

/**
 * プラグインの設定画面
 *
 * @package AnTracking
 * @author M-soft
 * @version $Id: $
 */
class LC_Page_Plugin_AnTracking_Config extends LC_Page_Admin_Ex {
    /**
     * フォームの値を収めた連想配列。
     * テンプレートで使用します。
     * 
     * @var array 
     */
    var $form_values;

    /**
     * フォームのエラーを収めた連想配列。
     * テンプレートで使用します。
     * 
     * @var array
     */
    var $form_errors;
    
    /**
     * ご利用の製品の選択肢を収めた連想配列。
     * テンプレートの {html_radios name=product options=$product_options} で使用します。
     * 
     * @var array
     */
    var $product_options = array(
        'an7' => 'AN7',
//         'an7_st' => 'AN7 セキュアトラッキング',
        'anpro' => 'アフィリナビPRO',
        'anpro_st' => 'アフィリナビPRO + セキュアトラッキングプラグイン',
    );
    
    function init() {
        parent::init();

        $this->tpl_subtitle = 'アフィリナビトラッキングプラグイン設定';
        $this->setTemplate(PLUGIN_UPLOAD_REALDIR . "AnTracking/templates/config.tpl");
    }
    
    function process() {
        $this->action();
        $this->sendResponse();
    }
    
    /**
     * 現在の画面モードに従ってアクションを呼び出します。
     */
    function action() {
        $mode = $this->getMode();
        switch ($mode) {
            case 'save':
                $this->actionSave();
                break;
                
            case 'edit':
            default:
                $this->actionEdit();
                break;
        }
    }
    
    /**
     * 編集アクションを実行します。
     */
    public function actionEdit() {
        $form = $this->buildForm();

        $settings = AnTracking::loadSettings();
        $this->setFormValues($form, $settings);
        
        $this->form_values = $form->getHashArray();
    }
    
    /**
     * 保存アクションを実行します。
     */
    public function actionSave() {
        $form = $this->buildForm();
        $form->setParam($_POST);
        
        $errors = $this->validateForm($form);
        if ($errors) {
            $this->form_values = $form->getHashArray();
            $this->form_errors = $errors;
            return;
        }

        $settings = $this->buildNewSettings($form);
        AnTracking::saveSettings($settings);
        
        $form = $this->buildForm();
        $this->setFormValues($form, $settings);
        $this->form_values = $form->getHashArray();
        $this->tpl_javascript = "$(window).load(function () { alert('登録しました。'); });";
    }
    
    protected function setFormValues($form, $settings) {
        $form->setValue('product', $settings->product);
        $form->setValue('api_settings_code', $settings->api ? json_encode($settings->api) : '');
        $form->setValue('an7_sell_tracking_tag', $settings->an7_sell_tracking_tag);
        $form->setValue('anpro_sell_tracking_tag', $settings->anpro_sell_tracking_tag);
    }
    
    /**
     * 設定フォームから新しい設定を構築します。
     * 
     * @param SC_FormParam_Ex $form
     */
    protected function buildNewSettings(SC_FormParam_Ex $form, stdClass $original = null) {
        if (!$original) {
            $settings = AnTracking::loadSettings(true);
        } else {
            $settings = $original;
        }
        
        $settings->product = $form->getValue('product');
        
        switch ($settings->product) {
            case 'anpro':
                $settings->product = 'anpro';
                $settings->product_version = null;
                $settings->api = null;
                $settings->anpro_sell_tracking_tag = $form->getValue('anpro_sell_tracking_tag');
                break;
                
            case 'an7':
                $settings->product = 'an7';
                $settings->product_version = null;
                $settings->api = null;
                $settings->an7_sell_tracking_tag = $form->getValue('an7_sell_tracking_tag');
                break;
                
            case 'anpro_st':
                $json = $form->getValue('api_settings_code');
                $api_settings = json_decode($json);
                $settings->product = $api_settings->product;
                $settings->product_version = $api_settings->product_version;
                $settings->api = $api_settings;
                break;
                
            default:
                throw new RuntimeException();
                break;
        }
        
        
        return $settings;
    }
    
    /**
     * 設定フォームを構築します。
     * 
     * @return SC_FormParam_Ex
     */
    protected function buildForm() {
        $form = new SC_FormParam_Ex();
        $form->addParam('ご利用のアフィリナビ', 'product', 32, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
        $form->addParam('設定コード', 'api_settings_code', LLTEXT_LEN, '', array('MAX_LENGTH_CHECK'));
        $form->addParam('売上トラッキングタグ', 'an7_sell_tracking_tag', LLTEXT_LEN, '', array('MAX_LENGTH_CHECK'));
        $form->addParam('売上トラッキングタグ', 'anpro_sell_tracking_tag', LLTEXT_LEN, '', array('MAX_LENGTH_CHECK'));
        return $form;
    }
    
    /**
     * 設定フォームを検証し、問題のある個所を配列で返します。
     * 
     * @param SC_FormParam_Ex $form
     * @return array キーにフォーム名、値にエラーメッセージを収めた連想配列。
     */
    function validateForm($form) {
        $errors = $form->checkError();
        if ($errors) {
            return $errors;
        }

        $errors = array();

        $product = $form->getValue('product');
        switch ($product) {
            case 'anpro':
                $key = 'anpro_sell_tracking_tag';
                $index = array_search($key, $form->keyname);
                $anpro_sell_tracking_tag = $form->getValue($key);
                if (strlen($anpro_sell_tracking_tag) == 0) {
                    $errors[$key] = '※ ' . $form->disp_name[$index] . 'が空欄です。<br />';
                }
                break;
                
            case 'an7':
                $key = 'an7_sell_tracking_tag';
                $index = array_search($key, $form->keyname);
                $anpro_sell_tracking_tag = $form->getValue($key);
                if (strlen($anpro_sell_tracking_tag) == 0) {
                    $errors[$key] = '※ ' . $form->disp_name[$index] . 'が空欄です。<br />';
                }
                break;
        
            default:
                $key = 'api_settings_code';
                $index = array_search($key, $form->keyname);
                $settings_code = $form->getValue($key);

                if (strlen($settings_code) == 0) {
                    $errors[$key] = '※ ' . $form->disp_name[$index] . 'が空欄です。<br />';
                }
                
                $settings = @json_decode($settings_code);
                if (!is_object($settings)) {
                    $errors[$key] = '※ ' . $form->disp_name[$index] . 'が認識できません。<br />';
                    return $errors;
                }
                
                $products = array('an7', 'anpro', 'anpro_st');
                if (!in_array(@$settings->product, $products, true)) {
                    $errors[$key] = '※ ' . $form->disp_name[$index] . 'の製品に対応していません。<br />';
                }
                
                $product_supported = false;
                $api_supported = true;
                switch ($settings->product) {
                    case 'anpro':
                        $product_supported = version_compare($settings->product_version, '1.0.0.0') >= 0;
                        break;
                
                    case 'anpro_st':
                        $product_supported = version_compare($settings->product_version, '1.9.0.0') >= 0;
                        break;
                
                    case 'an7':
                        @list($drupal, $module) = (array)explode('-', $settings->product_version, 2);
                        $product_supported = version_compare($module, '1.0') >= 0;
                        break;
                }
                if (!$product_supported) {
                    $errors[$key] = '※ ' . $form->disp_name[$index] . 'の製品のバージョンに対応していません。<br />';
                }
                if (!$api_supported) {
                    $errors[$key] = '※ ' . $form->disp_name[$index] . 'のAPIのバージョンに対応していません。<br />';
                }
                
                if (empty($settings->encryption_key)) {
                    $errors[$key] = '※ ' . $form->disp_name[$index] . 'の暗号化鍵が空欄です。<br />';
                }
                
                break;
        }
        
        return $errors;
    }
}
