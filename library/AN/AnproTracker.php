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
 * アフィリナビPRO用トラッカー
 *
 * @package AnTracking
 * @author M-soft
 * @version $Id: $
 */
class AN_AnproTracker extends AN_Tracker {
    /**
     * @param array $data ランディングトラッキングデータ
     * @return string ランディングトラッキングタグ
     */
    public function buildLandTrackingTag(array $data) {
        return '';
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
            'data' => '注文番号#' . $order_id,
        );
        
        return $data;
    }
    
    /**
     * @param array $data 売上トラッキングデータ
     * @return string 売上トラッキングタグ
     */
    public function buildSellTrackingTag(array $data) {
        $template = $this->settings->anpro_sell_tracking_tag;

        preg_match('#src=["\'](https?://[^\'"]+)["\']#u', $template, $captures);
        $endpoint = $captures[1];
        $url = parse_url($endpoint);

        $form = array(
            't' => 'action',
            'o' => 'i',
            'guid' => 'ON',
            'sales' => $data['sales'],
            'data' => $data['data'],
        );
        
        $new_endpoint = $url['scheme'] . '://' . $url['host'] . $url['path'] . '?' . http_build_query($form);
        $replacement = 'src="' . htmlspecialchars($new_endpoint, ENT_QUOTES, 'UTF-8') . '"';
        $tag = preg_replace('#src=["\']https?://[^\'"]+["\']#u', $replacement, $template);
        
        return $tag;
    }
}
