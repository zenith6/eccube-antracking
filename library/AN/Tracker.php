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
 * トラッカーの基礎
 *
 * @package AnTracking
 * @author M-soft
 * @version $Id: $
 */
abstract class AN_Tracker {
    /**
     * @var stdClass プラグイン設定
     */
    protected $settings;
    
    public function __construct($settings = null) {
        if ($settings === null) {
            $settings = AnTracking::loadSettings();
        }
        
        $this->settings = $settings;
    }
    
    /**
     * @param array $data 売上トラッキングデータ
     * @return string 売上トラッキングタグ
     */
    abstract public function buildSellTrackingTag(array $data);
    
    /**
     * @param stdClass $context 実行環境
     */
    abstract public function buildSellTrackingData(stdClass $context);
    
}
