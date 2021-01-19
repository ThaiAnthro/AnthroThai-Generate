<?php
if (!file_exists("env.php")) {
    die("env.php is required!");
}

require_once("env.php");

//TODO
//echo "Do delete everything in /AnthroThai\n"; //DO NOT DELETE .git

//create folder
mkdir(__DIR__ . "/AnthroThai");

echo "Load _theme.html to sting ... ";
$theme = file_get_contents(__DIR__ . "/theme/_theme.html");
$theme = str_replace("##CURRENT_YEAR##", date("Y"), $theme);
$theme = str_replace("##URL##", $url, $theme);
echo "DONE!";
echo "\n";


//do copy all file in assets to assets folder
mkdir(__DIR__ . "/AnthroThai/assets");
$dir = new DirectoryIterator((__DIR__ . "/assets"));
foreach ($dir as $fileinfo) {
    if (!$fileinfo->isDot()) {
        copy(__DIR__ . "/assets/" . $fileinfo->getFilename(), __DIR__ . "/AnthroThai/assets/" . $fileinfo->getFilename());
    }
}

echo "Building index.html ... ";
$content = file_get_contents(__DIR__ . "/theme/index.html");
$string = str_replace("##CONTENT##", $content, $theme);
$string = str_replace("##MENU##", menu_build("HOME"), $string);
$string = str_replace("##TITLE##", "หน้าแรก", $string);
file_put_contents(__DIR__ . "/AnthroThai/index.html", $string);


echo "\n";


echo "Building about.html ... ";
$content = file_get_contents(__DIR__ . "/theme/about.html");
$string = str_replace("##CONTENT##", $content, $theme);
$string = str_replace("##MENU##", menu_build("ABOUT"), $string);
$string = str_replace("##TITLE##", "เกี่ยวกับ", $string);
file_put_contents(__DIR__ . "/AnthroThai/about.html", $string);


echo "\n";

echo "Building contact.html ... ";
$content = file_get_contents(__DIR__ . "/theme/contact.html");
$string = str_replace("##CONTENT##", $content, $theme);
$string = str_replace("##MENU##", menu_build("CONTACT"), $string);
$string = str_replace("##TITLE##", "ติดต่อ", $string);

file_put_contents(__DIR__ . "/AnthroThai/contact.html", $string);


echo "\n";

$telegram_room = array();
require_once("telegram_group.php");

echo "Building community.html ... ";
$content = file_get_contents(__DIR__ . "/theme/community.html");
$string = str_replace("##CONTENT##", $content, $theme);
$string = str_replace("##MENU##", menu_build("COMMUNITY"), $string);
$string = str_replace("##TITLE##", "ชุมชน", $string);

//build link
$telegram_link = "";
foreach ($telegram_room as $tr) {
    $telegram_link = $telegram_link . "<li><a href='#' data-toggle='modal' data-target='#{$tr[2]}Modal'>{$tr[1]}</a> {$tr[3]}</li>";
}
$string = str_replace("##TELEGRAM_ROOM##", $telegram_link, $string);

#TELEGRAM_MODAL
$modal = "";
foreach ($telegram_room as $tr) {
    //gen new link
    //exportChatInviteLink

    $telegram_url = "https://t.me/joinchat/U5a9gacFoHUESBPs";
    $rule = "กรุณาปฎิบัติตามกฎและกติการที่ระบุไว้ในกฎและกติการการใช้ห้องแอนโทรไทยเทเลแกรม";

    $website = "https://api.telegram.org/bot" . $botToken;
    $params = [
        'chat_id' => $tr[0],
    ];
    $ch = curl_init($website . '/exportChatInviteLink');
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);
    curl_close($ch);

    $json = json_decode($result, true);
    if (!isset($json['ok']) || !$json['ok']) {
        die($result . "<< ERROR!");
    } else {
        $telegram_url = $json['result'];
    }

    $ux = "";
    $oo = explode("https://t.me/joinchat/", $telegram_url);
    $ux = base64_encode($oo[1]);


    $modal = $modal . '<div class="modal fade" id="' . $tr[2] . 'Modal" tabindex="-1" aria-labelledby="' . $tr[2] . 'ModalLabel" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="' . $tr[2] . 'ModalLabel">' . $tr[1] . '</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">
            ' . $rule . '
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" onclick="join_telegram(\'' . $ux . '\');">Join ...</button>
            </div>
        </div>
        </div>
    </div>';
}

$string = str_replace("##TELEGRAM_MODAL##", $modal, $string);


file_put_contents(__DIR__ . "/AnthroThai/community.html", $string);
echo "\n";

echo "Building meeting.html ... ";
$content = file_get_contents(__DIR__ . "/theme/meeting.html");
$string = str_replace("##CONTENT##", $content, $theme);
$string = str_replace("##MENU##", menu_build("MEETING"), $string);
$string = str_replace("##TITLE##", "มีตติง", $string);

file_put_contents(__DIR__ . "/AnthroThai/meeting.html", $string);


echo "\n";

echo "\n";
die("Generated! ready to go ...\n");


//TODO
echo "Building sitemap.html ... ";
echo "Building sitemap.xml ... ";
echo "Building AnthroThai.json ... ";