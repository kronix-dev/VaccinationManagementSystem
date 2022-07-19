<?php
class Orders extends user
{
    function __construct($d)
    {
        $this->data = $d;
    }
    function addOrder()
    {
        $d = $this->select_tbl("vaccine", "*", "status='1'");
?>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5><b>Make Order</b></h5>
                    </div>
                    <div class="card-body">
                        <form class="glass_form">
                            <table class="table table-striped table-hover">
                                <tr>
                                    <th>Name</th>
                                    <th>Available Quantity</th>
                                    <th>Quantity</th>
                                </tr>
                                <?php
                                $this->input("tr", null, 12, "hidden", "plord");
                                foreach ($d as $a) {
                                ?>
                                    <tr>
                                        <td><?php echo $a["name"] ?></td>
                                        <td><?php echo $a["quantity"] ?></td>
                                        <td><?php
                                            $this->input("vac[]", $a["name"] . " Quantity", 12, "number", 0, "max='" . $a["quantity"] . "'");
                                            $this->input("vid[]", null, 12, "hidden", $a["id"]);
                                            ?></td>
                                    </tr>
                                <?php
                                }
                                ?>
                            </table>
                            <button class="btn btn-primary">Place Order</button>
                        </form>
                        <?php
                        ?>
                    </div>
                </div>
            </div>
        </div>
    <?php
    }
    function viewOrders()
    {
    ?>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5><b>Orders</b></h5>
                    </div>
                    <div class="card-body">
                        <?php
                        if ($_SESSION["user"]["role"] == "nurse") {
                        ?>
                            <button class="btn btn-primary mb-3" onclick="loadToDiv('menu','ord','applet')">Place an Order</button>
                        <?php
                        }
                        ?>
                        <form class="glass_form">
                            <?php
                            $this->input("tr", null, 12, "hidden", "plcod");
                            // $this->select("items","Select ")
                            ?>
                        </form>
                        <div class="">
                            <?php
                            $this->tableTurner($this->listOrders(), ["User", "Date created", "Status"], "ord", "true", true, [
                                [
                                    "action" => "gord", "icon" => "eye", "status" => "primary",
                                ]
                            ]);
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php
    }
    function listOrders($argz = null)
    {
        if ($this->data["role"] == "nurse") {
            $cond = "uid='" . $this->data["uid"] . "'";
        } else {
            $cond = null;
        }
        $a = $this->select_tbl("orders", "*", $cond);
        $c = [];
        foreach ($a as $b) {
            $r = [];
            $r["User"] = $this->getUserInfo($b["uid"])["name"];
            $r["Date"] = $b["date"];
            $r["id"] = $b["id"];
            $r["status"] = $b["status"] == "2" ? "Denied" : ($b["status"] == "1" ? "Accepted" : "Pending");
            array_push($c, $r);
        }
        return $c;
    }
    function placeOrder()
    {
        $d = $this->data;
        $lot = false;
        for ($i = 0; $i < count($d["vid"]); $i++) {
            if (isset($d["vac"][$i]) && (int)$d["vac"] > 0) {
                $lot = true;
            }
        }
        if ($lot) {

            $ir = $this->ins_to_db("orders", ["uid", "status"], [$d["uid"], "0"]);
            for ($i = 0; $i < count($d["vid"]); $i++) {
                if ($d["vac"][$i] != "" && isset($d["vac"][$i]["quantity"]) && (int)$d["vac"][$i]["quantity"] > 0) {
                    $this->ins_to_db("order_items", ["vid", "oid", "quantity"], [$d["vid"][$i], $ir, $d["vac"][$i]]);
                }
            }
            $this->dialog("check", "Vaccine order has been sent successfully", "success", 12, 0);
            // echo json_encode($this->user);
        } else {
            $this->dialog("close", "No vaccine selected", "danger", 12, 0);
            $this->viewOrders();
        }
    }
    function acceptOrder($o, $action = true)
    {
        $i = 1;
        if (!$action) {
            $i = 2;
            $this->dialog("check", "You have Denied the order, proceed with other procedures", "warning", 8, 2);
        } else {
            $this->dialog("check", "You have Accepted the order, proceed with other procedures", "success", 8, 2);
        }
        $this->upd_to_db("orders", "status='$i'", "id='$o'");

        $this->viewOrder($o);
    }
    function viewOrder($id)
    {
        $o = $this->getOrder($id);
    ?>
        <div class="row">
            <div class="col-md-8 offset-2">
                <div class="card">
                    <div class="card-header">Order Information</div>
                    <div class="card-body row">
                        <?php
                        $this->textC("Health Facility", NTAdmin::getOffice(user::getUserInfo($o["uid"])["oid"])["name"], null, 12);
                        $this->textC("Order Date", $o["date"], null, 6);
                        $this->textC("Client", $this->getUserInfo($o["uid"])["name"], null, 6);
                        $this->textC("Order Status", $o["status"] == "2" ? "Denied" : ($o["status"] == "1" ? "Accepted" : "Pending"), null, 6);
                        if ($_SESSION["user"]["role"] == "provider" && $o["status"] == "0") {
                        ?>
                            <div class="col-12">
                                <button class="btn btn-secondary mb-3" onclick="loadToDiv('accept','<?php echo $id ?>','applet')">Approve Order</button>
                                <button class="btn btn-danger mb-3" onclick="loadToDiv('denied','<?php echo $id ?>','applet')">Deny Order</button>
                            </div>
                        <?php
                        }
                        ?>
                        <div class="col-12">
                            <?php
                            $this->tableTurner($this->getOrderItems($id), ["Name", "Quantity"], "s", "true", false);
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<?php
    }
    function getOrder($id)
    {
        return $this->select_tbl("orders", "*", "id='$id'")[0];
    }
    function getOrderItems($id)
    {
        $r = $this->select_tbl("order_items", "*", "oid='$id'");
        $p = [];
        foreach ($r as $a) {
            $v = new vaccine($this->data);
            $b = [];
            $b["id"] = $a["id"];
            $b["Name"] = $v->getVaccine($a["vid"])["name"];
            $b["Quantity"] = $a["quantity"];
            array_push($p, $b);
        }
        return $p;
    }
}
