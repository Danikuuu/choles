<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] == 1 || $_SESSION["role"] == 2) {
    header("Location: ../index.php");
    exit();
}

require_once '../data-handling/db/connection.php';

$user_id = $_SESSION['user_id'];

$sql = "SELECT fname, lname, email, mobile, province, city, barangay, street FROM user WHERE id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>CHOLES - Menu</title>

    <!-- Custom fonts for this template-->
    <link rel="stylesheet" href="../admin//dashboard/vendor/fontawesome-free/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link rel="stylesheet" href="../admin/dashboard/css//sb-admin-2.min.css">

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar" style="background-color:  #059652;">

        <!-- Sidebar - Brand -->
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
                <div class="sidebar-brand-text mx-3">CHOLES <sup>Catering</sup></div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Menu</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Reservations
            </div>

            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link" href="./reservation.php">
                    <i class="fas fa-fw fa-utensils"></i>
                    <span>Reservations</span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="./messages.php">
                    <i class="fas fa-envelope"></i> Messages
                    <span id="unreadBadge" class="badge badge-danger" style="display: none;"></span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="./reservation_history.php">
                    <i class="fas fa-fw fa-utensils"></i>
                    <span>Reservation History</span></a>
            </li>

            <!-- Heading -->
            <div class="sidebar-heading">
                Feedback
            </div>

            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link" href="./feedback.php">
                    <i class="fas fa-fw fa-utensils"></i>
                    <span>Feedback</span></a>
            </li>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="couponnDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-ticket-alt fa-fw"></i>
                                <!-- Counter - Notifications -->
                                <span class="badge badge-danger badge-counter" id="couponCount">0</span>
                            </a>
                            <!-- Dropdown - Notifications -->
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="notificationDropdown">
                                <h6 class="dropdown-header">
                                    Coupons
                                </h6>
                                <div id="couponList">
                                    <p class="text-center p-3 text-gray-600">No new coupon</p>
                                </div>
                            </div>
                        </li>
                    <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="notificationDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bell fa-fw"></i>
                                <!-- Counter - Notifications -->
                                <span class="badge badge-danger badge-counter" id="notificationCount">0</span>
                            </a>
                            <!-- Dropdown - Notifications -->
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="notificationDropdown">
                                <h6 class="dropdown-header">
                                    Notifications
                                </h6>
                                <div id="notificationList">
                                    <p class="text-center p-3 text-gray-600">No new notifications</p>
                                </div>
                            </div>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">CHOLES Admin</span>
                                <img class="img-profile rounded-circle"
                                    src="../admin/dashboard/img/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="./profile.php">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="../../destroy.php" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->
                <div class="container-fluid">
                <div class="d-sm-flex align-items-center justify-content-between">
                        <h1 class="h3 mb-0 text-gray-800">Messanges</h1>
                    </div>

                    <div class="d-flex justify-content-center align-items-center">
                    <div class="p-3" style="z-index: 11">
                            <div id="toastMessage" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                                <div class="d-flex">
                                    <div class="toast-body">
                                        <?php
                                        if (isset($_SESSION['success'])) {
                                            echo $_SESSION['success'];
                                            unset($_SESSION['success']); // Clear message after showing
                                        } elseif (isset($_SESSION['error'])) {
                                            echo $_SESSION['error'];
                                            unset($_SESSION['error']); // Clear message after showing
                                        }
                                        ?>
                                    </div>
                                    <button type="button" class="btn me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"><i class="fas fa-times"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <h4>Inbox</h4>
            <ul id="messageList" class="list-group">
                <!-- Messages will be displayed here -->
            </ul>
        </div>
        <div class="col-md-8">
            <div id="chatBox" style="height: 400px; overflow-y: scroll; border: 1px solid #ccc; padding: 10px;">
                <!-- Chat messages will load here -->
            </div>
            <input type="hidden" id="receiverId" value="1">
            <textarea id="messageInput" class="form-control" placeholder="Type your message..."></textarea>
            <button id="sendMessage" class="btn btn-primary mt-2">Send</button>
        </div>
    </div>
</div>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>CHOLES Catering Services 2025</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="../destroy.php">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="../admin//dashboard/vendor/jquery/jquery.min.js"></script>
    <script src="../admin//dashboard/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="../admin//dashboard/vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="j../admin//dashboard/s/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="../admin//dashboard/vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="../admin//dashboard/js/demo/chart-area-demo.js"></script>
    <script src="../admin//dashboard/js/demo/chart-pie-demo.js"></script>
    <script src="../admin//dashboard/js/demo/chart-bar-demo.js"></script>
    <script src="../admin//dashboard/js/demo/datatables-demo.js"></script>  

    </script>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        var toastEl = document.getElementById('toastMessage');
        if (toastEl && toastEl.textContent.trim() !== "") {
            var toast = new bootstrap.Toast(toastEl);
            toast.show();
        }
    });
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        $("#sidebarToggleTop").click(function () {
            $(".sidebar").toggleClass("d-none d-md-block"); // Toggle sidebar visibility
        });
    });
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    let receiverId = null;

    function fetchMessages() {
    let chatBox = $("#chatBox");
    let receiverId = $("#receiverId").val(); // Get receiver ID dynamically

    if (!receiverId) return;

    $.ajax({
    url: "fetch_messages.php",
    type: "GET",
    data: { receiver_id: receiverId },
    dataType: "json",
    success: function(messages) {
        console.log("Messages fetched:", messages); // Debugging

        let chatBox = $("#chatBox");
        chatBox.html("");

        if (!Array.isArray(messages) || messages.length === 0) {
            chatBox.append(`
                <div class="text-center mt-3">
                    <p>No messages yet.</p>
                    <button id="startNewChat" class="btn btn-primary">+ Start New Chat</button>
                </div>
            `);

            $("#startNewChat").click(function () {
                showUserListModal();
            });

            return;
        }

        messages.forEach(msg => {
                    let msgClass = msg.sender_id == "<?php echo $_SESSION['user_id']; ?>" ? "text-right text-primary" : "text-left text-dark";
                    chatBox.append(`<p class="${msgClass}"><strong>${msg.fname}:</strong> ${msg.message}</p>`);
                });

                chatBox.scrollTop(chatBox[0].scrollHeight);
    },
    error: function(xhr, status, error) {
        console.error("Error fetching messages:", error);
    }
});

}

function showUserListModal() {
    $.ajax({
        url: "fetch_users.php",
        type: "GET",
        dataType: "json",
        success: function(response) {
            if (response.error) {
                console.error("Server Error:", response.error);
                alert("Error: " + response.error);
                return;
            }

            if (!Array.isArray(response)) {
                console.error("Unexpected response:", response);
                alert("Error fetching users");
                return;
            }

            let modalContent = `
                <div class="modal fade" id="userListModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Select a User</h5>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <ul class="list-group">
            `;

            response.forEach(user => {
                modalContent += `
                    <li class="list-group-item user-item" data-user-id="${user.id}">
                        ${user.fname}
                    </li>
                `;
            });

            modalContent += `
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            $("body").append(modalContent);
            $("#userListModal").modal("show");

            $(".user-item").click(function () {
                let selectedUserId = $(this).data("user-id");
                $("#receiverId").val(selectedUserId);
                $("#userListModal").modal("hide");
                fetchMessages(); // Load chat with selected user
            });
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error:", error);
            alert("Failed to load users: " + error);
        }
    });
}

    function fetchInbox() {
    let messageList = $("#messageList");

    // Show a loading state before the request
    messageList.html('<li class="list-group-item text-center"><i class="fas fa-spinner fa-spin"></i> Loading messages...</li>');

    $.ajax({
        url: "fetch_inbox.php",
        type: "GET",
        dataType: "json",
        success: function(response) {
            messageList.html("");

            if (!Array.isArray(response)) {
                console.error("Unexpected response:", response);
                messageList.append('<li class="list-group-item text-danger text-center"><i class="fas fa-exclamation-circle"></i> Error fetching messages</li>');
                return;
            }

            if (response.length === 0) {
                messageList.append('<li class="list-group-item text-center text-muted"><i class="fas fa-envelope-open-text"></i> No messages yet</li>');
            } else {
                response.forEach(user => {
                    let unreadBadge = user.unread_count > 0 
                        ? `<span class="badge badge-danger float-right">${user.unread_count}</span>` 
                        : "";

                    messageList.append(`
                        <li class="list-group-item user-item d-flex align-items-center justify-content-between" 
                            data-receiver-id="${user.sender_id}" 
                            style="cursor: pointer;">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-user-circle text-primary mr-2" style="font-size: 1.5rem;"></i>
                                <strong>${user.fname}</strong>
                            </div>
                            ${unreadBadge}
                        </li>
                    `);
                });

                // Add click event for user-item
                $(".user-item").click(function () {
                    let receiverId = $(this).data("receiver-id");
                    $("#receiverId").val(receiverId); // Update the hidden input field
                    console.log("Receiver ID set to:", receiverId);
                });
            }
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error:", error);
            messageList.html('<li class="list-group-item text-danger text-center"><i class="fas fa-exclamation-circle"></i> Failed to load messages</li>');
        }
    });
}




    $("#messageList").on("click", ".user-item", function() {
        receiverId = $(this).data("receiver-id");
        $("#receiverId").val(receiverId);
        fetchMessages();
    });

    $("#sendMessage").click(function () {
    let message = $("#messageInput").val().trim();
    let receiverId = $("#receiverId").val(); // Get receiver ID from hidden input

    if (message !== "" && receiverId) {
        $.ajax({
            url: "send_message.php",
            type: "POST",
            data: { receiver_id: receiverId, message: message },
            dataType: "json",
            success: function (response) {
                if (response.status === "success") {
                    $("#messageInput").val(""); // Clear the input field
                    fetchMessages(); // Reload messages
                    fetchInbox(); // Reload inbox
                } else if (response.status === "error") {
                    alert("Error sending message.");
                    console.error("Message send error:", response.error);
                } else if (response.status === "empty") {
                    alert("Message cannot be empty.");
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error:", error);
            }
        });
    } else {
        alert("Select a user to send a message.");
    }
});


    fetchInbox();
    setInterval(fetchMessages, 2000);
    setInterval(fetchInbox, 5000);
});
</script>
<script>
function fetchUnreadCount() {
    $.ajax({
        url: "fetch_unread.php",
        type: "GET",
        dataType: "json",
        success: function(response) {
            if (response.unread_count > 0) {
                $("#unreadBadge").text(response.unread_count).show();
            } else {
                $("#unreadBadge").hide();
            }
        }
    });
}

setInterval(fetchUnreadCount, 5000); // Check for new messages every 5 seconds
</script>
<script>
        document.addEventListener("DOMContentLoaded", function () {
    function fetchNotifications() {
        fetch("fetch_notifications.php") // Replace with your backend endpoint
            .then(response => response.json())
            .then(data => {
                let count = data.length;
                let notificationCount = document.getElementById("notificationCount");
                let notificationList = document.getElementById("notificationList");

                if (count > 0) {
                    notificationCount.innerText = count;
                    notificationCount.style.display = "inline-block";

                    notificationList.innerHTML = "";
                    data.forEach(notification => {
                        let item = document.createElement("a");
                        item.href = "#"; // Update with actual link
                        item.classList.add("dropdown-item", "d-flex", "align-items-center");
                        item.innerHTML = `
                            <div class="mr-3">
                                <div class="icon-circle bg-primary">
                                    <i class="fas fa-info text-white"></i>
                                </div>
                            </div>
                            <div>
                                <div class="small text-gray-500">${notification.date}</div>
                                <span class="font-weight-bold">${notification.message}</span>
                            </div>
                        `;
                        notificationList.appendChild(item);
                    });
                } else {
                    notificationCount.style.display = "none";
                    notificationList.innerHTML = '<p class="text-center p-3 text-gray-600">No new notifications</p>';
                }
            });
    }

    // Fetch notifications when page loads
    fetchNotifications();

    // Mark notifications as seen when dropdown is clicked
    document.getElementById("notificationDropdown").addEventListener("click", function () {
        fetch("mark_reservations_seen.php", { method: "POST" });
        document.getElementById("notificationCount").style.display = "none";
    });

    // Auto-refresh notifications every 30 seconds
    setInterval(fetchNotifications, 30000);
});
    </script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap (CSS and JS) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>