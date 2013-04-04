<!--{*
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
 *}-->

<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_header.tpl"}-->

<h2><!--{$tpl_subtitle|h}--></h2>

<form name="form1" id="form1" method="post" action="<!--{$smarty.server.REQUEST_URI|h}-->">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="save" />

<script type="text/javascript">
$(document).ready(function () {
    var switchProductForm = function () {
        var product = $("input[name='product']:checked").val();
        $("[data-product]:not([data-product~='" + product + "'])").each(function () {
            $(this).hide();
        });
        $("[data-product~='" + product + "']").each(function () {
            $(this).show();
        });
    };

    switchProductForm();
    $("input[name='product']").change(switchProductForm);
});
</script>

<table class="form">
    <tr>
        <th>ご利用のアフィリナビ</th>
        <td>
            <!--{assign var="key" value="product"}-->
            <span class="attention"><!--{$form_errors[$key]}--></span>
            <!--{html_radios name=$key options=$product_options selected=$form_values[$key] separator='<br />'}-->
        </td>
    </tr>
    <tr data-product="an7_st anpro_st">
        <th>API設定コード</th>
        <td>
            <!--{assign var="key" value="api_settings_code"}-->
            <!--{if $form_errors[$key]}--><span class="attention"><!--{$form_errors[$key]}--></span><!--{/if}--><br />
            <textarea name="<!--{$key}-->" cols="50" rows="8" class="area50" maxlength="<!--{$smarty.const.LLTEXT_LEN}-->" style="<!--{$form_errors[$key]|sfGetErrorColor}-->"><!--{$form_values[$key]|h}--></textarea>
            <span data-product="an7_st">API設定コードはAN7の各キャンペーンのトラッキングタグから取得できます。</span>
            <span data-product="anpro_st">API設定コードはアフィリナビ管理画面のセキュアトラッキングから取得できます。</span>
        </td>
    </tr>
    <tr data-product="an7">
        <th>売上トラッキングタグ</th>
        <td>
            <!--{assign var="key" value="an7_sell_tracking_tag"}-->
            <!--{if $form_errors[$key]}--><span class="attention"><!--{$form_errors[$key]}--></span><!--{/if}--><br />
            <textarea name="<!--{$key}-->" cols="50" rows="20" class="box50" maxlength="<!--{$smarty.const.LLTEXT_LEN}-->" wrap="off" style="<!--{$form_errors[$key]|sfGetErrorColor}-->"><!--{$form_values[$key]|h|smarty:nodefaults}--></textarea>
            <span>各キャンペーンのトラッキングタグ画面で取得した売上トラッキングタグをそのまま入力して下さい。</span>
            <span class="attention">注文番号はユーザー定義パラメーター「注文番号」に記録されます。AN7側で定義されていない場合でも記録されます。</span>
        </td>
    </tr>
    <tr data-product="anpro">
        <th>売上トラッキングタグ</th>
        <td>
            <!--{assign var="key" value="anpro_sell_tracking_tag"}-->
            <!--{if $form_errors[$key]}--><span class="attention"><!--{$form_errors[$key]}--></span><!--{/if}--><br />
            <textarea name="<!--{$key}-->" cols="50" rows="8" class="area50" maxlength="<!--{$smarty.const.LLTEXT_LEN}-->" style="<!--{$form_errors[$key]|sfGetErrorColor}-->"><!--{$form_values[$key]|h}--></textarea>
            <span>アフィリナビ管理画面で取得したトラッキングタグをそのまま入力して下さい。</span>
            <span class="attention">ダイレクトリンク用トラッキングタグは入力しないで下さい。</span>
        </td>
    </tr>
</table>

<div class="btn-area">
    <ul>
        <li>
            <a class="btn-action" href="#" onclick="window.close(); return false;"><span class="btn-next">閉じる</span></a>
        </li>
        <li>
            <a class="btn-action" href="#" onclick="document.form1.submit(); return false;"><span class="btn-next">登録する</span></a>
        </li>
    </ul>
</div>

</form>

<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_footer.tpl"}-->
