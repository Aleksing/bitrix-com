<div>
<?php if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["getDiscount"])): ?>
<p>Ваша скидка: <?=$arResult["DISCOUNT_VALUE"]?>%</p>
<p>Код скидки: <?=$arResult["DISCOUNT_CODE"]?></p>		
<?php else: ?>

	<?php if($arResult["SHOW_BUTTON"] == true): ?>
        <form method="POST" action="">
            <button type="submit" name="getDiscount">Получить скидку</button>
        </form>
		<p></p>
	<?php else: ?>
		<form method="POST" action="">
			<input type="text" name="discountCode" placeholder="Введите код скидки">
			<button type="submit" name="checkDiscountCode">Проверить скидку</button>
		</form>
		<p></p> 
    <?php endif; ?>

<?php endif; ?>

    <?php if(isset($arResult["VALID_DISCOUNT"])): ?>
        <p>Скидка действительна!</p>
    <?php elseif(isset($arResult["INVALID_DISCOUNT"])): ?>
        <p>Скидка недействительна</p>
    <?php endif; ?>
</div>