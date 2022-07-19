<?php
class vaccine extends user
{
    function __construct($d, $f = [])
    {
        $this->coreTable = "vaccines";
        $this->data = $d;
    }
    function listVaccines()
    {
?>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Vaccine Management</h5>
                        <button class="btn btn-primary" onclick="loadToDiv('menu','adv','applet')">Add Vaccine</button>
                    </div>
                    <div class="card-body">
                        <div>
                            <?php
                            $this->tableTurner($this->getVaccines("table"), ["Vac. name", "Vac. description", "Vac. prescription", "Vac. quantity", "Status"], "bh", "true", true, [
                                // ["action" => "vva", "icon" => "eye", "status" => "primary"],
                                ["action" => "edv", "icon" => "edit", "status" => "success"]
                            ]);
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php
    }
    static function getVaccines($argz = null)
    {
        $a = new auth();
        $d = $a->select_tbl("vaccine", "*", null);
        $er = [];
        foreach ($d as $a) {
            $r = [];
            $r["name"] = $a["name"];
            $r["description"] = $a["description"];
            $r["prescription"] = $a["prescription"];
            $r["id"] = $a["id"];
            $r["quantity"] = $a["quantity"];
            $r["status"] = $a["status"];
            $r["status"] = $a["status"] == 1 ? "Active" : "Inactive";
            if (!$argz == "table") {
                $r["v_interval"] = $a["v_interval"];
            }
            array_push($er, $r);
        }
        return $er;
    }
    static function getVaccine($id)
    {
        $n = new auth();
        return $n->select_tbl("vaccine", "*", "id='$id'")[0];
    }
    function addVaccine()
    {
    ?>
        <div class="row">
            <div class="col-md-6 offset-3">
                <div class="card">
                    <div class="card-header">
                        Add a new Vaccine
                    </div>
                    <div class="card-body">
                        <form class="glass_form">

                            <?php
                            $this->input("tr", null, 12, "hidden", "advac");
                            $this->input("name", "Vaccine Name", 12);
                            $this->input("quantity", "Quantity", "12", "number");
                            $this->input("description", "Description", "12", "text");
                            $this->input("prescription", "Prescription (Visits) in days", 12, "text");
                            $this->input("interv", "Interval (Visits) in days", 12, "text");
                            ?>
                            <div class="col-12">
                                <button class="btn btn-primary btn-block">Add Vaccine</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php
    }
    function save()
    {
        $p = $this->data;
        $this->ins_to_db("vaccine", ["name", "quantity", "description", "prescription", "v_interval"], [$p["name"], $p["quantity"], $p["description"], $p["prescription"], $p["interv"]]);
        $this->dialog("check", "Vaccine has been added successfully", "primary", "6", 3);
        $this->addVaccine();
    }
    static function checkLastValidity($v, $u)
    {
        $a = new auth();
        $d = $a->select_tbl("vaccine_history", "*", " vid='$v' and pid='$u' ORDER BY id DESC LIMIT 1");
        if (count($d) > 0) {
            $d = $d[0];
            $date = substr($d["date"], 0, 10);
            $date = (int)str_replace("-", "", $date);
            $vaccine = vaccine::getVaccine($v);
            // $date=str_replace("","",$date);
            $rr = (int)date("Ymd") - $date;
            // echo "<h1>$rr</h1>";
            if ($rr >= $vaccine["v_interval"]) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }
    function updateChanjo($id)
    {
        $f = $this->getVaccine($id);
    ?>
        <div class="row">
            <div class="col-6 offset-3">
                <div class="card">
                    <div class="card-header">
                        <h2><b>UPDATE VACCINE</b></h2>
                    </div>
                    <div class="card-body">
                        <form class="glass_form">
                            <?php
                            $this->input("tr", null, 12, "hidden", "upc");
                            $this->input("vid", null, 12, "hidden", $id);
                            $this->input("name", "Vaccine name", 12, "text", $f["name"]);
                            $this->input("quantity", "Vaccine Quantity", 12, "text", $f["quantity"]);
                            $this->input("vi", "Vaccine Interval", 12, "text", $f["v_interval"]);
                            $this->input("vd", "Vaccine Description", 12, "text", $f["description"]);
                            $this->input("vp", "Vaccine Prescription", 12, "text", $f["prescription"]);
                            ?>
                            <div class="col-12">
                                <button type="submit" class="btn-danger btn">Disable Vaccine</button>
                                <button type="submit" class="btn-primary btn">Update Vaccine</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
<?php
    }
    function updateVac()
    {
        $d = $this->data;
        $id = $d["vid"];
        $this->upd_to_db("vaccine", "name='" . $d["name"] .
            "', quantity='" . $d["quantity"] .
            "', v_interval='" . $d["vi"] .
            "', description='" . $d["vd"] .
            "', prescription='" . $d["vp"] . "'", "id='$id'");
        $this->dialog("check", "Vaccine has been updated", "success", 6, 3);
        $this->updateChanjo($id);
    }
}
