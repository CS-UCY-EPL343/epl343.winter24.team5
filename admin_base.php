<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include the navbar
require_once 'navbar.php';
require_once 'db_functions.php';
require_once 'session_check.php';


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: index.php");
    exit();
}

try {
    // Call the GetAllPolls stored procedure
    $polls = getAllPolls(); // Assuming getAllPolls() is defined in db_functions.php
} catch (PDOException $e) {
    $error = handleSqlError($e);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['poll_id'])) {
    $pollID = intval($_POST['poll_id']);
    updatePollStatusAndVerdict($pollID);
    header("Location: " . $_SERVER['PHP_SELF']); // Refresh the page
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<style>
    .sidebar {
        display: flex;
        flex-direction: column;
        height: 100%;
        justify-content: space-between;
        background-color: #f8f9fa;
        /* Adjust background as needed */
        padding: 10px;
    }

    .sidebar-title {
        font-size: 1.5rem;
        margin-bottom: 1rem;
        text-align: left;
    }

    .sidebar-links {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .sidebar-links li {
        margin-bottom: 0.5rem;
    }

    .sidebar-links a {
        text-decoration: none;
        color: #000;
        font-size: 1rem;
        transition: color 0.2s ease;
    }

    .sidebar-links a:hover {
        color: #007bff;
    }

    .sidebar-bottom {
        text-align: center;
        margin-top: auto;
        /* Push to the bottom */
    }

    .sidebar-link {
        display: inline-block;
        text-decoration: none;
        text-align: center;
    }

    .sidebar-icon {
        width: 50px;
        /* Increase width */
        height: 50px;
        /* Increase height */
        transition: transform 0.2s ease;
    }

    .sidebar-link:hover .sidebar-icon {
        transform: scale(1.2);
    }

    .sidebar-links a.active {
        background-color: #6db4ff;
    }
</style>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://d3js.org/d3.v3.min.js"></script> <!-- Include D3.js -->
</head>

<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h3 class="sidebar-title" style="text-align: center;">Admin Dashboard</h3>
            <ul class="sidebar-links">
                <li><a href="admin_page.php" class="<?= basename($_SERVER['PHP_SELF']) == 'admin_page.php' ? 'active' : '' ?>">Polls</a></li>
                <li><a href="jobs.php" class="<?= basename($_SERVER['PHP_SELF']) == 'jobs.php' ? 'active' : '' ?>">Jobs</a></li>
                <li><a href="Tasks.php" class="<?= basename($_SERVER['PHP_SELF']) == 'Tasks.php' ? 'active' : '' ?>">Tasks</a></li>
                <li><a href="writeAiChat.php" class="<?= basename($_SERVER['PHP_SELF']) == 'writeAiChat.php' ? 'active' : '' ?>">ChatBot</a></li>
                <li><a href="create_poll.php" class="<?= basename($_SERVER['PHP_SELF']) == 'create_poll.php' ? 'active' : '' ?>">Create Poll</a></li>
                <li><a href="create_tasks.php" class="<?= basename($_SERVER['PHP_SELF']) == 'create_tasks.php' ? 'active' : '' ?>">Create a Task</a></li>
                <li><a href="pending_user_approvals.php" class="<?= basename($_SERVER['PHP_SELF']) == 'pending_user_approvals.php' ? 'active' : '' ?>">User Approvals</a></li>
            </ul>
            <div class="sidebar-bottom">
                <a href="admin_easter_egg.html" class="sidebar-link">
                    <img src="videos/dinoegg.png" alt="Dino Egg" class="sidebar-icon">
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="dashboard-main">
            <div id="graph-container"></div> <!-- Add a container for the graph -->
        </main>
    </div>
    <?php require_once 'footer.php'; ?>

    <!-- JavaScript -->
    <script>
        var w = 1000,
            h = 800,
            circleWidth = 10; // Default circle size

        var palette = {
            "lightgray": "#E5E8E8",
            "gray": "#708284",
            "mediumgray": "#536870",
            "blue": "#3B757F"
        };

        var colors = d3.scale.category20();

        var nodes = [{
                name: "Polls",
                value: 30
            },
            {
                name: "Jobs",
                value: 20,
                target: [0]
            },
            {
                name: "Tasks",
                value: 25,
                target: [0, 1]
            },
            {
                name: "ChatBot",
                value: 15,
                target: [0, 1, 2]
            },
            {
                name: "Create Poll",
                value: 18,
                target: [0, 3]
            },
            {
                name: "Create a Task",
                value: 22,
                target: [0, 3, 4]
            },
            {
                name: "User Approvals",
                value: 17,
                target: [0, 1, 2]
            },
            {
                name: "Settings",
                value: 10,
                target: [0, 1, 2]
            }
        ];

        var links = [];

        for (var i = 0; i < nodes.length; i++) {
            if (nodes[i].target !== undefined) {
                for (var x = 0; x < nodes[i].target.length; x++) {
                    links.push({
                        source: nodes[i],
                        target: nodes[nodes[i].target[x]]
                    });
                }
            }
        }

        var myChart = d3.select("#graph-container")
            .append("svg")
            .attr("width", w)
            .attr("height", h);

        var force = d3.layout.force()
            .nodes(nodes)
            .links([])
            .gravity(0.1)
            .charge(-1000)
            .size([w, h]);

        var link = myChart.selectAll('line')
            .data(links).enter().append('line')
            .attr('stroke', palette.gray)
            .attr('strokewidth', '2');

        var node = myChart.selectAll('g')
            .data(nodes).enter()
            .append('g')
            .call(force.drag);

        node.append('circle')
            .attr('r', function(d) {
                return circleWidth + d.value + 20; // Scale size based on `value`
            })
            .attr('fill', function(d, i) {
                return colors(i); // Assign unique color
            })
            .attr('stroke', function(d, i) {
                return 'black'; // Add a black border to circles
            })
            .attr('strokewidth', '2');

        node.append('text')
            .text(function(d) {
                return d.name;
            })
            .attr('font-family', 'Helvetica')
            .attr('text-anchor', 'middle')
            .attr('font-size', '.8em')
            .attr('dy', 4); // Center text inside the circle

        force.on('tick', function(e) {
            node.attr('transform', function(d) {
                return 'translate(' + d.x + ',' + d.y + ')';
            });

            link.attr('x1', function(d) {
                    return d.source.x;
                })
                .attr('y1', function(d) {
                    return d.source.y;
                })
                .attr('x2', function(d) {
                    return d.target.x;
                })
                .attr('y2', function(d) {
                    return d.target.y;
                });
        });

        force.start();
    </script>
</body>

</html>