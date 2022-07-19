<?php
class Patients extends user
{
    function __construct($d)
    {
        $this->data = $d;
    }
    function addPatient()
    {
?>
        <div class="row">
            <div class="col-12">
                <form class="glass_form">
                    <div class="card">
                        <div class="card-header">Add Patient Information</div>
                        <div class="card-body row">
                            <?php
                            $this->input("tr", null, "12", "hidden", "adpat");
                            $this->input("name", "Patient's Full Name", 4, "text");
                            $this->input("reg", "Birth Registration number", 4);
                            $this->input("birthplace", "Birth Place", 4);
                            $this->input("birthf", "Birth Facilitator", 4, "text");
                            $this->input("dob", "Date of Birth", 4, "date", null);
                            $this->select("gender", "Gender", [["Male", "Male"], ["Female", "Female"]], 4, false);
                            ?>
                        </div>
                        <div class="card-header">
                        </div>
                        <div class="card-body row">
                            <div class="col-12">
                                <h5>Location</h5>
                            </div>
                            <?php
                            $this->input("hamlet", "Hamlet", 4, "text");
                            $this->input("village", "Village", 4, "text");
                            $this->input("street", "Street", 4, "text");
                            ?>
                        </div>
                        <div class="card-header">
                        </div>
                        <div class="card-body row">
                            <div class="col-12">
                                <h5>1.Parent's Information</h5>
                            </div>
                            <?php
                            $this->input("name1", "Fullname", 4, "text");
                            $this->input("email1", "Email", 4, "text");
                            $this->input("phone1", "Phone", 4, "text");
                            $this->select("relation1", "Relationship", [["Father", "Father"], ["Mother", "Mother"]], 4, false);

                            ?>
                        </div>
                        <div class="card-header">
                        </div>
                        <div class="card-body row">
                            <div class="col-md-12">
                                <h5>2. Parent's Information</h5>
                            </div>
                            <?php
                            $this->input("name2", "Fullname", 4, "text");
                            $this->input("email2", "Email", 4, "text");
                            $this->input("phone2", "Phone", 4, "text");
                            $this->select("relation2", "Relationship", [["Father", "Father"], ["Mother", "Mother"]], 4, false);
                            ?>
                            <div class="col-md-12">
                                <br />
                                <button class="btn btn-primary btn-block">Save Patient</button>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    <?php
    }
    function updatePatient()
    {
    }
    function vaccinate($id)
    {
    ?>
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><b>VACCINATION PROGRAM</b></h5>
                    </div>
                    <div class="card-body">

                        <form class="glass_form row">
                            <?php
                            $this->input("tr", null, 12, "hidden", "gch");
                            $this->input("pid", null, 12, "hidden", $id);
                            $nes = vaccine::getVaccines();

                            foreach ($nes as $a) {
                            ?>
                                <div class="col-md-4">
                                    <div class="card row mb-3" style="padding: 5px;">
                                        <?php
                                        $ds = $this->getVaccineProgress($a["id"], $id);
                                        if (!$ds && !vaccine::checkLastValidity($a["id"], $id)) {
                                            $this->input("vaccine[]", $a["name"], "12", "checkbox", $a["id"], "nimo");
                                        } else {
                                            $this->textC($a["name"], null);
                                        }
                                        ?>
                                    </div>
                                </div>
                            <?php
                            }
                            $this->input("remarks", "Remarks", "12");
                            ?>
                            <div class="col-md-12">
                                <button class="btn btn-primary" type="submit">Vaccinate</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php
    }
    function getHistory($id)
    {
        $d = $this->select_tbl("vaccine_history", "*", "pid='$id' ORDER BY ID DESC");
        $r = [];
        // $t["pid"]=$a["pid"];
        foreach ($d as $a) {
            $t = [];
            $t["id"] = $a["id"];
            $t["name"] = vaccine::getVaccine($a["vid"])["name"];
            $t["date"] = $a["date"];
            $t["supervisor"] = user::getUserInfo($a["uid"])["name"];
            $t["remarks"] = $a["remarks"];
            array_push($r, $t);
        }
        return $r;
    }
    static function getPatient($id)
    {
        $auth = new auth();
        return $auth->select_tbl("patients", "*", "id='$id'")[0];
    }
    function provideVaccine()
    {
        foreach ($this->data["vaccine"] as $a) {
            $this->ins_to_db("vaccine_history", ["vid", "pid", "uid", "oid", "remarks"], [$a, $this->data["pid"], $_SESSION["user"]["id"], $_SESSION["user"]["oid"], $this->data["remarks"]]);
        }
        $this->dialog("check", "Vaccination has been recorded successfully", "success", "12", "0");
        $this->viewPatient($this->data["pid"]);
    }
    function getVaccineProgress($id, $p)
    {
        $d = count($this->select_tbl("vaccine_history", "*", "vid='$id' AND pid='$p'"));
        $v = $this->select_tbl("vaccine", "*", "id='$id'")[0];
    ?>
        <div class="col-12 row">
            <div class="col-10">
                <progress style="width: 100%" max="<?php echo $v["prescription"] ?>" class=" mb-3" value="<?php echo $d ?>"></progress>
            </div>
            <div class="col-2">
                (<?php echo $d . "/" . $v["prescription"] ?>)
            </div>
        </div>
    <?php
        return $d == (int)$v["prescription"] ? true : false;
    }
    static function getPatients($argz = null)
    {
        // echo $_SESSION["user"]
        if ($_SESSION["user"]["role"] == "nurse") {
            $cond = "oid='" . user::getUserInfo($_SESSION["user"]["id"])["oid"] . "'";
        } else {
            $cond = null;
        }
        $au = new auth();
        $d = $au->select_tbl("patients", "*", $cond);
        $r = [];
        foreach ($d as $a) {
            $a["oid"] = NTAdmin::getOffice($a["oid"])["name"];
            array_push($r, $a);
        }
        return $r;
    }
    function save()
    {
        $d = $this->data;
        $loc = $d["hamlet"] . ", " . $d["village"] . ", " . $d["street"];
        $id = $this->ins_to_db("patients", [
            "name",
            "dob",
            "gender",
            "birthplace",
            "birthfacilitator",
            "location",
            "brn",
            "oid"
        ], [
            $d["name"],
            $d["dob"],
            $d["gender"],
            $d["birthplace"],
            $d["birthf"],
            $loc,
            $d["reg"],
            $_SESSION["user"]["oid"]
        ]);
        $this->ins_to_db("v_parent", ["name", "email", "phone", "relation", "pid"], [$d["name1"], $d["email1"], $d["phone1"], $d["relation1"], $id]);
        $this->ins_to_db("v_parent", ["name", "email", "phone", "relation", "pid"], [$d["name2"], $d["email2"], $d["phone2"], $d["relation2"], $id]);
        $this->dialog("check", "Patient has been added successfully", "primary", "12", 0);
        $this->addPatient();
    }
    function viewPatient($id)
    {
        $d = $this->data;
        // $id=$d["id"];
        $vac = new vaccine($d);
        $pv = $this->select_tbl("patients", "*", "id='$id'")[0];
        $p = $this->select_tbl("v_parent", "*", "pid='$id'");
    ?>
        <div class="row">
            <div class="col-md-12 mb-3">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><b>PATIENT INFORMATION</b></h5>
                    </div>
                    <div class="card-body">

                        <div class="row mb-3">
                            <?php
                            $this->textC("Full name", $pv["name"]);
                            $this->textC("Date Of Birth", $pv["dob"]);
                            $this->textC("Birth Registration no#", $pv["brn"]);
                            $this->textC("Birth Place", $pv["birthplace"]);
                            $this->textC("Birth Facilitator", $pv["birthfacilitator"]);
                            $this->textC("Location", $pv["location"]);
                            ?>
                        </div>
                        <h5><b>PARENTS INFORMATION</b></h5>
                        <div class="row">
                            <?php
                            foreach ($p as $a) {
                                $this->textC("Full name", $a["name"], null, 3);
                                $this->textC("Email Address", $a["email"], null, 3);
                                $this->textC("Phone Number", $a["phone"], null, 3);
                                $this->textC("Relation", $a["relation"], null, 3);
                            ?>
                                <div class="col-md-12">
                                    <hr />
                                </div>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <!-- <div class="card">
                    <div class="card-header">
                        <h5><b>VACCINATION HISTORY</b></h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $this->tableTurner($this->getHistory($id), ["Vaccination name", "Date", "Supervisor", "remarks"], "vnb");
                        ?>
                    </div>
                </div> -->
            </div>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5><b>Notification Panel</b></h5>
                    </div>
                    <div class="card-body">
                        <form class="glass_form row">
                            <?php
                            $this->input("tr", null, 12, "hidden", "sndnot");
                            $this->input("type", null, 12, "hidden", "user");
                            $this->input("pid", null, 12, "hidden", $id);
                            $this->input("name", "Notification Name", "12", "text");
                            $this->input("message", "Message", 12, "text");
                            ?>
                            <div class="col-12">
                                <button class="btn btn-secondary">Notify Clients</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-12">

            </div>
        </div>
    <?php
    }
    function showPatients()
    {
    ?>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Patients</h5>
                        <button class="btn btn-primary" onclick="loadToDiv('menu','adpat','applet')">Add Patient</button>
                    </div>
                    <div class="card-body">
                        <?php
                        $oid = $_SESSION["user"]["oid"];
                        $t = $this->select_tbl("patients", ["name", "dob", "id", "gender", "location"], "oid='$oid'");
                        $this->tableTurner(
                            $t,
                            ["Name", "Date of birth", "Gender", "Location"],
                            "fv",
                            "true",
                            true,
                            [
                                ["action" => "vte", "icon" => "check", "status" => "primary"],
                                ["action" => "gfv", "icon" => "eye", "status" => "success"]
                            ]
                        );
                        ?>
                    </div>
                </div>
            </div>
        </div>
<?php
    }
    static function getPatientParents($pid)
    {
        $a = new auth();
        return $a->select_tbl("v_parent", "*", "pid='$pid'");
    }
}
