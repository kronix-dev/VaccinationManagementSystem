<?php
class Panels extends user
{
    public $role, $v = [];
    function __construct()
    {
        $this->role = $_SESSION["user"]["role"];
    }
    function dashboard()
    {
        // echo $_SESSION["user"]["role"];
        $data = [
            "fromd" => null,
            "type" => "day",
            "tod" => null,
            "office" => $this->role == "nurse" ? $_SESSION["user"]["oid"] : "all"
        ];
        $data2 = [
            "fromd" => null,
            "type" => "month",
            "tod" => null,
            "office" => $this->role == "nurse" ? $_SESSION["user"]["oid"] : "all"
        ];
?>
        <div class="row">
            <div class="col-md-10 col-sm-12 offset-1">
                <br />
                <br />
                <br />
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <center>
                                    <br />
                                    <img src="../libs/img/ngao.png" class="card-image" />
                                    <br />
                                    <br />
                                    <br />
                                    <!-- <h1><i class="fa fa-info"></i></h1> -->
                                    <h2><b>Welcome to Under Five (V) Vaccination Management System</b></h2>
                                    <br />
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="card bg-primary dx">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <center>
                                                                <!-- <h1><i class="fa fa-info"></i></h1> -->
                                                                <h5><b>All Patients Registered</b></h5>

                                                                <h1><b><?php echo count(Patients::getPatients()) ?></b></h1>
                                                            </center>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card bg-success dx">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <center>
                                                                <!-- <h1><i class="fa fa-info"></i></h1> -->
                                                                <h5><b>Total Vaccinations provided Today </b></h5>
                                                                <h1><b><?php echo Reports::getReport($data)["total_vac"] ?></b></h1>
                                                            </center>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card bg-warning dx">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <center>
                                                                <!-- <h1><i class="fa fa-info"></i></h1> -->
                                                                <h5><b>Total Vaccinations provided (<?php echo date('Y') ?>)</b></h5>
                                                                <h1><b><?php echo Reports::getReport($data2)["total_vac"] ?></b></h1>
                                                            </center>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <br />
                                    <br />
                                    <br />
                                    <font color="red">Last Login : <?php echo date("d.F.Y H:i:s a") ?></font>
                                    <hr />
                                    &copy UVMIS v0.1
                                </center>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<?php
    }
}
?>