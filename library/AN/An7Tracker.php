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
 * AN7用トラッカー
 *
 * @package AnTracking
 * @author M-soft
 * @version $Id: $
 */
class AN_An7Tracker extends AN_Tracker {
    /**
     * @param array $data ランディングトラッキングデータ
     * @return string ランディングトラッキングタグ
     */
    public function buildLandTrackingTag(array $data) {
        return $this->settings->an7_land_tracking_tag;
    }

    /**
     * @param stdClass $context 実行環境
     */
    public function buildLandTrackingData(stdClass $context) {
        return array();
    }
    
    /**
     * @param stdClass $context 実行環境
     */
    public function buildSellTrackingData(stdClass $context) {
        $page = $context->page;
        $order_id = $page->arrForm['order_id'];

        $query = SC_Query::getSingletonInstance();
        $order = $query->getRow('*', 'dtb_order', 'order_id = ?', array($order_id));
        
        $data = array(
            'sales' => $order['subtotal'],
            'order_id' => $order_id,
        );
        return $data;
    }
    
    /**
     * @param array $data 売上トラッキングデータ
     * @return string 売上トラッキングタグ
     */
    public function buildSellTrackingTag(array $data) {
        $template = $this->settings->an7_sell_tracking_tag;
        $patterns = $replacements = array();
        
        // 売上金額
        $patterns['sales'] = '/(?<=\\s)params\\["sales"\\]\\s*=[^;]+;/mu';
        $replacements['sales'] = 'params["sales"] = ' . json_encode($data['sales']) . ';';
        
        // 注文番号（ユーザー定義パラメーター「注文番号」が存在する場合に置換）
        $patterns['order_id'] = '/(?<=\\s)params\\["custom\\[\\\\u6ce8\\\\u6587\\\\u756a\\\\u53f7\\]"\\]\\s*=[^;]+;/mu';
        $replacements['order_id'] = 'params["custom[\\u6ce8\\u6587\\u756a\\u53f7]"] = ' . json_encode($data['order_id']) . ';';

        // 注文番号（ユーザー定義パラメーター「注文番号」が存在しない場合、ユーザー定義パラメーターを追加する）
        if (!preg_match($patterns['order_id'], $template)) {
            $patterns[] = '/(?=var tracking_url =)/u';
            $replacements[] = $replacements['order_id'] . "\n\n";
        }
        
        $tag = preg_replace($patterns, $replacements, $template);
        return $tag;
    }
}
