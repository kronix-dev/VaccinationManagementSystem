<?php
class Notifications extends user
{
    function __construct($d)
    {
        $this->data = $d;
    }
    function prepareNotification()
    {
?>
        <?php
        $this->dialog("info", "Notifications will be sent to all parents", "info", 8, 2);
        ?>
        <div class="row">
            <div class="col-md-8 offset-2">
                <div class="card">
                    <div class="card-header">Create Notification</div>
                    <div class="card-body">
                        <form class="glass_form">
                            <?php
                            $this->input("tr", null, 12, "hidden", "crn");
                            $this->input("type", null, 12, "hidden", "major");
                            $this->input("name", "Notification Name", "12", "text");
                            $this->input("message", "Message", "12", "textarea");
                            ?>
                            <div class="col-12 mt-3">
                                <button class="btn btn-primary">Send Notification</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php
    }
    function saveNotification()
    {
        if ($this->data["type"] == "major") {
            $this->ins_to_db("notifications", ["name", "office", "message", "type"], [$this->data["name"], $_SESSION["user"]["oid"], $this->data["message"], $this->data["type"]]);
        } else {
            $this->ins_to_db("notifications", ["name", "office", "message", "type", "pid"], [$this->data["name"], $_SESSION["user"]["oid"], $this->data["message"], $this->data["type"], $this->data["pid"]]);
        }
        $this->dialog("info", "Notification On Queue", "warning", 8, 2);
    }
    function showNotifications()
    {
        if ($this->data["role"] == "nurse") {
            $cond = "office='" . user::getUserInfo($this->data["uid"])["oid"] . "'";
        } else {
            $cond = null;
        }
    ?>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5><b>Notifications Panel</b></h5>
                        <button class="btn btn-primary" onclick="loadToDiv('menu','nnfs','applet')">Create new notification</button>
                    </div>
                    <div class="card-body">
                        <?php
                        $this->tableTurner($this->select_tbl("notifications", ["id", "name", "message", "status"], $cond), ["Name", "Message", "status"], "dn", "true", false);
                        ?>
                    </div>
                </div>
            </div>
        </div>
<?php
    }
    function getNotifees($typ)
    {
        $d = $this->select_tbl("patients", "*", $typ);
        $t = [];
        // print_r($d);
        foreach ($d as $a) {
            $pid = $a["id"];
            $f = $this->select_tbl("v_parent", "*", "pid='$pid'");
            foreach ($f as $c) {
                array_push($t, $c["phone"]);
            }
        }
        return $t;
    }
    function getUnsentNotifications()
    {
        $d = $this->select_tbl("notifications", "*", "status='pending' LIMIT 1");
        $nfs_Data = [];
        foreach ($d as $a) {
            $oid = $a["office"];
            $uid = $a["pid"];
            $nfs_Data["message"] = $a["message"];
            $nfs_Data["nid"] = $a["id"];
            if ($a["type"] == "major") {
                $c = $this->getNotifees("oid='$oid'");
            } else {
                $c = $this->getNotifees("id='$uid'");
            }
            $nfs_Data["addressList"] = $c;
        }
        return [$nfs_Data];
    }
    function updateUnsentNotifications($id)
    {
        $d = $this->upd_to_db("notifications", "status='1'", "id='$id'");
    }
}
