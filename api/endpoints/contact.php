<?php
/**
 * Created by PhpStorm.
 * User: bohda
 * Date: 28/11/2016
 * Time: 9:38 AM
 */

require_once "../bootstrap.php";

use App\Cache;
use App\ContactInfo;
use App\Template;
use App\Config;


/*
 * Send the notification email that someone has submitted the contact form
 */

$response = [
    "code" => 200,
    "data" => []
];

$info = new ContactInfo($_POST);
$notifyMail = createMail();

foreach (Config::read("email.notify") as $sendTo) {
    $notifyMail->addAddress($sendTo["email"], $sendTo["name"]);
}

$notifyMail->Subject = "Contact Form Submitted: " . $info->organisation . " (" . $info->formName . ")";
$notifyMail->Body    = Template::render("email/notification.html", [ "{{message}}" => $info->asHtml() ]);
$notifyMail->AltBody = Template::render("email/notification.txt", [ "{{message}}" => $info->asRawString() ]);

if(!$notifyMail->send()) {
    App\Log::write($notifyMail->ErrorInfo);
    $response["code"] = 500;
    $response["data"] = [
        "message" => "Message could not be send",
        "mailer-error" => $notifyMail->ErrorInfo
    ];
}

/*
 * Send the thank you email
 */

if ($info->name && $info->email) {
    $thanksMail = createMail();
    $thanksMail->addAddress($info->email);
    $thanksMail->Subject = "Thanks for contacting BlueQ";
    $thanksMail->Body = Template::render("email/confirmation.html", [ "{{name}}" => $info->name ]);
    $thanksMail->AltBody = Template::render("email/confirmation.txt", [ "{{name}}" => $info->name ]);

    if(!$thanksMail->send()) {
        App\Log::write($thanksMail->ErrorInfo);
        $response["code"] = 500;
        $response["data"] = [
            "message" => "Confirmation message could not be sent",
            "mailer-error" => $thanksMail->ErrorInfo
        ];
    }
}


/*
 * Also add it to Pipedrive
 */


$pipedrive = new \Benhawker\Pipedrive\Pipedrive(Config::read("pipedrive.api_token"));

// Create org
$org = $pipedrive->organizations()->add([
    "name" => $info->organisation
]);

// Create person and link them to an organisation
$person = $pipedrive->persons()->add([
    "name" => $info->name,
    "email" => [$info->email],
    "phone" => [$info->phone],
    "org_id" => $org["data"]["id"]
]);

/** @var int[] $owners List of Owner IDs that can be set for the owner of the deal */
$owners = Config::read("pipedrive.owners");
$lastIndex = Cache::read("pipedrive_last_owner_index", 0);
// Select the next index by increasing by one and wrapping around
$nextIndex = ($lastIndex + 1) % count($owners);
Cache::write("pipedrive_last_owner_index", $nextIndex);


$buttonName = $info->buttonName ? " (" . $info->buttonName . ")" : "";

// Create deal and link org and person
$pipedrive->deals()->add([
    "title" => $info->organisation . " deal",
    "person_id" => $person["data"]["id"],
    "org_id" => $org["data"]["id"],
    "user_id" => $owners[$nextIndex],
    "pipeline_id" => Config::read("pipedrive.pipeline"),
    // Number of employees
    "4317ce112396396cd3d82b1acbe5e40ca2f49e0c" => $info->numEmployees,
    // Whether or not this is inbound or outbound. Hint it's always inbound
    "f3ec7bf1108052a313cd5a1dceb2a866ccac3e5c" => "Inbound",
    // Which contact form the contact info has come from
    "4b29a6b844c31586021a5e9c4a1e290d8db888e5" => $info->formName . $buttonName
]);

// Output response code and JSON response
//http_response_code($response["code"]);
//echo json_encode($response["data"], JSON_PRETTY_PRINT);
\App\Log::flush();


/**
 * Create a new PHPMailer object
 * @return PHPMailer
 */
function createMail() {
    $mail = new PHPMailer();

    // Enable verbose debug output
    $mail->SMTPDebug = 3;

    $mail->Debugoutput = function ($message, $level) {
        \App\Log::write($message, $level);
    };

    $mail->isSMTP();
    $mail->Host = Config::read("email.gmail.host");
    $mail->SMTPAuth = Config::read("email.gmail.use_auth");
    $mail->Username = Config::read("email.gmail.username");
    $mail->Password = Config::read("email.gmail.password");
    $mail->SMTPSecure = Config::read("email.gmail.security");
    $mail->Port = Config::read("email.gmail.port");

    $mail->setFrom(Config::read("email.from.email"), Config::read("email.from.name"));
    $mail->addReplyTo(Config::read("email.from.email"), Config::read("email.from.name"));

    $mail->isHTML(true);

    return $mail;
}
