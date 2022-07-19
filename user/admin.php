<?php
class NTAdmin extends user
{
    function addUser()
    {
?>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h1>Add User</h1>
                    </div>
                    <div class="card-body">
                        <form class="glass_form row">
                            <?php
                            $this->input("tr", null, 12, "hidden", "adu", "style='display:none'");
                            $this->input("name", "Full name", 12);
                            $this->input("email", "Email Address", 6, "email");
                            $this->input("phone", "Phone Number", 6);
                            $this->select("roley", "Role", [
                                ["admin", "Admin"],
                                ["nurse", "nurse"],
                                ["provider", "Supplier"]
                            ], 6, false);
                            $this->select("oid", "Office", $this->getOffices(), 6)
                            ?>
                            <div class="col-12">
                                <br />
                                <button class="btn btn-windows btn-block" type="submit">Add User</button>
                                <br />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php
    }
    function getOffices()
    {
        return $this->select_tbl("offices", "*", null);
    }
    function addOffice()
    {
    ?>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4><b>Add Office</b></h4>
                    </div>
                    <div class="card-body">
                        <form class="glass_form">
                            <?php
                            $this->input("tr", null, 12, "hidden", "ado", "style='display:none'");
                            $this->input("name", "Office name", "12");
                            $this->input("description", "Location", "12");
                            // $this->select("uid","User",$this->getUsers(),12);
                            ?>
                            <div class="col-12"><button type="submit" class="btn btn-primary">Add Office</button></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php
    }
    static function getOffice($id)
    {
        $f = new auth();
        return $f->select_tbl("offices", "*", "id='$id'")[0];
    }
    function saveOffice($p)
    {
        $this->ins_to_db("offices", ["name", "location"], [$p["name"], $p["description"]]);
        $this->dialog("check", "Office added successfully", "primary", 12, 0);
    }
    function getUserOffice($uid)
    {
        $d = $this->select_tbl("offices", "*", "uid='$uid'");
        return $d;
    }
    function saveUser($d)
    {
        $d = $this->ins_to_db("users", ["name", "email", "role", "pwd", "status", "oid"], [$d["name"], $d["email"], $d["roley"], password_hash("Tanzania", PASSWORD_BCRYPT), "active", $d["oid"]]);
        $this->dialog("check", "User has been added successfully", "success", 12, 0);
        $this->showUsers();
    }
    function showUsers()
    {
    ?>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Users Panel</h4>

                        <button class="btn btn-primary" onclick="loadToDiv('menu','adu','applet')">Add new user</button>
                    </div>
                    <div class="card-body">
                        <?php
                        $this->tableTurner($this->getUsers(), ["Name", "Email", "Role", "Status"], "us", "true", true, [["action" => "delu", "icon" => "trash", "status" => "danger"]]);
                        ?>
                    </div>
                </div>
            </div>
        </div>
    <?php
    }
    function showOffices()
    {
    ?>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Offices Panel</h4>

                        <button class="btn btn-primary" onclick="loadToDiv('menu','ado','applet')">Add new Office</button>
                    </div>
                    <div class="card-body">
                        <?php
                        $this->tableTurner($this->getOffices(), ["Name", "Location"], "us", "true", true, [["action" => "delo", "icon" => "trash", "status" => "danger"]]);
                        ?>
                    </div>
                </div>
            </div>
        </div>
<?php
    }
    function getUsers()
    {
        return $this->select_tbl("users", ["name", "email", "role", "status", "id",], null);
    }
}
