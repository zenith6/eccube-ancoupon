<!--{*
 * アフィリナビクーポンプラグイン
 * Copyright (C) 2014 M-soft All Rights Reserved.
 * http://m-soft.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *}-->

<div id="undercolumn">
    <h2 class="title"><!--{$tpl_title|h}--></h2>

    <div id="undercolumn_coupon">
        <!--{if $coupon_expired_error}-->
            <div class="error">
                <p><span class="attention">注文処理中にクーポンの使用が許可されませんでした。申し訳ありませんがクーポンコードを再度入力し直して、再度ご購入手続きへお進み下さい。</span></p>
            </div>
        <!--{else}-->
            <p>当店が発行したクーポンコードをご入力いただくと割引を受けられます。クーポンコードをお持ちの方はぜひご活用ください。</p>
            <p>クーポンによって割引対象になる商品や割引額は変わります。実際の割引額は各商品の価格欄でご確認いただけます。</p>
            <p>クーポンコードは注文完了の時点で使用されたとみなされます。それ以前ならば何時でも使用を取り消すことが可能です。使用期間や回数が決まっているものもありますのでご注意ください。</p>
        <!--{/if}-->

        <form name="form1" method="post" action="?">
            <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
            <input type="hidden" name="mode" value="use" />
            <input type="hidden" name="context" value="<!--{$context|h}-->" />

            <table summary="クーポンコードの入力">
                <colgroup>
                    <col width="30%">
                    <col width="70%">
                </colgroup>
                <tr>
                    <th><!--{$form.coupon_code.title|h}--><span class="attention"> *</span></th>
                    <td>
                        <!--{if $form.coupon_code.error}--><span class="attention"><!--{$form.coupon_code.error}--></span><!--{/if}-->
                        <input type="text" name="coupon_code" value="<!--{$form.coupon_code.value|h}-->" maxlength="<!--{$form.coupon_code.maxlength|h}-->" size="40" class="box240 coupon-code" <!--{if $form.coupon_code.error}--><!--{sfSetErrorStyle}--><!--{/if}--> />
                    </td>
                </tr>
            </table>

            <div class="btn_area">
                <ul>
                    <li>
                        <input type="submit" name="use" value="クーポンを利用する" />
                    </li>
                </ul>
            </div>
        </form>
    </div>
</div>
