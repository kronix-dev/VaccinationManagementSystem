<?php
class Reports extends user
{
    static function getReport($d)
    {
        $auth = new auth();
        $d1 = $d["fromd"];
        $d2 = $d["tod"];
        $type = $d["type"];
        $office = $d["office"];
        $cond = null;
        $heading = "General Report";
        if ($office != "all") {
            $cond = "oid='$office'";
            $heading = "Report From Health Facility : " . NTAdmin::getOffice($office)["name"];
        }
        $dp = $auth->select_tbl("vaccine_history", "*", $cond);
        $v = $auth->select_tbl("vaccine", ["id", "name"], null);
        $pat = $auth->select_tbl("patients", "*", $cond);
        $r = [];
        $ps = [];
        $totus = [];
        foreach ($dp as $a) {
            $date = substr($a["date"], 0, 10);
            $date = str_replace(" ", "", $date);
            $date = str_replace(":", "", $date);
            $date = str_replace("-", "", $date);
            if ($type == "range") {
                if ($date >= $d1 && $date <= $d2) {
                    array_push($r, $a);
                }
            } elseif ($type == "day") {
                if (date("Ymd") == $date) {
                    array_push($r, $a);
                }
            } elseif ($type == "month") {
                if (date("Ym") == substr($date, 0, 6)) {
                    array_push($r, $a);
                }
            } elseif ($type == "year") {
                if (date("Y") == substr($date, 0, 4)) {
                    array_push($r, $a);
                }
            }
        }
        $v_data = [];
        $v_pro = 0;
        $used = [];
        $unused = [];
        foreach ($v as $a) {
            $a["history"] = [];
            foreach ($r as $z) {
                if (!in_array($z["pid"], $totus)) {
                    array_push($totus, $z["pid"]);
                }
                if ($a["id"] == $z["vid"]) {
                    if ($office != "all" && $office == $z["oid"]) {
                        array_push($a["history"], $z);
                    } else if ($office == "all") {
                        array_push($a["history"], $z);
                    }
                    array_push($used, $z["vid"]);
                }
            }
            $a["provided"] = count($a["history"]);
            $v_pro += count($a["history"]);
            unset($a["history"]);
            array_push($v_data, $a);
        }
        foreach ($v as $a) {
            if (!in_array($a["id"], $used)) {
                array_push($unused, $a);
            }
        }
        foreach ($pat as $p) {
            $date = substr($p["date"], 0, 10);
            $date = str_replace(" ", "", $date);
            $date = str_replace(":", "", $date);
            $date = str_replace("-", "", $date);
            if ($type == "range") {
                if ($date >= $d1 && $date <= $d2) {
                    array_push($ps, $a);
                }
            } elseif ($type == "day") {
                if (date("Ymd") == $date) {
                    array_push($ps, $a);
                }
            } elseif ($type == "month") {
                if (date("Ym") == substr($date, 0, 6)) {
                    array_push($ps, $a);
                }
            } elseif ($type == "year") {
                if (date("Y") == substr($date, 0, 4)) {
                    array_push($ps, $a);
                }
            }
        }
        $v_d = [];
        foreach ($r as $a) {
            $g = [];
            $g["patient"] = Patients::getPatient($a["pid"])["name"];
            $g["Vaccine"] = vaccine::getVaccine($a["vid"])["name"];
            $g["User"] = user::getUserInfo($a["uid"])["name"];
            $g["Office"] = NTAdmin::getOffice($a["oid"])["name"];
            $g["remarks"] = $a["remarks"];
            $g["date"] = $a["date"];
            $g["id"] = $a["pid"];
            array_push($v_d, $g);
        }
        $u = [];
        foreach ($unused as $un) {
            array_push($u, ["name" => $un["name"]]);
        }
        return [
            "heading" => $heading,
            "v_d" => $v_d,
            "v_data" => $v_data,
            "v_datr" => $u,
            "total_vac" => $v_pro,
            "totus" => $totus,
            "new_p" => $ps,
            "pat" => Patients::getPatients()
        ]
?>

    <?php
    }
    function viewReport($heading, $v_d, $v_data, $v_datr, $t, $p, $pn)
    {
    ?>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <center>
                            <h4><b>
                                    <?php echo $heading ?>
                                </b></h4>
                        </center>
                        <button class="btn btn-success noprint" onclick="print()">Print Report</button>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-success">
                            <h4><b>Report Summary</b></h4>
                            <h5><b>Short Summary</b></h5>
                        </div>
                        <div class="row">
                            <?php
                            $this->textC("Total Provided Vaccines", count($v_d));
                            $this->textC("Total Vaccinated Patients", count($t));
                            $this->textC("New Patients added", count($p));
                            $this->textC("All Patients", count($pn)); ?>
                        </div>
                        <div class="alert alert-success">
                            <h4><b></b></h4>
                            <h5><b>Vaccination History</b></h5>
                        </div>
                        <?php

                        $this->tableTurner($v_d, ["Vaccine", "Patient", "Officer", "Office", "Remarks", "Date"], "s", "false", false);
                        ?>
                        <div class="alert alert-success mt-3">
                            <h4><b>Vaccines</b></h4>
                            <h5><b>Individual Vaccine Provision</b></h5>
                        </div>
                        <?php
                        $this->tableTurner($v_data, ["Vaccine name", "Amount Provided"], "b", "false", false);
                        ?>
                        <div class="alert alert-success mt-3">
                            <h5><b>Unused Vaccines</b></h5>
                        </div>
                        <?php
                        $this->tableTurner($v_datr, ["Vaccine name"], "b", "false", false);
                        ?>
                    </div>
                </div>
            </div>
        </div>
<?php
    }
}
