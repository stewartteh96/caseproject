<?php
session_start();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>Admin Page</title>
    <link href="../../css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="../../css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" media="screen" href="../../css/style.css" />
    <script src="../../js/jquery-3.2.1.min.js"></script>
    <script src="../../js/parsley.min.js"></script>
    <script src="../../js/bootstrap.min.js"></script>
    <script src="../../js/jquery.dataTables.min.js"></script>
</head>

<body>
    <header class='pageHeader'>
        <h1 class='pageTitle'>Coast City Sports Centre</h1>

    <?php
    if(isset($_SESSION['profile']) && $_SESSION["profile"]["loggedIn"] && $_SESSION["profile"]["userType"] == "admin") {
        echo "<div class='loginDiv'>";
        // echo "<p>";
        echo "Welcome, " . $_SESSION["profile"]["name"] . "! &nbsp;";
        echo "<input type='button' value='Logout' onclick='location.href=\"../logout.php\";' />";
        // echo "</p>";
        echo "</div>";
    } 
    ?>

    </header>

    <div class="content">
        <div class="container">
            <?php //Only show content to admin
		    if(isset($_SESSION['profile']) && $_SESSION["profile"]["loggedIn"] && $_SESSION["profile"]["userType"] == "admin") {
			?>

            <script>
                $(document).ready(function () {
                    //All members list
                    var table = $('#tblMemberList').DataTable({ //Data table to display member list
                        ajax: {
                            url: "admin_serverProcessing.php", //JSON datasource
                            dataSrc: '', //Tell DataTables where the data array is in the JSON structure, left empty if it's an array
                            data: { action: "loadAll" },
                            type: "POST",
                        },
                        columns: [ //Tell DataTables where to get the data for each cell in that row
                            { data: "cc_memberID" },
                            { data: "surname" },
                            { data: "forename" },
                            { data: "address" },
                            { data: "cc_gradeID" }
                        ]
                    });

                    //Modify members' details list
                    var tblModifyMemberList = $('#tblModifyMemberList').DataTable({ //Data table to display member list
                        ajax: {
                            url: "admin_serverProcessing.php", //JSON datasource
                            dataSrc: '', //Tell DataTables where the data array is in the JSON structure, left empty if it's an array
                            data: { action: "loadAll" },
                            type: "POST",
                        },
                        columns: [ //Tell DataTables where to get the data for each cell in that row
                            { data: "cc_memberID" },
                            { data: "surname" },
                            { data: "forename" },
                            { data: "address" },
                            { data: "cc_gradeID" },
                            { data: null, defaultContent: "<button>Edit</button>" }
                        ]
                    });

                    $('#tblModifyMemberList tbody').on('click', 'button', function () {
                        var data = tblModifyMemberList.row($(this).parents('tr')).data(); //Get selected row from data table
                        $("#modalEditDetails").find('.modal-body #txtSurname').val(data["surname"]);
                        $("#modalEditDetails").find('.modal-body #txtForename').val(data["forename"]);
                        $("#modalEditDetails").find('.modal-body #txtAddress').val(data["address"]);
                        $("#modalEditDetails").find('.modal-body #txtMembership').val(data["cc_gradeID"]);
                        $("#modalEditDetails").find('.modal-body #lblMemberID').text(data["cc_memberID"]);
                        // $("#modalConfirmation").find('.modal-body #lblUsername').text(data["username"]); //Hidden label; For ajax use when btnBanMember onclick
                        $("#modalEditDetails").modal("show");
                    });

					$('#btnUpdate').on('click', function(e) { 
						if ($('#formEditDetails').parsley().isValid()) {
                            var memberID = $("#modalEditDetails").find('.modal-body #lblMemberID').text(); 
                            var surname = $("#modalEditDetails").find('.modal-body #txtSurname').val();
                            var forename = $("#modalEditDetails").find('.modal-body #txtForename').val();
                            var address = $("#modalEditDetails").find('.modal-body #txtAddress').val();
                            var membership = $("#modalEditDetails").find('.modal-body #txtMembership').val(); 

                            $.ajax({
								url :"admin_serverProcessing.php",
								type: "POST",
								data: "action=updateDetails&surname=" + surname + "&forename=" + forename + "&address=" + address + 
                                "&membership=" + membership + "&memberID=" + memberID,
								success: function(data) {
									if (data == "1") { //Successfully updated details
										$("#modalEditDetails").modal("hide");
										alert("Details successfully updated!");
										tblModifyMemberList.ajax.reload(); //Reload data table
									}
									else if (data == "0") { //Failed to update details
										alert("Failed to update details!");
									}
								}
							});
                        }
                    });                    
                });
            </script>

            <div class="well">
                <!-- Tab options -->
                <ul class="nav nav-tabs" id="tabDetails">
                    <li class="active">
                        <a href="#tabMemberList" data-toggle="tab">All Members</a>
                    </li>

                    <li>
                        <a href="#tabModifyMemberList" data-toggle="tab">Modify Members' Details</a>
                    </li>
                </ul>
                <!-- End tab options -->

                <div id="myTabContent" class="tab-content">
                    <!-- Member list tab -->
                    <div class="tab-pane active in" id="tabMemberList">
                        <table id="tblMemberList" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>MemberID</th>
                                    <th>Surname</th>
                                    <th>Forename</th>
                                    <th>Address</th>
                                    <th>Membership</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <!-- End member list tab -->

                    <!-- Modify member list tab -->
                    <div class="tab-pane" id="tabModifyMemberList">
                        <table id="tblModifyMemberList" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>MemberID</th>
                                    <th>Surname</th>
                                    <th>Forename</th>
                                    <th>Address</th>
                                    <th>Membership</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <!-- End modify member list tab -->
                </div>
            </div>

            <!-- Popup modal to edit members' details -->
            <div class="modal fade" id="modalEditDetails" role="dialog">
                <form id="formEditDetails" data-parsley-validate>
                    <div class="modal-dialog">
                        <!-- Modal content-->
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Update Details</h4>
                            </div>

                            <div class="modal-body">
                                <label ID="lblMemberID" hidden="hidden"></label>

                                <label>Surname: </label>

                                <input type="text" class="form-control" id='txtSurname' name='txtSurname' 
                                data-parsley-required="true" data-parsley-required-message="*" />

                                <label>Forename: </label>

                                <input type="text" class="form-control" id='txtForename' name='txtForename'
                                data-parsley-required="true" data-parsley-required-message="*" />

                                <label>Address: </label>

                                <input type="text" class="form-control" id='txtAddress' name='txtAddress' 
                                data-parsley-required="true" data-parsley-required-message="*" />

                                <label>Membership: </label>

                                <input type="text" class="form-control" id='txtMembership' name='txtMembership' 
                                data-parsley-required="true" data-parsley-required-message="*" />
                            </div>

                            <div class="modal-footer">
                                <input type="button" class="btn" id="btnCancel" data-dismiss="modal" name="btnCancel" value="Cancel" />
                                <input type="button" class="btn btn-primary" id="btnUpdate" name="btnUpdate" value="Confirm" />
                            </div>
                        </div>
                        <!-- End popup modal content -->
                    </div>
                </form>
            </div>
            <!-- End popup modal -->
            <?php
			}
            else { //Redirect user to home page
                header("Refresh:0;url=../../index.php");
            }
		    ?>
        </div>
    </div>
</body>

</html>