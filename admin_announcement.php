<!DOCTYPE html>
<html lang="en">

<head>
    <?php include(".head.php"); ?>  
    <link rel="stylesheet" href="./style/admin.css">
    <link rel="stylesheet" href="./style/modalbox.css">
    <title>PockeyMoney | Dashboard</title>
</head>
<body>
    <?php include("AD_navbar.php");

    //delete announcement
        if (isset($_POST['delete_submit'])) {
            $params['tableName'] = 'announcement';
            $params['idName'] = 'announcementID';
            $params['id'] = $_POST['delete_announcementID'];
            $result = $admin->adminDelete($params);
            if ($result['status'] == 'ok') {
                $admin->showAlert($result['statusMsg']);
            } else {
                $admin->showAlert($result['statusMsg']);
            }
            $admin->goTo('admin_announcement.php?role=admin');
        }

   //new transaction
   if (isset($_POST['new_submit'])) {
    $params['tableName'] = 'announcement';
    $params['data'] = array(
        'adminID' => $admin->getId(),
        'title' => $_POST['new_title'],
        'content' => $_POST['new_content'],
        'announcement_date' => $_POST['new_date'],
    );
    $result = $admin->adminInsert($params);

    if ($result['status'] == 'ok') {
        $admin->showAlert($result['statusMsg']);
    } else {
        $admin->showAlert($result['statusMsg']);
    }
    $admin->goTo('admin_announcement.php?role=admin');
}
    
    //update the transaction
    if (isset($_POST['edit_submit'])) {
        $params['tableName'] = 'announcement';
        $params['idName'] = 'announcementID';
        $params['id'] = $_POST['edit_announcementID'];
        $params['data'] = array(
            'adminID' => $admin->getId(),
            'announcement_date' => $_POST['edit_announcementDate'],
            'title' => $_POST['edit_announcementTitle'],
            'content' => $_POST['edit_announcementContent'],
        );
        $result = $admin->adminUpdate($params);
        if ($result['status'] == 'ok') {
            $admin->showAlert($result['statusMsg']);
        } else {
            $admin->showAlert($result['statusMsg']);
        }
        $admin->goTo('admin_announcement.php?role=admin');
    }

    ?>

    <div class="container-fluid background">
        <div class="container-fluid">
            <div class="container-fluid body announcement">
                <nav class="navbar navbar-expand-lg">
                    <a href="#" class="navbar-brand">ANNOUNCEMENT STATUS</a>
                </nav>
                <!--Delete Modal-->

                <div class="modal fade edit-modal" id="delete-row" tabindex="-1" role="dialog" aria-labelledby="delete-title" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                        <div class="modal-content">
                            <div class="modal-body">
                                <p>Are you sure want to Delete this Announcement?</p>
                            </div>
                            <div class="modal-footer">
                                <form action="" method="POST">
                                    <input type="hidden" id="delete_announcementID" name="delete_announcementID"></input>
                                    <button type="submit" class="btn btn-primary" name="delete_submit">Delete</button>
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!--Edit Model-->
                <div class="modal fade edit-modal" id="edit-row" tabindex="-1" role="dialog" aria-labelledby="edit-title" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                        <div class="modal-content">
                        <span class="close" data-dismiss="modal">&times;</span>
                        <form action="" method="POST" id="edit-announcement" onsubmit="return validateform(this);">
                            <div class="modal-body">
                            <label for="title"><b>Title</b></label>
                            <input type="text" placeholder="Title " id="edit_announcementTitle" name="edit_announcementTitle"  required>

                            <label for="content"><b>Content</b></label>
                            <input type="text" placeholder="Announcement Content" id="edit_announcementContent" name="edit_announcementContent" required>

                            <label for="date"><b>Post Date</b></label>
                            <input type="date" placeholder="Announcement Date" id="edit_announcementDate" name="edit_announcementDate" required>
                            </div>
                            <div class="modal-footer">
                                <input type="hidden" id="edit_announcementID" name="edit_announcementID"></input>
                                <button type="reset" class="btn btn-success" onclick="resetEdit()">Reset</button>
                                <button type="submit" name="edit_submit" class="btn btn-primary">Save changes</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        </form>
                            </div>
                        </div>
                    </div>
                </div>


                <table class="table announcement-table">
                    <thead class="thead-light">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col" style="min-width: 120px;">Post Date</th>
                            <th scope="col" style="min-width: 300px;">Title</th>
                            <th scope="col">Content</th>
                            <th scope="col" style="min-width: 180px;">ACTION</th>
                        </tr>
                    </thead>
                    <tbody id ="announcementbody">

                    <?php 
                    
                    $query = $admin->getDataByQuery("SELECT announcementID, adminID, announcement_date, title, content FROM announcement ORDER BY announcement_date DESC");
                    
                    if (!empty($query)) {
                    for($i = 0; $i < sizeof($query); $i++) {

                      //loop
                    ?> 
                  
                    <tr>
                    <input type="hidden" class="announcementID" value='<?php echo ($query[$i]['announcementID']); ?>'></input>
                    <th scope = "row"><?php echo ($i +1 ); ?></th>
                    <td class="announcement_date"><?php print_r($query[$i]['announcement_date']); ?></td>
                    <td class="title"><?php echo ($query[$i]['title']); ?></td>
                    <td class="content"><?php echo ($query[$i]['content']); ?></td>
                    <td class="action">
                        <a href="#" class="edit-announcement-anchor" data-toggle="modal" data-target="#edit-row">Edit</a>
                        <span> | </span>
                        <a href="#" class="delete-announcement-anchor" data-toggle="modal" data-target="#delete-row">Delete</a>
                    </td>
                    </tr> 
                    <?php }
                    }?>

                    </tbody>
                </table>
                <br><br>
                <button type="button" id="myBtn" class="btn btn-outline-primary add-announcement" data-toggle="modal" data-target="#AnnouncementModal">Add new announcement</button>
                <div class="modal fade" id="AnnouncementModal" tabindex="-1" role="dialog" aria-labelledby="new-title" aria-hidden="true">
                <!-- <div id="AnnouncementModal" class="modal"> -->
                    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                        <div class="modal-content">
                            <form action="" method="POST" id="testing" onsubmit="return validateform(this);">
                                <span class="close" data-dismiss="modal">&times;</span>
                                <label for="title"><b>Title</b></label>
                                <input type="text" placeholder="Title " id="new_title" name="new_title" required>

                                <label for="content"><b>Content</b></label>
                                <input type="text" placeholder="Announcement Content" id="new_content" name="new_content" required>

                                <label for="date"><b>Post Date</b></label>
                                <input type="date" class="announcement_date" placeholder="Announcement Date" id="new_date" name="new_date" required> 

                                <button type="submit" name="new_submit" id="new_submit" class="btn btn-primary">Add new</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<script src="./script/announcement.js"></script>
</html>
