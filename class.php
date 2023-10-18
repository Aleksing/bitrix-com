<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class MyComponent extends CBitrixComponent
{
    public function executeComponent()
    {
        global $USER;
		$userId = $USER->GetID();

        $lastDiscount = $this->getLastUserDiscount($userId);

		if (!$USER->IsAuthorized()) {
            echo "<p>Скидку может получить только зарегистрированный пользователь</p>";
			$this->arResult["SHOW_BUTTON"] = false;
        } else {
			if (!$lastDiscount || (time() - strtotime($lastDiscount["generated_at"])) >= 3600) {
				$this->arResult["SHOW_BUTTON"] = true;
			}
		}
		
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["getDiscount"])) {		
            $discountValue = rand(1, 50);
            $discountCode = uniqid();
            $this->saveUserDiscount($userId, $discountValue, $discountCode);

            $this->arResult["DISCOUNT_VALUE"] = $discountValue;
            $this->arResult["DISCOUNT_CODE"] = $discountCode;
        }
		
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["checkDiscountCode"])) {
            $enteredCode = htmlspecialcharsbx($_POST["discountCode"]);
            $isValidDiscount = $this->checkDiscountCode($userId, $enteredCode);
            if ($isValidDiscount) {
                $this->arResult["VALID_DISCOUNT"] = true;
            } else {
                $this->arResult["INVALID_DISCOUNT"] = true;
            }
        }
        
        $this->includeComponentTemplate();
		
    }

    private function getLastUserDiscount($userId)
    {
        global $DB;

        $sql = "SELECT * FROM b_user_discounts WHERE user_id = " . intval($userId) . " ORDER BY generated_at DESC LIMIT 1";
        $res = $DB->Query($sql);

        return $res->Fetch();
    }

    private function saveUserDiscount($userId, $discountValue, $discountCode)
    {
        global $DB;

        $sql = "INSERT INTO b_user_discounts (user_id, discount_value, discount_code, generated_at) VALUES (" . intval($userId) . ", " . intval($discountValue) . ", '" . $DB->ForSql($discountCode) . "', NOW())";
        $DB->Query($sql);
    }

    private function checkDiscountCode($userId, $enteredCode)
    {
        global $DB;

        $sql = "SELECT * FROM b_user_discounts WHERE user_id = " . intval($userId) . " AND discount_code = '" . $DB->ForSql($enteredCode) . "' AND generated_at > (NOW() - INTERVAL 3 HOUR)";
        $res = $DB->Query($sql);

        return $res->Fetch() ? true : false;
    }

}