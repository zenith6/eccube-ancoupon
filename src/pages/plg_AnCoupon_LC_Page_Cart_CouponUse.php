<?php
/*
 * EC-CUBEアフィリナビクーポンプラグイン
 * Copyright (C) 2014 M-soft All Rights Reserved.
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

require_once PLUGIN_UPLOAD_REALDIR . '/AnCoupon/pages/plg_AnCoupon_LC_Page.php';

/**
 * クーポンの編集画面
 *
 * @package AnCoupon
 * @author M-soft
 * @version $Id: $
 */
class plg_AnCoupon_LC_Page_Cart_CouponUse extends plg_AnCoupon_LC_Page
{
    /**
     *
     * @var string
     */
    protected $defaultMode = 'input';

    public function init()
    {
        parent::init();

        $this->tpl_title = 'クーポンの使用';
    }

    protected function initializeContext(An_Eccube_PageContext $context)
    {
        $coupon_code = '';
        $context['coupon'] = $coupon_code;

        $destination = isset($_GET['destination']) && is_string($_GET['destination']) ? $_GET['destination'] : '';
        $context['destination'] = $destination;
    }

    protected function doInput($errors = array())
    {
        $params = $this->buildFormParam();
        $form = $this->buildForm($params, $errors);
        $this->form = $form;

        $this->coupon_expired_error = isset($_GET['coupon_expired_error']);
    }

    protected function doUse()
    {
        try {
            $tx = An_Eccube_Model::beginTransaction();

            $params = $this->buildFormParam();
            $params->setParam($_POST);

            $errors = $this->validateFormParam($params);
            if ($errors) {
                $tx->rollback();
                $this->context['coupon_code'] = $params->getValue('coupon_code');
                $this->doInput($errors);
                return;
            }

            $coupon_code = $params->getValue('coupon_code');
            $coupon_code = An_Eccube_Coupon::normalizeCode($coupon_code);
            $plugin = AnCoupon::getInstance();
            $plugin->clearUsingCouponCode();
            $plugin->useCouponCode($coupon_code);

            $tx->commit();

            $destination = $this->context['destination'];
            if ($destination == '') {
                $destination = ROOT_URLPATH . 'cart/plg_AnCoupon_coupon_status.php';
            }
            SC_Response_Ex::sendRedirect($destination);
        } catch (Exception $e) {
            $tx->rollback();

            throw $e;
        }
    }

    /**
     * @param SC_FormParam_Ex $params
     * @param array $errors
     * @return array
     */
    protected function buildForm(SC_FormParam_Ex $params, $errors = array())
    {
        $form = array();

        foreach ($params->keyname as $index => $key) {
            $form[$key] = array(
                'title' => $params->disp_name[$index],
                'value' => $params->getValue($key),
                'maxlength' => $params->length[$index],
                'error' => null,
            );
        }

        foreach ($errors as $key => $error) {
            $form[$key]['error'] = $error;
        }

        return $form;
    }

    /**
     * @return SC_FormParam_Ex
     */
    protected function buildFormParam()
    {
        $params = new SC_FormParam_Ex();

        $params->addParam('クーポンコード', 'coupon_code', 64, '', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'), $this->context['coupon_code']);

        return $params;
    }

    /**
     * @param SC_FormParam_Ex $params
     * @return array キーにフォーム名、値にエラーメッセージを収めた連想配列。
     */
    protected function validateFormParam(SC_FormParam_Ex $params)
    {
        $errors = $params->checkError();

        // クーポンコード
        $name = 'coupon_code';
        $value = $params->getValue($name);
        $title = $params->disp_name[array_search($name, $params->keyname)];
        if ($value == '') {
        } else {
            $coupon_code = An_Eccube_Coupon::normalizeCode($value);
            $coupon = An_Eccube_Coupon::findByCode($coupon_code);
            $customer = new SC_Customer_Ex();
            $used_time = time();
            if (!$coupon) {
                $errors[$name] = "※ ご指定頂いたクーポンコードはご利用できません。入力内容に間違えがないかご確認下さい。<br />";
            } elseif ($coupon->isUsesLimitReached()) {
                $errors[$name] = "※ ご指定頂いたクーポンコードはご利用できません。使用回数を超えています。<br />";
            } elseif (!$coupon->isUserTargeted($customer)) {
                $errors[$name] = "※ ご指定頂いたクーポンコードはご利用できません。現在のログイン状態ではご利用いただけません。<br />";
            } elseif (!$coupon->isInPeriod(time())) {
                $errors[$name] = "※ ご指定頂いたクーポンコードはご利用できません。有効期間を過ぎています。<br />";
            } elseif (!$coupon->isAvailable($used_time, $customer)) {
                $errors[$name] = "※ ご指定頂いたクーポンコードはご利用できません。<br />";
            }
        }

        return $errors;
    }
}
