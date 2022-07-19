<?php
session_start();
// ini_set("display_errors", "1");
// error_reporting(E_ALL);

// require_once '../libs/php/auth.php';
require_once '../libs/php/bs_gui.php';
require_once 'user.php';
require_once 'admin.php';
require_once 'panels.php';
require_once 'orders.php';
require_once 'vaccine.php';
require_once 'patients.php';
require_once 'notifications.php';
require_once 'report.php';
$auth = new auth();
$gui = new gui();
$use = new user();
$d = $use->menus();
$pn = new Panels();
$a = new NTAdmin();
$rep = new Reports();
$use->role = $_SESSION["user"]["role"];
$use->id = $_SESSION["user"]["user_id"];

if (isset($_POST) && isset($_SESSION["user"])) {
    $gui->form_script('engine.php');
    $p = $auth->sanitize_array($_POST);
    $p["uid"] = $_SESSION["user"]["id"];
    $p["role"] = $_SESSION["user"]["role"];
    $vcc = new vaccine($p);
    $pat = new Patients($p);
    $ord = new Orders($p);
    $nfs = new Notifications($p);
    if (isset($p["tr"])) {
        switch ($p["tr"]) {
            case "advac":
                $vcc->save();
                break;
            case "adpat":
                $pat->save();
                break;
            case "plord":
                $ord->placeOrder();
                break;
            case "gch":
                $pat->provideVaccine();
                break;
            case "sndnot":
                $nfs->saveNotification();
                $pat->viewPatient($p["pid"]);
                break;
            case "crn":
                $nfs->saveNotification();
                $nfs->prepareNotification();
                break;
            case "adu":
                $a->saveUser($p);
                break;
            case "ado":
                $a->saveOffice($p);
                break;
            case "rep":
                $rr = $rep->getReport($p);
                $rep->viewReport($rr["heading"], $rr["v_d"], $rr["v_data"], $rr["v_datr"], $rr["totus"], $rr["new_p"], $rr["pat"]);
                break;
            case "upc":
                $vcc->updateVac();
                break;
            case "chp":
                $use->updatePWD($p);
                break;
            default:
                break;
        }
    }
    if (isset($p["key"]) && isset($p["val"])) {
        $key = $p["key"];
        $val = $p["val"];
        switch ($key) {
            case "gfv":
                $pat->viewPatient($val);
                break;
            case "vte":
                $pat->vaccinate($val);
                break;
            case "gord":
                $ord->viewOrder($val);
                break;
            case "accept":
                $ord->acceptOrder($val);
                break;
            case "denied":
                $ord->acceptOrder($val, false);
                break;
            case "edv":
                $vcc->updateChanjo($val);
                break;
            default:
                break;
        }
        if ($key == "menu") {
            unset($_SESSION["uniq"]);
            unset($_SESSION["vstat"]);
            switch ($val) {
                case "vcs":
                    $vcc->listVaccines();
                    break;
                case "adv":
                    $vcc->addVaccine();
                    break;
                case "pts":
                    $pat->showPatients();
                    break;
                case "adpat":
                    $pat->addPatient();
                    break;
                case "nfs":
                    $nfs->showNotifications();
                    break;
                case "nnfs":
                    $nfs->prepareNotification();
                    break;
                case "ord":
                    $ord->addOrder();
                    break;
                case "ordv":
                    $ord->viewOrders();
                    break;
                case "mngu":
                    $a->showUsers();
                    break;
                case "adu":
                    $a->addUser();
                    break;
                case "rep":
                    $use->reportform();
                    break;
                case "mno":
                    $a->showOffices();
                    break;
                case "ado":
                    $a->addOffice();
                    break;
                case "lgt":
                    $use->logout();
                    break;
                case "chp":
                    $use->changePwd();
                    break;
                default:
                    $pn->dashboard();
                    break;
            }
        }
    }
}