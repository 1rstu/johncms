<?php

defined('_IN_JOHNADM') or die('Error: restricted access');

// Проверяем права доступа
if ($rights < 9) {
    header('Location: http://johncms.com/?err');
    exit;
}

/** @var PDO $db */
$db = App::getContainer()->get(PDO::class);

echo '<div class="phdr"><a href="index.php"><b>' . _t('Admin Panel') . '</b></a> | ' . _t('Mail') . '</div>';

if (isset($_POST['submit'])) {
    // Сохраняем настройки системы
    $set_mail['cat_friends'] = 0;
    $set_mail['message_include'] = isset($_POST['message_include']) && $_POST['message_include'] == 1 ? 1 : 0;

    $stmt = $db->prepare('UPDATE `cms_settings` SET `val` = ? WHERE `key` = ?');
    $stmt->execute([$_POST['them_message'], 'them_message']);
    $stmt->execute([$_POST['reg_message'], 'reg_message']);

    $stmt->execute([serialize($set_mail), 'setting_mail']);
    $req = $db->query("SELECT * FROM `cms_settings`");
    $set = [];

    while ($res = $req->fetch()) {
        $set[$res[0]] = $res[1];
    }

    echo '<div class="rmenu">' . _t('Settings are saved successfully') . '</div>';
}

$set_mail = unserialize($set['setting_mail']);

if (!isset($set_mail['cat_friends'])) {
    $set_mail['cat_friends'] = 0;
}

if (!isset($set_mail['message_include'])) {
    $set_mail['message_include'] = 0;
}

// Форма ввода параметров системы
if (empty($set['them_message'])) {
    $set['them_message'] = _t('Welcome!');
}

if (empty($set['reg_message'])) {
    $set['reg_message'] = _t("Hi {LOGIN}!\n\nWe are glad to see you on our site.\nCome more often and here it will be pleasant to you.\n\nYours faithfully,\nAdministrator.");
}

echo '<form action="index.php?act=mail" method="post"><div class="menu">';

// Общие настройки
echo '<h3>' . _t('System message upon registration') . '</h3>' . _t('Subject of the system message') . ':<br>' .
    '<input type="text" name="them_message" value="' . (!empty($set['them_message']) ? htmlentities($set['them_message'], ENT_QUOTES, 'UTF-8') : '') . '"/><br>' .
    _t('Message') . ':<br><textarea rows="' . $set_user['field_h'] . '" name="reg_message">' . (!empty($set['reg_message']) ? htmlentities($set['reg_message'], ENT_QUOTES, 'UTF-8') : '') . '</textarea><br>' .
    '<br><h3>' . _t('Send a message') . '</h3>' .
    '<input type="radio" value="1" name="message_include" ' . ($set_mail['message_include'] == 1 ? 'checked="checked"' : '') . '/>&#160;' . _t('ON') . '<br>' .
    '<input type="radio" value="0" name="message_include" ' . (empty($set_mail['message_include']) ? 'checked="checked"' : '') . '/>&#160;' . _t('OFF') . '<br>' .
    '<br><h3>' . _t('Tags') . '</h3>{LOGIN} - ' . _t('recipient Nickname') . '<br>{TIME} - ' . _t('current time') . '<br>' .
    '<br><p><input type="submit" name="submit" value="' . _t('Save') . '"/></p></div></form>' .
    '<div class="phdr"><a href="index.php">' . _t('Admin Panel') . '</a></div>';